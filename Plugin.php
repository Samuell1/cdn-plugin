<?php namespace Samuell\Cdn;

use Backend;
use System\Classes\PluginBase;
use Samuell\Cdn\Classes\TwigExtension;

/**
 * Cdn Plugin Information File
 */
class Plugin extends PluginBase
{
    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'Cdn',
            'description' => 'Sync theme assets with cdn.',
            'author'      => 'Samuell',
            'icon'        => 'icon-refresh'
        ];
    }

    /**
     * Register method, called when the plugin is first registered.
     *
     * @return void
     */
    public function register()
    {
        $this->registerConsoleCommand('cdn:sync', 'Samuell\Cdn\Console\Sync');
        $this->registerConsoleCommand('cdn:clear', 'Samuell\Cdn\Console\Clear');
    }

    /**
     * Boot method, called right before the request route.
     *
     * @return array
     */
    public function boot()
    {

    }

    /**
     * Boot method, called right before the request route.
     *
     * @return array
     */
    public function registerMarkupTags()
    {
        return [
            'functions' => [
                'asset_cdn' => function ($path) {
                    return (new TwigExtension)->assetCdn($path);
                },
                'cdn' => function ($path) {
                    return (new TwigExtension)->cdn($path);
                },
            ]
        ];
    }
}
