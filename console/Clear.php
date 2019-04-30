<?php namespace Samuell\Cdn\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Support\Facades\Storage;

class Clear extends Command
{
    /**
     * @var string The console command name.
     */
    protected $name = 'cdn:clear';

    /**
     * @var string The console command description.
     */
    protected $description = 'Deletes assets on CDN';

    /**
     * Execute the console command.
     * @return void
     */
    public function handle()
    {
        $directory = config('cdn.assetsFolder');
        $storage = Storage::disk(config('cdn.filesystem.disk'));

        $storage->deleteDirectory($directory);
        $storage->makeDirectory($directory);

        $this->info('CDN folder cleared!');
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
