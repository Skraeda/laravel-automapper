<?php

namespace Skraeda\AutoMapper;

use DateInterval;
use Illuminate\Filesystem\Filesystem;
use Skraeda\AutoMapper\Contracts\AutoMapperCacheContract;
use Skraeda\AutoMapper\Contracts\AutoMapperScriptLoaderContract;
use Skraeda\AutoMapper\Exceptions\AutoMapperCacheException;
use Throwable;

/**
 * AutoMapperCache implementation using filesystem
 *
 * @author Gunnar Örn Baldursson <gunnar@sjukraskra.is>
 */
class AutoMapperCache implements AutoMapperCacheContract
{
    /**
     * Constructor
     *
     * @param \Illuminate\Filesystem\Filesystem $fs
     * @param string $dir
     */
    public function __construct(
        protected AutoMapperScriptLoaderContract $loader,
        protected Filesystem $fs,
        protected string $dir
    ) {
    }

    /**
     * {@inheritDoc}
     * @throws \Skraeda\AutoMapper\Exceptions\AutoMapperCacheException
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $this->validateKey($key);

        $path = $this->keyPath($key);

        if (!$this->fs->exists($path)) {
            return $default;
        }

        try {
            return $this->loader->require($path);
        } catch (Throwable $e) {
            throw AutoMapperCacheException::wrap("Failed to load cache $path", $e);
        }
    }

    /**
     * {@inheritDoc}
     * @throws \Skraeda\AutoMapper\Exceptions\AutoMapperCacheException
     */
    public function set(string $key, mixed $value, null|int|DateInterval $ttl = null): bool
    {
        $this->validateKey($key);

        $path = $this->keyPath($key);

        $this->fs->makeDirectory($this->dir, 0755, true, true);

        $this->delete($key);

        $this->fs->put($path, '<?php return '.var_export($value, true).';'.PHP_EOL);

        try {
            $this->loader->require($path);
        } catch (Throwable $e) {
            $this->fs->delete($path);

            throw AutoMapperCacheException::wrap("Mappers failed to serialize to $key", $e);
        }

        return true;
    }

    /**
     * {@inheritDoc}
     * @throws \Skraeda\AutoMapper\Exceptions\AutoMapperCacheException
     */
    public function delete(string $key): bool
    {
        $this->validateKey($key);

        $path = $this->keyPath($key);

        if ($this->fs->exists($path)) {
            return $this->fs->delete($path);
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function clear(): bool
    {
        return $this->fs->cleanDirectory($this->dir);
    }

    /**
     * {@inheritDoc}
     * @throws \Skraeda\AutoMapper\Exceptions\AutoMapperCacheException
     */
    public function has(string $key): bool
    {
        $this->validateKey($key);

        $path = $this->keyPath($key);

        return $this->fs->exists($path);
    }

    /**
     * {@inheritDoc}
     * @throws \Skraeda\AutoMapper\Exceptions\AutoMapperCacheException
     */
    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        $this->validateKeyArray($keys);

        $results = [];

        foreach ($keys as $key) {
            $results[$key] = $this->get($key, $default);
        }

        return $results;
    }

    /**
     * {@inheritDoc}
     * @throws \Skraeda\AutoMapper\Exceptions\AutoMapperCacheException
     */
    public function setMultiple(iterable $values, null|int|DateInterval $ttl = null): bool
    {
        $this->validateKeyArray($values);

        foreach ($values as $key => $value) {
            $this->set($key, $value, $ttl);
        }

        return true;
    }

    /**
     * {@inheritDoc}
     * @throws \Skraeda\AutoMapper\Exceptions\AutoMapperCacheException
     */
    public function deleteMultiple(iterable $keys): bool
    {
        $this->validateKeyArray($keys);

        foreach ($keys as $key) {
            if (!$this->delete($key)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get key path
     *
     * @param string $key
     * @return string
     */
    protected function keyPath(string $key): string
    {
        return $this->dir.DIRECTORY_SEPARATOR.$key;
    }

    /**
     * Validate a cache key is valid.
     *
     * @param string $key
     * @return void
     */
    protected function validateKey($key): void
    {
        if (!is_string($key) || !str_ends_with($key, '.php')) {
            throw new AutoMapperCacheException("Key must be a .php file path string");
        }
    }

    /**
     * Validate a cache key array
     *
     * @param array|\Traversable $keys
     * @return void
     */
    protected function validateKeyArray($keys): void
    {
        if (!is_iterable($keys)) {
            throw new AutoMapperCacheException("Keys must be iterable");
        }
    }
}
