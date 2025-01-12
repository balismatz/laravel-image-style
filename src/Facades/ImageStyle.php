<?php

namespace BalisMatz\ImageStyle\Facades;

use BalisMatz\ImageStyle\ImageStyle as LaravelImageStyle;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \BalisMatz\ImageStyle\Information\ImageStyleImageInformation|null imageInformation(string $style, string $path, array $styleParameters = [], mixed $informationParameters = null, ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false)
 * @method static \BalisMatz\ImageStyle\Information\ImageStyleImageInformation|null imageInformationToJpeg(string $style, string $path, array $styleParameters = [], mixed $informationParameters = null, ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false)
 * @method static \BalisMatz\ImageStyle\Information\ImageStyleImageInformation|null imageInformationToWebp(string $style, string $path, array $styleParameters = [], mixed $informationParameters = null, ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false)
 * @method static \BalisMatz\ImageStyle\Information\ImageStyleImageInformation|null imageInformationToPng(string $style, string $path, array $styleParameters = [], mixed $informationParameters = null, ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false)
 * @method static \BalisMatz\ImageStyle\Information\ImageStyleImageInformation|null imageInformationToGif(string $style, string $path, array $styleParameters = [], mixed $informationParameters = null, ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false)
 * @method static \BalisMatz\ImageStyle\Information\ImageStyleImageInformation|null imageInformationToBmp(string $style, string $path, array $styleParameters = [], mixed $informationParameters = null, ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false)
 * @method static \BalisMatz\ImageStyle\Information\ImageStyleImageInformation|null imageInformationToAvif(string $style, string $path, array $styleParameters = [], mixed $informationParameters = null, ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false)
 * @method static \BalisMatz\ImageStyle\Information\ImageStyleImageInformation|null imageInformationToTiff(string $style, string $path, array $styleParameters = [], mixed $informationParameters = null, ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false)
 * @method static \BalisMatz\ImageStyle\Information\ImageStyleImageInformation|null imageInformationToJpeg2000(string $style, string $path, array $styleParameters = [], mixed $informationParameters = null, ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false)
 * @method static \BalisMatz\ImageStyle\Information\ImageStyleImageInformation|null imageInformationToHeic(string $style, string $path, array $styleParameters = [], mixed $informationParameters = null, ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false)
 * @method static \Illuminate\Support\Collection<string, \BalisMatz\ImageStyle\Information\ImageStyleImageInformation|null> imagesInformation(array|string $styles, string $path, array $styleParameters = [], array $informationParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false)
 * @method static \Illuminate\Support\Collection<string, \BalisMatz\ImageStyle\Information\ImageStyleImageInformation|null> imagesInformationToJpeg(array|string $styles, string $path, array $styleParameters = [], array $informationParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false)
 * @method static \Illuminate\Support\Collection<string, \BalisMatz\ImageStyle\Information\ImageStyleImageInformation|null> imagesInformationToWebp(array|string $styles, string $path, array $styleParameters = [], array $informationParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false)
 * @method static \Illuminate\Support\Collection<string, \BalisMatz\ImageStyle\Information\ImageStyleImageInformation|null> imagesInformationToPng(array|string $styles, string $path, array $styleParameters = [], array $informationParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false)
 * @method static \Illuminate\Support\Collection<string, \BalisMatz\ImageStyle\Information\ImageStyleImageInformation|null> imagesInformationToGif(array|string $styles, string $path, array $styleParameters = [], array $informationParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false)
 * @method static \Illuminate\Support\Collection<string, \BalisMatz\ImageStyle\Information\ImageStyleImageInformation|null> imagesInformationToBmp(array|string $styles, string $path, array $styleParameters = [], array $informationParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false)
 * @method static \Illuminate\Support\Collection<string, \BalisMatz\ImageStyle\Information\ImageStyleImageInformation|null> imagesInformationToAvif(array|string $styles, string $path, array $styleParameters = [], array $informationParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false)
 * @method static \Illuminate\Support\Collection<string, \BalisMatz\ImageStyle\Information\ImageStyleImageInformation|null> imagesInformationToTiff(array|string $styles, string $path, array $styleParameters = [], array $informationParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false)
 * @method static \Illuminate\Support\Collection<string, \BalisMatz\ImageStyle\Information\ImageStyleImageInformation|null> imagesInformationToJpeg2000(array|string $styles, string $path, array $styleParameters = [], array $informationParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false)
 * @method static \Illuminate\Support\Collection<string, \BalisMatz\ImageStyle\Information\ImageStyleImageInformation|null> imagesInformationToHeic(array|string $styles, string $path, array $styleParameters = [], array $informationParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false)
 * @method static ?string path(string $style, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false, bool $relative = false)
 * @method static ?string pathToJpeg(string $style, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false, bool $relative = false)
 * @method static ?string pathToWebp(string $style, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false, bool $relative = false)
 * @method static ?string pathToPng(string $style, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false, bool $relative = false)
 * @method static ?string pathToGif(string $style, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false, bool $relative = false)
 * @method static ?string pathToBmp(string $style, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false, bool $relative = false)
 * @method static ?string pathToAvif(string $style, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false, bool $relative = false)
 * @method static ?string pathToTiff(string $style, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false, bool $relative = false)
 * @method static ?string pathToJpeg2000(string $style, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false, bool $relative = false)
 * @method static ?string pathToHeic(string $style, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false, bool $relative = false)
 * @method static \Illuminate\Support\Collection<string, string|null> paths(array|string $styles, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false, bool $relative = false)
 * @method static \Illuminate\Support\Collection<string, string|null> pathsToJpeg(array|string $styles, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false, bool $relative = false)
 * @method static \Illuminate\Support\Collection<string, string|null> pathsToWebp(array|string $styles, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false, bool $relative = false)
 * @method static \Illuminate\Support\Collection<string, string|null> pathsToPng(array|string $styles, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false, bool $relative = false)
 * @method static \Illuminate\Support\Collection<string, string|null> pathsToGif(array|string $styles, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false, bool $relative = false)
 * @method static \Illuminate\Support\Collection<string, string|null> pathsToBmp(array|string $styles, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false, bool $relative = false)
 * @method static \Illuminate\Support\Collection<string, string|null> pathsToAvif(array|string $styles, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false, bool $relative = false)
 * @method static \Illuminate\Support\Collection<string, string|null> pathsToTiff(array|string $styles, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false, bool $relative = false)
 * @method static \Illuminate\Support\Collection<string, string|null> pathsToJpeg2000(array|string $styles, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false, bool $relative = false)
 * @method static \Illuminate\Support\Collection<string, string|null> pathsToHeic(array|string $styles, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false, bool $relative = false)
 * @method static ?string url(string $style, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false)
 * @method static ?string urlToJpeg(string $style, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false)
 * @method static ?string urlToWebp(string $style, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false)
 * @method static ?string urlToPng(string $style, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false)
 * @method static ?string urlToGif(string $style, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false)
 * @method static ?string urlToBmp(string $style, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false)
 * @method static ?string urlToAvif(string $style, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false)
 * @method static ?string urlToTiff(string $style, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false)
 * @method static ?string urlToJpeg2000(string $style, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false)
 * @method static ?string urlToHeic(string $style, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false)
 * @method static \Illuminate\Support\Collection<string, string|null> urls(array|string $styles, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false)
 * @method static \Illuminate\Support\Collection<string, string|null> urlsToJpeg(array|string $styles, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false)
 * @method static \Illuminate\Support\Collection<string, string|null> urlsToWebp(array|string $styles, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false)
 * @method static \Illuminate\Support\Collection<string, string|null> urlsToPng(array|string $styles, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false)
 * @method static \Illuminate\Support\Collection<string, string|null> urlsToGif(array|string $styles, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false)
 * @method static \Illuminate\Support\Collection<string, string|null> urlsToBmp(array|string $styles, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false)
 * @method static \Illuminate\Support\Collection<string, string|null> urlsToAvif(array|string $styles, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false)
 * @method static \Illuminate\Support\Collection<string, string|null> urlsToTiff(array|string $styles, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false)
 * @method static \Illuminate\Support\Collection<string, string|null> urlsToJpeg2000(array|string $styles, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false)
 * @method static \Illuminate\Support\Collection<string, string|null> urlsToHeic(array|string $styles, string $path, array $styleParameters = [], ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, bool $recreate = false)
 * @method static \Illuminate\Http\Response preview(string $style, ?string $path = null, array $styleParameters = [], ?string $disk = null)
 * @method static void macro(string $name, object|callable $macro)
 * @method static void mixin(object $mixin, bool $replace = true)
 * @method static bool hasMacro(string $name)
 * @method static void flushMacros()
 * @method static mixed macroCall(string $method, array $parameters)
 *
 * @see \BalisMatz\ImageStyle\ImageStyle
 */
class ImageStyle extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return LaravelImageStyle::class;
    }
}
