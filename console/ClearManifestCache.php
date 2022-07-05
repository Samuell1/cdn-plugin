<?php

namespace Samuell\Cdn\Console;

use Cache;
use Illuminate\Console\Command;

class ClearManifestCache extends Command
{
    /**
     * @var string The console command name.
     */
    protected $name = 'cdn:clear-manifest-cache';

    /**
     * @var string The console command description.
     */
    protected $description = 'Flushes manifest cache';

    /**
     * Execute the console command.
     * @return mixed
     */
    public function handle()
    {
        Cache::forget('cdn:manifest');

        $this->info('Manifest cache cleared!');
    }

    /**
     * Get the console command arguments.
     * @return array
     */
    protected function getArguments()
    {
        return [];
    }

    /**
     * Get the console command options.
     * @return array
     */
    protected function getOptions()
    {
        return [];
    }
}