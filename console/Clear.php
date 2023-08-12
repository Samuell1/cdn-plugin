<?php

namespace Samuell\Cdn\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class Clear extends Command
{
    protected $name = 'cdn:clear';

    protected $description = 'Deletes assets on CDN';

    public function handle()
    {
        $filesystemManager = Storage::disk(config('cdn.filesystem.disk'));

        $filesOnCdn = $filesystemManager->allFiles();

        if (!$filesOnCdn) {
            $this->error('CDN storage is already empty.');
            return;
        }

        $bar = $this->output->createProgressBar(count($filesOnCdn));
        $bar->setFormat(
            "%current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s%\nThe current step is %current_step%\n"
        );

        if ($filesystemManager->delete($filesOnCdn)) {
            foreach ($filesOnCdn as $file) {
                $bar->setMessage($file, 'current_step');
                $bar->advance();
            }
        }

        $bar->finish();
        $this->info('CDN storage cleared!');
    }
}
