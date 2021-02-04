<?php

namespace Skraeda\AutoMapper;

use AutoMapperPlus\MapperInterface;
use Illuminate\Support\Str;
use ReflectionClass;
use Skraeda\AutoMapper\Attributes\Maps;
use Skraeda\AutoMapper\Contracts\AutoMapperOperatorContract;
use Skraeda\AutoMapper\Support\Facades\AutoMapperFacade;
use Symfony\Component\Finder\Finder;

/**
 * AutoMapperOperator implementation
 *
 * @author Gunnar Örn Baldursson <gunnar@sjukraskra.is>
 */
class AutoMapperOperator implements AutoMapperOperatorContract
{
    /**
     * Application namespace
     *
     * @var string
     */
    protected $namespace;

    /**
     * Application path
     *
     * @var string
     */
    protected $path;

    /**
     * Constructor
     *
     * @param string|null $path
     * @param string|null $namespace
     */
    public function __construct(?string $path = null, ?string $namespace = null)
    {
        $this->path = $path ?: app_path();
        $this->namespace = $namespace ?: app()->getNamespace();
    }

    /**
     * {@inheritDoc}
     */
    public function registerCustomMapper(string $mapperClass, string $sourceClass, string $targetClass): void
    {
        AutoMapperFacade::getConfiguration()
            ->registerMapping($sourceClass, $targetClass)
            ->useCustomMapper(new $mapperClass);
    }

    /**
     * {@inheritDoc}
     */
    public function scanMappingDirectory(string|array $dirs): array
    {
        $mappers = [];

        $appPaths = array_map(fn ($dir) => $this->path.DIRECTORY_SEPARATOR.$dir, is_array($dirs) ? $dirs : [ $dirs ]);

        $paths = array_filter(array_unique($appPaths), fn ($path) => is_dir($path));

        foreach ((new Finder)->in($paths)->files() as $mapping) {
            $mapper = $this->namespace.str_replace(
                ['/', '.php'],
                ['\\', ''],
                Str::after($mapping->getRealPath(), realpath($this->path).DIRECTORY_SEPARATOR)
            );

            $refl = new ReflectionClass($mapper);
            $attributes = $refl->getAttributes(Maps::class);

            foreach ($attributes as $attribute) {
                if ($refl->isInstantiable() && $refl->implementsInterface(MapperInterface::class)) {
                    $maps = $attribute->newInstance();
                        
                    $mappers[$mapper] = [ 'source' => $maps->source, 'target' => $maps->target ];
                }
            }
        }

        return $mappers;
    }
}
