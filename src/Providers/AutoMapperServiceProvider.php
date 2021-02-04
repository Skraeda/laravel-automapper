<?php

namespace Skraeda\AutoMapper\Providers;

use AutoMapperPlus\AutoMapper as AutoMapperPlusAutoMapper;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;
use Skraeda\AutoMapper\AutoMapper;
use Skraeda\AutoMapper\AutoMapperCache;
use Skraeda\AutoMapper\AutoMapperOperator;
use Skraeda\AutoMapper\Console\Commands\MakeMapper;
use Skraeda\AutoMapper\Contracts\AutoMapperCacheContract;
use Skraeda\AutoMapper\Contracts\AutoMapperContract;
use Skraeda\AutoMapper\Contracts\AutoMapperOperatorContract;
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

        $this->app->bind(AutoMapperOperatorContract::class, fn () => new AutoMapperOperator);

        $this->app->bind(AutoMapperCacheContract::class, fn () => new AutoMapperCache(
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
        $this->publishes([__DIR__.'/../../config/mapping.php' => config_path('mapping.php')]);

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
        $operator = app(AutoMapperOperatorContract::class);

        $cacheKey = config('mapping.cache.key');
        $cacheHit = false;

        $mappings = [];

        if (config('mapping.cache.enabled') && $cache->has($cacheKey)) {
            $mappings = $cache->get($cacheKey);
            $cacheHit = true;
        } else {
            $mappings = config('mapping.custom', []);

            if (config('mapping.scan.enabled')) {
                $scan = $operator->scanMappingDirectory(config('mapping.scan.dirs', []));

                $mappings = array_merge($mappings, $scan);
            }
        }

        foreach ($mappings as $mapper => $ctx) {
            [$source, $target] = $ctx;

            $operator->registerCustomMapper($mapper, $source, $target);
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
            $this->commands([MakeMapper::class]);
        }
    }
}
