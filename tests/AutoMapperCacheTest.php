<?php

namespace Skraeda\AutoMapper\Tests;

use Exception;
use Illuminate\Filesystem\Filesystem;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Skraeda\AutoMapper\AutoMapperCache;
use Skraeda\AutoMapper\Contracts\AutoMapperScriptLoaderContract;
use Skraeda\AutoMapper\Exceptions\AutoMapperCacheException;

/**
 * AutoMapperCache unit tests
 *
 * @author Gunnar Örn Baldursson <gunnar@sjukraskra.is>
 */
class AutoMapperCacheTest extends TestCase
{
    /**
     * Mock filesystem
     *
     * @var \Illuminate\Filesystem\Filesystem|\Mockery\MockInterface
     */
    protected Filesystem|MockInterface $mockFs;

    /**
     * Mock Script Loader
     *
     * @var \Skraeda\AutoMapper\Contracts\AutoMapperScriptLoaderContract|\Mockery\MockInterface
     */
    protected AutoMapperScriptLoaderContract|MockInterface $mockLoader;

    /**
     * Setup
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->mockFs = Mockery::mock(Filesystem::class);
        $this->mockLoader = Mockery::mock(AutoMapperScriptLoaderContract::class);
    }

    /** @test */
    public function itRaisesExceptionIfGetWithNonString()
    {
        $this->expectException(AutoMapperCacheException::class);

        $cache = new AutoMapperCache($this->mockLoader, $this->mockFs, __DIR__);

        $cache->get([]);
    }

    /** @test */
    public function itRaisesExceptionIfGetWithStringDoesntEndInPhp()
    {
        $this->expectException(AutoMapperCacheException::class);

        $cache = new AutoMapperCache($this->mockLoader, $this->mockFs, __DIR__);

        $cache->get('key');
    }

    /** @test */
    public function itLoadsDefaultIfFileDoesntExistOnGet()
    {
        $default = 123;
        
        $this->mockFs->shouldReceive('exists')->andReturn(false);

        $cache = new AutoMapperCache($this->mockLoader, $this->mockFs, __DIR__);

        $this->assertEquals($default, $cache->get('key.php', $default));
    }

    /** @test */
    public function itThrowsExceptionIfFailingToLoadFile()
    {
        $this->expectException(AutoMapperCacheException::class);

        $file = 'somethingthatdoesntexist.php';
        
        $this->mockFs->shouldReceive('exists')->with(__DIR__.DIRECTORY_SEPARATOR.$file)->andReturn(true);
        $this->mockLoader->shouldReceive('require')->with(__DIR__.DIRECTORY_SEPARATOR.$file)->andThrow(new Exception("Random error"));

        $cache = new AutoMapperCache($this->mockLoader, $this->mockFs, __DIR__);

        $cache->get($file);
    }

    /** @test */
    public function itLoadsFileIfItExists()
    {
        $file = 'exists.php';
        
        $this->mockFs->shouldReceive('exists')->with(__DIR__.DIRECTORY_SEPARATOR.$file)->andReturn(true);
        $this->mockLoader->shouldReceive('require')->with(__DIR__.DIRECTORY_SEPARATOR.$file)->andReturn('value');

        $cache = new AutoMapperCache($this->mockLoader, $this->mockFs, __DIR__);

        $this->assertEquals('value', $cache->get($file));
    }

    /** @test */
    public function itCanSetAFile()
    {
        $file = 'cache.php';

        $path = __DIR__.DIRECTORY_SEPARATOR.$file;
        
        $this->mockFs->shouldReceive('makeDirectory')->with(__DIR__, 0755, true, true);
        $this->mockFs->shouldReceive('exists')->with($path)->andReturn(false);
        $this->mockFs->shouldReceive('put')->with($path, "<?php return 'foo';".PHP_EOL);
        $this->mockLoader->shouldReceive('require')->with($path);

        $cache = new AutoMapperCache($this->mockLoader, $this->mockFs, __DIR__);

        $this->assertTrue($cache->set($file, 'foo'));
    }

    /** @test */
    public function itRemovesFileIfItIsUnableToLoadIt()
    {
        $this->expectException(AutoMapperCacheException::class);
        
        $file = 'invalid.php';

        $path = __DIR__.DIRECTORY_SEPARATOR.$file;
        
        $this->mockFs->shouldReceive('makeDirectory')->with(__DIR__, 0755, true, true);
        $this->mockFs->shouldReceive('exists')->with($path)->andReturn(false);
        $this->mockFs->shouldReceive('put')->with($path, "<?php return 'foo';".PHP_EOL);
        $this->mockLoader->shouldReceive('require')->with($path)->andThrow(new Exception("Some error"));
        $this->mockFs->shouldReceive('delete')->with($path);

        $cache = new AutoMapperCache($this->mockLoader, $this->mockFs, __DIR__);

        $cache->set($file, 'foo');
    }

    /** @test */
    public function itDeletesAFileIfItExists()
    {
        $file = 'delete.php';

        $path = __DIR__.DIRECTORY_SEPARATOR.$file;
        
        $this->mockFs->shouldReceive('exists')->with($path)->andReturn(true);
        $this->mockFs->shouldReceive('delete')->with($path)->andReturn(true);

        $cache = new AutoMapperCache($this->mockLoader, $this->mockFs, __DIR__);

        $this->assertTrue($cache->delete($file));
    }

    /** @test */
    public function itCanClearCacheDir()
    {
        $this->mockFs->shouldReceive('cleanDirectory')->with('someDir')->andReturn(true);

        $cache = new AutoMapperCache($this->mockLoader, $this->mockFs, 'someDir');

        $this->assertTrue($cache->clear('someDir'));
    }

    /** @test */
    public function itCanSayIfCacheKeyExists()
    {
        $file = 'exists.php';

        $path = __DIR__.DIRECTORY_SEPARATOR.$file;
        
        $this->mockFs->shouldReceive('exists')->with($path)->andReturn(true);

        $cache = new AutoMapperCache($this->mockLoader, $this->mockFs, __DIR__);

        $this->assertTrue($cache->has($file));
    }

    /** @test */
    public function itCanGetMultiple()
    {
        $key1 = 'ananas.php';
        $key2 = 'banani.php';

        $path1 = __DIR__.DIRECTORY_SEPARATOR.$key1;
        $path2 = __DIR__.DIRECTORY_SEPARATOR.$key2;
        
        $this->mockFs->shouldReceive('exists')->with($path1)->andReturn(false);
        $this->mockFs->shouldReceive('exists')->with($path2)->andReturn(true);
        $this->mockLoader->shouldReceive('require')->with($path2)->andReturn('foo');

        $cache = new AutoMapperCache($this->mockLoader, $this->mockFs, __DIR__);

        $results = $cache->getMultiple([$key1, $key2]);

        $this->assertCount(2, $results);
        $this->assertNull($results[$key1]);
        $this->assertEquals('foo', $results[$key2]);
    }

    /** @test */
    public function itCanNotSetMultipleWithInvalidKeys()
    {
        $this->expectException(AutoMapperCacheException::class);

        $cache = new AutoMapperCache($this->mockLoader, $this->mockFs, __DIR__);

        $cache->setMultiple(10);
    }

    /** @test */
    public function itCanSetMultiple()
    {
        $key1 = 'epli.php';
        $key2 = 'appelsina.php';

        $path1 = __DIR__.DIRECTORY_SEPARATOR.$key1;
        $path2 = __DIR__.DIRECTORY_SEPARATOR.$key2;
        
        $this->mockFs->shouldReceive('makeDirectory')->with(__DIR__, 0755, true, true);
        $this->mockFs->shouldReceive('exists')->with($path1)->andReturn(false);
        $this->mockFs->shouldReceive('put')->with($path1, "<?php return 'foo';".PHP_EOL);
        $this->mockLoader->shouldReceive('require')->with($path1);
        $this->mockFs->shouldReceive('makeDirectory')->with(__DIR__, 0755, true, true);
        $this->mockFs->shouldReceive('exists')->with($path2)->andReturn(false);
        $this->mockFs->shouldReceive('put')->with($path2, "<?php return 'bar';".PHP_EOL);
        $this->mockLoader->shouldReceive('require')->with($path2);

        $cache = new AutoMapperCache($this->mockLoader, $this->mockFs, __DIR__);

        $this->assertTrue($cache->setMultiple([ $key1 => 'foo', $key2 => 'bar' ]));
    }

    /** @test */
    public function itCanDeleteMultiple()
    {
        $key1 = 'delete1.php';
        $key2 = 'delete2.php';

        $path1 = __DIR__.DIRECTORY_SEPARATOR.$key1;
        $path2 = __DIR__.DIRECTORY_SEPARATOR.$key2;
        
        $this->mockFs->shouldReceive('exists')->with($path1)->andReturn(true);
        $this->mockFs->shouldReceive('delete')->with($path1)->andReturn(true);
        $this->mockFs->shouldReceive('exists')->with($path2)->andReturn(true);
        $this->mockFs->shouldReceive('delete')->with($path2)->andReturn(true);

        $cache = new AutoMapperCache($this->mockLoader, $this->mockFs, __DIR__);

        $this->assertTrue($cache->deleteMultiple([$key1, $key2]));
    }

    /** @test */
    public function itReturnsFalseIfItFailsToDeleteMultiple()
    {
        $key1 = 'delete1.php';
        $key2 = 'delete2.php';

        $path1 = __DIR__.DIRECTORY_SEPARATOR.$key1;
        $path2 = __DIR__.DIRECTORY_SEPARATOR.$key2;
        
        $this->mockFs->shouldReceive('exists')->with($path1)->andReturn(true);
        $this->mockFs->shouldReceive('delete')->with($path1)->andReturn(true);
        $this->mockFs->shouldReceive('exists')->with($path2)->andReturn(true);
        $this->mockFs->shouldReceive('delete')->with($path2)->andReturn(false);

        $cache = new AutoMapperCache($this->mockLoader, $this->mockFs, __DIR__);

        $this->assertFalse($cache->deleteMultiple([$key1, $key2]));
    }
}
