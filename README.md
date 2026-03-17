# Laravel Image Style

A Laravel package designed to manage application image styles in a simple and
organized way. Each image style is implemented as a dedicated PHP class that
defines the corresponding image modifications.

It provides a facade and a helper function for creating and retrieving styled images.
It also includes Artisan commands to create image styles, list all available image
styles, cache or clear image styles information, and flush styled images when
needed.

Image modifications are powered by the popular open source PHP image processing
library, the [Intervention Image](https://github.com/Intervention/image).

An official Intervention Image package for Laravel
([Intervention Image Laravel](https://github.com/Intervention/image-laravel))
is also available and provides basic functionality.

> [!IMPORTANT]
> This package is **NOT** the official
> [Intervention Image Laravel](https://github.com/Intervention/image-laravel)
> package.

## Requirements

- PHP 8.4 or higher
- Laravel 12.0 or higher
- Intervention Image 3.9 or higher

## Installation

Require the package using Composer:

```shell
composer require balismatz/laravel-image-style
```

Publish the [config](config/image-style.php) file by running the following
command:

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
   > - If multiple image styles have the same ID, the first detected will be
   > considered valid.

3. ***Help text***

   A command is provided to list all available image styles. Here, you may
   define a help text (useful for teams) to describe the image modifications,
   the use cases, or any other information.

4. ***Status***

   In certain scenarios, you may wish to define an image style as "Disabled"
   (e.g., for future use). Select the desired status from the available options.
   The "Default" status is considered active.

> [!TIP]
> You may bypass the interactive prompts by providing the options directly in
> the command. For more information, run the following command:
>
> ```shell
> php artisan make:image-style --help
> ```

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

### List image styles

List all available image styles by running the following command:

```shell
php artisan image-style:list
```

> [!TIP]
> Various listing options are available. For more information, run the following
> command:
>
> ```shell
> php artisan image-style:list --help
> ```

### Create / Retrieve styled images

Styled images are stored in the `styles/{{ image-style-id }}` directory.
For example, the thumbnail (image style ID) of the `posts/main.jpg` image
will be saved at `/styles/thumbnail/posts/main.jpg`.

The disk on which styled images will be stored depends on the
[configuration](config/image-style.php#L78) or the parameters provided to the
following methods.

> [!IMPORTANT]
> - The following methods are available through:
>   - Facade: `BalisMatz\ImageStyle\Facades\ImageStyle`
>   - Function: `imageStyle()`
>   - Dependency injection: `BalisMatz\ImageStyle\ImageStyle`
>
> - In Blade templates, the `ImageStyle` facade can be used without
> namespace `{{ ImageStyle::url() }}`.
>
> - The `ImageStyle` facade is macroable.

1. **[path()](src/ImageStyle.php#L494)**

    Based on the given image style and the original image path, this method
    creates, recreates (based on the provided parameters), or retrieves the styled image and
    returns its storage path.

    > Provides a basic functionality and is useful when you simply need to
    > create a styled image. See the "Performance" section below.

2. **[url()](src/ImageStyle.php#L924)**

    Based on the given image style and the original image path, this method
    creates, recreates (based on the provided parameters), or retrieves the styled image and
    returns its storage URL.

    > This is useful when displaying a styled image using the `<img>`
    > HTML tag.

3. **[imageInformation()](src/ImageStyle.php#L62)**

    Based on the given image style and the original image path, this method
    creates, recreates (based on the provided parameters), or retrieves the styled image and
    returns an `ImageStyleImageInformation` object containing the image URL,
    height, width, mimetype, and the provided parameters.

    > This is useful when displaying a styled image using the `<img>`
    > HTML tag and the `lazy` loading attribute. You may specify the
    > `<img>` height and width to avoid unexpected behaviors
    > (e.g., flickering).

4. **[paths()](src/ImageStyle.php#L710)**

    Based on the given image styles (array or string) and the original image
    path, this method creates, recreates (based on the provided parameters), or retrieves the
    styled images and returns their storage paths.

    > Provides a basic functionality and is useful when you simply need to
    > create multiple styled images. See the "Performance" section below.

5. **[urls()](src/ImageStyle.php#L1128)**

    Based on the given image styles (array or string) and the original image
    path, this method creates, recreates (based on the provided parameters), or retrieves the
    styled images and returns their storage URLs.

    > This is useful when displaying responsive images - based on image styles -
    > with the `<img>` HTML tag
    > ([more information](https://developer.mozilla.org/en-US/docs/Web/HTML/Responsive_images)).

6. **[imagesInformation()](src/ImageStyle.php#L278)**

    Based on the given image styles (array or string) and the original image
    path, this method creates, recreates (based on the provided parameters), or retrieves the
    styled images and returns a collection of `ImageStyleImageInformation`
    objects containing the image URL, height, width, mimetype, and the provided
    parameters.

    > This is useful when displaying responsive images - based on image styles - with
    > the `<img>` ([more information](https://developer.mozilla.org/en-US/docs/Web/HTML/Responsive_images))
    > or `<picture>` ([more information](https://developer.mozilla.org/en-US/docs/Web/HTML/Element/picture))
    > HTML tags.
    >
    > You can provide parameters for each image style. These parameters will be
    > included in the `ImageStyleImageInformation` objects and may be used,
    > for example, to define the media query associated with each styled image.

> [!NOTE]
> - Image style(s) parameter can be the image style ID or the class name
>    (with namespace). For example, `App\ImageStyles\ThumbnailImageStyle::class`.
> - `paths()` - `urls()` - `imagesInformation()` accept multiple
>    styles as an array or a comma-separated string.
> - All the above methods accept style parameters (`$styleParameters`) that
>    are passed to the image style class `modifications()` and `quality()`
>    methods. For example, you can pass a dynamic watermark, focal point, etc.
> - Each of the above methods also supports image format conversion using the
>    following suffixes:
>    - `{methodName}ToJpeg()`
>    - `{methodName}ToWebp()`
>    - `{methodName}ToPng()`
>    - `{methodName}ToGif()`
>    - `{methodName}ToBmp()`
>    - `{methodName}ToAvif()`
>    - `{methodName}ToTiff()`
>    - `{methodName}ToJpeg2000()`
>    - `{methodName}ToHeic()`

> [!TIP]
> Click on each method above to view the available parameters.

#### Fallback URL

When image style(s) or original image do not exist, and depending on the
[configuration](config/image-style.php#L65), the `url()`, `urls()`,
`imageInformation()`, and `imagesInformation()` methods will return the
default storage URL(s) or empty value(s).

#### Quality

You may change the output quality of each image style by overriding the
`quality()` method from `ImageStyleBase` class.

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
> You may bypass the interactive prompts by providing the options directly in
> the command. For more information, run the following command:
>
> ```shell
> php artisan image-style:flush --help
> ```

## Usage Examples

Usage examples are available in the
[Laravel Image Style Demo repository](https://github.com/balismatz/laravel-image-style-demo).

## Preview

You can preview the image style modifications by calling the
[`preview()`](src/ImageStyle.php#L1329) method.

> [!NOTE]
> Preview image is provided by [Freepik](https://www.freepik.com/).

## Deployment

When the [`optimize`](https://laravel.com/docs/master/deployment#optimization)
Artisan command is executed, all image style information is persisted to the
[configured cache store](config/image-style.php#L90), which improves the
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

   If everything above is correct, clear the image styles information
   cache by running the following command:

   ```shell
   php artisan image-style:clear
   ```

2. If styled images are not displayed, verify that the `filesystems.default`
   and `image-style.filesystem` configuration values are properly
   configured.

## License

Laravel Image Style is open-sourced software licensed under the
[MIT license](LICENSE.md).
