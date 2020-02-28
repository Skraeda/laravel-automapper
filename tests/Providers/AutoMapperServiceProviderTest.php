<?php

namespace Skraeda\AutoMapper\Tests\Providers;

use AutoMapperPlus\CustomMapper\CustomMapper;
use Illuminate\Support\Collection;
use Orchestra\Testbench\TestCase;
use Skraeda\AutoMapper\AutoMapper;
use Skraeda\AutoMapper\Support\Facades\AutoMapperFacade;
use Skraeda\AutoMapper\Providers\AutoMapperServiceProvider;
use Skraeda\AutoMapper\Contracts\AutoMapperContract;

/**
 * Feature tests for \Skraeda\AutoMapper\Providers\AutoMapperServiceProvider
 *
 * @author Gunnar Örn Baldursson <gunnar@sjukraskra.is>
 */
class AutoMapperServiceProviderTest extends TestCase
{
    /**
     * Class mapper.
     *
     * @var object|null
     */
    protected $mappingClass;

    /**
     * Source class for mapper.
     *
     * @var object|null
     */
    protected $sourceClass;

    /**
     * Target class for mapper.
     *
     * @var object|null
     */
    protected $targetClass;

    /** @test */
    public function itRegistersAnAutoMapper()
    {
        $this->assertInstanceOf(AutoMapper::class, $this->app[AutoMapperContract::class]);
    }

    /** @test */
    public function itRegistersCustomMappings()
    {
        $target = AutoMapperFacade::map($this->getSourceClass(), get_class($this->getTargetClass()));
        $this->assertEquals('foo', $target->a);
    }

    /** @test */
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
     * {@inheritDoc}
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('mapping.custom', [
            get_class($this->getMappingClass()) => [
                'source' => get_class($this->getSourceClass()),
                'target' => get_class($this->getTargetClass())
            ]
        ]);
    }

    /**
     * Get Mapping Class.
     *
     * @return object
     */
    protected function getMappingClass()
    {
        if ($this->mappingClass === null) {
            $this->mappingClass = new class extends CustomMapper
            {
                public function mapToObject($source, $destination, array $ctx = [])
                {
                    $destination->a = $source->a;
                    return $destination;
                }
            };
        }

        return $this->mappingClass;
    }

    /**
     * Get Source Class.
     *
     * @return object
     */
    protected function getSourceClass()
    {
        if ($this->sourceClass === null) {
            $this->sourceClass = new class
            {
                public $a = 'foo';
            };
        }

        return $this->sourceClass;
    }

    /**
     * Get Target Class.
     *
     * @return object
     */
    protected function getTargetClass()
    {
        if ($this->targetClass === null) {
            $this->targetClass = new class
            {
                public $a;
            };
        }

        return $this->targetClass;
    }
}
