<?php

namespace Skraeda\AutoMapper;

use Skraeda\AutoMapper\Contracts\AutoMapperScriptLoaderContract;
use Skraeda\AutoMapper\Exceptions\AutoMapperException;
use Throwable;

/**
 * Script Loader for AutoMapper
 *
 * @author Gunnar Örn Baldursson <gunnar@sjukraskra.is>
 */
class AutoMapperScriptLoader implements AutoMapperScriptLoaderContract
{
    /**
     * {@inheritDoc}
     * @throws \Skraeda\AutoMapper\Exceptions\AutoMapperException
     */
    public function require(string $file): mixed
    {
        try {
            return require $file;
        } catch (Throwable $e) {
            throw AutoMapperException::wrap("Failed to require $file", $e);
        }
    }
}
