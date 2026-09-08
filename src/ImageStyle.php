<?php

namespace BalisMatz\ImageStyle;

use BalisMatz\ImageStyle\Information\ImageStyleImageInformation;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Http\Response;
use Illuminate\Image\Image;
use Illuminate\Image\ImageException;
use Illuminate\Image\ImagePipeline;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Traits\Macroable;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\File\File;

class ImageStyle
{
    use Macroable;

    /**
     * The loaded filesystem adapters.
     *
     * @var FilesystemAdapter[]
     */
    protected array $loadedFilesystemAdapters = [];

    /**
     * Create a new image style instance.
     */
    public function __construct(
        protected ImageStyleManager $imageStyleManager,
        protected FilesystemManager $filesystemManager,
    ) {}

    /**
     * Get the information for a styled image.
     *
     * @param  string  $style
     *                         The style ID or the fully qualified class name.
     * @param  string  $path
     *                        The path to the image in the filesystem.
     * @param  mixed  $styleParameters
     *                                  Optional: The parameters to pass to the style class's "modifications" method.
     * @param  mixed  $informationParameters
     *                                        Optional: The parameters to pass to the information object, such as alt text, title text, or media query values for responsive images.
     * @param  bool  $dominantColor
     *                               Optional: Whether to include the dominant (average) image color as a hex string. Available in Laravel 13.24 and later.
     * @param  string|null  $disk
     *                             Optional: The disk used to load the original image. If null, the default (filesystems.default) disk will be used.
     * @param  bool  $styleSameDisk
     *                               Optional: Whether the styled image should be stored on the same disk as the original image. If false, it will be stored on the configured image-style (image-style.filesystem) disk.
     * @param  string|null  $filename
     *                                 Optional: Some applications may use multiple disks for files and store styled images on a single disk. This can cause conflicts when the same filename is used in the same filesystem path. Set a different filename to resolve these conflicts. A common pattern is to prefix or suffix the source disk name in the filename, for example: image => s3-image.
     * @param  string|null  $format
     *                               Optional: The output format for the styled image. Supported values: webp, jpg, png, gif, avif, heic, bmp (see: https://laravel.com/docs/images#encoding-images).
     * @param  bool  $recreate
     *                          Optional: Whether to force regeneration of the styled image.
     * @return ImageStyleImageInformation|null
     *                                         The information for a styled image, or null.
     *
     * @throws ImageException If the format is invalid.
     */
    public function imageInformation(string $style, string $path, mixed $styleParameters = null, mixed $informationParameters = null, bool $dominantColor = false, ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, ?string $format = null, bool $recreate = false): ?ImageStyleImageInformation
    {
        return $this->imagesInformation(
            $this->extractStyles($style)->keys()->first(),
            $path,
            $styleParameters,
            [$informationParameters],
            $dominantColor,
            $disk,
            $styleSameDisk,
            $filename,
            $format,
            $recreate
        )->first();
    }

    /**
     * Get the information for styled images.
     *
     * @param  array<string>|string  $styles
     *                                        The style IDs or the fully qualified class names.
     * @param  string  $path
     *                        The path to the image in the filesystem.
     * @param  mixed  $styleParameters
     *                                  Optional: The parameters to pass to each style class's "modifications" method.
     * @param  array<mixed>  $informationParameters
     *                                               Optional: The parameters to pass to the information objects, such as alt text, title text, or media query values for responsive images. Use a new array item for each style information object.
     * @param  bool  $dominantColor
     *                               Optional: Whether to include the dominant (average) image color as a hex string. Available in Laravel 13.24 and later.
     * @param  string|null  $disk
     *                             Optional: The disk used to load the original image. If null, the default (filesystems.default) disk will be used.
     * @param  bool  $styleSameDisk
     *                               Optional: Whether the styled images should be stored on the same disk as the original image. If false, they will be stored on the configured image-style (image-style.filesystem) disk.
     * @param  string|null  $filename
     *                                 Optional: Some applications may use multiple disks for files and store styled images on a single disk. This can cause conflicts when the same filenames are used in the same filesystem path. Set different filenames to resolve these conflicts. A common pattern is to prefix or suffix the source disk name in each filename, for example: image => s3-image.
     * @param  string|null  $format
     *                               Optional: The output format for the styled images. Supported values: webp, jpg, png, gif, avif, heic, bmp (see: https://laravel.com/docs/images#encoding-images).
     * @param  bool  $recreate
     *                          Optional: Whether to force regeneration of the styled images.
     * @return Collection<string, ImageStyleImageInformation|null>
     *                                                             The information for styled images, keyed by the given values ($styles); each value may be null.
     *
     * @throws ImageException If the format is invalid.
     */
    public function imagesInformation(array|string $styles, string $path, mixed $styleParameters = null, array $informationParameters = [], bool $dominantColor = false, ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, ?string $format = null, bool $recreate = false): Collection
    {
        $stylesFilesystem = match ($styleSameDisk) {
            true => $this->filesystemAdapter($disk),
            default => $this->filesystemAdapter(config('image-style.filesystem'))
        };

        $stylesPathsIndex = -1;

        return $this->paths(
            $styles,
            $path,
            $styleParameters,
            $disk,
            $styleSameDisk,
            $filename,
            $format,
            $recreate,
            true
        )->map(function (?string $stylePath, string $style) use (&$stylesPathsIndex, $informationParameters, $path, $disk, $stylesFilesystem, $dominantColor): ?ImageStyleImageInformation {
            $stylesPathsIndex++;

            $styleInformationParameters = $informationParameters[$style] ?? $informationParameters[$stylesPathsIndex] ?? null;

            if (! $stylePath) {
                if ($styleFallbackUrl = $this->fallbackUrl($path, $disk)) {
                    if ($image = $this->image($path, $disk)) {
                        return new ImageStyleImageInformation(
                            $styleFallbackUrl,
                            $image->height(),
                            $image->width(),
                            $image->mimeType(),
                            /** @phpstan-ignore-next-line function.alreadyNarrowedType */
                            $dominantColor && method_exists($image, 'dominantColor') ?
                                $image->dominantColor() : null,
                            $styleInformationParameters
                        );
                    }

                    return new ImageStyleImageInformation(
                        url: $styleFallbackUrl,
                        parameters: $styleInformationParameters
                    );
                }

                return null;
            }

            $image = $stylesFilesystem->image($stylePath);

            return new ImageStyleImageInformation(
                $stylesFilesystem->url($stylePath),
                $image->height(),
                $image->width(),
                $image->mimeType(),
                /** @phpstan-ignore-next-line function.alreadyNarrowedType */
                $dominantColor && method_exists($image, 'dominantColor') ?
                    $image->dominantColor() : null,
                $styleInformationParameters
            );
        });
    }

    /**
     * Get the path for a styled image.
     *
     * @param  string  $style
     *                         The style ID or the fully qualified class name.
     * @param  string  $path
     *                        The path to the image in the filesystem.
     * @param  mixed  $styleParameters
     *                                  Optional: The parameters to pass to the style class's "modifications" method.
     * @param  string|null  $disk
     *                             Optional: The disk used to load the original image. If null, the default (filesystems.default) disk will be used.
     * @param  bool  $styleSameDisk
     *                               Optional: Whether the styled image should be stored on the same disk as the original image. If false, it will be stored on the configured image-style (image-style.filesystem) disk.
     * @param  string|null  $filename
     *                                 Optional: Some applications may use multiple disks for files and store styled images on a single disk. This can cause conflicts when the same filename is used in the same filesystem path. Set a different filename to resolve these conflicts. A common pattern is to prefix or suffix the source disk name in the filename, for example: image => s3-image.
     * @param  string|null  $format
     *                               Optional: The output format for the styled image. Supported values: webp, jpg, png, gif, avif, heic, bmp (see: https://laravel.com/docs/images#encoding-images).
     * @param  bool  $recreate
     *                          Optional: Whether to force regeneration of the styled image.
     * @param  bool  $relative
     *                          Optional: When using the local driver, the absolute path to the image will be returned. However, to get the image size, MIME type, etc. from the "Storage" facade, the relative path must be used. If true, the relative path to the image will be returned for all drivers.
     * @return string|null
     *                     The path for a styled image, or null.
     *
     * @throws ImageException If the format is invalid.
     */
    public function path(string $style, string $path, mixed $styleParameters = null, ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, ?string $format = null, bool $recreate = false, bool $relative = false): ?string
    {
        return $this->paths(
            $this->extractStyles($style)->keys()->first(),
            $path,
            $styleParameters,
            $disk,
            $styleSameDisk,
            $filename,
            $format,
            $recreate,
            $relative
        )->first();
    }

    /**
     * Get the paths for styled images.
     *
     * @param  array<string>|string  $styles
     *                                        The style IDs or the fully qualified class names.
     * @param  string  $path
     *                        The path to the image in the filesystem.
     * @param  mixed  $styleParameters
     *                                  Optional: The parameters to pass to each style class's "modifications" method.
     * @param  string|null  $disk
     *                             Optional: The disk used to load the original image. If null, the default (filesystems.default) disk will be used.
     * @param  bool  $styleSameDisk
     *                               Optional: Whether the styled images should be stored on the same disk as the original image. If false, they will be stored on the configured image-style (image-style.filesystem) disk.
     * @param  string|null  $filename
     *                                 Optional: Some applications may use multiple disks for files and store styled images on a single disk. This can cause conflicts when the same filenames are used in the same filesystem path. Set different filenames to resolve these conflicts. A common pattern is to prefix or suffix the source disk name in each filename, for example: image => s3-image.
     * @param  string|null  $format
     *                               Optional: The output format for the styled images. Supported values: webp, jpg, png, gif, avif, heic, bmp (see: https://laravel.com/docs/images#encoding-images).
     * @param  bool  $recreate
     *                          Optional: Whether to force regeneration of the styled images.
     * @param  bool  $relative
     *                          Optional: When using the local driver, the absolute path to images will be returned. However, to get the image size, MIME type, etc. from the "Storage" facade, the relative paths must be used. If true, the relative path to images will be returned for all drivers.
     * @return Collection<string, string|null>
     *                                         The paths for styled images, keyed by the given values ($styles); each value may be null.
     *
     * @throws ImageException If the format is invalid.
     */
    public function paths(array|string $styles, string $path, mixed $styleParameters = null, ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, ?string $format = null, bool $recreate = false, bool $relative = false): Collection
    {
        $stylesPaths = $this->findOrCreate(
            $styles,
            $path,
            $styleParameters,
            $disk,
            $styleSameDisk,
            $filename,
            $format,
            $recreate
        );

        if ($relative) {
            return $stylesPaths;
        }

        $stylesFilesystem = match ($styleSameDisk) {
            true => $this->filesystemAdapter($disk),
            default => $this->filesystemAdapter(config('image-style.filesystem'))
        };

        return $stylesPaths->map(function (?string $stylePath) use ($stylesFilesystem): ?string {
            if (! $stylePath) {
                return null;
            }

            return $stylesFilesystem->path($stylePath);
        });
    }

    /**
     * Get the URL for a styled image.
     *
     * @param  string  $style
     *                         The style ID or the fully qualified class name.
     * @param  string  $path
     *                        The path to the image in the filesystem.
     * @param  mixed  $styleParameters
     *                                  Optional: The parameters to pass to the style class's "modifications" method.
     * @param  string|null  $disk
     *                             Optional: The disk used to load the original image. If null, the default (filesystems.default) disk will be used.
     * @param  bool  $styleSameDisk
     *                               Optional: Whether the styled image should be stored on the same disk as the original image. If false, it will be stored on the configured image-style (image-style.filesystem) disk.
     * @param  string|null  $filename
     *                                 Optional: Some applications may use multiple disks for files and store styled images on a single disk. This can cause conflicts when the same filename is used in the same filesystem path. Set a different filename to resolve these conflicts. A common pattern is to prefix or suffix the source disk name in the filename, for example: image => s3-image.
     * @param  string|null  $format
     *                               Optional: The output format for the styled image. Supported values: webp, jpg, png, gif, avif, heic, bmp (see: https://laravel.com/docs/images#encoding-images).
     * @param  bool  $recreate
     *                          Optional: Whether to force regeneration of the styled image.
     * @return string|null
     *                     The URL for a styled image, or null.
     *
     * @throws ImageException If the format is invalid.
     */
    public function url(string $style, string $path, mixed $styleParameters = null, ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, ?string $format = null, bool $recreate = false): ?string
    {
        return $this->urls(
            $this->extractStyles($style)->keys()->first(),
            $path,
            $styleParameters,
            $disk,
            $styleSameDisk,
            $filename,
            $format,
            $recreate
        )->first();
    }

    /**
     * Get the URLs for styled images.
     *
     * @param  array<string>|string  $styles
     *                                        The style IDs or the fully qualified class names.
     * @param  string  $path
     *                        The path to the image in the filesystem.
     * @param  mixed  $styleParameters
     *                                  Optional: The parameters to pass to each style class's "modifications" method.
     * @param  string|null  $disk
     *                             Optional: The disk used to load the original image. If null, the default (filesystems.default) disk will be used.
     * @param  bool  $styleSameDisk
     *                               Optional: Whether the styled images should be stored on the same disk as the original image. If false, they will be stored on the configured image-style (image-style.filesystem) disk.
     * @param  string|null  $filename
     *                                 Optional: Some applications may use multiple disks for files and store styled images on a single disk. This can cause conflicts when the same filenames are used in the same filesystem path. Set different filenames to resolve these conflicts. A common pattern is to prefix or suffix the source disk name in each filename, for example: image => s3-image.
     * @param  string|null  $format
     *                               Optional: The output format for the styled images. Supported values: webp, jpg, png, gif, avif, heic, bmp (see: https://laravel.com/docs/images#encoding-images).
     * @param  bool  $recreate
     *                          Optional: Whether to force regeneration of the styled images.
     * @return Collection<string, string|null>
     *                                         The URLs for styled images, keyed by the given values ($styles); each value may be null.
     *
     * @throws ImageException If the format is invalid.
     */
    public function urls(array|string $styles, string $path, mixed $styleParameters = null, ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, ?string $format = null, bool $recreate = false): Collection
    {
        $stylesFilesystem = match ($styleSameDisk) {
            true => $this->filesystemAdapter($disk),
            default => $this->filesystemAdapter(config('image-style.filesystem'))
        };

        return $this->paths($styles, $path, $styleParameters, $disk, $styleSameDisk, $filename, $format, $recreate, true)
            ->map(function (?string $stylePath) use ($path, $disk, $stylesFilesystem): ?string {
                if (! $stylePath) {
                    return $this->fallbackUrl($path, $disk);
                }

                return $stylesFilesystem->url($stylePath);
            });
    }

    /**
     * Preview a styled image.
     *
     * @param  string  $style
     *                         The style ID or the fully qualified class name.
     * @param  string|null  $path
     *                             Optional: The path to the image in the filesystem. If null, a default image will be used.
     * @param  mixed  $styleParameters
     *                                  Optional: The parameters to pass to the style class's "modifications" method.
     * @param  string|null  $disk
     *                             Optional: The disk used to load the original image. If null, the default (filesystems.default) disk will be used.
     * @return Response
     *                  The styled image preview response.
     *
     * @throws InvalidArgumentException
     *                                  If the image style does not exist.
     */
    public function preview(string $style, ?string $path = null, mixed $styleParameters = null, ?string $disk = null): Response
    {
        if (! $styleInformation = $this->imageStyleManager->get($style)) {
            throw new InvalidArgumentException('The image style does not exist.');
        }

        $imagePath = __DIR__.'/../preview.jpg';

        $image = new Image(
            fn (): string => file_get_contents($imagePath),
        );

        if ($path && $pathImage = $this->image($path, $disk)) {
            $imagePath = $path;

            $image = $pathImage;
        }

        $manipulatedImage = $this->manipulatedImage(
            $image,
            $styleInformation->class,
            $styleParameters
        );

        /** @var ImagePipeline $manipulatedImagePipeline */
        /** @phpstan-ignore method.notFound */
        $manipulatedImagePipeline = $manipulatedImage->getPipeline();

        $imagePathInfo = collect(pathinfo($imagePath))
            ->when(
                $manipulatedImagePipeline->output->format,
                fn (Collection $collection, string $value): Collection => $collection->put('extension', $value)
            );

        $filename = "{$imagePathInfo->get('filename')}.{$imagePathInfo->get('extension')}";

        $manipulatedImageBytes = $manipulatedImage->toBytes();

        return response(
            content: $manipulatedImageBytes,
            headers: [
                'Content-Disposition' => 'inline; filename="'.$filename.'"',
                'Content-Length' => strlen($manipulatedImageBytes),
                'Content-Type' => $manipulatedImage->mimeType(),
            ]
        );
    }

    /**
     * Extract style IDs or fully qualified class names as collection keys.
     *
     * @param  array<string>|string  $styles
     *                                        The style IDs or the fully qualified class names.
     * @return Collection<string, null>
     *                                  The extracted styles.
     */
    protected function extractStyles(array|string $styles): Collection
    {
        if (is_string($styles)) {
            $styles = array_map('trim', explode(',', $styles));
        }

        return collect($styles)
            /** @phpstan-ignore function.alreadyNarrowedType */
            ->filter(fn (mixed $value): bool => $value && is_string($value))
            ->flip()
            ->map(fn (): null => null);
    }

    /**
     * Find or create the paths for one or more styled images.
     *
     * @param  array<string>|string  $styles
     *                                        The style IDs or the fully qualified class names.
     * @param  string  $path
     *                        The path to the image in the filesystem.
     * @param  mixed  $styleParameters
     *                                  Optional: The parameters to pass to each style class's "modifications" method.
     * @param  string|null  $disk
     *                             Optional: The disk used to load the original image. If null, the default (filesystems.default) disk will be used.
     * @param  bool  $styleSameDisk
     *                               Optional: Whether the styled images should be stored on the same disk as the original image. If false, they will be stored on the configured image-style (image-style.filesystem) disk.
     * @param  string|null  $filename
     *                                 Optional: Some applications may use multiple disks for files and store styled images on a single disk. This can cause conflicts when the same filenames are used in the same filesystem path. Set different filenames to resolve these conflicts. A common pattern is to prefix or suffix the source disk name in each filename, for example: image => s3-image.
     * @param  string|null  $format
     *                               Optional: The output format for the styled images. Supported values: webp, jpg, png, gif, avif, heic, bmp (see: https://laravel.com/docs/images#encoding-images).
     * @param  bool  $recreate
     *                          Optional: Whether to force regeneration of the styled images.
     * @return Collection<string, string|null>
     *                                         The paths for styled images in the filesystem, keyed by the given values ($styles); each value may be null.
     *
     * @throws ImageException If the format is invalid.
     */
    protected function findOrCreate(array|string $styles, string $path, mixed $styleParameters = null, ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, ?string $format = null, bool $recreate = false): Collection
    {
        $filesystem = $this->filesystemAdapter($disk);

        $stylesFilesystemDisk = $styleSameDisk ?
            $disk :
            config('image-style.filesystem');

        $stylesFilesystem = $this->filesystemAdapter($stylesFilesystemDisk);

        $image = $this->image($path, $disk);

        return $this->filesystemPaths($styles, $path, $styleParameters, $disk, $filename, $format)
            ->map(function (?string $stylePath, string $style) use ($recreate, $image, $stylesFilesystem, $stylesFilesystemDisk, $styleParameters, $format, $filesystem, $path): ?string {
                if (! $stylePath || (! $recreate && $stylesFilesystem->exists($stylePath))) {
                    return $stylePath;
                }

                $manipulatedImage = $this->manipulatedImage(
                    $image,
                    $this->imageStyleManager->get($style)->class,
                    $styleParameters,
                    $format
                );

                $stylePathInfo = pathinfo($stylePath);

                if (! $manipulatedImage->storeAs(
                    $stylePathInfo['dirname'],
                    $stylePathInfo['basename'],
                    $stylesFilesystemDisk,
                    ['visibility' => $filesystem->visibility($path)]
                )) {
                    return null;
                }

                return $stylePath;
            });
    }

    /**
     * Get the fallback URL when an image or a style does not exist.
     *
     * @param  string  $path
     *                        The path to the image in the filesystem.
     * @param  string|null  $disk
     *                             Optional: The disk used to load the image. If null, the default (filesystems.default) disk will be used.
     * @return string|null
     *                     The fallback URL.
     */
    protected function fallbackUrl(string $path, ?string $disk = null): ?string
    {
        return match (config('image-style.fallback_url')) {
            'storage_url' => $this->filesystemAdapter($disk)->url($path),
            default => null
        };
    }

    /**
     * Get the image.
     *
     * @param  string  $path
     *                        The path to the image in the filesystem.
     * @param  string|null  $disk
     *                             Optional: The disk used to load the image. If null, the default (filesystems.default) disk will be used.
     * @return Image|null
     *                    The image, or null.
     */
    protected function image(string $path, ?string $disk = null): ?Image
    {
        try {
            $file = new File(
                $this->filesystemAdapter($disk)->path($path)
            );

            $fileValidator = Validator::make(
                ['file' => $file],
                ['file' => 'image']
            );

            if ($fileValidator->fails()) {
                throw new InvalidArgumentException;
            }

            return new Image($file->getContent());
        } catch (FileNotFoundException|InvalidArgumentException) {
            return null;
        }
    }

    /**
     * Get the filesystem adapter.
     *
     * @param  string|null  $disk
     *                             Optional: The disk name. If null, the default (filesystems.default) disk will be used.
     * @return FilesystemAdapter
     *                           The filesystem adapter.
     */
    protected function filesystemAdapter(?string $disk = null): FilesystemAdapter
    {
        $disk ??= config('filesystems.default');

        if (array_key_exists($disk, $this->loadedFilesystemAdapters)) {
            return $this->loadedFilesystemAdapters[$disk];
        }

        /** @var FilesystemAdapter $filesystemAdapter */
        $filesystemAdapter = $this->filesystemManager->disk($disk);

        $this->loadedFilesystemAdapters[$disk] = $filesystemAdapter;

        return $this->loadedFilesystemAdapters[$disk];
    }

    /**
     * Get the paths in the filesystem for styled images.
     *
     * @param  array<string>|string  $styles
     *                                        The style IDs or the fully qualified class names.
     * @param  string  $path
     *                        The path to the image in the filesystem.
     * @param  mixed  $styleParameters
     *                                  Optional: The parameters to pass to each style class's "modifications" method.
     * @param  string|null  $disk
     *                             Optional: The disk used to load the original image. If null, the default (filesystems.default) disk will be used.
     * @param  string|null  $filename
     *                                 Optional: Some applications may use multiple disks for files and store styled images on a single disk. This can cause conflicts when the same filenames are used in the same filesystem path. Set different filenames to resolve these conflicts. A common pattern is to prefix or suffix the source disk name in each filename, for example: image => s3-image.
     * @param  string|null  $format
     *                               Optional: The output format for the styled images. Supported values: webp, jpg, png, gif, avif, heic, bmp (see: https://laravel.com/docs/images#encoding-images).
     * @return Collection<string, string|null>
     *                                         The paths in the filesystem for styled images, keyed by the given values ($styles); each value may be null.
     *
     * @throws ImageException If the format is invalid.
     */
    protected function filesystemPaths(array|string $styles, string $path, mixed $styleParameters = null, ?string $disk = null, ?string $filename = null, ?string $format = null): Collection
    {
        $styles = $this->extractStyles($styles);

        if (! $image = $this->image($path, $disk)) {
            return $styles;
        }

        $pathInfo = collect(pathinfo($path));

        return $styles->map(function (null $stylePath, string $style) use ($pathInfo, $filename, $image, $styleParameters, $format): ?string {
            if (! $styleInformation = $this->imageStyleManager->get($style)) {
                return null;
            }

            return $pathInfo
                ->only(['dirname', 'filename'])
                ->when($filename, fn (Collection $collection) => $collection->put('filename', $filename))
                ->filter(fn (string $value): bool => $value !== '.')
                ->map(function (string $value, string $key) use ($image, $styleInformation, $styleParameters, $format, $pathInfo) {
                    if ($key !== 'filename') {
                        return $value;
                    }

                    // Use the image to determine whether the style changes the
                    // file type (extension).
                    $manipulatedImage = $this->manipulatedImage(
                        $image,
                        $styleInformation->class,
                        $styleParameters,
                        $format
                    );

                    /** @var ImagePipeline $manipulatedImagePipeline */
                    /** @phpstan-ignore method.notFound */
                    $manipulatedImagePipeline = $manipulatedImage->getPipeline();

                    $extension = $manipulatedImagePipeline->output->format ?? $pathInfo->get('extension');

                    return "{$value}.{$extension}";
                })
                ->prepend($styleInformation->id)
                ->prepend('styles')
                ->join(DIRECTORY_SEPARATOR);
        });
    }

    /**
     * Apply style manipulations to the given image.
     *
     * @param  Image  $image
     *                        The image.
     * @param  string  $styleClass
     *                              The fully qualified style class name.
     * @param  mixed  $styleParameters
     *                                  Optional: The parameters to pass to the style class's "modifications" method.
     * @param  string|null  $format
     *                               Optional: The output format for the manipulated image. Supported values: webp, jpg, png, gif, avif, heic, bmp (see: https://laravel.com/docs/images#encoding-images).
     * @return Image
     *               The manipulated image.
     *
     * @throws ImageException If the format is invalid.
     */
    protected function manipulatedImage(Image $image, string $styleClass, mixed $styleParameters = null, ?string $format = null): Image
    {
        /** @var ImageStyleBase $style */
        $style = new $styleClass;

        $manipulatedImage = $style->manipulations($image, $styleParameters);

        /** @var ImagePipeline $manipulatedImagePipeline */
        /** @phpstan-ignore method.notFound */
        $manipulatedImagePipeline = $manipulatedImage->getPipeline();

        if (! $manipulatedImagePipeline->output->quality) {
            $manipulatedImage = $manipulatedImage->quality(
                max(1, min(100, config('image-style.quality')))
            );
        }

        if ($format) {
            $manipulatedImage = $manipulatedImage->toFormat($format);
        }

        return $manipulatedImage;
    }
}
