<?php

namespace Skraeda\AutoMapper\Tests;

use Orchestra\Testbench\TestCase;
use Skraeda\AutoMapper\AutoMapperFinder;
use Skraeda\AutoMapper\Exceptions\AutoMapperFinderException;
use Skraeda\AutoMapper\Providers\AutoMapperServiceProvider;
use Skraeda\AutoMapper\Tests\Data\A;
use Skraeda\AutoMapper\Tests\Data\ABMapper;
use Skraeda\AutoMapper\Tests\Data\B;

/**
 * AutoMapperFinder unit tests
 *
 * @author Gunnar Örn Baldursson <gunnar@sjukraskra.is>
 */
class AutoMapperFinderTest extends TestCase
{
    /** @test */
    public function itCanScanDirectoriesForCustomMappers()
    {
        $finder = new AutoMapperFinder(__DIR__, 'Skraeda\\AutoMapper\\Tests\\');

        $mappers = $finder->scanMappingDirectory(['Data']);

        $this->assertCount(1, $mappers);
        $this->assertEquals(A::class, $mappers[ABMapper::class]['source']);
        $this->assertEquals(B::class, $mappers[ABMapper::class]['target']);
    }

    /** @test */
    public function itCanScanSingleDirectoryForCustomMappers()
    {
        $finder = new AutoMapperFinder(__DIR__, 'Skraeda\\AutoMapper\\Tests\\');

        $mappers = $finder->scanMappingDirectory('Data');

        $this->assertCount(1, $mappers);
        $this->assertEquals(A::class, $mappers[ABMapper::class]['source']);
        $this->assertEquals(B::class, $mappers[ABMapper::class]['target']);
    }

    /** @test */
    public function itRaisesExceptionIfItFindsNonMappersWhenScanning()
    {
        $this->expectException(AutoMapperFinderException::class);

        (new AutoMapperFinder(__DIR__))->scanMappingDirectory('cache');
    }

    /** @test */
    public function itReturnsEmptyArrayIfNoPathsAreActuallyFound()
    {
        $finder = new AutoMapperFinder(__DIR__, 'Skraeda\\AutoMapper\\Tests\\');

        $mappers = $finder->scanMappingDirectory('SomethingThatDoesntExist');
        
        $this->assertCount(0, $mappers);
    }

    /**
     * {@inheritDoc}
     */
    protected function getPackageProviders($app)
    {
        return [
            AutoMapperServiceProvider::class
        ];
    }
}
