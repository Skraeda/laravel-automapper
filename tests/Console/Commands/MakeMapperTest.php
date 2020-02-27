<?php

namespace Skraeda\AutoMapper\Tests\Console\Commands;

use Orchestra\Testbench\TestCase;
use Skraeda\AutoMapper\Support\Facades\AutoMapperFacade;
use Skraeda\AutoMapper\Providers\AutoMapperServiceProvider;

/**
 * Feature test for \Skraeda\AutoMapper\Console\Commands\MakeMapper
 *
 * @author Gunnar Örn Baldursson <gunnar@sjukraskra.is>
 */
class MakeMapperTest extends TestCase
{
    public function testCommand()
    {
        $this->assertEquals(0, $this->artisan('make:mapper', ['name' => 'MyMapper']));
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
