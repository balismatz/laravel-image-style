<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Image Style Driver
    |--------------------------------------------------------------------------
    |
    | Image styles use the Intervention Image (intervention/image) package to
    | modify images (resize, change brightness / contrast etc.).
    |
    | Intervention Image supports “GD Library” and “Imagick” to process images
    | internally. Depending on your PHP setup, you can choose one of them.
    |
    | Included options:
    |   - \Intervention\Image\Drivers\Gd\Driver::class
    |   - \Intervention\Image\Drivers\Imagick\Driver::class
    |
    */

    'driver' => \Intervention\Image\Drivers\Gd\Driver::class,

    /*
    |--------------------------------------------------------------------------
    | Image Style Options
    |--------------------------------------------------------------------------
    |
    | These options are used, by default, when an image is modified via image
    | style(s).
    |
    | - autoOrientation: Controls whether an imported image should be
    |   automatically rotated according to any existing Exif data.
    |
    | - decodeAnimation: Decides whether a possibly animated image is decoded as
    |   such or whether the animation is discarded.
    |
    | - blendingColor: Defines the default blending color.
    |
    | - quality: Defines the default image encoding quality from 0 to 100.
    |
    */

    'options' => [
        'autoOrientation' => true,
        'decodeAnimation' => true,
        'blendingColor' => 'ffffff',
        'quality' => 75,
    ],

    /*
    |--------------------------------------------------------------------------
    | Image Style Fallback URL
    |--------------------------------------------------------------------------
    |
    | Here you specify the behavior, when the requested image or style do not
    | exist.
    |
    | Available options:
    |   - storage_url: Returns the image URL from Storage::url().
    |   - null: Returns an empty value.
    |
    */

    'fallback_url' => 'storage_url',

    /*
    |--------------------------------------------------------------------------
    | Image Style Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the image style filesystem disk that should be used
    | to store the styled images. The "local" disk, as well as a variety of
    | cloud based disks are available to your application for file storage.
    |
    */

    'filesystem' => env('IMAGE_STYLE_FILESYSTEM_DISK', config('filesystems.default')),

    /*
    |--------------------------------------------------------------------------
    | Image Style Cache Store
    |--------------------------------------------------------------------------
    |
    | Here you may specify the cache store that will be used to store the image
    | styles information (id, class, help, active).
    |
    */

    'cache' => env('IMAGE_STYLE_CACHE_STORE', config('cache.default')),

];
