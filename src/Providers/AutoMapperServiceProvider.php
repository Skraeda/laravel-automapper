<?php

namespace Skraeda\AutoMapper\Providers;

use AutoMapperPlus\AutoMapper as AutoMapperPlusAutoMapper;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;
use Skraeda\AutoMapper\AutoMapper;
use Skraeda\AutoMapper\AutoMapperCache;
use Skraeda\AutoMapper\AutoMapperFinder;
use Skraeda\AutoMapper\AutoMapperScriptLoader;
use Skraeda\AutoMapper\Console\Commands\MakeMapper;
use Skraeda\AutoMapper\Console\Commands\MappingCache;
use Skraeda\AutoMapper\Console\Commands\MappingClear;
use Skraeda\AutoMapper\Contracts\AutoMapperCacheContract;
use Skraeda\AutoMapper\Contracts\AutoMapperContract;
use Skraeda\AutoMapper\Contracts\AutoMapperFinderContract;
use Skraeda\AutoMapper\Support\Facades\AutoMapperFacade;

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
        $this->app->singleton(AutoMapperContract::class, fn () => new AutoMapper(
            new AutoMapperPlusAutoMapper
        ));

        $this->app->bind(AutoMapperFinderContract::class, fn () => new AutoMapperFinder);

        $this->app->bind(AutoMapperCacheContract::class, fn () => new AutoMapperCache(
            new AutoMapperScriptLoader,
            app(Filesystem::class),
            config('mapping.cache.dir')
        ));

        $this->mergeConfigFrom(__DIR__.'/../../config/mapping.php', 'mapping');
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([__DIR__.'/../../config/mapping.php' => config_path('mapping.php')], 'automapper-config');

        $this->addCommands();

        $this->addCollectionMacro();

        $this->addCustomMappers();
    }

    /**
     * Register Custom Mappers defined via config.
     *
     * @return void
     */
    protected function addCustomMappers()
    {
        $cache = app(AutoMapperCacheContract::class);
        $finder = app(AutoMapperFinderContract::class);

        $cacheKey = config('mapping.cache.key');
        $cacheHit = false;

        $mappings = [];

        if (config('mapping.cache.enabled') && $cache->has($cacheKey)) {
            $mappings = $cache->get($cacheKey);
            $cacheHit = true;
        } else {
            $mappings = config('mapping.custom', []);

            if (config('mapping.scan.enabled')) {
                $scan = $finder->scanMappingDirectory(config('mapping.scan.dirs', []));

                $mappings = array_merge($mappings, $scan);
            }
        }

        foreach ($mappings as $mapper => $ctx) {
            AutoMapperFacade::registerCustomMapper($mapper, $ctx['source'], $ctx['target']);
        }

        if (config('mapping.cache.enabled') && !$cacheHit) {
            $cache->set($cacheKey, $mappings);
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
    protected function addCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeMapper::class,
                MappingClear::class,
                MappingCache::class
            ]);
        }
    }
}
