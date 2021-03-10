<?php

namespace Skraeda\AutoMapper\Tests\Console\Commands;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Orchestra\Testbench\TestCase;
use Skraeda\AutoMapper\Console\Commands\MakeMapper;
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
        $this->artisan('make:mapper', ['name' => 'MyMapper'])->assertExitCode(0);
    }

    public function testStub()
    {
        $cmd = new MakeMapper(new Filesystem);

        $stubPath = Str::replaceFirst(
            'tests/',
            'src/',
            implode(DIRECTORY_SEPARATOR, [__DIR__, 'Stubs', 'make-mapper.stub'])
        );

        $this->assertEquals($stubPath, $cmd->getStubLocation());
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
