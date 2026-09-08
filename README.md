# Image Style for Laravel

A Laravel package for managing application image styles in a simple and
organized way. Each image style is implemented as a dedicated PHP class that
defines the corresponding image manipulations.

It provides a facade and a helper function for creating and retrieving styled images.
It also includes Artisan commands to create image styles, list all available image
styles, cache or clear image style information, and flush styled images when
needed.

Image manipulations are performed through [Laravel's fluent image manipulation API](https://laravel.com/framework/docs/13.x/images),
introduced in Laravel 13.20.

## Requirements

- PHP 8.3 or higher
- Laravel 13.20 or higher
- Intervention Image 4.0 or higher

## Installation

Require the package using Composer:

```shell
composer require 'balismatz/laravel-image-style:^2.0'
```

Optionally, you can publish and customize the [config](config/image-style.php)
file by running the following command:

```shell
php artisan vendor:publish --provider="BalisMatz\ImageStyle\ImageStyleServiceProvider"
```

## Usage

### Create image styles

You may create a new image style by running the following command:

```shell
php artisan make:image-style
```

After running the command above, you will be prompted for the following:

1. ***What should the image style be named?***

   Specify the image style class name. This name will be used to autogenerate
   — if needed — the unique image style ID (see below).

2. ***ID***

   By default, the package generates the unique image style ID based
   on the class name. This prompt allows you to define a custom ID, if desired.
   Leave it empty to use the default behavior.

   > - If the image style ID cannot be generated from the provided class name,
   > it falls back to "default".
   > - If multiple image styles have the same ID, the first one detected will be
   > considered valid.

3. ***Help text***

   A command is provided to list all available image styles. Here, you may
   define a help text (useful for teams) to describe the image manipulations,
   the use cases, or any other information.

4. ***Status***

   In certain scenarios, you may wish to define an image style as "Disabled"
   (e.g., for future use). Select the desired status from the available options.
   The "Default" status is considered active.

Image style classes are located in the `/app/ImageStyles` directory.

> [!TIP]
> You can use directory depth levels (from 0 to 3) to better organize your image
> styles.
>
> ```
> -- Levels --
>
> 0 : /app/ImageStyles/ThumbnailImageStyle.php
> 1 : /app/ImageStyles/Posts/ThumbnailImageStyle.php
> 2 : /app/ImageStyles/Posts/Show/ThumbnailImageStyle.php
> 3 : /app/ImageStyles/Posts/Show/Gallery/ThumbnailImageStyle.php
> ```

#### Image style example

An image style may be defined as follows:

```php
<?php

namespace App\ImageStyles;

use BalisMatz\ImageStyle\ImageStyleBase;
use Illuminate\Image\Image;

class Thumbnail extends ImageStyleBase
{
    /**
     * {@inheritDoc}
     */
    public function manipulations(Image $image, mixed $parameters): Image
    {
        return $image
            ->cover(300, 250);
    }
}
```

See the available [manipulation methods](https://laravel.com/framework/docs/13.x/images#manipulating-images).

*Additional examples are available in the [Image Style for Laravel Demo repository](https://github.com/balismatz/laravel-image-style-demo/tree/main/app/ImageStyles).*

### List image styles

List all available image styles by running the following command:

```shell
php artisan image-style:list
```

### Create / Retrieve styled images

Styled images are stored in the `styles/{{ image-style-id }}` directory.
For example, the thumbnail (image style ID) of the `posts/main.jpg` image
will be saved at `/styles/thumbnail/posts/main.jpg`.

The disk on which styled images will be stored depends on the
[configuration](config/image-style.php#L43) or the parameters provided to the
following methods.

> [!IMPORTANT]
> - The following methods are available through:
>   - Facade: `\BalisMatz\ImageStyle\Facades\ImageStyle`
>   - Function: `imageStyle()`
>   - Dependency injection: `\BalisMatz\ImageStyle\ImageStyle`
>
> - In Blade templates, the `ImageStyle` facade can be used without
> namespace `{{ ImageStyle::url() }}`.
>
> - The `ImageStyle` facade is macroable.

1. **[path()](src/ImageStyle.php#L199)**

    Based on the given image style and the original image path, this method
    creates, recreates (based on the provided parameters), or retrieves the
    styled image and returns its storage path.

    > Provides basic functionality and is useful when you simply need to
    > create a styled image. See the "Performance" section below.

2. **[url()](src/ImageStyle.php#L295)**

    Based on the given image style and the original image path, this method
    creates, recreates (based on the provided parameters), or retrieves the
    styled image and returns its storage URL.

    > This is useful when displaying a styled image using the `<img>`
    > HTML tag.

3. **[imageInformation()](src/ImageStyle.php#L66)**

    Based on the given image style and the original image path, this method
    creates, recreates (based on the provided parameters), or retrieves the
    styled image and returns an `ImageStyleImageInformation` object containing
    the image URL, height, width, MIME type, optional dominant color (Laravel
    13.24 or later), and the provided parameters.

    > This is useful when displaying a styled image using the `<img>`
    > HTML tag with its `loading` attribute set to `lazy`. You may specify
    > the `height` and `width` attributes to avoid layout shifts.

4. **[paths()](src/ImageStyle.php#L240)**

    Based on the given image styles (array or string) and the original image
    path, this method creates, recreates (based on the provided parameters), or
    retrieves the styled images and returns their storage paths.

    > Provides basic functionality and is useful when you simply need to
    > create multiple styled images. See the "Performance" section below.

5. **[urls()](src/ImageStyle.php#L333)**

    Based on the given image styles (array or string) and the original image
    path, this method creates, recreates (based on the provided parameters), or
    retrieves the styled images and returns their storage URLs.

    > This is useful when displaying responsive images - based on image styles -
    > with the `<img>` HTML tag
    > ([more information](https://developer.mozilla.org/en-US/docs/Web/HTML/Responsive_images)).

6. **[imagesInformation()](src/ImageStyle.php#L110)**

    Based on the given image styles (array or string) and the original image
    path, this method creates, recreates (based on the provided parameters), or
    retrieves the styled images and returns a collection of
    `ImageStyleImageInformation` objects containing the image URL, height,
    width, MIME type, optional dominant color (Laravel 13.24 or later), and
    the provided parameters.

    > This is useful when displaying responsive images - based on image styles -
    > with the `<img>` ([more information](https://developer.mozilla.org/en-US/docs/Web/HTML/Responsive_images))
    > or `<picture>` ([more information](https://developer.mozilla.org/en-US/docs/Web/HTML/Element/picture))
    > HTML tags.
    >
    > You can provide parameters for each image style. These parameters will be
    > included in the `ImageStyleImageInformation` objects and may be used,
    > for example, to define the media query associated with each styled image.

> [!NOTE]
> - Image style(s) parameter can be the image style ID or the fully qualified
>    class name. For example, `\App\ImageStyles\ThumbnailImageStyle::class`.
> - `paths()` - `urls()` - `imagesInformation()` accept multiple
>    styles as an array or a comma-separated string.
> - All the above methods accept style parameters (`$styleParameters`) that
>    are passed to the image style class `manipulations()` method. For example,
>    you can pass a dynamic watermark, focal point, etc.

#### Format

You may change the format of a styled image within an image style by using the
[format methods](https://laravel.com/framework/docs/13.x/images#encoding-images),
or retrieve a styled image in a specific format by using the `$format` parameter
of the above methods.

> [!WARNING]
> An `\Illuminate\Image\ImageException` is thrown when the `$format` parameter
> is invalid.

#### Quality

You may change the default output quality of styled images through the
[configuration](config/image-style.php#L16) or by calling the
[`quality()`](https://laravel.com/framework/docs/13.x/images#encoding-images) /
[`optimize()`](https://laravel.com/framework/docs/13.x/images#encoding-images)
methods in an image style.

#### Fallback URL

When the requested image or image style(s) do not exist, and depending on the
[configuration](config/image-style.php#L32), the `url()`, `urls()`,
`imageInformation()`, and `imagesInformation()` methods will return the
default storage URL(s) or empty value(s).

#### Performance

By default, styled images are created when one of the above methods is called.
This means that styled images are created the first time they are requested.
You can avoid this behavior by calling the `path()` or `paths()`
method (for each image), for example, when storing the original image.

### Flush styled images

Remove (flush) styled images by running the following command:

```shell
php artisan image-style:flush
```

> [!TIP]
> You may bypass interactive prompts for this package's Artisan commands by
> providing command options directly.
> Use the `--help` option to view the available arguments and options.

## Usage Examples

Usage examples for creating image styles and creating / retrieving styled images
are available in the [Image Style for Laravel Demo repository](https://github.com/balismatz/laravel-image-style-demo).

## Preview

You can preview image style manipulations by calling the
[`preview()`](src/ImageStyle.php#L367) method.

> [!NOTE]
> The preview image is provided by [Freepik](https://www.freepik.com/).

## Deployment

When the [`optimize`](https://laravel.com/docs/master/deployment#optimization)
Artisan command is executed, all image style information is persisted to the
[configured cache store](config/image-style.php#L55), which improves the
performance of image style information retrieval.

If the `optimize` Artisan command is not part of your deployment process,
you should explicitly run the `image-style:cache` Artisan command:

```shell
php artisan image-style:cache
```

## Troubleshooting

1. If an image style does not appear in the available styles list, verify that:

   - The class is located in the `/app/ImageStyles` directory.
   - The class is located in a supported directory level.
   - The class extends the `ImageStyleBase` class.
   - The class is not declared as `abstract`.

   If everything above is correct, clear the image style information
   cache by running the following command:

   ```shell
   php artisan image-style:clear
   ```

2. If styled images are not displayed, verify that the `filesystems.default`
   and `image-style.filesystem` configuration values are properly
   configured.

## Upgrade Guide

Version 1.x used Intervention Image 3 directly for image manipulations.

The current version uses [Laravel's fluent image manipulation API](https://laravel.com/framework/docs/13.x/images),
introduced in Laravel 13.20. Laravel's API provides first-party integration
with Intervention Image 4, including support for custom image drivers and
transformations.

To upgrade from 1.x, address the following breaking changes:

1. Publishing the [config](config/image-style.php) file is optional. Delete an
    existing configuration file published by the package to use the package
    defaults. If your application has custom configuration options, align them
    with the current configuration options. The image driver is now configured
    through `config('images.default')`.

2. The default disk for styled images, `config('image-style.filesystem')`, has
    changed from `local` to `public`.

3. Image style classes must implement the `manipulations()` method instead of
    the `modifications()` method. Its signature is
    `manipulations(\Illuminate\Image\Image $image, mixed $parameters): \Illuminate\Image\Image`.

4. The `quality()` method on image style classes has been removed. Call
    [`$image->quality()`](https://laravel.com/framework/docs/13.x/images#encoding-images) or
    [`$image->optimize()`](https://laravel.com/framework/docs/13.x/images#encoding-images)
    within `manipulations()` instead. An example is available in the
    [Image Style for Laravel Demo repository](https://github.com/balismatz/laravel-image-style-demo/blob/main/app/ImageStyles/Section/Banner/BannerBase.php).

5. The `ToJpeg()`, `ToWebp()`, `ToPng()`, `ToGif()`, `ToBmp()`, `ToAvif()`,
    `ToTiff()`, `ToJpeg2000()`, and `ToHeic()` method suffixes have been
    removed from `path()`, `url()`, `imageInformation()`, `paths()`, `urls()`,
    and `imagesInformation()`.

    Use the corresponding method with its `$format` parameter instead. For
    example, replace `urlToWebp('thumbnail', 'image.jpg')` with
    `url('thumbnail', 'image.jpg', format: 'webp')`. TIFF output is not
    supported.

6. The `mimetype` property in `ImageStyleImageInformation` has been renamed to
    [`mimeType`](src/Information/ImageStyleImageInformation.php#L14).

## License

Image Style for Laravel is open-sourced software licensed under the
[MIT license](LICENSE.md).
