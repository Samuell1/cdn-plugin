# OctoberCMS CDN Plugin
Sync theme assets with CDN.


## Usage

### Config


### Commands

#### Sync Assets
Sync all assets but deletes old files that are on CDN.

```
php artisan cdn:sync
```

#### Push Assets
Pushes assets but does not delete files on CDN.

```
php artisan cdn:sync
```

#### Delete all assets from CDN
Deletes all assets from CDN.

```
php artisan cdn:empty
```

### Twig Functions

- Replace `'path'|theme` with `asset_cdn('path')`.
- Replace any asset that is going out of asset theme directory with `cdn('path')`.