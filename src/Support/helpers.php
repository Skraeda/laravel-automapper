<?php

use Illuminate\Support\Collection;
use Skraeda\AutoMapper\Support\Facades\AutoMapperFacade;

if (!function_exists('auto_map')) {
    /**
     * Map a source to a target class.
     *
     * @param array|object $source
     * @param string|object $targetClass
     * @param array $context
     * @return mixed
     * @throws \AutoMapperPlus\Exception\UnregisteredMappingException
     */
    function auto_map($source, $targetClass, array $context = [])
    {
        return AutoMapperFacade::map($source, $targetClass, $context);
    }
}

if (!function_exists('auto_map_to_object')) {
    /**
     * Map a source to an existing object.
     *
     * @param array|object $source
     * @param object $target
     * @param array $context
     * @return mixed
     * @throws \AutoMapperPlus\Exception\UnregisteredMappingException
     */
    function auto_map_to_object($source, $target, array $context = [])
    {
        return AutoMapperFacade::map($source, $target, $context);
    }
}

if (!function_exists('auto_map_multiple')) {
    /**
     * Map multiple sources to a target.
     *
     * @param array|\Traversable $collection
     * @param string $targetClass
     * @param array $context
     * @return \Illuminate\Support\Collection
     * @throws \AutoMapperPlus\Exception\UnregisteredMappingException
     */
    function auto_map_multiple($collection, string $targetClass, array $context = []): Collection
    {
        return AutoMapperFacade::mapMultiple($collection, $targetClass, $context);
    }
}
