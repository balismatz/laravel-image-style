<?php

use Illuminate\Image\ImageOutputOptions;

return [

    /*
    |--------------------------------------------------------------------------
    | Image Style Output Quality
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default output quality of styled images.
    |
    */

    'quality' => ImageOutputOptions::DEFAULT_QUALITY,

    /*
    |--------------------------------------------------------------------------
    | Image Style Fallback URL
    |--------------------------------------------------------------------------
    |
    | Here you may specify the behavior when the requested image or style does
    | not exist.
    |
    | Available options:
    |   - 'storage_url': Returns the original image URL from Storage::url().
    |   - null: Returns null.
    |
    */

    'fallback_url' => 'storage_url',

    /*
    |--------------------------------------------------------------------------
    | Image Style Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the filesystem disk that should be used to store
    | the styled images.
    |
    */
    'filesystem' => env('IMAGE_STYLE_FILESYSTEM_DISK', 'public'),

    /*
    |--------------------------------------------------------------------------
    | Image Style Cache Store
    |--------------------------------------------------------------------------
    |
    | Here you may specify the cache store that will be used to store the image
    | style information (id, class, help, active).
    |
    */

    'cache' => env('IMAGE_STYLE_CACHE_STORE', 'database'),

];
