<?php namespace Samuell\Cdn\Classes;

use Cms\Classes\Controller;

class TwigExtension
{

    /**
     * Get asset path for cdn.
     *
     * @param  string  $path
     * @return string
     */
    public static function assetCdn($path): string
    {
        $path = config('cdn.assetsFolder', '/assets/').$path;

        // If cdn is disabled return url from local active theme.
        if (!config('cdn.active')) {
            return (new Controller)->themeUrl($path);
        }
        $cdnUrl = config('cdn.url');

        // Remove slashes from ending of the path
        $cdnUrl = rtrim($cdnUrl, '/');
        return $cdnUrl . '/' . trim($path, '/');
    }

    /**
     * Get path for cdn.
     *
     * @param  string  $path
     * @return string
     */
    public static function cdn($path): string
    {
        $path = $path;

        $cdnUrl = config('cdn.url');

        // Remove slashes from ending of the path
        $cdnUrl = rtrim($cdnUrl, '/');
        return $cdnUrl . '/' . trim($path, '/');
    }
}
