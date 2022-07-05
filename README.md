# OctoberCMS CDN Plugin
Push, sync, clear and serve assets to/from a CDN or use it for including manifest files from webpack or laravel-mix.

https://octobercms.com/plugin/samuell-cdn

## Usage

In your theme change asset links to use *asset_cdn* function. 

*Example:*
```html
<link rel="stylesheet" href="{{ asset_cdn('assets/css/app.css') }}">
<script src="{{ asset_cdn('assets/js/app.js') }}"></script>
```

**Note:** If you doesn't want to use CDN you can disable cdn in config and it will automatically fallback to theme path and use files from theme. (This is default configuration)

**With manifest integration enabled:**

We define file name that is compiled from Webpack or LaravelMix and exists in `manifest.json` file. Don't forget to enabled manifest integration and define correct `manifest.json` path in config file.
```html
<link rel="stylesheet" href="{{ asset_cdn('app.css') }}">
```

Getting assets from cdn that are not in `manifest.json` file use `cdn` function with full path to file:
```html
<link rel="stylesheet" href="{{ cdn('assets/css/app.css') }}">
```

**After configuration (configuration steps are below) we can sync theme assets to cdn**

Depending on theme run command to sync specific theme. For example to sync theme with name **demo** we use `php artisan cdn:sync demo`. If you want to remove old files that are compared with your local copy you can use flag `--delete-old`

## Configuration

##### 1. Configure Filesystem
If is your plan to use CDN, you can use this config. If not you can skip this step.
*Example is for AWS S3 with Cloudfront.*

**config/filesystems.php**
```php
'asset-cdn' => [
    'driver' => 's3',
    'key'    => env('S3_KEY'),
    'secret' => env('S3_SECRET'),
    'region' => env('S3_REGION'),
    'bucket' => env('S3_BUCKET'),
    'root'   => 'assets/',
],
```

##### 2. Configure config
Create `cdn.php` file in **config** folder to configure cdn plugin, this allows to use different configs for developing or local enviroments.

*Don't forget to define `root` filesystem folder same as `assetsFolder` best is to use default as `assets` to match october default theme assets folder.*

**config/cdn.php**

```php
return [

  // CDN integration
  'active' => false,
  'url' => 'https://cdn.mydomain.com/',
  'assetsFolder' => '/assets/',

  // Manifest integration (webpack, laravel mix)
  'useManifest' => false,
  'manifestPath' => '/assets/compiled/manifest.json',

  // Filesystem information that will be used with sync, push, clear commands
  'filesystem' => [
    'disk' => 'asset-cdn',
    'options' => []
  ]
  
  // Files filter
  'files' => [
    'ignoreDotFiles' => true,
    'ignoreVCS' => true,
    'include' => [
        'paths' => [
            //
        ],
        'files' => [
            //
        ],
        'extensions' => [
            //
        ],
        'patterns' => [
            //
        ],
    ],
    'exclude' => [
      'paths' => [
        //
      ],
      'files' => [
          //
      ],
      'extensions' => [
          //
      ],
      'patterns' => [
          //
      ],
    ],
  ],
];
```

**Optional filesystem options:**

Filesystem allows to define custom options.
*The following example is recommended for AWS S3.*

**cdn.php > filesystem.options**
```php
    'options' => [
       'ACL' => 'public-read',
       'CacheControl' => 'max-age=31536000, public'
    ]
```

### Commands

#### Sync Assets
Sync all assets but deletes old files that are on CDN.
```
php artisan cdn:sync {theme} {--delete-old}
```
- delete-old option enables automatic deletion of old files that does not exist in local folder

#### Push Assets
Pushes assets but does not delete old files on CDN.
```
php artisan cdn:push {theme}
```

#### Delete all assets from CDN
Deletes all assets from CDN.
```
php artisan cdn:clear {theme}
```

### Twig Functions
- Replace `'path'|theme` with `asset_cdn('path')` (It can read manifest.json file if option **useManifest** is set to true in config).
- Replace any asset that is going out of asset theme directory or is not used in manifest with `cdn('path')`.


*Inspired by AsssetCDN package for Laravel https://github.com/arubacao/asset-cdn*
