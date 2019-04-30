<?php namespace Samuell\Cdn\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Support\Facades\Storage;

class Push extends Command
{
    /**
     * @var string The console command name.
     */
    protected $name = 'cdn:push';

    /**
     * @var string The console command description.
     */
    protected $description = 'Push assets on CDN';

    /**
     * Execute the console command.
     * @return void
     */
    public function handle()
    {

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
