<?php

namespace Skraeda\AutoMapper\Tests;

use AutoMapperPlus\CustomMapper\CustomMapper;
use Orchestra\Testbench\TestCase;
use Skraeda\AutoMapper\AutoMapperOperator;
use Skraeda\AutoMapper\Exceptions\AutoMapperOperatorException;
use Skraeda\AutoMapper\Providers\AutoMapperServiceProvider;
use Skraeda\AutoMapper\Support\Facades\AutoMapperFacade;
use Skraeda\AutoMapper\Tests\Data\A;
use Skraeda\AutoMapper\Tests\Data\ABMapper;
use Skraeda\AutoMapper\Tests\Data\B;

/**
 * AutoMapperOperator unit tests
 *
 * @author Gunnar Örn Baldursson <gunnar@sjukraskra.is>
 */
class AutoMapperOperatorTest extends TestCase
{
    /** @test */
    public function itCanRegisterACustomMapper()
    {
        $mapper = new class extends CustomMapper {
            public function mapToObject($source, $destination, array $context = [])
            {
                $destination->Value = $source->Value + 2;
                return $destination;
            }
        };

        $source = new class {
            public $Value = 1;
        };

        $target = new class {
            public $Value;
        };

        $operator = new AutoMapperOperator;

        $operator->registerCustomMapper(get_class($mapper), get_class($source), get_class($target));

        $result = AutoMapperFacade::map($source, $target);

        $this->assertEquals(3, $result->Value);
    }

    /** @test */
    public function itRaisesExceptionIfItFailsToRegisterACustomMapper()
    {
        $this->expectException(AutoMapperOperatorException::class);

        (new AutoMapperOperator)->registerCustomMapper('a', 'b', 'c');
    }

    /** @test */
    public function itCanScanDirectoriesForCustomMappers()
    {
        $operator = new AutoMapperOperator(__DIR__, 'Skraeda\\AutoMapper\\Tests\\');

        $mappers = $operator->scanMappingDirectory(['Data']);

        $this->assertCount(1, $mappers);
        $this->assertEquals(A::class, $mappers[ABMapper::class]['source']);
        $this->assertEquals(B::class, $mappers[ABMapper::class]['target']);
    }

    /** @test */
    public function itCanScanSingleDirectoryForCustomMappers()
    {
        $operator = new AutoMapperOperator(__DIR__, 'Skraeda\\AutoMapper\\Tests\\');

        $mappers = $operator->scanMappingDirectory('Data');

        $this->assertCount(1, $mappers);
        $this->assertEquals(A::class, $mappers[ABMapper::class]['source']);
        $this->assertEquals(B::class, $mappers[ABMapper::class]['target']);
    }

    /** @test */
    public function itRaisesExceptionIfItFindsNonMappersWhenScanning()
    {
        $this->expectException(AutoMapperOperatorException::class);

        (new AutoMapperOperator)->scanMappingDirectory('.');
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
