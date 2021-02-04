<?php

namespace Skraeda\AutoMapper;

use Skraeda\AutoMapper\Contracts\AutoMapperCacheContract;

/**
 * AutoMapperCache implementation using filesystem
 *
 * @author Gunnar Örn Baldursson <gunnar@sjukraskra.is>
 */
class AutoMapperCache implements AutoMapperCacheContract
{
    /**
     * {@inheritDoc}
     */
    public function save(array $mappings, string $key): void
    {
    }
    
    /**
     * {@inheritDoc}
     */
    public function load(string $key): array
    {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function clear(string $key): void
    {
    }
}
