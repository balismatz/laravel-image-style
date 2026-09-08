<?php

namespace BalisMatz\ImageStyle\Tests;

use BalisMatz\ImageStyle\Information\ImageStyleImageInformation;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;

class ImageStyleNullFallbackUrlTest extends ImageStyleTestBase
{
    /**
     * {@inheritDoc}
     */
    protected function defineEnvironment($app)
    {
        parent::defineEnvironment($app);

        tap($app['config'], function (Repository $config) {
            $config->set('image-style.fallback_url', null);
        });
    }

    /**
     * Tests whether imageInformation() returns an empty value for an unknown
     * style.
     */
    public function test_image_information_unknown_style(): void
    {
        $this->assertEquals(
            null,
            $this->imageStyle->imageInformation('unknown', $this->filePaths['local-image'])
        );
    }

    /**
     * Tests whether imageInformation() returns an empty value for a missing
     * image.
     */
    public function test_image_information_missing_image(): void
    {
        $this->assertEquals(
            null,
            $this->imageStyle->imageInformation('default', 'missing-image.jpg')
        );
    }

    /**
     * Tests whether imageInformation() returns an empty value for an unknown
     * style and a missing image.
     */
    public function test_image_information_unknown_style_missing_image(): void
    {
        $this->assertEquals(
            null,
            $this->imageStyle->imageInformation('unknown', 'missing-image.jpg')
        );
    }

    /**
     * Tests whether imageInformation() returns an empty value for an invalid
     * file.
     */
    public function test_image_information_invalid_file(): void
    {
        $this->assertEquals(
            null,
            $this->imageStyle->imageInformation('default', $this->filePaths['local-document'])
        );
    }

    /**
     * Tests whether imageInformation(), with parameters, returns an empty value
     * for an unknown style.
     */
    public function test_image_information_unknown_style_parameters(): void
    {
        $this->assertEquals(
            null,
            $this->imageStyle->imageInformation('unknown', $this->filePaths['local-image'], informationParameters: 'parameters')
        );
    }

    /**
     * Tests whether imageInformation(), with parameters, returns an empty value
     * for a missing image.
     */
    public function test_image_information_missing_image_parameters(): void
    {
        $this->assertEquals(
            null,
            $this->imageStyle->imageInformation('default', 'missing-image.jpg', informationParameters: 'parameters')
        );
    }

    /**
     * Tests whether imageInformation(), with parameters, returns an empty value
     * for an unknown style and a missing image.
     */
    public function test_image_information_unknown_style_missing_image_parameters(): void
    {
        $this->assertEquals(
            null,
            $this->imageStyle->imageInformation('unknown', 'missing-image.jpg', informationParameters: 'parameters')
        );
    }

    /**
     * Tests whether imageInformation(), with parameters, returns an empty value
     * for an invalid file.
     */
    public function test_image_information_invalid_file_parameters(): void
    {
        $this->assertEquals(
            null,
            $this->imageStyle->imageInformation('default', $this->filePaths['local-document'], informationParameters: 'parameters')
        );
    }

    /**
     * Tests whether imagesInformation() returns a collection of empty values
     * for unknown styles.
     */
    public function test_images_information_unknown_styles(): void
    {
        $this->assertEquals(
            collect([
                'unknown' => null,
                'unknown-2' => null,
            ]),
            $this->imageStyle->imagesInformation(['unknown', 'unknown-2'], $this->filePaths['local-image'])
        );
    }

    /**
     * Tests whether imagesInformation() returns a collection of empty values
     * for a missing image.
     */
    public function test_images_information_missing_image(): void
    {
        $this->assertEquals(
            collect([
                'default' => null,
                'thumbnail' => null,
            ]),
            $this->imageStyle->imagesInformation(['default', 'thumbnail'], 'missing-image.jpg')
        );
    }

    /**
     * Tests whether imagesInformation() returns a collection of empty values
     * for unknown styles and a missing image.
     */
    public function test_images_information_unknown_styles_missing_image(): void
    {
        $this->assertEquals(
            collect([
                'unknown' => null,
                'unknown-2' => null,
            ]),
            $this->imageStyle->imagesInformation(['unknown', 'unknown-2'], 'missing-image.jpg')
        );
    }

    /**
     * Tests whether imagesInformation() creates styled images for mixed
     * (known and unknown) styles and returns either a collection of
     * ImageStyleImageInformation objects or a collection of empty values.
     */
    public function test_images_information_mixed_styles_image(): void
    {
        $path = $this->filePaths['local-image'];

        /** @var FilesystemAdapter $styleFilesystem */
        $styleFilesystem = Storage::disk(config('image-style.filesystem'));

        $styleFilesystemUrl = $styleFilesystem->url('');

        $this->assertEquals(
            collect([
                'default' => new ImageStyleImageInformation(
                    $styleFilesystemUrl.'styles/default/'.$path, 5, 5, 'image/jpeg'
                ),
                'unknown' => null,
                'thumbnail' => new ImageStyleImageInformation(
                    $styleFilesystemUrl.'styles/thumbnail/'.$path, 5, 5, 'image/jpeg'
                ),
            ]),
            $this->imageStyle->imagesInformation(['default', 'unknown', 'thumbnail'], $path)
        );
    }

    /**
     * Tests whether imagesInformation() returns a collection of empty values
     * for an invalid file.
     */
    public function test_images_information_invalid_file(): void
    {
        $this->assertEquals(
            collect([
                'default' => null,
                'thumbnail' => null,
            ]),
            $this->imageStyle->imagesInformation(['default', 'thumbnail'], $this->filePaths['local-document'])
        );
    }

    /**
     * Tests whether imagesInformation(), with parameters, returns a collection
     * of empty values for unknown styles.
     */
    public function test_images_information_unknown_styles_parameters(): void
    {
        $this->assertEquals(
            collect([
                'unknown' => null,
                'unknown-2' => null,
            ]),
            $this->imageStyle->imagesInformation(['unknown', 'unknown-2'], $this->filePaths['local-image'], informationParameters: [
                'unknown' => 'unknown-parameters',
                'unknown-2' => 'unknown-2-parameters',
            ])
        );
    }

    /**
     * Tests whether imagesInformation(), with parameters, returns a collection
     * of empty values for a missing image.
     */
    public function test_images_information_missing_image_parameters(): void
    {
        $this->assertEquals(
            collect([
                'default' => null,
                'thumbnail' => null,
            ]),
            $this->imageStyle->imagesInformation(['default', 'thumbnail'], 'missing-image.jpg', informationParameters: [
                'default' => 'default-parameters',
                'thumbnail' => 'thumbnail-parameters',
            ])
        );
    }

    /**
     * Tests whether imagesInformation(), with parameters, returns a collection
     * of empty values for unknown styles and a missing image.
     */
    public function test_images_information_unknown_styles_missing_image_parameters(): void
    {
        $this->assertEquals(
            collect([
                'unknown' => null,
                'unknown-2' => null,
            ]),
            $this->imageStyle->imagesInformation(['unknown', 'unknown-2'], 'missing-image.jpg', informationParameters: [
                'unknown' => 'unknown-parameters',
                'unknown-2' => 'unknown-2-parameters',
            ])
        );
    }

    /**
     * Tests whether imagesInformation(), with parameters, creates styled images
     * for mixed (known and unknown) styles and returns either a collection of
     * ImageStyleImageInformation objects or a collection of empty values.
     */
    public function test_images_information_mixed_styles_image_parameters(): void
    {
        $path = $this->filePaths['local-image'];

        /** @var FilesystemAdapter $styleFilesystem */
        $styleFilesystem = Storage::disk(config('image-style.filesystem'));

        $styleFilesystemUrl = $styleFilesystem->url('');

        $this->assertEquals(
            collect([
                'default' => new ImageStyleImageInformation(
                    $styleFilesystemUrl.'styles/default/'.$path, 5, 5, 'image/jpeg', parameters: 'default-parameters'
                ),
                'unknown' => null,
                'thumbnail' => new ImageStyleImageInformation(
                    $styleFilesystemUrl.'styles/thumbnail/'.$path, 5, 5, 'image/jpeg', parameters: 'thumbnail-parameters'
                ),
            ]),
            $this->imageStyle->imagesInformation(['default', 'unknown', 'thumbnail'], $path, informationParameters: [
                'default' => 'default-parameters',
                'unknown' => 'unknown-parameters',
                'thumbnail' => 'thumbnail-parameters',
            ])
        );
    }

    /**
     * Tests whether imagesInformation(), with parameters, returns a collection
     * of empty values for an invalid file.
     */
    public function test_images_information_invalid_file_parameters(): void
    {
        $this->assertEquals(
            collect([
                'default' => null,
                'thumbnail' => null,
            ]),
            $this->imageStyle->imagesInformation(['default', 'thumbnail'], $this->filePaths['local-document'], informationParameters: [
                'default' => 'default-parameters',
                'thumbnail' => 'thumbnail-parameters',
            ])
        );
    }

    /**
     * Tests whether url() returns an empty value for an unknown style.
     */
    public function test_url_unknown_style(): void
    {
        $this->assertEquals(
            null,
            $this->imageStyle->url('unknown', $this->filePaths['local-image'])
        );
    }

    /**
     * Tests whether url() returns an empty value for a missing image.
     */
    public function test_url_missing_image(): void
    {
        $this->assertEquals(
            null,
            $this->imageStyle->url('default', 'missing-image.jpg')
        );
    }

    /**
     * Tests whether url() returns an empty value for an unknown style and a
     * missing image.
     */
    public function test_url_unknown_style_missing_image(): void
    {
        $this->assertEquals(
            null,
            $this->imageStyle->url('unknown', 'missing-image.jpg')
        );
    }

    /**
     * Tests whether url() returns an empty value for an invalid file.
     */
    public function test_url_invalid_file(): void
    {
        $this->assertEquals(
            null,
            $this->imageStyle->url('default', $this->filePaths['local-document'])
        );
    }

    /**
     * Tests whether urls() returns a collection of empty values for unknown
     * styles.
     */
    public function test_urls_unknown_styles(): void
    {
        $this->assertEquals(
            collect([
                'unknown' => null,
                'unknown-2' => null,
            ]),
            $this->imageStyle->urls(['unknown', 'unknown-2'], $this->filePaths['local-image'])
        );
    }

    /**
     * Tests whether urls() returns a collection of empty values for a missing
     * image.
     */
    public function test_urls_missing_image(): void
    {
        $this->assertEquals(
            collect([
                'default' => null,
                'thumbnail' => null,
            ]),
            $this->imageStyle->urls(['default', 'thumbnail'], 'missing-image.jpg')
        );
    }

    /**
     * Tests whether urls() returns a collection of empty values for unknown
     * styles and a missing image.
     */
    public function test_urls_unknown_styles_missing_image(): void
    {
        $this->assertEquals(
            collect([
                'unknown' => null,
                'unknown-2' => null,
            ]),
            $this->imageStyle->urls(['unknown', 'unknown-2'], 'missing-image.jpg')
        );
    }

    /**
     * Tests whether urls() creates styled images for mixed (known and unknown)
     * styles and returns either the URLs or empty values.
     */
    public function test_urls_mixed_styles_image(): void
    {
        $path = $this->filePaths['local-image'];

        /** @var FilesystemAdapter $styleFilesystem */
        $styleFilesystem = Storage::disk(config('image-style.filesystem'));

        $styleFilesystemUrl = $styleFilesystem->url('');

        $this->assertEquals(
            collect([
                'default' => $styleFilesystemUrl.'styles/default/'.$path,
                'unknown' => null,
                'thumbnail' => $styleFilesystemUrl.'styles/thumbnail/'.$path,
            ]),
            $this->imageStyle->urls(['default', 'unknown', 'thumbnail'], $path)
        );
    }

    /**
     * Tests whether urls() returns a collection of empty values for an invalid
     * file.
     */
    public function test_urls_invalid_file(): void
    {
        $this->assertEquals(
            collect([
                'default' => null,
                'thumbnail' => null,
            ]),
            $this->imageStyle->urls(['default', 'thumbnail'], $this->filePaths['local-document'])
        );
    }
}
