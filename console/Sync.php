<?php namespace Samuell\Cdn\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Cms\Classes\Theme;
use Illuminate\Http\File as FileIlluminate;
use October\Rain\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Finder\SplFileInfo;

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
        $filesToDelete = $this->filesToDelete($filesOnCdn, $localFiles);
        $filesToSync = $this->filesToSync($filesOnCdn, $localFiles);

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

        $this->info('Files succesfuly uploaded!');

        if ($this->filesystemManager
            ->disk($this->filesystem)
            ->delete($filesToDelete)) {
            foreach ($filesToDelete as $file) {
                $this->info("Successfully deleted: {$file}");
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
        return [];
    }

    /**
     * @param string[] $filesOnCdn
     * @param SplFileInfo[] $localFiles
     * @return SplFileInfo[]
     */
    private function filesToSync(array $filesOnCdn, array $localFiles): array
    {
        $array = array_filter($localFiles, function (SplFileInfo $localFile) use ($filesOnCdn) {
            $localFilePathname = $localFile->getRelativePathname();
            if (!in_array($localFilePathname, $filesOnCdn)) {
                return true;
            }
            $filesizeOfCdn = $this->filesystemManager
                ->disk($this->filesystem)
                ->size($localFilePathname);
            if ($filesizeOfCdn != $localFile->getSize()) {
                return true;
            }
            $md5OfCdn = md5(
                $this->filesystemManager
                    ->disk($this->filesystem)
                    ->get($localFilePathname)
            );
            $md5OfLocal = md5_file($localFile->getRealPath());
            if ($md5OfLocal != $md5OfCdn) {
                return true;
            }
            return false;
        });
        return array_values($array);
    }
    /**
     * @param string[] $filesOnCdn
     * @param SplFileInfo[] $localFiles
     * @return string[]
     */
    private function filesToDelete(array $filesOnCdn, array $localFiles): array
    {
        $localFiles = $this->mapToPathname($localFiles);
        $array = array_filter($filesOnCdn, function (string $fileOnCdn) use ($localFiles) {
            return !in_array($fileOnCdn, $localFiles);
        });
        return array_values($array);
    }

    protected function mapToPathname(array $files): array
    {
        return array_map(function (SplFileInfo $file) {
            return $file->getRelativePathname();
        }, $files);
    }
}
