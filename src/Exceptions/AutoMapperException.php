<?php

namespace Skraeda\AutoMapper\Exceptions;

use Exception;
use Throwable;

/**
 * AutoMapperException common interface for library.
 *
 * @author Gunnar Örn Baldursson <gunnar@sjukraskra.is>
 */
class AutoMapperException extends Exception
{
    /**
     * Wrap Throwable
     *
     * @param string $message
     * @param \Throwable $e
     * @return static
     */
    public static function wrap(string $message, Throwable $e): static
    {
        return new static($message.": ".$e->getMessage(), $e->getCode(), $e);
    }
}
