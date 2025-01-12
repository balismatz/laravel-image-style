# Laravel Image Style

A Laravel package to manage your app image styles with an easy and organized
way. Each image style is a PHP class that contains the image modifications.
A facade, a helper function and an Artisan command are provided to create,
retrieve and flush styled images. Also, Artisan commands are provided to easily
create image styles, list all the available image styles and cache or clear
their information.

To modify images, it uses the most popular open source PHP image processing
library, the [Intervention Image](https://github.com/Intervention/image).

There is an official Intervention Image package for Laravel
([Intervention Image Laravel](https://github.com/Intervention/image-laravel))
with a basic functionality. Check it out.

> [!IMPORTANT]
> This is **NOT** the official
> [Intervention Image Laravel](https://github.com/Intervention/image-laravel)
> package.

## Requirements
- PHP 8.4 or higher
- Laravel 11.0 or higher
- Intervention Image 3.9 or higher

## Installation

Require the package using Composer:

```shell
composer require balismatz/laravel-image-style
```

## Configuration

Package provides various configuration options. If you want to change the
default configuration, publish the [config](config/image-style.php) file by
running the following command:

```shell
php artisan vendor:publish --provider="BalisMatz\ImageStyle\ImageStyleServiceProvider"
```

## Usage

> [!NOTE]
> An extended documentation and a repository with examples will come in the near
> future.

### Create image styles

You can create an image style by running the following command:

```shell
php artisan image-style:make
```

After running the above command, the following prompts will appear:

1) ***What should the image style be named?***

   Set the image style class name. This name will be used to autogenerate - if
   needed - the unique image style ID (see next).

2) ***ID***

   By default, the package will try to generate the unique image style ID based
   on the class name, but this prompt gives you the ability to set your own ID,
   if you want. Leave it empty to autogenerate the image style ID.

   > - If the image style ID can not be generated from the given class name,
   > fallbacks to "default".
   > - In cases of multiple image styles with same ID, the first detected will
   > be considered as valid.

3) ***Help text***

   The package gives you the ability to list all available image styles. Here
   you can set a help text (important for teams) to describe the image
   modifications, why it is created, when to use it or other useful information.

4) ***Status***

   There are cases where you want to have a "Disabled" image style, ie. to use
   it in the future. You can set it's status by selecting one of the available
   options. "Default" is considered as active.

> [!TIP]
> You can avoid the prompts by passing the options in the command. Run the
> following command for more information:
>
> ```shell
> php artisan image-style:make --help
> ```

Image style classes are placed in ```/app/ImageStyles``` directory.

> [!TIP]
> You can use directory depth levels (from 0 to 3) to organize better your image
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

You can list all available image styles by running the following command:

```shell
php artisan image-style:list
```

> [!TIP]
> There are various list options. Run the following command for more information:
>
> ```shell
> php artisan image-style:list --help
> ```

### Create / Retrieve styled images

Styled images are stored in the ```styles/{{ image-style-id }}``` directory.
In example, the thumbnail (image style ID) of ```posts/main.jpg``` image will be
saved at ```/styles/thumbnail/posts/main.jpg```.

The disk that styled images will be stored, depends on
[configuration](config/image-style.php#L78) or the given parameters of the
following methods.

> [!IMPORTANT]
> - The following methods are available to you by either the
> ```BalisMatz\ImageStyle\Facades\ImageStyle``` facade or the ```imageStyle()```
> function.
>
> - In blade templates you can use the ```ImageStyle``` facade without namespace,
> as ```{{ ImageStyle::url() }}```.
>
> - ```ImageStyle``` facade is macroable.

1) **[path()](src/ImageStyle.php#L494)**

    Based on the given image style and the original image path, creates,
    recreates (see parameters) or retrieves the styled image and returns it's
    storage path.

    > Provides a basic functionality and it is useful when you simply want to
    > create a styled image. See the "Performance" section.

2) **[url()](src/ImageStyle.php#L924)**

    Based on the given image style and the original image path, creates,
    recreates (see parameters) or retrieves the styled image and returns it's
    storage URL.

    > It is useful when you want to display a styled image with the ```<img>```
    > HTML tag.

3) **[imageInformation()](src/ImageStyle.php#L62)**

    Based on the given image style and the original image path, creates,
    recreates (see parameters) or retrieves the styled image and returns an
    ```ImageStyleImageInformation``` object that contains the image URL, height,
    width, mimetype and the given parameters.

    > It is useful when you want to display a styled image with the ```<img>```
    > HTML tag and the ```lazy``` loading attribute. You can set the ```<img>```
    > height and width to avoid unexpected behaviors (ie. flickering).

4) **[paths()](src/ImageStyle.php#L710)**

    Based on the given image styles (array or string) and the original image
    path, creates, recreates (see parameters) or retrieves the styled images and
    returns their storage paths.

    > Provides a basic functionality and it is useful when you simply want to
    > create multiple styled images. See the "Performance" section.

5) **[urls()](src/ImageStyle.php#L1128)**

    Based on the given image styles (array or string) and the original image
    path, creates, recreates (see parameters) or retrieves the styled images and
    returns their storage URLs.

    > It is useful when you want to display responsive images - based on styles -
    > with the ```<img>``` HTML tag
    > ([more information](https://developer.mozilla.org/en-US/docs/Web/HTML/Responsive_images)).

6) **[imagesInformation()](src/ImageStyle.php#L278)**

    Based on the given image styles (array or string) and the original image
    path, creates, recreates (see parameters) or retrieves the styled images and
    returns a collection of ```ImageStyleImageInformation``` objects that
    contain the image URL, height, width, mimetype and the given parameters.

    > It is useful when you want to display responsive images - based on styles -
    > with the ```<img>``` ([more information](https://developer.mozilla.org/en-US/docs/Web/HTML/Responsive_images))
    > or ```<picture>``` ([more information](https://developer.mozilla.org/en-US/docs/Web/HTML/Element/picture))
    > HTML tags.
    >
    > You can pass - to each style - parameters that will be returned with
    > ```ImageStyleImageInformation``` objects. These parameters may contain the
    > media query for each styled image.

> [!NOTE]
> - Image style(s) parameter can be the image style ID or the class name
>    (with namespace). In example, ```App\ImageStyles\ThumbnailImageStyle::class```.
> - ```paths()``` - ```urls()``` - ```imagesInformation()``` get multiple
>    styles as array or string, separate by "," (comma).
> - All the above methods get style parameters (```$styleParameters```) that
>    are passed to image style class ```modifications()``` and ```quality()```
>    methods. In example, you can pass a dynamic watermark, focal point etc.
> - All the above methods have the following name suffixes, to convert the
>    styled image(s) in other format:
>    - ```{methodName}ToJpeg()```
>    - ```{methodName}ToWebp()```
>    - ```{methodName}ToPng()```
>    - ```{methodName}ToGif()```
>    - ```{methodName}ToBmp()```
>    - ```{methodName}ToAvif()```
>    - ```{methodName}ToTiff()```
>    - ```{methodName}ToJpeg2000()```
>    - ```{methodName}ToHeic()```

> [!TIP]
> Click each of the above methods to see the available parameters.

#### Fallback URL

When style(s) or original image do not exist and based on the
[configuration](config/image-style.php#L65), ```url()``` - ```urls()``` -
```imageInformation()``` - ```imagesInformation()``` would return the default
storage URL(s) or empty value(s).

#### Quality

You can change the quality of each image style by overriding the ```quality()```
method from ```ImageStyleBase``` class.

#### Performance

By default, styled images are created when one of the above methods is called.
This means that styled images will be created the first time that a user visits
the page. You can avoid this behavior by simply calling the ```path()``` or
```paths()``` method (for each image) with a queued job or when the model,
that references the image(s), is saved.

### Flush styled images

You can flush styled images by running the following command:

```shell
php artisan image-style:flush
```

> [!TIP]
> You can avoid the prompts by passing the options in the command. Run the
> following command for more information:
>
> ```shell
> php artisan image-style:flush --help
> ```

## Preview

You can preview the image style modifications by calling the
[```preview()```](src/ImageStyle.php#L1329) method.

> [!NOTE]
> Preview image by [Freepik](https://www.freepik.com/).

## Deployment

If you are building an application with many image styles, you should make sure
that you are running the ```image-style:cache``` Artisan command during your
deployment process:

```shell
php artisan image-style:cache
```

This command caches the image styles information in the
[configured cache store](config/image-style.php#L90), improving the performance of
the image style information retrieval.

## Troubleshooting

If image style is not listed on the available styles, check if:

1) Class is in the ```/app/ImageStyles``` directory.
2) Class is placed in a supported directory level.
3) Class extends the ```ImageStyleBase``` class.
4) Class is not an ```abstract``` class.

If all of the above is correct, try to clear the cache of image styles
information by running the following command:

```shell
php artisan image-style:clear
```

## License

Laravel Image Style is open-sourced software licensed under the
[MIT license](LICENSE.md).
