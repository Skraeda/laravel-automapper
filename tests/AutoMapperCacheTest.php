<?php

namespace Skraeda\AutoMapper\Tests;

use Illuminate\Filesystem\Filesystem;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Skraeda\AutoMapper\AutoMapperCache;
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
     * Setup
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->mockFs = Mockery::mock(Filesystem::class);
    }

    /** @test */
    public function itRaisesExceptionIfGetWithNonString()
    {
        $this->expectException(AutoMapperCacheException::class);

        $cache = new AutoMapperCache($this->mockFs, __DIR__);

        $cache->get([]);
    }

    /** @test */
    public function itRaisesExceptionIfGetWithStringDoesntEndInPhp()
    {
        $this->expectException(AutoMapperCacheException::class);

        $cache = new AutoMapperCache($this->mockFs, __DIR__);

        $cache->get('key');
    }

    /** @test */
    public function itLoadsDefaultIfFileDoesntExistOnGet()
    {
        $default = 123;
        
        $this->mockFs->shouldReceive('exists')->andReturn(false);

        $cache = new AutoMapperCache($this->mockFs, __DIR__);

        $this->assertEquals($default, $cache->get('key.php', $default));
    }
}
