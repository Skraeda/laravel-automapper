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
}
