<?php

namespace Samuell\Cdn\Console;

use Cms\Classes\Theme;
use Illuminate\Console\Command;
use Illuminate\Http\File as FileIlluminate;
use Illuminate\Support\Facades\Storage;
use Samuell\Cdn\Classes\Finder;
use Samuell\Cdn\Traits\FilesSync;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class Push extends Command
{
    use FilesSync;

    protected $name = 'cdn:push';

    protected $description = 'Push assets on CDN';

    protected $signature = 'cdn:push {theme} {--overwrite}';

    private $filesystemManager;

    private $filesystem;

    private $assetsFolder;

    public function handle()
    {
        $this->filesystem = config('cdn.filesystem.disk');
        $this->assetsFolder = config('cdn.assetsFolder');
        $this->filesystemManager = Storage::disk($this->filesystem);

        $assetsThemePath = (new Theme)->getPath($this->argument('theme')) . $this->assetsFolder;

        $filesOnCdn = $this->filesystemManager->allFiles();
        $localFiles = (new Finder($assetsThemePath))->getFiles();
        $filesToSync = $this->option('overwrite') ? $localFiles : $this->filesToSync($filesOnCdn, $localFiles);

        if (!$filesToSync) {
            $this->info('No files to push to CDN. (use "--overwrite" option to replace already existing files)');
            return;
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

    protected function getArguments(): array
    {
        return [
            ['theme', InputArgument::REQUIRED, 'Please specifiy theme name.']
        ];
    }

    protected function getOptions(): array
    {
        return [
            ['overwrite', null, InputOption::VALUE_NONE, 'Overwrite files even when exists', null],
        ];
    }
}
