<?php

namespace Skraeda\AutoMapper;

use Skraeda\AutoMapper\Contracts\AutoMapperCacheContract;
use Skraeda\AutoMapper\Exceptions\AutoMapperCacheException;

/**
 * AutoMapperCache implementation using filesystem
 *
 * @author Gunnar Örn Baldursson <gunnar@sjukraskra.is>
 */
class AutoMapperCache implements AutoMapperCacheContract
{
    public function get($key, $default = null)
    {
    }

    public function set($key, $value, $ttl = null)
    {
    }

    public function delete($key)
    {
    }

    public function clear()
    {
    }

    public function has($key)
    {
    }

    public function getMultiple($keys, $default = null)
    {
    }

    public function setMultiple($values, $ttl = null)
    {
    }

    public function deleteMultiple($keys)
    {
    }
}
