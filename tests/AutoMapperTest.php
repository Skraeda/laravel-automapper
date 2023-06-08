<?php

namespace Skraeda\AutoMapper\Tests;

use AutoMapperPlus\AutoMapperInterface;
use AutoMapperPlus\Configuration\AutoMapperConfigInterface;
use AutoMapperPlus\Configuration\MappingInterface;
use AutoMapperPlus\MapperInterface;
use Mockery;
use PHPUnit\Framework\TestCase;
use Skraeda\AutoMapper\AutoMapper;
use Skraeda\AutoMapper\Tests\Data\A;
use Skraeda\AutoMapper\Tests\Data\ABMapper;
use Skraeda\AutoMapper\Tests\Data\B;

/**
 * Unit tests for \Skraeda\AutoMapper\AutoMapper
 *
 * @author Gunnar Örn Baldursson <gunnar@sjukraskra.is>
 */
class AutoMapperTest extends TestCase
{
    /**
     * AutoMapper interface mock.
     *
     * @var \AutoMapperPlus\AutoMapperInterface|\Mockery\MockInterface
     */
    protected $mapper;

    /**
     * Setup the test cases.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->mapper = Mockery::mock(AutoMapperInterface::class);
    }

    public function testMap()
    {
        $source = 'Skraeda/Source';
        $target = 'Skraeda/Target';
        $context = [];
        $this->mapper->shouldReceive('map')->with($source, $target, $context)->andReturn(1);
        $this->assertEquals(1, (new AutoMapper($this->mapper))->map($source, $target, $context));
    }

    public function testMapToObject()
    {
        $source = 'Skraeda/Source';
        $target = (object) ['Foo' => 'Bar'];
        $context = [];
        $this->mapper->shouldReceive('map')->with($source, $target, $context)->andReturn(1);
        $this->assertEquals(1, (new AutoMapper($this->mapper))->mapToObject($source, $target, $context));
    }

    public function testMapMultiple()
    {
        $source = ['Skraeda/Source'];
        $target = 'Skraeda/Target';
        $context = [];
        $this->mapper->shouldReceive('mapMultiple')->with($source, $target, $context)->andReturn(['1' => 1]);
        $this->assertEquals(1, (new AutoMapper($this->mapper))->mapMultiple($source, $target, $context)['1']);
    }

    public function testGetConfiguration()
    {
        $configMock = Mockery::mock(AutoMapperConfigInterface::class);
        $this->mapper->shouldReceive('getConfiguration')->andReturn($configMock);
        $this->assertEquals($configMock, (new AutoMapper($this->mapper))->getConfiguration());
    }

    public function testRegisterCustomMapper()
    {
        $configMock = Mockery::mock(AutoMapperConfigInterface::class);
        $mappingMock = Mockery::mock(MappingInterface::class);
        $this->mapper->shouldReceive('getConfiguration')->andReturn($configMock);
        $configMock->shouldReceive('registerMapping')->with(A::class, B::class)->andReturn($mappingMock);
        $mappingMock->shouldReceive('useCustomMapper')->with(Mockery::on(fn ($class) => get_class($class) === ABMapper::class));

        $result = (new AutoMapper($this->mapper))->registerCustomMapper(ABMapper::class, A::class, B::class);

        $this->assertNull($result);
    }
}
