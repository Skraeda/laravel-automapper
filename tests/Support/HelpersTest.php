<?php

namespace Skraeda\AutoMapper\Tests\Support;

use Illuminate\Support\Collection;
use Orchestra\Testbench\TestCase;
use Skraeda\AutoMapper\Providers\AutoMapperServiceProvider;
use Skraeda\AutoMapper\Support\Facades\AutoMapperFacade;

/**
 * Unit tests for function helpers.
 *
 * @author Gunnar Örn Baldursson <gunnar@sjukraskra.is>
 */
class HelpersTest extends TestCase
{
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

    public function testAutoMap()
    {
        $source = 'Skraeda/Source';
        $target = 'Skraeda/Target';
        $context = [];
        AutoMapperFacade::shouldReceive('map')->once()->with($source, $target, $context)->andReturn(true);
        $this->assertTrue(auto_map($source, $target, $context));
    }

    public function testAutoMapMultiple()
    {
        $source = ['Skraeda/Source'];
        $target = 'Skraeda/Target';
        $context = [];
        AutoMapperFacade::shouldReceive('mapMultiple')
                        ->once()
                        ->with($source, $target, $context)
                        ->andReturn(Collection::make([true]));
        $this->assertTrue(auto_map_multiple($source, $target, $context)[0]);
    }

    public function testAutoMapToObject()
    {
        $source = 'Skraeda/Source';
        $target = (object) ['Foo' => 'Bar'];
        $context = [];
        AutoMapperFacade::shouldReceive('map')->once()->with($source, $target, $context)->andReturn(true);
        $this->assertTrue(auto_map_to_object($source, $target, $context));
    }
}
