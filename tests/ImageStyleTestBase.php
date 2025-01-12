<?php

namespace BalisMatz\ImageStyle\Tests;

use BalisMatz\ImageStyle\ImageStyle;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Imagick\Driver;

abstract class ImageStyleTestBase extends ImageStyleTestCase
{
    /**
     * The image style.
     */
    protected ImageStyle $imageStyle;

    /**
     * The file paths.
     *
     * @var string[]
     */
    protected array $filePaths = [];

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        $this->afterApplicationCreated(function () {
            $this->imageStyle = $this->app->make(ImageStyle::class);

            $imageFile = UploadedFile::fake()->image('image.jpg', 5, 5);

            $this->filePaths = [
                'local-image' => $imageFile->storeAs('images', 'image.jpg', [
                    'disk' => 'local',
                ]),
                'local-image-png' => $imageFile->storeAs('images', 'image.png', [
                    'disk' => 'local',
                ]),
                'local-document' => UploadedFile::fake()->create('document.pdf')->storeAs('documents', 'document.pdf', [
                    'disk' => 'local',
                ]),
                'private-local-image' => $imageFile->storeAs('images', 'image-private.jpg', [
                    'disk' => 'local',
                    'visibility' => 'private',
                ]),
                'public-image' => $imageFile->storeAs('images', 'public-image.jpg', [
                    'disk' => 'public',
                ]),
            ];
        });

        $this->beforeApplicationDestroyed(function () {
            Storage::disk()->deleteDirectory('documents');

            Storage::disk()->deleteDirectory('images');

            Storage::disk()->deleteDirectory('styles');

            Storage::disk('public')->deleteDirectory('images');

            Storage::disk('public')->deleteDirectory('styles');
        });

        parent::setUp();
    }

    /**
     * {@inheritDoc}
     */
    protected function defineEnvironment($app)
    {
        tap($app['config'], function (Repository $config) {
            $config->set('image-style.driver', Driver::class);

            $config->set('image-style.options', [
                'autoOrientation' => true,
                'decodeAnimation' => true,
                'blendingColor' => 'ffffff',
                'quality' => 75,
            ]);

            $config->set('image-style.fallback_url', 'storage_url');

            $config->set('image-style.filesystem', 'public');

            $config->set('filesystems.default', 'local');
        });
    }
}
