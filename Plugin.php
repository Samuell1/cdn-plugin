<?php namespace Samuell\Cdn;

use Backend;
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
        $this->registerConsoleCommand('samuell.cdn:sync', 'Samuell\Cdn\Console\Sync');
        $this->registerConsoleCommand('samuell.cdn:clear', 'Samuell\Cdn\Console\Clear');
    }

    /**
     * Boot method, called right before the request route.
     *
     * @return array
     */
    public function boot()
    {

    }

    public function registerMarkupTags()
    {
        return [
            'functions' => [
                'asset_cdn' => ['Samuell\Cdn\Classes\AssetCdn', 'assetCdn'],
            ]
        ];
    }

    /**
     * Registers any front-end components implemented in this plugin.
     *
     * @return array
     */
    public function registerComponents()
    {
        return []; // Remove this line to activate

        return [
            'Samuell\Cdn\Components\MyComponent' => 'myComponent',
        ];
    }

    /**
     * Registers any back-end permissions used by this plugin.
     *
     * @return array
     */
    public function registerPermissions()
    {
        return []; // Remove this line to activate

        return [
            'samuell.cdn.some_permission' => [
                'tab' => 'Cdn',
                'label' => 'Some permission'
            ],
        ];
    }

    /**
     * Registers back-end navigation items for this plugin.
     *
     * @return array
     */
    public function registerNavigation()
    {
        return []; // Remove this line to activate

        return [
            'cdn' => [
                'label'       => 'Cdn',
                'url'         => Backend::url('samuell/cdn/mycontroller'),
                'icon'        => 'icon-leaf',
                'permissions' => ['samuell.cdn.*'],
                'order'       => 500,
            ],
        ];
    }
}
