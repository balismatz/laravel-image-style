<?php

namespace BalisMatz\ImageStyle\Tests;

use BalisMatz\ImageStyle\ImageStyleServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase;

abstract class ImageStyleTestCase extends TestCase
{
    use RefreshDatabase, WithWorkbench;

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        $this->afterApplicationCreated(function () {
            $this->artisan('make:image-style ImageStyle')->run();

            $this->artisan('make:image-style CustomIdImageStyle --id=custom-image-style-id')->run();

            $this->artisan('make:image-style InactiveImageStyle --active=false')->run();

            $this->artisan('make:image-style ThumbnailImageStyle --help-text="Resize image to 100px"')->run();

            $this->artisan('make:image-style UserThumbnail')->run();

            $this->artisan('make:image-style Posts/ThumbnailImageStyle')->run();

            $this->artisan('make:image-style Posts/Show/ThumbnailImageStyle')->run();

            $this->artisan('make:image-style Posts/Show/Gallery/ThumbnailImageStyle')->run();

            $this->artisan('make:image-style Posts/Show/Gallery/Item/ThumbnailImageStyle')->run();

            $this->artisan('make:image-style Posts/ConflictImageStyle')->run();

            $this->artisan('make:image-style PostsConflictImageStyle')->run();
        });

        $this->beforeApplicationDestroyed(function () {
            File::deleteDirectory(app_path('ImageStyles'));
        });

        parent::setUp();
    }

    /**
     * {@inheritDoc}
     */
    protected function getPackageProviders($app)
    {
        return [
            ImageStyleServiceProvider::class,
        ];
    }
}
