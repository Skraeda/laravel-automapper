<?php

namespace Skraeda\AutoMapper\Support\Facades;

/**
 * AutoMapper facade alias.
 *
 * @author Gunnar Örn Baldursson <gunnar@sjukraskra.is>
 * @method static mixed map(array|object $source, string|object $targetClass, array $context = [])
 * @method static mixed mapToObject(array|object $source, object $target, array $context = [])
 * @method static \Illuminate\Support\Collection mapMultiple($collection, string $targetClass, array $context = [])
 * @method static \AutoMapperPlus\Configuration\AutoMapperConfigInterface getConfiguration()
 * @method static void registerCustomMapper(string $mapper, string $source, string $target)
 * @method static \AutoMapperPlus\Configuration\MappingInterface registerMapping(string $source, string $target)
 */
class AutoMapper extends AutoMapperFacade
{
}
