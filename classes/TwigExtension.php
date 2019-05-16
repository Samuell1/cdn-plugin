<?php namespace Samuell\Cdn\Classes;

use Cms\Classes\Controller;
use Cms\Classes\Theme;
use Cache;
use SystemException;

class TwigExtension
{

    /**
     * Get asset path for cdn.
     *
     * @param  string  $path
     * @param  boolean $useManifest
     * @return string
     */
    public function assetCdn($path, $useManifest = true): string
    {
        // Use manifest to determine path
        if (config('cdn.useManifest') && $useManifest) {
            $outputPath = $this->readManifest(basename($path));
        } else {
            // If cdn is disabled return url from local active theme
            if (!config('cdn.active')) {
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
        if (!config('cdn.active')) {
            return (new Controller)->themeUrl($path);
        }

        // Remove slashes from ending of the path
        $cdnUrl = rtrim(config('cdn.url'), '/');

        return $cdnUrl . '/' . trim($path, '/');
    }

    private function readManifest($path)
    {
        $manifest = Cache::rememberForever('cdn:manifest', function () {
            $theme = Theme::getActiveTheme();
            $manifestPath = $theme->getPath() . config('cdn.manifestPath');
            if (config('cdn.useHotreload') && !config('cdn.active')) {
                return $this->getHotreloadManifest($theme->getDirName());
            } else {
                return $this->getLocalManifest($manifestPath);
            }
        });

        return $manifest->$path;
    }

    private function getHotreloadManifest($themeDir)
    {
        return json_decode($this->curl_get_contents(config('cdn.hotReloadUrl', 'http://localhost:8080').'/themes/' . $themeDir . config('cdn.manifestPath')));
    }

    private function getLocalManifest($manifestPath)
    {
        if (file_exists($manifestPath)) {
            return json_decode(file_get_contents($manifestPath));
        } else {
            throw new SystemException('Missing manifest.json file');
        }
    }

    private function curl_get_contents($url)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);

        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }
}
