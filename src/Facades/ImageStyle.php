<?php

namespace BalisMatz\ImageStyle\Facades;

use BalisMatz\ImageStyle\ImageStyle as LaravelImageStyle;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \BalisMatz\ImageStyle\Information\ImageStyleImageInformation|null imageInformation(string $style, string $path, mixed $styleParameters = null, mixed $informationParameters = null, bool $dominantColor = false, ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, ?string $format = null, bool $recreate = false)
 * @method static \Illuminate\Support\Collection<string, \BalisMatz\ImageStyle\Information\ImageStyleImageInformation|null> imagesInformation(array<string>|string $styles, string $path, mixed $styleParameters = null, array<mixed> $informationParameters = [], bool $dominantColor = false, ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, ?string $format = null, bool $recreate = false)
 * @method static ?string path(string $style, string $path, mixed $styleParameters = null, ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, ?string $format = null, bool $recreate = false, bool $relative = false)
 * @method static \Illuminate\Support\Collection<string, string|null> paths(array<string>|string $styles, string $path, mixed $styleParameters = null, ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, ?string $format = null, bool $recreate = false, bool $relative = false)
 * @method static ?string url(string $style, string $path, mixed $styleParameters = null, ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, ?string $format = null, bool $recreate = false)
 * @method static \Illuminate\Support\Collection<string, string|null> urls(array<string>|string $styles, string $path, mixed $styleParameters = null, ?string $disk = null, bool $styleSameDisk = false, ?string $filename = null, ?string $format = null, bool $recreate = false)
 * @method static \Illuminate\Http\Response preview(string $style, ?string $path = null, mixed $styleParameters = null, ?string $disk = null)
 * @method static void macro(string $name, object|callable $macro)
 * @method static void mixin(object $mixin, bool $replace = true)
 * @method static bool hasMacro(string $name)
 * @method static void flushMacros()
 * @method static mixed macroCall(string $method, array<mixed> $parameters)
 *
 * @see LaravelImageStyle
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
