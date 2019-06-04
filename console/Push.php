<?php namespace Samuell\Cdn\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Cms\Classes\Theme;
use Illuminate\Http\File as FileIlluminate;
use October\Rain\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class Push extends Command
{
    use \Samuell\Cdn\Traits\FilesSync;

    /**
     * @var string The console command name.
     */
    protected $name = 'cdn:push';

    /**
     * @var string The console command description.
     */
    protected $description = 'Push assets on CDN';

    protected $signature = 'cdn:push {theme} {--overwrite}';

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
        $filesToSync = $this->option('overwrite') ? $localFiles : $this->filesToSync($filesOnCdn, $localFiles);

        if (!$filesToSync) {
            return $this->info('No files to push to CDN. (use "--overwrite" option to replace already existing files)');
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
            ['overwrite', null, InputOption::VALUE_NONE, 'Overwrite files even when exists', null],
        ];
    }
}
