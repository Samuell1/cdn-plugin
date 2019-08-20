# OctoberCMS CDN Plugin
Push, sync, clear and serve assets to/from a CDN or use it for including manifest files from webpack or laravel-mix.

## Usage
```
<link rel="stylesheet" href="{{ asset_cdn('assets/css/app.css') }}">
```

**With manifest integration enabled:**

We define file name that is compiled from Webpack or LaravelMix and exists in `manifest.json` file.
```
<link rel="stylesheet" href="{{ asset_cdn('app.css') }}">
```

Getting assets from cdn that are not in manifest use `cdn` function:
```
<link rel="stylesheet" href="{{ cdn('assets/css/myfile.css') }}">
```

### Config

**config/filesystems.php**
```
'asset-cdn' => [
    'driver' => 's3',
    'key'    => env('S3_KEY'),
    'secret' => env('S3_SECRET'),
    'region' => env('S3_REGION'),
    'bucket' => env('S3_BUCKET'),
    'root'   => 'assets/', // specifi folder for asset files
],
```

**config/cdn.php**
```
return [

  // CDN integration
  'active' => false,
  'url' => 'https://cdn.mydomain.com/',
  'assetsFolder' => '/assets/',

  // Manifest integration (webpack, laravel mix)
  'useManifest' => true,
  'manifestPath' => '/assets/compiled/manifest.json',

  // Filesystem information that will be used with sync, push, clear commands
  'filesystem' => [
    'disk' => 'asset-cdn',
    'options' => []
  ]
];
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

- Replace `'path'|theme` with `asset_cdn('path')` (It can read manifest.json file if options is set to true in config).
- Replace any asset that is going out of asset theme directory or is not used in manifest with `cdn('path')`.
