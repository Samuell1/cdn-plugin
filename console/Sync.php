<?php namespace Samuell\Cdn\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Cms\Classes\Theme;
use Illuminate\Http\File as FileIlluminate;
use October\Rain\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

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
        $this->filesystem = config('cdn.filesystem.disk');
        $this->assetsFolder = config('cdn.assetsFolder');

        $assetsThemePath = (new Theme)->getPath($this->argument('theme')).$this->assetsFolder;

        $localFiles = File::allFiles($assetsThemePath);

        $bar = $this->output->createProgressBar(count($localFiles));
        $bar->setFormat(
            "%current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s%\nThe current step is %current_step%\n"
        );

        foreach ($localFiles as $file) {

            $bar->setMessage($file->getRelativePathname(), 'current_step');

            $fileUploaded = Storage::disk($this->filesystem)
                ->putFileAs(
                    $this->assetsFolder.$file->getRelativePath(),
                    new FileIlluminate($file->getRealPath()),
                    $file->getFilename(),
                    config('cdn.filesystem.options')
                );

            if (!$fileUploaded) {
                $this->error("Problem uploading: {$file->getRelativePathname()}");
            } else {
                $bar->advance();
            }
        }

        $bar->finish();

        $this->info('Files succesfuly uploaded!');
    }

    /**
     * Get the console command arguments.
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['theme', InputArgument::REQUIRED, 'Please specifiy theme name.']
        ];
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
