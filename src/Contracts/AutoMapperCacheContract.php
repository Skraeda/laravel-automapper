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
     * Load mapping config from a file
     *
     * @param string $file
     * @return array
     */
    public function load(string $file): array;

    /**
     * Save mapping config to a file
     *
     * @param array $mappings
     * @param string $file
     * @return void
     */
    public function save(array $mappings, string $file): void;

    /**
     * Clear mappin config file
     *
     * @param string $file
     * @return void
     */
    public function clear(string $file): void;
}
