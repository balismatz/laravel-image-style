<?php

namespace BalisMatz\ImageStyle;

use BalisMatz\ImageStyle\Information\ImageStyleImageInformation;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Traits\Macroable;
use Intervention\Image\Interfaces\EncodedImageInterface;
use Intervention\Image\Interfaces\ImageInterface;
use Intervention\Image\Interfaces\ImageManagerInterface;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Mime\MimeTypes;

class ImageStyle
{
    use Macroable;

    /**
     * The loaded filesystem adapters.
     *
     * @var \Illuminate\Filesystem\FilesystemAdapter[]
     */
    protected array $loadedFilesystemAdapters = [];

    /**
     * Create a new image style instance.
     */
    public function __construct(
        protected ImageStyleManager $imageStyleManager,
        protected ImageManagerInterface $imageManager,
        protected FilesystemManager $filesystemManager,
    ) {}

    /**
     * Get the styled image URL with information.
     *
     * @param  string  $style
     *                         The style ID or class.
     * @param  string  $path
     *                        The image path to filesystem.
     * @param  array  $styleParameters
     *                                  Optional: The style parameters to pass.
     * @param  mixed  $informationParameters
     *                                        Optional: The parameters to pass in the information object. This is useful for information like alt, title or media query (for responsive images).
     * @param  string|null  $disk
     *                             Optional: The disk to load the original image. If it is null, the default disk will be used.
     * @param  bool  $styleSameDisk
     *                               Optional: By default, styled image will be saved at image styles disk. When this parameter is true, styled image will be saved at the same disk where the original image is stored.
     * @param  string|null  $filename
     *                                 Optional: Some applications might use multiple disks for files and store styled images at one disk. This can lead to conflicts when there are files with the same name at the same filesystem path. Set a different file name to resolve these conflicts. A possible fix would be to use the source disk as filename prefix or suffix (ie. image => s3-image).
     * @param  bool  $recreate
     *                          Optional: Recreate the styled image.
     * @return \BalisMatz\ImageStyle\Information\ImageStyleImageInformation|null
     *                                                                           The styled image URL with information or null.
     */
    public function imageInformation(string $style, string $path, array $styleParameters = [], mixed $informationParameters = null, ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false): ?ImageStyleImageInformation
    {
        return $this->getImageInformation(
            $style,
            $path,
            $styleParameters,
            $informationParameters,
            $disk,
            $styleSameDisk,
            $filename,
            $recreate
        );
    }

    /**
     * Get the styled image URL with information in JPEG format.
     *
     * @see \BalisMatz\ImageStyle\ImageStyle::imageInformation() Check params and return.
     */
    public function imageInformationToJpeg(string $style, string $path, array $styleParameters = [], mixed $informationParameters = null, ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false): ?ImageStyleImageInformation
    {
        return $this->getImageInformation(
            $style,
            $path,
            $styleParameters,
            $informationParameters,
            $disk,
            $styleSameDisk,
            $filename,
            $recreate,
            'toJpeg'
        );
    }

    /**
     * Get the styled image URL with information in WebP graphic format.
     *
     * @see \BalisMatz\ImageStyle\ImageStyle::imageInformation() Check params and return.
     */
    public function imageInformationToWebp(string $style, string $path, array $styleParameters = [], mixed $informationParameters = null, ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false): ?ImageStyleImageInformation
    {
        return $this->getImageInformation(
            $style,
            $path,
            $styleParameters,
            $informationParameters,
            $disk,
            $styleSameDisk,
            $filename,
            $recreate,
            'toWebp'
        );
    }

    /**
     * Get the styled image URL with information in PNG format.
     *
     * @see \BalisMatz\ImageStyle\ImageStyle::imageInformation() Check params and return.
     */
    public function imageInformationToPng(string $style, string $path, array $styleParameters = [], mixed $informationParameters = null, ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false): ?ImageStyleImageInformation
    {
        return $this->getImageInformation(
            $style,
            $path,
            $styleParameters,
            $informationParameters,
            $disk,
            $styleSameDisk,
            $filename,
            $recreate,
            'toPng'
        );
    }

    /**
     * Get the styled image URL with information in GIF format.
     *
     * @see \BalisMatz\ImageStyle\ImageStyle::imageInformation() Check params and return.
     */
    public function imageInformationToGif(string $style, string $path, array $styleParameters = [], mixed $informationParameters = null, ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false): ?ImageStyleImageInformation
    {
        return $this->getImageInformation(
            $style,
            $path,
            $styleParameters,
            $informationParameters,
            $disk,
            $styleSameDisk,
            $filename,
            $recreate,
            'toGif'
        );
    }

    /**
     * Get the styled image URL with information in Windows Bitmap format.
     *
     * @see \BalisMatz\ImageStyle\ImageStyle::imageInformation() Check params and return.
     */
    public function imageInformationToBmp(string $style, string $path, array $styleParameters = [], mixed $informationParameters = null, ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false): ?ImageStyleImageInformation
    {
        return $this->getImageInformation(
            $style,
            $path,
            $styleParameters,
            $informationParameters,
            $disk,
            $styleSameDisk,
            $filename,
            $recreate,
            'toBmp'
        );
    }

    /**
     * Get the styled image URL with information in AVIF format.
     *
     * @see \BalisMatz\ImageStyle\ImageStyle::imageInformation() Check params and return.
     */
    public function imageInformationToAvif(string $style, string $path, array $styleParameters = [], mixed $informationParameters = null, ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false): ?ImageStyleImageInformation
    {
        return $this->getImageInformation(
            $style,
            $path,
            $styleParameters,
            $informationParameters,
            $disk,
            $styleSameDisk,
            $filename,
            $recreate,
            'toAvif'
        );
    }

    /**
     * Get the styled image URL with information in TIFF format.
     *
     * @see \BalisMatz\ImageStyle\ImageStyle::imageInformation() Check params and return.
     */
    public function imageInformationToTiff(string $style, string $path, array $styleParameters = [], mixed $informationParameters = null, ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false): ?ImageStyleImageInformation
    {
        return $this->getImageInformation(
            $style,
            $path,
            $styleParameters,
            $informationParameters,
            $disk,
            $styleSameDisk,
            $filename,
            $recreate,
            'toTiff'
        );
    }

    /**
     * Get the styled image URL with information in JPEG 2000 format.
     *
     * @see \BalisMatz\ImageStyle\ImageStyle::imageInformation() Check params and return.
     */
    public function imageInformationToJpeg2000(string $style, string $path, array $styleParameters = [], mixed $informationParameters = null, ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false): ?ImageStyleImageInformation
    {
        return $this->getImageInformation(
            $style,
            $path,
            $styleParameters,
            $informationParameters,
            $disk,
            $styleSameDisk,
            $filename,
            $recreate,
            'toJpeg2000'
        );
    }

    /**
     * Get the styled image URL with information in HEIC format.
     *
     * @see \BalisMatz\ImageStyle\ImageStyle::imageInformation() Check params and return.
     */
    public function imageInformationToHeic(string $style, string $path, array $styleParameters = [], mixed $informationParameters = null, ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false): ?ImageStyleImageInformation
    {
        return $this->getImageInformation(
            $style,
            $path,
            $styleParameters,
            $informationParameters,
            $disk,
            $styleSameDisk,
            $filename,
            $recreate,
            'toHeic'
        );
    }

    /**
     * Get the styled image URLs with information.
     *
     * @param  array|string  $styles
     *                                The style IDs or classes.
     * @param  string  $path
     *                        The image path to filesystem.
     * @param  array  $styleParameters
     *                                  Optional: The style parameters to pass.
     * @param  array  $informationParameters
     *                                        Optional: The parameters to pass in the information objects. This is useful for information like alt, title or media query (for responsive images). Use a new array item for each style information object.
     * @param  string|null  $disk
     *                             Optional: The disk to load the original image. If it is null, the default disk will be used.
     * @param  bool  $styleSameDisk
     *                               Optional: By default, styled images will be saved at image styles disk. When this parameter is true, styled images will be saved at the same disk where the original image is stored.
     * @param  string|null  $filename
     *                                 Optional: Some applications might use multiple disks for files and store styled images at one disk. This can lead to conflicts when there are files with the same name at the same filesystem path. Set a different file name to resolve these conflicts. A possible fix would be to use the source disk as filename prefix or suffix (ie. image => s3-image).
     * @param  bool  $recreate
     *                          Optional: Recreate the styled images.
     * @return \Illuminate\Support\Collection<string, \BalisMatz\ImageStyle\Information\ImageStyleImageInformation|null>
     *                                                                                                                   The styled image URLs with information or null, keyed by the given values ($styles).
     */
    public function imagesInformation(array|string $styles, string $path, array $styleParameters = [], array $informationParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false): Collection
    {
        return $this->getImagesInformation(
            $styles,
            $path,
            $styleParameters,
            $informationParameters,
            $disk,
            $styleSameDisk,
            $filename,
            $recreate
        );
    }

    /**
     * Get the styled image URLs with information in JPEG format.
     *
     * @see \BalisMatz\ImageStyle\ImageStyle::imagesInformation() Check params and return.
     */
    public function imagesInformationToJpeg(array|string $styles, string $path, array $styleParameters = [], array $informationParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false): Collection
    {
        return $this->getImagesInformation(
            $styles,
            $path,
            $styleParameters,
            $informationParameters,
            $disk,
            $styleSameDisk,
            $filename,
            $recreate,
            'toJpeg'
        );
    }

    /**
     * Get the styled image URLs with information in WebP graphic format.
     *
     * @see \BalisMatz\ImageStyle\ImageStyle::imagesInformation() Check params and return.
     */
    public function imagesInformationToWebp(array|string $styles, string $path, array $styleParameters = [], array $informationParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false): Collection
    {
        return $this->getImagesInformation(
            $styles,
            $path,
            $styleParameters,
            $informationParameters,
            $disk,
            $styleSameDisk,
            $filename,
            $recreate,
            'toWebp'
        );
    }

    /**
     * Get the styled image URLs with information in PNG format.
     *
     * @see \BalisMatz\ImageStyle\ImageStyle::imagesInformation() Check params and return.
     */
    public function imagesInformationToPng(array|string $styles, string $path, array $styleParameters = [], array $informationParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false): Collection
    {
        return $this->getImagesInformation(
            $styles,
            $path,
            $styleParameters,
            $informationParameters,
            $disk,
            $styleSameDisk,
            $filename,
            $recreate,
            'toPng'
        );
    }

    /**
     * Get the styled image URLs with information in GIF format.
     *
     * @see \BalisMatz\ImageStyle\ImageStyle::imagesInformation() Check params and return.
     */
    public function imagesInformationToGif(array|string $styles, string $path, array $styleParameters = [], array $informationParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false): Collection
    {
        return $this->getImagesInformation(
            $styles,
            $path,
            $styleParameters,
            $informationParameters,
            $disk,
            $styleSameDisk,
            $filename,
            $recreate,
            'toGif'
        );
    }

    /**
     * Get the styled image URLs with information in Windows Bitmap format.
     *
     * @see \BalisMatz\ImageStyle\ImageStyle::imagesInformation() Check params and return.
     */
    public function imagesInformationToBmp(array|string $styles, string $path, array $styleParameters = [], array $informationParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false): Collection
    {
        return $this->getImagesInformation(
            $styles,
            $path,
            $styleParameters,
            $informationParameters,
            $disk,
            $styleSameDisk,
            $filename,
            $recreate,
            'toBmp'
        );
    }

    /**
     * Get the styled image URLs with information in AVIF format.
     *
     * @see \BalisMatz\ImageStyle\ImageStyle::imagesInformation() Check params and return.
     */
    public function imagesInformationToAvif(array|string $styles, string $path, array $styleParameters = [], array $informationParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false): Collection
    {
        return $this->getImagesInformation(
            $styles,
            $path,
            $styleParameters,
            $informationParameters,
            $disk,
            $styleSameDisk,
            $filename,
            $recreate,
            'toAvif'
        );
    }

    /**
     * Get the styled image URLs with information in TIFF format.
     *
     * @see \BalisMatz\ImageStyle\ImageStyle::imagesInformation() Check params and return.
     */
    public function imagesInformationToTiff(array|string $styles, string $path, array $styleParameters = [], array $informationParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false): Collection
    {
        return $this->getImagesInformation(
            $styles,
            $path,
            $styleParameters,
            $informationParameters,
            $disk,
            $styleSameDisk,
            $filename,
            $recreate,
            'toTiff'
        );
    }

    /**
     * Get the styled image URLs with information in JPEG 2000 format.
     *
     * @see \BalisMatz\ImageStyle\ImageStyle::imagesInformation() Check params and return.
     */
    public function imagesInformationToJpeg2000(array|string $styles, string $path, array $styleParameters = [], array $informationParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false): Collection
    {
        return $this->getImagesInformation(
            $styles,
            $path,
            $styleParameters,
            $informationParameters,
            $disk,
            $styleSameDisk,
            $filename,
            $recreate,
            'toJpeg2000'
        );
    }

    /**
     * Get the styled image URLs with information in HEIC format.
     *
     * @see \BalisMatz\ImageStyle\ImageStyle::imagesInformation() Check params and return.
     */
    public function imagesInformationToHeic(array|string $styles, string $path, array $styleParameters = [], array $informationParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false): Collection
    {
        return $this->getImagesInformation(
            $styles,
            $path,
            $styleParameters,
            $informationParameters,
            $disk,
            $styleSameDisk,
            $filename,
            $recreate,
            'toHeic'
        );
    }

    /**
     * Get the styled image path.
     *
     * @param  string  $style
     *                         The style ID or class.
     * @param  string  $path
     *                        The image path to filesystem.
     * @param  array  $styleParameters
     *                                  Optional: The style parameters to pass.
     * @param  string|null  $disk
     *                             Optional: The disk to load the original image. If it is null, the default disk will be used.
     * @param  bool  $styleSameDisk
     *                               Optional: By default, styled image will be saved at image styles disk. When this parameter is true, styled image will be saved at the same disk where the original image is stored.
     * @param  string|null  $filename
     *                                 Optional: Some applications might use multiple disks for files and store styled images at one disk. This can lead to conflicts when there are files with the same name at the same filesystem path. Set a different file name to resolve these conflicts. A possible fix would be to use the source disk as filename prefix or suffix (ie. image => s3-image).
     * @param  bool  $recreate
     *                          Optional: Recreate the styled image.
     * @param  bool  $relative
     *                          Optional: By default, when using the local driver, the absolute path to the image will be returned. But, if we want to get the image size, mimetype etc. from "Storage" facade, we must use the relative path. If this parameter is true, the relative path to the image will be returned for all drivers.
     * @return string|null
     *                     The styled image path or null.
     */
    public function path(string $style, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false, bool $relative = false): ?string
    {
        return $this->getPath(
            $style,
            $path,
            $styleParameters,
            $disk,
            $styleSameDisk,
            $filename,
            $recreate,
            relative: $relative
        );
    }

    /**
     * Get the styled image path in JPEG format.
     *
     * @see \BalisMatz\ImageStyle\ImageStyle::path() Check params and return.
     */
    public function pathToJpeg(string $style, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false, bool $relative = false): ?string
    {
        return $this->getPath(
            $style,
            $path,
            $styleParameters,
            $disk,
            $styleSameDisk,
            $filename,
            $recreate,
            'toJpeg',
            $relative
        );
    }

    /**
     * Get the styled image path in WebP graphic format.
     *
     * @see \BalisMatz\ImageStyle\ImageStyle::path() Check params and return.
     */
    public function pathToWebp(string $style, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false, bool $relative = false): ?string
    {
        return $this->getPath(
            $style,
            $path,
            $styleParameters,
            $disk,
            $styleSameDisk,
            $filename,
            $recreate,
            'toWebp',
            $relative
        );
    }

    /**
     * Get the styled image path in PNG format.
     *
     * @see \BalisMatz\ImageStyle\ImageStyle::path() Check params and return.
     */
    public function pathToPng(string $style, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false, bool $relative = false): ?string
    {
        return $this->getPath(
            $style,
            $path,
            $styleParameters,
            $disk,
            $styleSameDisk,
            $filename,
            $recreate,
            'toPng',
            $relative
        );
    }

    /**
     * Get the styled image path in GIF format.
     *
     * @see \BalisMatz\ImageStyle\ImageStyle::path() Check params and return.
     */
    public function pathToGif(string $style, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false, bool $relative = false): ?string
    {
        return $this->getPath(
            $style,
            $path,
            $styleParameters,
            $disk,
            $styleSameDisk,
            $filename,
            $recreate,
            'toGif',
            $relative
        );
    }

    /**
     * Get the styled image path in Windows Bitmap format.
     *
     * @see \BalisMatz\ImageStyle\ImageStyle::path() Check params and return.
     */
    public function pathToBmp(string $style, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false, bool $relative = false): ?string
    {
        return $this->getPath(
            $style,
            $path,
            $styleParameters,
            $disk,
            $styleSameDisk,
            $filename,
            $recreate,
            'toBmp',
            $relative
        );
    }

    /**
     * Get the styled image path in AVIF format.
     *
     * @see \BalisMatz\ImageStyle\ImageStyle::path() Check params and return.
     */
    public function pathToAvif(string $style, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false, bool $relative = false): ?string
    {
        return $this->getPath(
            $style,
            $path,
            $styleParameters,
            $disk,
            $styleSameDisk,
            $filename,
            $recreate,
            'toAvif',
            $relative
        );
    }

    /**
     * Get the styled image path in TIFF format.
     *
     * @see \BalisMatz\ImageStyle\ImageStyle::path() Check params and return.
     */
    public function pathToTiff(string $style, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false, bool $relative = false): ?string
    {
        return $this->getPath(
            $style,
            $path,
            $styleParameters,
            $disk,
            $styleSameDisk,
            $filename,
            $recreate,
            'toTiff',
            $relative
        );
    }

    /**
     * Get the styled image path in JPEG 2000 format.
     *
     * @see \BalisMatz\ImageStyle\ImageStyle::path() Check params and return.
     */
    public function pathToJpeg2000(string $style, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false, bool $relative = false): ?string
    {
        return $this->getPath(
            $style,
            $path,
            $styleParameters,
            $disk,
            $styleSameDisk,
            $filename,
            $recreate,
            'toJpeg2000',
            $relative
        );
    }

    /**
     * Get the styled image path in HEIC format.
     *
     * @see \BalisMatz\ImageStyle\ImageStyle::path() Check params and return.
     */
    public function pathToHeic(string $style, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false, bool $relative = false): ?string
    {
        return $this->getPath(
            $style,
            $path,
            $styleParameters,
            $disk,
            $styleSameDisk,
            $filename,
            $recreate,
            'toHeic',
            $relative
        );
    }

    /**
     * Get the styled image paths.
     *
     * @param  array|string  $styles
     *                                The style IDs or classes.
     * @param  string  $path
     *                        The image path to filesystem.
     * @param  array  $styleParameters
     *                                  Optional: The style parameters to pass.
     * @param  string|null  $disk
     *                             Optional: The disk to load the original image. If it is null, the default disk will be used.
     * @param  bool  $styleSameDisk
     *                               Optional: By default, styled images will be saved at image styles disk. When this parameter is true, styled images will be saved at the same disk where the original image is stored.
     * @param  string|null  $filename
     *                                 Optional: Some applications might use multiple disks for files and store styled images at one disk. This can lead to conflicts when there are files with the same name at the same filesystem path. Set a different file name to resolve these conflicts. A possible fix would be to use the source disk as filename prefix or suffix (ie. image => s3-image).
     * @param  bool  $recreate
     *                          Optional: Recreate the styled images.
     * @param  bool  $relative
     *                          Optional: By default, when using the local driver, the absolute paths to the images will be returned. But, if we want to get the images size, mimetype etc. from "Storage" facade, we must use the relative path. If this parameter is true, the relative path to images will be returned for all drivers.
     * @return \Illuminate\Support\Collection<string, string|null>
     *                                                             The styled image paths or null, keyed by the given values ($styles).
     */
    public function paths(array|string $styles, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false, bool $relative = false): Collection
    {
        return $this->getPaths(
            $styles,
            $path,
            $styleParameters,
            $disk,
            $styleSameDisk,
            $filename,
            $recreate,
            relative: $relative
        );
    }

    /**
     * Get the styled image paths in JPEG format.
     *
     * @see \BalisMatz\ImageStyle\ImageStyle::paths() Check params and return.
     */
    public function pathsToJpeg(array|string $styles, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false, bool $relative = false): Collection
    {
        return $this->getPaths(
            $styles,
            $path,
            $styleParameters,
            $disk,
            $styleSameDisk,
            $filename,
            $recreate,
            'toJpeg',
            $relative
        );
    }

    /**
     * Get the styled image paths in WebP graphic format.
     *
     * @see \BalisMatz\ImageStyle\ImageStyle::paths() Check params and return.
     */
    public function pathsToWebp(array|string $styles, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false, bool $relative = false): Collection
    {
        return $this->getPaths(
            $styles,
            $path,
            $styleParameters,
            $disk,
            $styleSameDisk,
            $filename,
            $recreate,
            'toWebp',
            $relative
        );
    }

    /**
     * Get the styled image paths in PNG format.
     *
     * @see \BalisMatz\ImageStyle\ImageStyle::paths() Check params and return.
     */
    public function pathsToPng(array|string $styles, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false, bool $relative = false): Collection
    {
        return $this->getPaths(
            $styles,
            $path,
            $styleParameters,
            $disk,
            $styleSameDisk,
            $filename,
            $recreate,
            'toPng',
            $relative
        );
    }

    /**
     * Get the styled image paths in GIF format.
     *
     * @see \BalisMatz\ImageStyle\ImageStyle::paths() Check params and return.
     */
    public function pathsToGif(array|string $styles, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false, bool $relative = false): Collection
    {
        return $this->getPaths(
            $styles,
            $path,
            $styleParameters,
            $disk,
            $styleSameDisk,
            $filename,
            $recreate,
            'toGif',
            $relative
        );
    }

    /**
     * Get the styled image paths in Windows Bitmap format.
     *
     * @see \BalisMatz\ImageStyle\ImageStyle::paths() Check params and return.
     */
    public function pathsToBmp(array|string $styles, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false, bool $relative = false): Collection
    {
        return $this->getPaths(
            $styles,
            $path,
            $styleParameters,
            $disk,
            $styleSameDisk,
            $filename,
            $recreate,
            'toBmp',
            $relative
        );
    }

    /**
     * Get the styled image paths in AVIF format.
     *
     * @see \BalisMatz\ImageStyle\ImageStyle::paths() Check params and return.
     */
    public function pathsToAvif(array|string $styles, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false, bool $relative = false): Collection
    {
        return $this->getPaths(
            $styles,
            $path,
            $styleParameters,
            $disk,
            $styleSameDisk,
            $filename,
            $recreate,
            'toAvif',
            $relative
        );
    }

    /**
     * Get the styled image paths in TIFF format.
     *
     * @see \BalisMatz\ImageStyle\ImageStyle::paths() Check params and return.
     */
    public function pathsToTiff(array|string $styles, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false, bool $relative = false): Collection
    {
        return $this->getPaths(
            $styles,
            $path,
            $styleParameters,
            $disk,
            $styleSameDisk,
            $filename,
            $recreate,
            'toTiff',
            $relative
        );
    }

    /**
     * Get the styled image paths in JPEG 2000 format.
     *
     * @see \BalisMatz\ImageStyle\ImageStyle::paths() Check params and return.
     */
    public function pathsToJpeg2000(array|string $styles, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false, bool $relative = false): Collection
    {
        return $this->getPaths(
            $styles,
            $path,
            $styleParameters,
            $disk,
            $styleSameDisk,
            $filename,
            $recreate,
            'toJpeg2000',
            $relative
        );
    }

    /**
     * Get the styled image paths in HEIC format.
     *
     * @see \BalisMatz\ImageStyle\ImageStyle::paths() Check params and return.
     */
    public function pathsToHeic(array|string $styles, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false, bool $relative = false): Collection
    {
        return $this->getPaths(
            $styles,
            $path,
            $styleParameters,
            $disk,
            $styleSameDisk,
            $filename,
            $recreate,
            'toHeic',
            $relative
        );
    }

    /**
     * Get the styled image URL.
     *
     * @param  string  $style
     *                         The style ID or class.
     * @param  string  $path
     *                        The image path to filesystem.
     * @param  array  $styleParameters
     *                                  Optional: The style parameters to pass.
     * @param  string|null  $disk
     *                             Optional: The disk to load the original image. If it is null, the default disk will be used.
     * @param  bool  $styleSameDisk
     *                               Optional: By default, styled image will be saved at image styles disk. When this parameter is true, styled image will be saved at the same disk where the original image is stored.
     * @param  string|null  $filename
     *                                 Optional: Some applications might use multiple disks for files and store styled images at one disk. This can lead to conflicts when there are files with the same name at the same filesystem path. Set a different file name to resolve these conflicts. A possible fix would be to use the source disk as filename prefix or suffix (ie. image => s3-image).
     * @param  bool  $recreate
     *                          Optional: Recreate the styled image.
     * @return string|null
     *                     The styled image URL or null.
     */
    public function url(string $style, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false): ?string
    {
        return $this->getUrl(
            $style,
            $path,
            $styleParameters,
            $disk,
            $styleSameDisk,
            $filename,
            $recreate
        );
    }

    /**
     * Get the styled image URL in JPEG format.
     *
     * @see \BalisMatz\ImageStyle\ImageStyle::url() Check params and return.
     */
    public function urlToJpeg(string $style, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false): ?string
    {
        return $this->getUrl(
            $style,
            $path,
            $styleParameters,
            $disk,
            $styleSameDisk,
            $filename,
            $recreate,
            'toJpeg'
        );
    }

    /**
     * Get the styled image URL in WebP graphic format.
     *
     * @see \BalisMatz\ImageStyle\ImageStyle::url() Check params and return.
     */
    public function urlToWebp(string $style, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false): ?string
    {
        return $this->getUrl(
            $style,
            $path,
            $styleParameters,
            $disk,
            $styleSameDisk,
            $filename,
            $recreate,
            'toWebp'
        );
    }

    /**
     * Get the styled image URL in PNG format.
     *
     * @see \BalisMatz\ImageStyle\ImageStyle::url() Check params and return.
     */
    public function urlToPng(string $style, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false): ?string
    {
        return $this->getUrl(
            $style,
            $path,
            $styleParameters,
            $disk,
            $styleSameDisk,
            $filename,
            $recreate,
            'toPng'
        );
    }

    /**
     * Get the styled image URL in GIF format.
     *
     * @see \BalisMatz\ImageStyle\ImageStyle::url() Check params and return.
     */
    public function urlToGif(string $style, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false): ?string
    {
        return $this->getUrl(
            $style,
            $path,
            $styleParameters,
            $disk,
            $styleSameDisk,
            $filename,
            $recreate,
            'toGif'
        );
    }

    /**
     * Get the styled image URL in Windows Bitmap format.
     *
     * @see \BalisMatz\ImageStyle\ImageStyle::url() Check params and return.
     */
    public function urlToBmp(string $style, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false): ?string
    {
        return $this->getUrl(
            $style,
            $path,
            $styleParameters,
            $disk,
            $styleSameDisk,
            $filename,
            $recreate,
            'toBmp'
        );
    }

    /**
     * Get the styled image URL in AVIF format.
     *
     * @see \BalisMatz\ImageStyle\ImageStyle::url() Check params and return.
     */
    public function urlToAvif(string $style, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false): ?string
    {
        return $this->getUrl(
            $style,
            $path,
            $styleParameters,
            $disk,
            $styleSameDisk,
            $filename,
            $recreate,
            'toAvif'
        );
    }

    /**
     * Get the styled image URL in TIFF format.
     *
     * @see \BalisMatz\ImageStyle\ImageStyle::url() Check params and return.
     */
    public function urlToTiff(string $style, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false): ?string
    {
        return $this->getUrl(
            $style,
            $path,
            $styleParameters,
            $disk,
            $styleSameDisk,
            $filename,
            $recreate,
            'toTiff'
        );
    }

    /**
     * Get the styled image URL in JPEG 2000 format.
     *
     * @see \BalisMatz\ImageStyle\ImageStyle::url() Check params and return.
     */
    public function urlToJpeg2000(string $style, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false): ?string
    {
        return $this->getUrl(
            $style,
            $path,
            $styleParameters,
            $disk,
            $styleSameDisk,
            $filename,
            $recreate,
            'toJpeg2000'
        );
    }

    /**
     * Get the styled image URL in HEIC format.
     *
     * @see \BalisMatz\ImageStyle\ImageStyle::url() Check params and return.
     */
    public function urlToHeic(string $style, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false): ?string
    {
        return $this->getUrl(
            $style,
            $path,
            $styleParameters,
            $disk,
            $styleSameDisk,
            $filename,
            $recreate,
            'toHeic'
        );
    }

    /**
     * Get the styled image URLs.
     *
     * @param  array|string  $styles
     *                                The style IDs or classes.
     * @param  string  $path
     *                        The image path to filesystem.
     * @param  array  $styleParameters
     *                                  Optional: The style parameters to pass.
     * @param  string|null  $disk
     *                             Optional: The disk to load the original image. If it is null, the default disk will be used.
     * @param  bool  $styleSameDisk
     *                               Optional: By default, styled images will be saved at image styles disk. When this parameter is true, styled images will be saved at the same disk where the original image is stored.
     * @param  string|null  $filename
     *                                 Optional: Some applications might use multiple disks for files and store styled images at one disk. This can lead to conflicts when there are files with the same name at the same filesystem path. Set a different file name to resolve these conflicts. A possible fix would be to use the source disk as filename prefix or suffix (ie. image => s3-image).
     * @param  bool  $recreate
     *                          Optional: Recreate the styled images.
     * @return \Illuminate\Support\Collection<string, string|null>
     *                                                             The styled image URLs or null, keyed by the given values ($styles).
     */
    public function urls(array|string $styles, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false): Collection
    {
        return $this->getUrls(
            $styles,
            $path,
            $styleParameters,
            $disk,
            $styleSameDisk,
            $filename,
            $recreate
        );
    }

    /**
     * Get the styled image URLs in JPEG format.
     *
     * @see \BalisMatz\ImageStyle\ImageStyle::urls() Check params and return.
     */
    public function urlsToJpeg(array|string $styles, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false): Collection
    {
        return $this->getUrls(
            $styles,
            $path,
            $styleParameters,
            $disk,
            $styleSameDisk,
            $filename,
            $recreate,
            'toJpeg'
        );
    }

    /**
     * Get the styled image URLs in WebP graphic format.
     *
     * @see \BalisMatz\ImageStyle\ImageStyle::urls() Check params and return.
     */
    public function urlsToWebp(array|string $styles, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false): Collection
    {
        return $this->getUrls(
            $styles,
            $path,
            $styleParameters,
            $disk,
            $styleSameDisk,
            $filename,
            $recreate,
            'toWebp'
        );
    }

    /**
     * Get the styled image URLs in PNG format.
     *
     * @see \BalisMatz\ImageStyle\ImageStyle::urls() Check params and return.
     */
    public function urlsToPng(array|string $styles, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false): Collection
    {
        return $this->getUrls(
            $styles,
            $path,
            $styleParameters,
            $disk,
            $styleSameDisk,
            $filename,
            $recreate,
            'toPng'
        );
    }

    /**
     * Get the styled image URLs in GIF format.
     *
     * @see \BalisMatz\ImageStyle\ImageStyle::urls() Check params and return.
     */
    public function urlsToGif(array|string $styles, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false): Collection
    {
        return $this->getUrls(
            $styles,
            $path,
            $styleParameters,
            $disk,
            $styleSameDisk,
            $filename,
            $recreate,
            'toGif'
        );
    }

    /**
     * Get the styled image URLs in Windows Bitmap format.
     *
     * @see \BalisMatz\ImageStyle\ImageStyle::urls() Check params and return.
     */
    public function urlsToBmp(array|string $styles, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false): Collection
    {
        return $this->getUrls(
            $styles,
            $path,
            $styleParameters,
            $disk,
            $styleSameDisk,
            $filename,
            $recreate,
            'toBmp'
        );
    }

    /**
     * Get the styled image URLs in AVIF format.
     *
     * @see \BalisMatz\ImageStyle\ImageStyle::urls() Check params and return.
     */
    public function urlsToAvif(array|string $styles, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false): Collection
    {
        return $this->getUrls(
            $styles,
            $path,
            $styleParameters,
            $disk,
            $styleSameDisk,
            $filename,
            $recreate,
            'toAvif'
        );
    }

    /**
     * Get the styled image URLs in TIFF format.
     *
     * @see \BalisMatz\ImageStyle\ImageStyle::urls() Check params and return.
     */
    public function urlsToTiff(array|string $styles, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false): Collection
    {
        return $this->getUrls(
            $styles,
            $path,
            $styleParameters,
            $disk,
            $styleSameDisk,
            $filename,
            $recreate,
            'toTiff'
        );
    }

    /**
     * Get the styled image URLs in JPEG 2000 format.
     *
     * @see \BalisMatz\ImageStyle\ImageStyle::urls() Check params and return.
     */
    public function urlsToJpeg2000(array|string $styles, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false): Collection
    {
        return $this->getUrls(
            $styles,
            $path,
            $styleParameters,
            $disk,
            $styleSameDisk,
            $filename,
            $recreate,
            'toJpeg2000'
        );
    }

    /**
     * Get the styled image URLs in HEIC format.
     *
     * @see \BalisMatz\ImageStyle\ImageStyle::urls() Check params and return.
     */
    public function urlsToHeic(array|string $styles, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false): Collection
    {
        return $this->getUrls(
            $styles,
            $path,
            $styleParameters,
            $disk,
            $styleSameDisk,
            $filename,
            $recreate,
            'toHeic'
        );
    }

    /**
     * Preview the image with style modifications.
     *
     * @param  string  $style
     *                         The style ID or class.
     * @param  string|null  $path
     *                             Optional: The image path. If it is null, a default image will be used.
     * @param  array  $styleParameters
     *                                  Optional: The style parameters to pass.
     * @param  string|null  $disk
     *                             Optional: The disk to load the image. If it is null, the default disk will be used.
     * @return \Illuminate\Http\Response
     *                                   The image preview response.
     *
     * @throws \InvalidArgumentException
     *                                   If image style does not exist.
     */
    public function preview(string $style, ?string $path = null, array $styleParameters = [], ?string $disk = null): Response
    {
        $imagePath = match (true) {
            $path && $this->getFile($path, $disk) => $this->getFilesystemAdapter($disk)->path($path),
            default => __DIR__.'/../preview.jpg'
        };

        if (! $styleInformation = $this->imageStyleManager->get($style)) {
            throw new InvalidArgumentException('The given image style does not exist.');
        }

        $modifiedImage = $this->getModifiedImage(
            $styleInformation->class,
            $imagePath,
            $styleParameters
        );

        $encodedImage = match (true) {
            $modifiedImage instanceof EncodedImageInterface => $modifiedImage,
            default => $modifiedImage->encode()
        };

        $pathInfo = collect(pathinfo($imagePath));

        $pathInfo->put(
            'extension',
            MimeTypes::getDefault()->getExtensions($encodedImage->mimetype())[0] ?? $pathInfo->get('extension')
        );

        $filename = "{$pathInfo->get('filename')}.{$pathInfo->get('extension')}";

        return response(
            content: $encodedImage,
            headers: [
                'Content-Disposition' => 'inline; filename="'.$filename.'"',
                'Content-Length' => $encodedImage->size(),
                'Content-Type' => $encodedImage->mimetype(),
            ]
        );
    }

    /**
     * Extract styles (ID or class) as collection keys.
     */
    protected function extractStyles(array|string $styles): Collection
    {
        if (is_string($styles)) {
            $styles = array_map('trim', explode(',', $styles));
        }

        return collect($styles)
            ->filter(fn (mixed $value): bool => $value && is_string($value))
            ->flip()
            ->map(fn (): null => null);
    }

    /**
     * Find or create the styled image(s).
     *
     * @param  array|string  $styles
     *                                The style IDs or classes.
     * @param  string  $path
     *                        The image path to filesystem.
     * @param  array  $styleParameters
     *                                  Optional: The style parameters to pass.
     * @param  string|null  $disk
     *                             Optional: The disk to load the original image. If it is null, the default disk will be used.
     * @param  bool  $styleSameDisk
     *                               Optional: By default, styled image(s) will be saved at image styles disk. When this parameter is true, styled image(s) will be saved at the same disk where the original image is stored.
     * @param  string|null  $filename
     *                                 Optional: Some applications might use multiple disks for files and store styled images at one disk. This can lead to conflicts when there are files with the same name at the same filesystem path. Set a different file name to resolve these conflicts. A possible fix would be to use the source disk as filename prefix or suffix (ie. image => s3-image).
     * @param  bool  $recreate
     *                          Optional: Recreate the styled image.
     * @param  string|null  $format
     *                               Optional: The format that image should be saved.
     * @return \Illuminate\Support\Collection<string, string|null>
     *                                                             The image paths to filesystem or null, keyed by the given values ($styles).
     */
    protected function findOrCreate(array|string $styles, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false, ?string $format = null): Collection
    {
        $filesystem = $this->getFilesystemAdapter($disk);

        $stylesFilesystem = match ($styleSameDisk) {
            true => $filesystem,
            default => $this->getFilesystemAdapter(config('image-style.filesystem'))
        };

        $imagePath = $filesystem->path($path);

        return $this->getFilesystemPaths($styles, $path, $styleParameters, $disk, $filename, $format)
            ->map(function (?string $stylePath, string $style) use ($filesystem, $stylesFilesystem, $path, $styleParameters, $recreate, $format, $imagePath): ?string {
                if (! $stylePath || (! $recreate && $stylesFilesystem->exists($stylePath))) {
                    return $stylePath;
                }

                $modifiedImage = $this->getModifiedImage(
                    $this->imageStyleManager->get($style)->class,
                    $imagePath,
                    $styleParameters,
                    $format
                );

                $encodedImage = match (true) {
                    $modifiedImage instanceof EncodedImageInterface => $modifiedImage,
                    default => $modifiedImage->encode()
                };

                if (! $stylesFilesystem->put($stylePath, $encodedImage, $filesystem->visibility($path))) {
                    return null;
                }

                return $stylePath;
            });
    }

    /**
     * Get the fallback URL.
     */
    protected function getFallbackUrl(string $path, ?string $disk = null): ?string
    {
        return match (config('image-style.fallback_url')) {
            'storage_url' => $this->getFilesystemAdapter($disk)->url($path),
            default => null
        };
    }

    /**
     * Get the image file.
     */
    protected function getFile(string $path, ?string $disk = null): ?File
    {
        try {
            $file = new File(
                $this->getFilesystemAdapter($disk)->path($path)
            );

            $fileValidator = Validator::make(
                ['file' => $file],
                ['file' => 'image']
            );

            if ($fileValidator->fails()) {
                throw new InvalidArgumentException;
            }

            return $file;
        } catch (FileNotFoundException|InvalidArgumentException) {
            return null;
        }
    }

    /**
     * Get the filesystem adapter.
     */
    protected function getFilesystemAdapter(?string $disk = null): FilesystemAdapter
    {
        $disk ??= config('filesystems.default');

        if (array_key_exists($disk, $this->loadedFilesystemAdapters)) {
            return $this->loadedFilesystemAdapters[$disk];
        }

        $this->loadedFilesystemAdapters[$disk] = $this->filesystemManager->disk($disk);

        return $this->loadedFilesystemAdapters[$disk];
    }

    /**
     * Get the styled image path to filesystem.
     */
    protected function getFilesystemPath(string $style, string $path, array $styleParameters = [], ?string $disk = null, ?string $filename = null, ?string $format = null): ?string
    {
        return $this->getFilesystemPaths(
            $this->extractStyles($style)->keys()->first(),
            $path,
            $styleParameters,
            $disk,
            $filename,
            $format
        )->first();
    }

    /**
     * Get the styled image paths to filesystem.
     */
    protected function getFilesystemPaths(array|string $styles, string $path, array $styleParameters = [], ?string $disk = null, ?string $filename = null, ?string $format = null): Collection
    {
        $styles = $this->extractStyles($styles);

        if (! $file = $this->getFile($path, $disk)) {
            return $styles;
        }

        $image = $this->imageManager->create(1, 1);

        $pathInfo = collect(pathinfo($path));

        return $styles->map(function (null $stylePath, string $style) use ($pathInfo, $styleParameters, $file, $filename, $format, $image): ?string {
            if (! $styleInformation = $this->imageStyleManager->get($style)) {
                return null;
            }

            return $pathInfo
                ->only(['dirname', 'filename'])
                ->when($filename, fn (Collection $collection) => $collection->put('filename', $filename))
                ->filter(fn (string $value): bool => $value !== '.')
                ->map(function (string $value, string $key) use ($pathInfo, $styleInformation, $file, $styleParameters, $format, $image) {
                    if ($key !== 'filename') {
                        return $value;
                    }

                    // Use the dummy image to check if image style changes the
                    // file type (extension).
                    $modifiedImage = $this->getModifiedImage(
                        $styleInformation->class,
                        $image,
                        $styleParameters,
                        $format
                    );

                    $extension = match (true) {
                        $modifiedImage instanceof ImageInterface => $file->guessExtension(),
                        $modifiedImage instanceof EncodedImageInterface => MimeTypes::getDefault()->getExtensions($modifiedImage->mimetype())[0] ?? null,
                        default => null
                    } ?: $pathInfo->get('extension');

                    return "{$value}.{$extension}";
                })
                ->prepend($styleInformation->id)
                ->prepend('styles')
                ->join(DIRECTORY_SEPARATOR);
        });
    }

    /**
     * Get the styled image URL with information in custom format (if needed).
     */
    protected function getImageInformation(string $style, string $path, array $styleParameters = [], mixed $informationParameters = null, ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false, ?string $format = null): ?ImageStyleImageInformation
    {
        return $this->getImagesInformation(
            $this->extractStyles($style)->keys()->first(),
            $path,
            $styleParameters,
            [$informationParameters],
            $disk,
            $styleSameDisk,
            $filename,
            $recreate,
            $format
        )->first();
    }

    /**
     * Get the styled image URLs with information in custom format (if needed).
     */
    protected function getImagesInformation(array|string $styles, string $path, array $styleParameters = [], array $informationParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false, ?string $format = null): Collection
    {
        $stylesFilesystem = match ($styleSameDisk) {
            true => $this->getFilesystemAdapter($disk),
            default => $this->getFilesystemAdapter(config('image-style.filesystem'))
        };

        $stylesPathsIndex = -1;

        return $this->getPaths(
            $styles,
            $path,
            $styleParameters,
            $disk,
            $styleSameDisk,
            $filename,
            $recreate,
            $format,
            true
        )->map(function (?string $stylePath, string $style) use (&$stylesPathsIndex, $informationParameters, $path, $disk, $stylesFilesystem): ?ImageStyleImageInformation {
            $stylesPathsIndex++;

            $styleInformationParameters = $informationParameters[$style] ?? $informationParameters[$stylesPathsIndex] ?? null;

            if (! $stylePath) {
                if ($styleFallbackUrl = $this->getFallbackUrl($path, $disk)) {
                    if ($file = $this->getFile($path, $disk)) {
                        $image = $this->imageManager->read($file);

                        return new ImageStyleImageInformation(
                            $styleFallbackUrl,
                            $image->height(),
                            $image->width(),
                            $image->encode()->mimetype(),
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

            $image = $this->imageManager->read(
                $stylesFilesystem->path($stylePath)
            );

            return new ImageStyleImageInformation(
                $stylesFilesystem->url($stylePath),
                $image->height(),
                $image->width(),
                $image->encode()->mimetype(),
                $styleInformationParameters
            );
        });
    }

    /**
     * Get the image with style modifications.
     */
    protected function getModifiedImage(string $styleClass, string|ImageInterface $input, array $styleParameters = [], ?string $format = null): ImageInterface|EncodedImageInterface
    {
        /** @var \BalisMatz\ImageStyle\ImageStyleBase $style */
        $style = new $styleClass;

        $image = $this->imageManager->read($input);

        $modifiedImage = $style->modifications($image, $styleParameters);

        if (is_string($input)) {
            $modifiedImageQuality = $style->quality($styleParameters);

            $modifiedImage = $this->imageManager->read($modifiedImage)->encodeByMediaType(
                quality: $modifiedImageQuality >= 0 && $modifiedImageQuality <= 100 ?
                            $modifiedImageQuality : config('image-style.options.quality')
            );
        }

        if ($format) {
            $modifiedImage = match (true) {
                $modifiedImage instanceof EncodedImageInterface => $this->imageManager->read($modifiedImage),
                default => $modifiedImage
            };

            if (! method_exists($modifiedImage, $format)) {
                return $modifiedImage;
            }

            $modifiedImage = match ($format) {
                'toJpeg', 'toWebp', 'toAvif', 'toTiff', 'toJpeg2000', 'toHeic' => $modifiedImage->{$format}(
                    quality: 100
                ),
                default => $modifiedImage->{$format}()
            };
        }

        return $modifiedImage;
    }

    /**
     * Get the styled image path with custom format (if needed).
     */
    protected function getPath(string $style, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false, ?string $format = null, bool $relative = false): ?string
    {
        return $this->getPaths(
            $this->extractStyles($style)->keys()->first(),
            $path,
            $styleParameters,
            $disk,
            $styleSameDisk,
            $filename,
            $recreate,
            $format,
            $relative
        )->first();
    }

    /**
     * Get the styled image paths with custom format (if needed).
     */
    protected function getPaths(array|string $styles, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false, ?string $format = null, bool $relative = false): Collection
    {
        $stylesPaths = $this->findOrCreate(
            $styles,
            $path,
            $styleParameters,
            $disk,
            $styleSameDisk,
            $filename,
            $recreate,
            $format
        );

        if ($relative) {
            return $stylesPaths;
        }

        $stylesFilesystem = match ($styleSameDisk) {
            true => $this->getFilesystemAdapter($disk),
            default => $this->getFilesystemAdapter(config('image-style.filesystem'))
        };

        return $stylesPaths->map(function (?string $stylePath) use ($stylesFilesystem): ?string {
            if (! $stylePath) {
                return null;
            }

            return $stylesFilesystem->path($stylePath);
        });
    }

    /**
     * Get the styled image URL with custom format (if needed).
     */
    protected function getUrl(string $style, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false, ?string $format = null): ?string
    {
        return $this->getUrls(
            $this->extractStyles($style)->keys()->first(),
            $path,
            $styleParameters,
            $disk,
            $styleSameDisk,
            $filename,
            $recreate,
            $format
        )->first();
    }

    /**
     * Get the styled image URLs with custom format (if needed).
     */
    protected function getUrls(array|string $styles, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false, ?string $format = null): Collection
    {
        $stylesFilesystem = match ($styleSameDisk) {
            true => $this->getFilesystemAdapter($disk),
            default => $this->getFilesystemAdapter(config('image-style.filesystem'))
        };

        return $this->getPaths($styles, $path, $styleParameters, $disk, $styleSameDisk, $filename, $recreate, $format, true)
            ->map(function (?string $stylePath) use ($path, $disk, $stylesFilesystem): ?string {
                if (! $stylePath) {
                    return $this->getFallbackUrl($path, $disk);
                }

                return $stylesFilesystem->url($stylePath);
            });
    }
}
