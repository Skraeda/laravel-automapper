<?php

namespace Skraeda\AutoMapper\Exceptions;

use Psr\SimpleCache\InvalidArgumentException;

/**
 * Exception for AutoMapperCache
 *
 * @author Gunnar Örn Baldursson <gunnar@sjukraskra.is>
 */
class AutoMapperCacheException extends AutoMapperException implements InvalidArgumentException
{
}
