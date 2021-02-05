<?php

namespace Skraeda\AutoMapper\Console\Commands;

use Illuminate\Console\Command;
use Skraeda\AutoMapper\Contracts\AutoMapperCacheContract;
use Skraeda\AutoMapper\Contracts\AutoMapperFinderContract;

/**
 * Clear Mapping cache command.
 *
 * @author Gunnar Örn Baldursson <gunnar@sjukraskra.is>
 */
class MappingCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'automapper:cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Save Custom Mappers to a cache file for faster loading';

    /**
     * Execute the console command.
     *
     * @param \Skraeda\AutoMapper\Contracts\AutoMapperFinderContract $finder
     * @param \Skraeda\AutoMapper\Contracts\AutoMapperCacheContract $cache
     * @return mixed
     */
    public function handle(AutoMapperFinderContract $finder, AutoMapperCacheContract $cache)
    {
        $this->call('automapper:clear');

        $mappings = config('mapping.custom', []);

        if (config('mapping.scan.enabled')) {
            $scan = $finder->scanMappingDirectory(config('mapping.scan.dirs', []));

            $mappings = array_merge($mappings, $scan);
        }

        $cache->set(config('mapping.cache.key'), $mappings);

        $this->info('AutoMapper cached successfully!');
    }
}
