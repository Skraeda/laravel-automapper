<?php

namespace Skraeda\AutoMapper\Support\Facades;

use Illuminate\Support\Facades\Facade as IlluminateFacade;
use Skraeda\AutoMapper\Contracts\AutoMapperContract;

/**
 * AutoMapper facade.
 *
 * @author Gunnar Örn Baldursson <gunnar@sjukraskra.is>
 * @method static mixed map($source, string $targetClass, array $context)
 * @method static mixed mapToObject($source, $target, array $context)
 * @method static \Illuminate\Support\Collection mapMultiple($collection, string $targetClass, array $context)
 * @method static \AutoMapperPlus\Configuration\AutoMapperConfigInterface getConfiguration()
 */
class AutoMapperFacade extends IlluminateFacade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return AutoMapperContract::class;
    }
}
