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

        // $this->app->bind(AutoMapperCacheContract::class, fn () => new AutoMapperCache);

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
    }

    /**
     * Register Custom Mappers defined in custom key in config.
     *
     * @return void
     */
    protected function registerCustomMappers()
    {
        // If cache, register from cache

        $this->registerNewMappings();

        // If cache, save to cache
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
     * Register new mappings found through config.
     *
     * @return array
     */
    protected function registerNewMappings(): array
    {
        $operator = app(AutoMapperOperatorContract::class);

        $customMappers = array_merge(
            config('mapping.custom', []),
            config('mapping.scan.enabled') ? $operator->scanMappingDirectory(config('mapping.scan.dirs', [])) : []
        );

        foreach ($customMappers as $mapper => $classes) {
            $operator->registerCustomMapper($mapper, $classes['source'], $classes['target']);
        }

        return $customMappers;
    }
}
