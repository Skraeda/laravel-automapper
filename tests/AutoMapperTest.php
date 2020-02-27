<?php

namespace Skraeda\AutoMapper\Tests;

use AutoMapperPlus\AutoMapperInterface;
use AutoMapperPlus\Configuration\AutoMapperConfigInterface;
use Mockery;
use PHPUnit\Framework\TestCase;
use Skraeda\AutoMapper\AutoMapper;

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
     * @var \AutoMapperPlus\AutoMapperInterface
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
        $this->mapper->shouldReceive('mapToObject')->with($source, $target, $context)->andReturn(1);
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
}
