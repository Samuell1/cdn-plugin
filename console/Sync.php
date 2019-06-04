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
    use \Samuell\Cdn\Traits\FilesSync;
    /**
     * @var string The console command name.
     */
    protected $name = 'cdn:sync';

    /**
     * @var string The console command description.
     */
    protected $description = 'Synchronizes assets to CDN';

    protected $signature = 'cdn:sync {theme} {--delete-old}';

    private $filesystemManager;

    /**
     * Execute the console command.
     * @return void
     */
    public function handle()
    {
        $this->filesystem = config('cdn.filesystem.disk');
        $this->assetsFolder = config('cdn.assetsFolder');
        $this->filesystemManager = Storage::disk($this->filesystem);

        $assetsThemePath = (new Theme)->getPath($this->argument('theme')) . $this->assetsFolder;

        $filesOnCdn = $this->filesystemManager->allFiles();
        $localFiles = File::allFiles($assetsThemePath);
        $filesToSync = $this->filesToSync($filesOnCdn, $localFiles);

        if (!$filesToSync) {
            return $this->info('Files on CDN are equal to local files.');
        }

        $bar = $this->output->createProgressBar(count($filesToSync));
        $bar->setFormat(
            "%current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s%\nThe current step is %current_step%\n"
        );

        foreach ($filesToSync as $file) {

            $bar->setMessage($file->getRelativePathname(), 'current_step');

            $fileUploaded = $this->filesystemManager
                ->putFileAs(
                    $file->getRelativePath(),
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
        $this->info('Files succesfuly uploaded to CDN!');


        // Delete old files
        if ($this->option('delete-old')) {
            $this->info('\n Deleting old files from CDN:');
            $filesToDelete = $this->filesToDelete($filesOnCdn, $localFiles);
            if ($filesToDelete && $this->filesystemManager
                ->delete($filesToDelete)) {

                $this->info('Deleting old files');

                $barDeleted = $this->output->createProgressBar(count($filesToDelete));
                $barDeleted->setFormat(
                    "%current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s%\nThe current step is %current_step%\n"
                );

                foreach ($filesToDelete as $file) {
                    $bar->setMessage($file, 'current_step');
                    $bar->advance();
                }

                $bar->finish();
                $this->info('Old files are deleted from CDN!');
            }
        }

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
        return [
            ['delete-old', null, InputOption::VALUE_NONE, 'Removes old files from CDN', null],
        ];
    }
}
