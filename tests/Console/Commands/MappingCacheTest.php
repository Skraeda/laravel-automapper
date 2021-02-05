<?php

namespace Skraeda\AutoMapper\Tests\Console\Commands;

use Mockery;
use Orchestra\Testbench\TestCase;
use Skraeda\AutoMapper\Contracts\AutoMapperCacheContract;
use Skraeda\AutoMapper\Contracts\AutoMapperFinderContract;
use Skraeda\AutoMapper\Support\Facades\AutoMapperFacade;
use Skraeda\AutoMapper\Providers\AutoMapperServiceProvider;

/**
 * Feature test for \Skraeda\AutoMapper\Console\Commands\MappingCache
 *
 * @author Gunnar Örn Baldursson <gunnar@sjukraskra.is>
 */
class MappingCacheTest extends TestCase
{
    /**
     * @environment-setup useEnv
     */
    public function testCommand()
    {
        $this->artisan('automapper:cache')
             ->assertExitCode(0)
             ->expectsOutput('AutoMapper cache cleared!')
             ->expectsOutput('AutoMapper cached successfully!');
    }

    /**
     * Environment with Cache enabled but no cache set
     *
     * @param \Illuminate\Foundation\Application $app
     * @return void
     */
    protected function useEnv($app)
    {
        /** @var \Mockery\MockInterface */
        $mockCache = Mockery::mock(AutoMapperCacheContract::class);

        $mockCache->shouldReceive('clear');
        $mockCache->shouldReceive('set')->with('automapper.php', []);

        $app->bind(AutoMapperCacheContract::class, fn () => $mockCache);

        /** @var \Mockery\MockInterface */
        $mockFinder = Mockery::mock(AutoMapperFinderContract::class);

        $mockFinder->shouldReceive('scanMappingDirectory')->with(['Mappings'])->andReturn([]);

        $app->bind(AutoMapperFinderContract::class, fn () => $mockFinder);

        $app['config']->set('mapping', [
            'custom' => [],
            'scan' => [
                'enabled' => true,
                'dirs' => ['Mappings']
            ],
            'cache' => [
                'key' => 'automapper.php'
            ]
        ]);
    }

    /**
     * {@inheritDoc}
     */
    protected function getPackageProviders($app)
    {
        return [AutoMapperServiceProvider::class];
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
}
