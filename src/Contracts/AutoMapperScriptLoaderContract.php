<?php

namespace Skraeda\AutoMapper\Contracts;

/**
 * AutoMapperScriptLoader interface.
 *
 * @author Gunnar Örn Baldursson <gunnar@sjukraskra.is>
 */
interface AutoMapperScriptLoaderContract
{
    /**
     * Require a script file
     *
     * @param string $file
     * @return mixed
     */
    public function require(string $file): mixed;
}
