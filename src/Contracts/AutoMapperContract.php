<?php

namespace Skraeda\AutoMapper\Contracts;

use AutoMapperPlus\Configuration\AutoMapperConfigInterface;
use Illuminate\Support\Collection;

/**
 * Interface for an AutoMapper
 *
 * @author Gunnar Örn Baldursson <gunnar@sjukraskra.is>
 */
interface AutoMapperContract
{
    /**
     * Map a source to a target class.
     *
     * @param array|object $source
     * @param string|object $targetClass
     * @param array $context
     * @return mixed
     * @throws \AutoMapperPlus\Exception\UnregisteredMappingException
     */
    public function map($source, $targetClass, array $context = []);

    /**
     * Map a source to an existing object.
     *
     * @param array|object $source
     * @param object $target
     * @param array $context
     * @return mixed
     * @throws \AutoMapperPlus\Exception\UnregisteredMappingException
     * @deprecated The `map` method should now be used instead.
     */
    public function mapToObject($source, $target, array $context = []);

    /**
     * Map multiple sources to a target.
     *
     * @param array|\Traversable $collection
     * @param string $targetClass
     * @param array $context
     * @return \Illuminate\Support\Collection
     * @throws \AutoMapperPlus\Exception\UnregisteredMappingException
     */
    public function mapMultiple($collection, string $targetClass, array $context = []): Collection;

    /**
     * Get AutoMapper configuration.
     *
     * @return \AutoMapperPlus\Configuration\AutoMapperConfigInterface
     */
    public function getConfiguration(): AutoMapperConfigInterface;
}
