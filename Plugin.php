<?php

namespace Samuell\Cdn;

use Samuell\Cdn\Classes\TwigExtension;
use System\Classes\PluginBase;

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
            'name' => 'Cdn',
            'description' => 'Sync theme assets with cdn.',
            'author' => 'Samuell',
            'icon' => 'icon-refresh'
        ];
    }

    public function register(): void
    {
        $this->registerConsoleCommand('cdn:sync', 'Samuell\Cdn\Console\Sync');
        $this->registerConsoleCommand('cdn:push', 'Samuell\Cdn\Console\Push');
        $this->registerConsoleCommand('cdn:clear', 'Samuell\Cdn\Console\Clear');

        $this->registerConsoleCommand('cdn:clear-manifest-cache', 'Samuell\Cdn\Console\ClearManifestCache');
    }

    public function registerMarkupTags(): array
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
