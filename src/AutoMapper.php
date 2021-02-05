<?php

namespace Skraeda\AutoMapper;

use AutoMapperPlus\AutoMapperInterface;
use AutoMapperPlus\Configuration\AutoMapperConfigInterface;
use Illuminate\Support\Collection;
use Skraeda\AutoMapper\Contracts\AutoMapperContract;

/**
 * AutoMapper contract implementation using AutoMapper plus by Mark Gerarts.
 *
 * @author Gunnar Örn Baldursson <gunnar@sjukraskra.is>
 * @see https://github.com/mark-gerarts/automapper-plus
 */
class AutoMapper implements AutoMapperContract
{
    /**
     * AutoMapper instance.
     *
     * @var \AutoMapperPlus\AutoMapperInterface
     */
    protected $mapper;

    /**
     * Constructor.
     */
    public function __construct(AutoMapperInterface $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * {@inheritDoc}
     */
    public function map($source, $targetClass, array $context = [])
    {
        return $this->mapper->map($source, $targetClass, $context);
    }

    /**
     * {@inheritDoc}
     */
    public function mapToObject($source, $target, array $context = [])
    {
        return $this->mapper->map($source, $target, $context);
    }

    /**
     * {@inheritDoc}
     */
    public function mapMultiple($collection, string $targetClass, array $context = []): Collection
    {
        return Collection::make($this->mapper->mapMultiple($collection, $targetClass, $context));
    }

    /**
     * {@inheritDoc}
     */
    public function getConfiguration(): AutoMapperConfigInterface
    {
        return $this->mapper->getConfiguration();
    }
    
    /**
     * {@inheritDoc}
     */
    public function registerCustomMapper(string $mapper, string $source, string $target): void
    {
        $this->getConfiguration()->registerMapping($source, $target)->useCustomMapper(new $mapper);
    }
}
