<?php

namespace BalisMatz\ImageStyle;

use BalisMatz\ImageStyle\Console\Commands\ImageStyleCacheCommand;
use BalisMatz\ImageStyle\Console\Commands\ImageStyleClearCommand;
use BalisMatz\ImageStyle\Console\Commands\ImageStyleFlushCommand;
use BalisMatz\ImageStyle\Console\Commands\ImageStyleListCommand;
use BalisMatz\ImageStyle\Console\Commands\ImageStyleMakeCommand;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Support\ServiceProvider;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class ImageStyleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(ImageStyleManager::class, function (Application $app) {
            return new ImageStyleManager($app->getNamespace());
        });

        $this->app->singleton(ImageStyle::class, function (Application $app) {
            return new ImageStyle(
                $app->make(ImageStyleManager::class),
                new ImageManager(
                    config('image-style.driver', Driver::class),
                    ...array_filter(
                        config('image-style.options', []),
                        fn (string $optionKey) => in_array(
                            $optionKey,
                            ['autoOrientation', 'decodeAnimation', 'blendingColor'],
                            true
                        ),
                        ARRAY_FILTER_USE_KEY
                    ),
                ),
                $app->make(FilesystemManager::class)
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/image-style.php' => config_path('image-style.php'),
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                ImageStyleCacheCommand::class,
                ImageStyleClearCommand::class,
                ImageStyleFlushCommand::class,
                ImageStyleListCommand::class,
                ImageStyleMakeCommand::class,
            ]);
        }
    }
}
