<?php

namespace Skraeda\AutoMapper\Console\Commands;

use Illuminate\Console\GeneratorCommand;

/**
 * Make Mapper generator command.
 *
 * @author Gunnar Örn Baldursson <gunnar@sjukraskra.is>
 */
class MakeMapper extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'make:mapper';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new custom mapper';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Mapper';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return  __DIR__ . '/Stubs/make-mapper.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Mappings';
    }
}
