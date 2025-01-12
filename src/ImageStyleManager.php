<?php

namespace BalisMatz\ImageStyle;

use BalisMatz\ImageStyle\Attributes\ImageStyle;
use BalisMatz\ImageStyle\Information\ImageStyleInformation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use ReflectionClass;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class ImageStyleManager
{
    /**
     * Create a new image style manager instance.
     */
    public function __construct(
        protected string $appNamespace,
    ) {}

    /**
     * Get all image styles information.
     *
     * @return \Illuminate\Support\Collection<string, \BalisMatz\ImageStyle\Information\ImageStyleInformation>
     */
    public function all(): Collection
    {
        return once(function () {
            if ($cachedStyles = Cache::get($this->getCacheKey())) {
                return $cachedStyles;
            }

            if (! is_dir($stylesPath = app_path('ImageStyles'))) {
                return collect();
            }

            return collect(Finder::create()->in($stylesPath)->files()->depth(['>= 0', '<= 3'])->name('*.php'))
                ->map(function (SplFileInfo $file): string {
                    return $this->appNamespace.str_replace(
                        ['/', '.php'],
                        ['\\', ''],
                        Str::after($file->getRealPath(), realpath(app_path()).DIRECTORY_SEPARATOR)
                    );
                })
                ->map(function (string $class): ?ReflectionClass {
                    if (! class_exists($class) ||
                        ! is_subclass_of($class, ImageStyleBase::class)) {
                        return null;
                    }

                    $reflectionClass = new ReflectionClass($class);

                    if ($reflectionClass->isAbstract()) {
                        return null;
                    }

                    return $reflectionClass;
                })
                ->filter()
                ->map(function (ReflectionClass $reflectionClass): ImageStyleInformation {
                    $styleAttribute = collect($reflectionClass->getAttributes(ImageStyle::class))
                        ->last()?->newInstance();

                    $styleClass = $reflectionClass->getName();

                    return new ImageStyleInformation(
                        $styleClass,
                        $styleAttribute?->id ?: $this->generateId($styleClass),
                        $styleAttribute?->help,
                        $styleAttribute->active ?? true,
                    );
                })
                ->keyBy('id')
                ->sortBy('id', SORT_NATURAL);
        });
    }

    /**
     * Get the image style information.
     */
    public function get(string $style): ?ImageStyleInformation
    {
        return $this->all()
            ->filter(function (ImageStyleInformation $styleInformation) use ($style): bool {
                return $styleInformation->active && in_array($style, [
                    $styleInformation->class,
                    $styleInformation->id,
                ], true);
            })
            ->first();
    }

    /**
     * Get the cache key that image styles information will be stored.
     */
    public function getCacheKey(): string
    {
        return 'image-styles';
    }

    /**
     * Generate the image style ID.
     */
    protected function generateId(string $class): string
    {
        return str($class)
            ->replace(
                [$this->appNamespace.'ImageStyles', 'ImageStyle', '\\'],
                ['', '', '']
            )
            ->kebab()
            ->toString() ?: 'default';
    }
}
