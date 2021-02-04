<?php

namespace Skraeda\AutoMapper\Contracts;

/**
 * AutoMapperCache interface to define operations to cache registered mappings.
 *
 * @author Gunnar Örn Baldursson <gunnar@sjukraskra.is>
 */
interface AutoMapperCacheContract
{
    /**
     * Load mapping config
     *
     * @param string $key
     * @return array
     */
    public function load(string $key): array;

    /**
     * Save mapping config
     *
     * @param array $mappings
     * @param string $key
     * @return void
     */
    public function save(array $mappings, string $key): void;

    /**
     * Clear mapping config
     *
     * @param string $key
     * @return void
     */
    public function clear(string $key): void;
}
