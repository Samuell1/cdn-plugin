<?php namespace Samuell\Cdn\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class Sync extends Command
{
    /**
     * @var string The console command name.
     */
    protected $name = 'cdn:sync';

    /**
     * @var string The console command description.
     */
    protected $description = 'Synchronizes assets to CDN';

    /**
     * Execute the console command.
     * @return void
     */
    public function handle()
    {
        $this->output->writeln('Hello world!');
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
