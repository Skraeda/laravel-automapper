<?php

namespace Skraeda\AutoMapper\Console\Commands;

use Illuminate\Console\Command;
use Skraeda\AutoMapper\Contracts\AutoMapperCacheContract;

/**
 * Clear Mapping cache command.
 *
 * @author Gunnar Örn Baldursson <gunnar@sjukraskra.is>
 */
class MappingClear extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'automapper:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear AutoMapper cache';

    /**
     * Execute the console command.
     *
     * @param \Skraeda\AutoMapper\Contracts\AutoMapperCacheContract $cache
     * @return mixed
     */
    public function handle(AutoMapperCacheContract $cache)
    {
        $cache->clear();

        $this->info('AutoMapper cache cleared!');
    }
}
