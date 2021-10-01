<?php

return [
  // CDN integration
  'active' => env('CDN_MANIFEST_ACTIVE', false),
  'url' => 'https://cdn.mydomain.com/',
  'assetsFolder' => '/assets/',

  // Manifest integration (webpack, laravel mix)
  'useManifest' => false,
  'manifestPath' => '/assets/compiled/manifest.json',

  // Filesystem information that will be used with sync, push, clear commands
  'filesystem' => [
    'disk' => 'asset-cdn',
    'options' => []
  ],

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
