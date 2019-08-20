<?php namespace Samuell\Cdn\Classes;

use Cms\Classes\Controller;
use Cms\Classes\Theme;
use Cache;
use SystemException;
use PhpImap\Exception;

class TwigExtension
{

    /**
     * Get asset path for cdn.
     *
     * @param  string  $path
     * @param  boolean $useManifest
     * @return string
     */
    public function assetCdn($path): string
    {
        // Use manifest to determine path
        if (config('cdn.useManifest', false)) {
            $outputPath = $this->readManifest(basename($path));
        } else {
            // If cdn is disabled return url from local active theme
            if (!config('cdn.active', false)) {
                return (new Controller)->themeUrl($path);
            }

            $cdnUrl = rtrim(config('cdn.url'), '/');
            $outputPath = $cdnUrl . '/' . trim($path, '/');
        }

        return $outputPath;
    }

    /**
     * Get path for cdn.
     *
     * @param  string  $path
     * @return string
     */
    public function cdn($path): string
    {
        // If cdn is disabled return url from local active theme
        if (!config('cdn.active', false)) {
            return (new Controller)->themeUrl($path);
        }

        // Remove slashes from ending of the path
        $cdnUrl = rtrim(config('cdn.url'), '/');

        return $cdnUrl . '/' . trim($path, '/');
    }

    private function readManifest($path)
    {
        $manifest = Cache::rememberForever('cdn:manifest', function () {
            $manifestPath = Theme::getActiveTheme()->getPath() . config('cdn.manifestPath');
            return $this->getLocalManifest($manifestPath);
        });
        if (isset($manifest->$path)) {
            return $manifest->$path;
        } else {
            throw new SystemException('Missing ' . $path . ' file in manifest.json');
        }
    }

    private function getLocalManifest($manifestPath)
    {
        if (file_exists($manifestPath)) {
            return json_decode(file_get_contents($manifestPath));
        } else {
            throw new SystemException('Missing manifest.json file in "' . $manifestPath . '" ');
        }
    }
}
