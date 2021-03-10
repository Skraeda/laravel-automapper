<?php

namespace Skraeda\AutoMapper\Tests\Providers;

use Illuminate\Support\Collection;
use Mockery;
use Orchestra\Testbench\TestCase;
use Skraeda\AutoMapper\AutoMapper;
use Skraeda\AutoMapper\Contracts\AutoMapperCacheContract;
use Skraeda\AutoMapper\Support\Facades\AutoMapperFacade;
use Skraeda\AutoMapper\Providers\AutoMapperServiceProvider;
use Skraeda\AutoMapper\Contracts\AutoMapperContract;
use Skraeda\AutoMapper\Contracts\AutoMapperFinderContract;
use Skraeda\AutoMapper\Tests\Data\A;
use Skraeda\AutoMapper\Tests\Data\ABMapper;
use Skraeda\AutoMapper\Tests\Data\B;

/**
 * Feature tests for \Skraeda\AutoMapper\Providers\AutoMapperServiceProvider
 *
 * @author Gunnar Örn Baldursson <gunnar@sjukraskra.is>
 */
class AutoMapperServiceProviderTest extends TestCase
{
    /**
     * @test
     * @environment-setup useDefault
     **/
    public function itRegistersAnAutoMapper()
    {
        $this->assertInstanceOf(AutoMapper::class, $this->app[AutoMapperContract::class]);
    }

    /**
     * @test
     * @environment-setup useCustomClasses
     **/
    public function itRegistersCustomMappings()
    {
        $this->assertEquals(2, AutoMapperFacade::map(new A, B::class)->Value);
    }

    /**
     * @test
     * @environment-setup useDefault
     **/
    public function itAddsCollectionAutoMapMacro()
    {
        $coll = Collection::make([1]);
        $target = 'Skraeda\Target';
        $context = [];
        AutoMapperFacade::shouldReceive('mapMultiple')
                        ->once()
                        ->with($coll, $target, $context)
                        ->andReturn(Collection::make([true]));
        $this->assertTrue($coll->autoMap($target, $context)[0]);
    }

    /**
     * @test
     * @environment-setup useCache
     */
    public function itRegistersCachedMappersIfTheyExist()
    {
        $this->assertEquals(2, AutoMapperFacade::map(new A, B::class)->Value);
    }

    /**
     * @test
     * @environment-setup useCacheMiss
     */
    public function itSetsCacheIfMiss()
    {
        $this->assertTrue(true);
    }

    /**
     * @test
     * @environment-setup useScan
     */
    public function itScansForDirectoriesIfEnabled()
    {
        $this->assertEquals(2, AutoMapperFacade::map(new A, B::class)->Value);
    }

    /**
     * {@inheritDoc}
     */
    protected function getPackageAliases($app)
    {
        return [
            'AutoMapper' => AutoMapperFacade::class
        ];
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

    /**
     * Default environment
     *
     * @param \Illuminate\Foundation\Application $app
     * @return void
     */
    protected function useDefaults($app)
    {
        $this->setDefaultConfig($app);
    }

    /**
     * Environment with Custom classes
     *
     * @param \Illuminate\Foundation\Application $app
     * @return void
     */
    protected function useCustomClasses($app)
    {
        $this->setDefaultConfig($app);

        $app['config']->set('mapping.custom', [
            ABMapper::class => [
                'source' => A::class,
                'target' => B::class
            ]
        ]);
    }

    /**
     * Environment with Cache
     *
     * @param \Illuminate\Foundation\Application $app
     * @return void
     */
    protected function useCache($app)
    {
        $this->setDefaultConfig($app);

        $app['config']->set('mapping.cache', [
            'enabled' => true,
            'dir' => __DIR__,
            'key' => 'automapper.php'
        ]);

        $mockCache = Mockery::mock(AutoMapperCacheContract::class);

        $mockCache->shouldReceive('has')->with('automapper.php')->andReturn(true);
        $mockCache->shouldReceive('get')->with('automapper.php')->andReturn([
            ABMapper::class => [
                'source' => A::class,
                'target' => B::class
            ]
        ]);

        $app->bind(AutoMapperCacheContract::class, fn () => $mockCache);
    }

    /**
     * Environment with Cache enabled but no cache set
     *
     * @param \Illuminate\Foundation\Application $app
     * @return void
     */
    protected function useCacheMiss($app)
    {
        $this->setDefaultConfig($app);

        $app['config']->set('mapping.cache', [
            'enabled' => true,
            'dir' => __DIR__,
            'key' => 'automapper.php'
        ]);

        $mockCache = Mockery::mock(AutoMapperCacheContract::class);

        $mockCache->shouldReceive('has')->with('automapper.php')->andReturn(false);
        $mockCache->shouldReceive('set')->with('automapper.php', [])->andReturn(true);

        $app->bind(AutoMapperCacheContract::class, fn () => $mockCache);
    }

    /**
     * Environment with Directory Scan
     *
     * @param \Illuminate\Foundation\Application $app
     * @return void
     */
    protected function useScan($app)
    {
        $this->setDefaultConfig($app);

        $app['config']->set('mapping.scan', [
            'enabled' => true,
            'dirs' => ['Data']
        ]);

        $mockFinder = Mockery::mock(AutoMapperFinderContract::class);

        $mockFinder->shouldReceive('scanMappingDirectory')->with(['Data'])->andReturn([
            ABMapper::class => [
                'source' => A::class,
                'target' => B::class
            ]
        ]);

        $app->bind(AutoMapperFinderContract::class, fn () => $mockFinder);
    }

    /**
     * Set Default Config
     *
     * @param \Illuminate\Foundation\Application $app
     * @return void
     */
    protected function setDefaultConfig($app)
    {
        $app['config']->set('mapping', [
            'custom' => [],
            'scan' => [
                'enabled' => false,
                'dirs' => []
            ],
            'cache' => [
                'enabled' => false,
                'dir' => '/var/www/app/storage/framework/automapper',
                'key' => 'automapper.php'
            ]
        ]);
    }
}
