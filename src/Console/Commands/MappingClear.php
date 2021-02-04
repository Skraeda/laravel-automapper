<?php

namespace Skraeda\AutoMapper\Console\Commands;

use Illuminate\Console\Command;
use Skraeda\AutoMapper\Contracts\AutoMapperCacheContract;

/**
 * Make Mapper generator command.
 *
 * @author Gunnar Örn Baldursson <gunnar@sjukraskra.is>
 */
class MakeMapper extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mapping:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear mapping cache';

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
