<?php namespace Samuell\Cdn\Traits;

use Symfony\Component\Finder\SplFileInfo;

trait FilesSync
{
    /**
     * @param string[] $filesOnCdn
     * @param SplFileInfo[] $localFiles
     * @return SplFileInfo[]
     */
    private function filesToSync(array $filesOnCdn, array $localFiles): array
    {
        $array = array_filter($localFiles, function (SplFileInfo $localFile) use ($filesOnCdn) {
            $localFilePathname = str_replace('\\', '/', $localFile->getRelativePathname());
            if (!in_array($localFilePathname, $filesOnCdn)) {
                return true;
            }
            $filesizeOfCdn = $this->filesystemManager
                ->size($localFilePathname);
            if ($filesizeOfCdn != $localFile->getSize()) {
                return true;
            }
            $md5OfCdn = md5(
                $this->filesystemManager
                    ->get($localFilePathname)
            );
            $md5OfLocal = md5_file(str_replace('\\', '/', $localFile->getRealPath()));
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
            return str_replace('\\', '/', $file->getRelativePathname());
        }, $files);
    }
}
