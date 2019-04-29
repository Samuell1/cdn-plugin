<?php namespace Samuell\Cdn\Classes;

class AssetCdn
{

    /**
     * Get asset path for cdn.
     *
     * @param  string  $path
     * @return string
     */
    public function assetCdn($path)
    {
        if (!config('cdn.use_cdn')) {
            return asset($path);
        }
        $cdnUrl = config('cdn.cdn_url');
        // Remove slashes from ending of the path
        $cdnUrl = rtrim($cdnUrl, '/');
        return $cdnUrl . '/' . trim($path, '/');
    }
}
