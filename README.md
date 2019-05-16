# OctoberCMS CDN Plugin
Sync theme assets with CDN.


## Usage

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
  'active' => false,
  'url' => 'https://cdn.mydomain.com/',
  'assetsFolder' => '/assets/',

  // webpack, laravel mix integration
  'useManifest' => true,
  'manifestPath' => '/assets/compiled/manifest.json',
  'useHotreload' => true,

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
php artisan cdn:sync
```

#### Push Assets
Pushes assets but does not delete files on CDN.

```
php artisan cdn:push
```

#### Delete all assets from CDN
Deletes all assets from CDN.

```
php artisan cdn:empty
```

### Twig Functions

- Replace `'path'|theme` with `asset_cdn('path')`.
- Replace any asset that is going out of asset theme directory with `cdn('path')`.
