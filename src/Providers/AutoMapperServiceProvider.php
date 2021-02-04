<?php

namespace Skraeda\AutoMapper\Providers;

use AutoMapperPlus\AutoMapper as AutoMapperPlusAutoMapper;
use AutoMapperPlus\MapperInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;
use Illuminate\Support\Str;
use ReflectionClass;
use Skraeda\AutoMapper\Attributes\Maps;
use Skraeda\AutoMapper\AutoMapper;
use Skraeda\AutoMapper\AutoMapperCache;
use Skraeda\AutoMapper\AutoMapperOperator;
use Skraeda\AutoMapper\Console\Commands\MakeMapper;
use Skraeda\AutoMapper\Contracts\AutoMapperCacheContract;
use Skraeda\AutoMapper\Contracts\AutoMapperContract;
use Skraeda\AutoMapper\Contracts\AutoMapperOperatorContract;
use Skraeda\AutoMapper\Support\Facades\AutoMapperFacade;
use Symfony\Component\Finder\Finder;

/**
 * AutoMapper service provider.
 *
 * @author Gunnar Örn Baldursson <gunnar@sjukraskra.is>
 */
class AutoMapperServiceProvider extends IlluminateServiceProvider
{
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(AutoMapperContract::class, fn () => new AutoMapper(new AutoMapperPlusAutoMapper));

        $this->app->bind(AutoMapperOperatorContract::class, fn () => new AutoMapperOperator);

        $this->app->bind(AutoMapperCacheContract::class, fn () => new AutoMapperCache);

        $this->mergeConfigFrom(__DIR__.'/../../config/mapping.php', 'mapping');
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([__DIR__.'/../../config/mapping.php' => config_path('mapping.php')]);

        $this->registerCommands();

        $this->addCollectionMacro();

        $this->registerCustomMappers();

        $this->scanForMappers();
    }

    /**
     * Register Custom Mappers defined in custom key in config.
     *
     * @return array
     */
    protected function registerCustomMappers()
    {
        foreach (config('mapping.custom') as $mapper => $classes) {
            AutoMapperFacade::registerCustomMapper($mapper, $classes['source'], $classes['target']);
        }
    }

    /**
     * Add autoMap Collection macro
     *
     * @return void
     */
    protected function addCollectionMacro()
    {
        Collection::macro('autoMap', function (string $targetClass, array $context = []) {
            return AutoMapperFacade::mapMultiple($this, $targetClass, $context);
        });
    }

    /**
     * Register Artisan commands
     *
     * @return void
     */
    protected function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([MakeMapper::class]);
        }
    }

    /**
     * Scan for CustomMappers defined in scan key in config.
     *
     * @return void
     */
    protected function scanForMappers()
    {
        if (!config('mapping.scan.enabled', false)) {
            return;
        }

        $appPaths = array_map(fn ($path) => app_path().DIRECTORY_SEPARATOR.$path, config('mapping.scan.dirs', []));

        $paths = array_filter(array_unique($appPaths), fn ($path) => is_dir($path));

        if (empty($paths)) {
            return;
        }

        $ns = app()->getNamespace();

        foreach ((new Finder)->in($paths)->files() as $mapping) {
            $mapper = $ns.str_replace(
                ['/', '.php'],
                ['\\', ''],
                Str::after($mapping->getRealPath(), realpath(app_path()).DIRECTORY_SEPARATOR)
            );

            $refl = new ReflectionClass($mapper);
            $attributes = $refl->getAttributes(Maps::class);

            foreach ($attributes as $attribute) {
                if ($refl->isInstantiable() && $refl->implementsInterface(MapperInterface::class)) {
                    $maps = $attribute->newInstance();
    
                    AutoMapperFacade::registerCustomMapper($mapper, $maps->source, $maps->target);
                }
            }
        }
    }
}
