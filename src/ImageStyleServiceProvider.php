<?php

namespace BalisMatz\ImageStyle;

use BalisMatz\ImageStyle\Console\Commands\ImageStyleCacheCommand;
use BalisMatz\ImageStyle\Console\Commands\ImageStyleClearCommand;
use BalisMatz\ImageStyle\Console\Commands\ImageStyleFlushCommand;
use BalisMatz\ImageStyle\Console\Commands\ImageStyleListCommand;
use BalisMatz\ImageStyle\Console\Commands\ImageStyleMakeCommand;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Image\Image;
use Illuminate\Image\ImagePipeline;
use Illuminate\Support\ServiceProvider;

class ImageStyleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/image-style.php', 'image-style');

        $this->app->singleton(ImageStyleManager::class, function (Application $app) {
            return new ImageStyleManager($app->getNamespace());
        });

        $this->app->singleton(ImageStyle::class, function (Application $app) {
            return new ImageStyle(
                $app->make(ImageStyleManager::class),
                $app->make(FilesystemManager::class)
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Image::macro('getPipeline', fn (): ImagePipeline => $this->pipeline);

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

            $this->optimizes(
                optimize: 'image-style:cache',
                clear: 'image-style:clear'
            );
        }
    }
}
