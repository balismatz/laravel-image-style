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
            $this->artisan('image-style:make ImageStyle')->run();

            $this->artisan('image-style:make CustomIdImageStyle --id=custom-image-style-id')->run();

            $this->artisan('image-style:make InactiveImageStyle --active=false')->run();

            $this->artisan('image-style:make ThumbnailImageStyle --help-text="Resize image to 100px"')->run();

            $this->artisan('image-style:make UserThumbnail')->run();

            $this->artisan('image-style:make Posts/ThumbnailImageStyle')->run();

            $this->artisan('image-style:make Posts/Show/ThumbnailImageStyle')->run();

            $this->artisan('image-style:make Posts/Show/Gallery/ThumbnailImageStyle')->run();

            $this->artisan('image-style:make Posts/Show/Gallery/Item/ThumbnailImageStyle')->run();

            $this->artisan('image-style:make Posts/ConflictImageStyle')->run();

            $this->artisan('image-style:make PostsConflictImageStyle')->run();
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
