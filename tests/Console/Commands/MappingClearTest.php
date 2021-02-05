<?php

namespace Skraeda\AutoMapper\Tests\Console\Commands;

use Mockery;
use Orchestra\Testbench\TestCase;
use Skraeda\AutoMapper\Contracts\AutoMapperCacheContract;
use Skraeda\AutoMapper\Support\Facades\AutoMapperFacade;
use Skraeda\AutoMapper\Providers\AutoMapperServiceProvider;

/**
 * Feature test for \Skraeda\AutoMapper\Console\Commands\MappingClear
 *
 * @author Gunnar Örn Baldursson <gunnar@sjukraskra.is>
 */
class MappingClearTest extends TestCase
{
    /**
     * @environment-setup useEnv
     */
    public function testCommand()
    {
        $this->artisan('mapping:clear')
             ->assertExitCode(0)
             ->expectsOutput('AutoMapper cache cleared!');
    }

    /**
     * Environment with Cache enabled but no cache set
     *
     * @param \Illuminate\Foundation\Application $app
     * @return void
     */
    protected function useEnv($app)
    {
        $mockCache = Mockery::mock(AutoMapperCacheContract::class);

        $mockCache->shouldReceive('clear');

        $app->bind(AutoMapperCacheContract::class, fn () => $mockCache);
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
