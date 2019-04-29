<?php namespace Samuell\Cdn\Classes;

class AssetCdn
{

    /**
     * Get asset path for cdn.
     *
     * @param  string  $path
     * @return string
     */
    public function assetCdn($path): string
    {
        if (!config('cdn.use_cdn')) {
            $theme = themes_path();
            return asset($theme.$path);
        }
        $cdnUrl = config('cdn.cdn_url');

        // Remove slashes from ending of the path
        $cdnUrl = rtrim($cdnUrl, '/');
        return $cdnUrl . '/' . trim($path, '/');
    }
}
