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
        $filesystemManager = Storage::disk(config('cdn.filesystem.disk'));
        
        $filesOnCdn = $filesystemManager->allFiles();
        
        if (!$filesOnCdn) {
            return $this->error('CDN storage is already empty.');
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
