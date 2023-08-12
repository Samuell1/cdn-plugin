<?php

namespace Samuell\Cdn\Console;

use Cache;
use Illuminate\Console\Command;

class ClearManifestCache extends Command
{
    protected $name = 'cdn:clear-manifest-cache';

    protected $description = 'Flushes manifest cache';

    public function handle()
    {
        Cache::forget('cdn:manifest');

        $this->info('Manifest cache cleared!');
    }
}
