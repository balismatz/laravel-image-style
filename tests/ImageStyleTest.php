<?php

namespace BalisMatz\ImageStyle\Tests;

use BalisMatz\ImageStyle\Information\ImageStyleImageInformation;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

/**
 * Tests are focused in path() and paths() as the other methods mainly rely on
 * them.
 */
class ImageStyleTest extends ImageStyleTestBase
{
    /**
     * Tests if path() returns null for unknown style.
     */
    public function test_path_unknown_style(): void
    {
        $this->assertNull(
            $this->imageStyle->path('unknown', $this->filePaths['local-image'])
        );
    }

    /**
     * Tests if path() returns null for missing image.
     */
    public function test_path_missing_image(): void
    {
        $this->assertNull(
            $this->imageStyle->path('default', 'missing-image.jpg')
        );
    }

    /**
     * Tests if path() returns null for unknown style and missing image.
     */
    public function test_path_unknown_style_missing_image(): void
    {
        $this->assertNull(
            $this->imageStyle->path('unknown', 'missing-image.jpg')
        );
    }

    /**
     * Tests if path() creates a styled image and returns the path.
     */
    public function test_path_style_image(): void
    {
        $path = $this->filePaths['local-image'];

        $this->assertEquals(
            Storage::disk(config('image-style.filesystem'))->path('').'styles/default/'.$path,
            $this->imageStyle->path('default', $path)
        );
    }

    /**
     * Tests if path() creates a styled image and returns the relative path.
     */
    public function test_path_style_image_relative(): void
    {
        $path = $this->filePaths['local-image'];

        $this->assertEquals(
            'styles/default/'.$path,
            $this->imageStyle->path('default', $path, relative: true)
        );
    }

    /**
     * Tests if path() creates a styled image from another disk and returns the
     * path.
     */
    public function test_path_style_image_disk(): void
    {
        $path = $this->filePaths['public-image'];

        $this->assertEquals(
            Storage::disk(config('image-style.filesystem'))->path('').'styles/default/'.$path,
            $this->imageStyle->path('default', $path, disk: 'public')
        );
    }

    /**
     * Tests if path() creates a styled image to the same disk and returns the
     * path.
     */
    public function test_path_style_image_same_disk(): void
    {
        $path = $this->filePaths['local-image'];

        $this->assertEquals(
            Storage::disk('local')->path('').'styles/default/'.$path,
            $this->imageStyle->path('default', $path, styleSameDisk: true)
        );
    }

    /**
     * Tests if path() creates a styled image with custom name and returns the
     * path.
     */
    public function test_path_style_image_filename(): void
    {
        $path = $this->filePaths['local-image'];

        $filename = 'custom-name-'.pathinfo($path)['filename'];

        $this->assertEquals(
            Storage::disk(config('image-style.filesystem'))->path('').'styles/default/images/'.$filename.'.jpg',
            $this->imageStyle->path('default', $path, filename: $filename)
        );
    }

    /**
     * Tests if path() creates a styled image with same visibility and returns
     * the path.
     */
    public function test_path_style_image_visibility(): void
    {
        /** @var \Illuminate\Filesystem\FilesystemAdapter $styleFilesystem */
        $styleFilesystem = Storage::disk(config('image-style.filesystem'));

        $this->assertEquals(
            'public',
            $styleFilesystem->getVisibility(
                $this->imageStyle->path('default', $this->filePaths['local-image'], relative: true)
            )
        );
    }

    /**
     * Tests if path() creates a styled image with same visibility (private)
     * and returns the path.
     */
    public function test_path_style_image_visibility_private(): void
    {
        /** @var \Illuminate\Filesystem\FilesystemAdapter $styleFilesystem */
        $styleFilesystem = Storage::disk(config('image-style.filesystem'));

        $this->assertEquals(
            'private',
            $styleFilesystem->getVisibility(
                $this->imageStyle->path('default', $this->filePaths['private-local-image'], relative: true)
            )
        );
    }

    /**
     * Tests if path() returns null for invalid file.
     */
    public function test_path_style_invalid_file(): void
    {
        $path = $this->filePaths['local-document'];

        $this->assertNull(
            $this->imageStyle->path('default', $path)
        );
    }

    /**
     * Tests if path() returns the first path of multiple given styles.
     */
    public function test_path_styles_image_first(): void
    {
        $path = $this->filePaths['local-image'];

        $this->assertEquals(
            'styles/default/'.$path,
            $this->imageStyle->path('default,unknown,thumbnail', $path, relative: true)
        );
    }

    /**
     * Tests if pathToJpeg() creates a styled image in JPEG format and returns
     * the path.
     */
    public function test_path_to_jpeg_style_image(): void
    {
        /** @var \Illuminate\Filesystem\FilesystemAdapter $styleFilesystem */
        $styleFilesystem = Storage::disk(config('image-style.filesystem'));

        $this->assertEquals(
            'image/jpeg',
            $styleFilesystem->mimetype(
                $this->imageStyle->pathToJpeg('default', $this->filePaths['local-image-png'], relative: true)
            )
        );
    }

    /**
     * Tests if pathToWebp() creates a styled image in WebP graphic format and
     * returns the path.
     */
    public function test_path_to_webp_style_image(): void
    {
        /** @var \Illuminate\Filesystem\FilesystemAdapter $styleFilesystem */
        $styleFilesystem = Storage::disk(config('image-style.filesystem'));

        $this->assertEquals(
            'image/webp',
            $styleFilesystem->mimetype(
                $this->imageStyle->pathToWebp('default', $this->filePaths['local-image'], relative: true)
            )
        );
    }

    /**
     * Tests if pathToPng() creates a styled image in PNG format and returns the
     * path.
     */
    public function test_path_to_png_style_image(): void
    {
        /** @var \Illuminate\Filesystem\FilesystemAdapter $styleFilesystem */
        $styleFilesystem = Storage::disk(config('image-style.filesystem'));

        $this->assertEquals(
            'image/png',
            $styleFilesystem->mimetype(
                $this->imageStyle->pathToPng('default', $this->filePaths['local-image'], relative: true)
            )
        );
    }

    /**
     * Tests if pathToGif() creates a styled image in GIF format and returns the
     * path.
     */
    public function test_path_to_gif_style_image(): void
    {
        /** @var \Illuminate\Filesystem\FilesystemAdapter $styleFilesystem */
        $styleFilesystem = Storage::disk(config('image-style.filesystem'));

        $this->assertEquals(
            'image/gif',
            $styleFilesystem->mimetype(
                $this->imageStyle->pathToGif('default', $this->filePaths['local-image'], relative: true)
            )
        );
    }

    /**
     * Tests if pathToBmp() creates a styled image in Windows Bitmap format and
     * returns the path.
     */
    public function test_path_to_bmp_style_image(): void
    {
        /** @var \Illuminate\Filesystem\FilesystemAdapter $styleFilesystem */
        $styleFilesystem = Storage::disk(config('image-style.filesystem'));

        $this->assertEquals(
            'image/bmp',
            $styleFilesystem->mimetype(
                $this->imageStyle->pathToBmp('default', $this->filePaths['local-image'], relative: true)
            )
        );
    }

    /**
     * Tests if pathToAvif() creates a styled image in AVIF format and returns
     * the path.
     */
    public function test_path_to_avif_style_image(): void
    {
        /** @var \Illuminate\Filesystem\FilesystemAdapter $styleFilesystem */
        $styleFilesystem = Storage::disk(config('image-style.filesystem'));

        $this->assertEquals(
            'image/avif',
            $styleFilesystem->mimetype(
                $this->imageStyle->pathToAvif('default', $this->filePaths['local-image'], relative: true)
            )
        );
    }

    /**
     * Tests if pathToTiff() creates a styled image in TIFF format and returns
     * the path.
     */
    public function test_path_to_tiff_style_image(): void
    {
        /** @var \Illuminate\Filesystem\FilesystemAdapter $styleFilesystem */
        $styleFilesystem = Storage::disk(config('image-style.filesystem'));

        $this->assertEquals(
            'image/tiff',
            $styleFilesystem->mimetype(
                $this->imageStyle->pathToTiff('default', $this->filePaths['local-image'], relative: true)
            )
        );
    }

    /**
     * Tests if pathToJpeg2000() creates a styled image in JPEG 2000 format and
     * returns the path.
     */
    public function test_path_to_jpeg_2000_style_image(): void
    {
        /** @var \Illuminate\Filesystem\FilesystemAdapter $styleFilesystem */
        $styleFilesystem = Storage::disk(config('image-style.filesystem'));

        $this->assertEquals(
            'image/jp2',
            $styleFilesystem->mimetype(
                $this->imageStyle->pathToJpeg2000('default', $this->filePaths['local-image'], relative: true)
            )
        );
    }

    /**
     * Tests if pathToHeic() creates a styled image in HEIC format and returns
     * the path.
     */
    public function test_path_to_heic_style_image(): void
    {
        /** @var \Illuminate\Filesystem\FilesystemAdapter $styleFilesystem */
        $styleFilesystem = Storage::disk(config('image-style.filesystem'));

        $this->assertEquals(
            'image/heic',
            $styleFilesystem->mimetype(
                $this->imageStyle->pathToHeic('default', $this->filePaths['local-image'], relative: true)
            )
        );
    }

    /**
     * Tests if paths() returns a collection of null values for unknown styles.
     */
    public function test_paths_unknown_styles(): void
    {
        $this->assertEquals(
            collect(['unknown' => null, 'unknown-2' => null]),
            $this->imageStyle->paths(['unknown', 'unknown-2'], $this->filePaths['local-image'])
        );
    }

    /**
     * Tests if paths() returns a collection of null values for missing image.
     */
    public function test_paths_missing_image(): void
    {
        $this->assertEquals(
            collect(['default' => null, 'thumbnail' => null]),
            $this->imageStyle->paths(['default', 'thumbnail'], 'missing-image.jpg')
        );
    }

    /**
     * Tests if paths() returns a collection of null values for unknown styles
     * and missing image.
     */
    public function test_paths_unknown_styles_missing_image(): void
    {
        $this->assertEquals(
            collect(['unknown' => null, 'unknown-2' => null]),
            $this->imageStyle->paths(['unknown', 'unknown-2'], 'missing-image.jpg')
        );
    }

    /**
     * Tests if paths() creates styled images and returns the paths.
     */
    public function test_paths_styles_image(): void
    {
        $path = $this->filePaths['local-image'];

        $styleFilesystemPath = Storage::disk(config('image-style.filesystem'))->path('');

        $this->assertEquals(
            collect([
                'default' => $styleFilesystemPath.'styles/default/'.$path,
                'thumbnail' => $styleFilesystemPath.'styles/thumbnail/'.$path,
            ]),
            $this->imageStyle->paths(['default', 'thumbnail'], $path)
        );
    }

    /**
     * Tests if paths() creates styled images and returns the relative paths.
     */
    public function test_paths_styles_image_relative(): void
    {
        $path = $this->filePaths['local-image'];

        $this->assertEquals(
            collect([
                'default' => 'styles/default/'.$path,
                'thumbnail' => 'styles/thumbnail/'.$path,
            ]),
            $this->imageStyle->paths(['default', 'thumbnail'], $path, relative: true)
        );
    }

    /**
     * Tests if paths() creates styled images by styles string and returns the
     * paths.
     */
    public function test_paths_string_styles_image(): void
    {
        $path = $this->filePaths['local-image'];

        $styleFilesystemPath = Storage::disk(config('image-style.filesystem'))->path('');

        $this->assertEquals(
            collect([
                'default' => $styleFilesystemPath.'styles/default/'.$path,
                'thumbnail' => $styleFilesystemPath.'styles/thumbnail/'.$path,
            ]),
            $this->imageStyle->paths('default, thumbnail', $path)
        );
    }

    /**
     * Tests if paths() creates styled images by styles string and returns the
     * relative paths.
     */
    public function test_paths_string_styles_image_relative(): void
    {
        $path = $this->filePaths['local-image'];

        $this->assertEquals(
            collect([
                'default' => 'styles/default/'.$path,
                'thumbnail' => 'styles/thumbnail/'.$path,
            ]),
            $this->imageStyle->paths('default, thumbnail', $path, relative: true)
        );
    }

    /**
     * Tests if paths() creates styled images by mixed (known & unknown) styles
     * and returns the paths.
     */
    public function test_paths_mixed_styles_image(): void
    {
        $path = $this->filePaths['local-image'];

        $styleFilesystemPath = Storage::disk(config('image-style.filesystem'))->path('');

        $this->assertEquals(
            collect([
                'default' => $styleFilesystemPath.'styles/default/'.$path,
                'unknown' => null,
                'thumbnail' => $styleFilesystemPath.'styles/thumbnail/'.$path,
            ]),
            $this->imageStyle->paths(['default', 'unknown', 'thumbnail'], $path)
        );
    }

    /**
     * Tests if paths() creates styled images by mixed styles (known & unknown)
     * and returns the relative paths.
     */
    public function test_paths_mixed_styles_image_relative(): void
    {
        $path = $this->filePaths['local-image'];

        $this->assertEquals(
            collect([
                'default' => 'styles/default/'.$path,
                'unknown' => null,
                'thumbnail' => 'styles/thumbnail/'.$path,
            ]),
            $this->imageStyle->paths(['default', 'unknown', 'thumbnail'], $path, relative: true)
        );
    }

    /**
     * Tests if paths() creates styled images from another disk and returns the
     * paths.
     */
    public function test_paths_styles_image_disk(): void
    {
        $path = $this->filePaths['public-image'];

        $styleFilesystemPath = Storage::disk(config('image-style.filesystem'))->path('');

        $this->assertEquals(
            collect([
                'default' => $styleFilesystemPath.'styles/default/'.$path,
                'thumbnail' => $styleFilesystemPath.'styles/thumbnail/'.$path,
            ]),
            $this->imageStyle->paths(['default', 'thumbnail'], $path, disk: 'public')
        );
    }

    /**
     * Tests if paths() creates styled images to the same disk and returns the
     * paths.
     */
    public function test_paths_styles_image_same_disk(): void
    {
        $path = $this->filePaths['local-image'];

        $localFilesystemPath = Storage::disk('local')->path('');

        $this->assertEquals(
            collect([
                'default' => $localFilesystemPath.'styles/default/'.$path,
                'thumbnail' => $localFilesystemPath.'styles/thumbnail/'.$path,
            ]),
            $this->imageStyle->paths(['default', 'thumbnail'], $path, styleSameDisk: true)
        );
    }

    /**
     * Tests if paths() creates styled images with custom name and returns the
     * paths.
     */
    public function test_paths_styles_image_filename(): void
    {
        $path = $this->filePaths['local-image'];

        $styleFilesystemPath = Storage::disk(config('image-style.filesystem'))->path('');

        $filename = 'custom-name-'.pathinfo($path)['filename'];

        $this->assertEquals(
            collect([
                'default' => $styleFilesystemPath.'styles/default/images/'.$filename.'.jpg',
                'thumbnail' => $styleFilesystemPath.'styles/thumbnail/images/'.$filename.'.jpg',
            ]),
            $this->imageStyle->paths(['default', 'thumbnail'], $path, filename: $filename)
        );
    }

    /**
     * Tests if paths() creates styled images with same visibility and returns
     * the paths.
     */
    public function test_paths_styles_image_visibility(): void
    {
        /** @var \Illuminate\Filesystem\FilesystemAdapter $styleFilesystem */
        $styleFilesystem = Storage::disk(config('image-style.filesystem'));

        $this->assertEquals(
            collect([
                'default' => 'public',
                'thumbnail' => 'public',
            ]),
            $this->imageStyle->paths(['default', 'thumbnail'], $this->filePaths['local-image'], relative: true)
                ->map(fn (string $path): string => $styleFilesystem->getVisibility($path))
        );
    }

    /**
     * Tests if paths() creates styled images with same visibility (private)
     * and returns the paths.
     */
    public function test_paths_styles_image_visibility_private(): void
    {
        /** @var \Illuminate\Filesystem\FilesystemAdapter $styleFilesystem */
        $styleFilesystem = Storage::disk(config('image-style.filesystem'));

        $this->assertEquals(
            collect([
                'default' => 'private',
                'thumbnail' => 'private',
            ]),
            $this->imageStyle->paths(['default', 'thumbnail'], $this->filePaths['private-local-image'], relative: true)
                ->map(fn (string $path): string => $styleFilesystem->getVisibility($path))
        );
    }

    /**
     * Tests if paths() returns a collection of null values for invalid file.
     */
    public function test_paths_styles_invalid_file(): void
    {
        $path = $this->filePaths['local-document'];

        $this->assertEquals(
            collect([
                'default' => null,
                'thumbnail' => null,
            ]),
            $this->imageStyle->paths(['default', 'thumbnail'], $path)
        );
    }

    /**
     * Tests if pathsToJpeg() creates styled images in JPEG format and returns
     * the paths.
     */
    public function test_paths_to_jpeg_styles_image(): void
    {
        /** @var \Illuminate\Filesystem\FilesystemAdapter $styleFilesystem */
        $styleFilesystem = Storage::disk(config('image-style.filesystem'));

        $this->assertEquals(
            collect([
                'default' => 'image/jpeg',
                'thumbnail' => 'image/jpeg',
            ]),
            $this->imageStyle->pathsToJpeg(['default', 'thumbnail'], $this->filePaths['local-image-png'], relative: true)
                ->map(fn (string $path): string => $styleFilesystem->mimetype($path))
        );
    }

    /**
     * Tests if pathsToWebp() creates styled images in WebP graphic format and
     * returns the paths.
     */
    public function test_paths_to_webp_styles_image(): void
    {
        /** @var \Illuminate\Filesystem\FilesystemAdapter $styleFilesystem */
        $styleFilesystem = Storage::disk(config('image-style.filesystem'));

        $this->assertEquals(
            collect([
                'default' => 'image/webp',
                'thumbnail' => 'image/webp',
            ]),
            $this->imageStyle->pathsToWebp(['default', 'thumbnail'], $this->filePaths['local-image'], relative: true)
                ->map(fn (string $path): string => $styleFilesystem->mimetype($path))
        );
    }

    /**
     * Tests if pathsToPng() creates styled images in PNG format and returns the
     * paths.
     */
    public function test_paths_to_png_styles_image(): void
    {
        /** @var \Illuminate\Filesystem\FilesystemAdapter $styleFilesystem */
        $styleFilesystem = Storage::disk(config('image-style.filesystem'));

        $this->assertEquals(
            collect([
                'default' => 'image/png',
                'thumbnail' => 'image/png',
            ]),
            $this->imageStyle->pathsToPng(['default', 'thumbnail'], $this->filePaths['local-image'], relative: true)
                ->map(fn (string $path): string => $styleFilesystem->mimetype($path))
        );
    }

    /**
     * Tests if pathsToGif() creates styled images in GIF format and returns the
     * paths.
     */
    public function test_paths_to_gif_styles_image(): void
    {
        /** @var \Illuminate\Filesystem\FilesystemAdapter $styleFilesystem */
        $styleFilesystem = Storage::disk(config('image-style.filesystem'));

        $this->assertEquals(
            collect([
                'default' => 'image/gif',
                'thumbnail' => 'image/gif',
            ]),
            $this->imageStyle->pathsToGif(['default', 'thumbnail'], $this->filePaths['local-image'], relative: true)
                ->map(fn (string $path): string => $styleFilesystem->mimetype($path))
        );
    }

    /**
     * Tests if pathsToBmp() creates styled images in Windows Bitmap format and
     * returns the paths.
     */
    public function test_paths_to_bmp_styles_image(): void
    {
        /** @var \Illuminate\Filesystem\FilesystemAdapter $styleFilesystem */
        $styleFilesystem = Storage::disk(config('image-style.filesystem'));

        $this->assertEquals(
            collect([
                'default' => 'image/bmp',
                'thumbnail' => 'image/bmp',
            ]),
            $this->imageStyle->pathsToBmp(['default', 'thumbnail'], $this->filePaths['local-image'], relative: true)
                ->map(fn (string $path): string => $styleFilesystem->mimetype($path))
        );
    }

    /**
     * Tests if pathsToAvif() creates styled images in AVIF format and returns
     * the paths.
     */
    public function test_paths_to_avif_styles_image(): void
    {
        /** @var \Illuminate\Filesystem\FilesystemAdapter $styleFilesystem */
        $styleFilesystem = Storage::disk(config('image-style.filesystem'));

        $this->assertEquals(
            collect([
                'default' => 'image/avif',
                'thumbnail' => 'image/avif',
            ]),
            $this->imageStyle->pathsToAvif(['default', 'thumbnail'], $this->filePaths['local-image'], relative: true)
                ->map(fn (string $path): string => $styleFilesystem->mimetype($path))
        );
    }

    /**
     * Tests if pathsToTiff() creates styled images in TIFF format and returns
     * the paths.
     */
    public function test_paths_to_tiff_styles_image(): void
    {
        /** @var \Illuminate\Filesystem\FilesystemAdapter $styleFilesystem */
        $styleFilesystem = Storage::disk(config('image-style.filesystem'));

        $this->assertEquals(
            collect([
                'default' => 'image/tiff',
                'thumbnail' => 'image/tiff',
            ]),
            $this->imageStyle->pathsToTiff(['default', 'thumbnail'], $this->filePaths['local-image'], relative: true)
                ->map(fn (string $path): string => $styleFilesystem->mimetype($path))
        );
    }

    /**
     * Tests if pathsToJpeg2000() creates styled images in JPEG 2000 format and
     * returns the paths.
     */
    public function test_paths_to_jpeg_2000_styles_image(): void
    {
        /** @var \Illuminate\Filesystem\FilesystemAdapter $styleFilesystem */
        $styleFilesystem = Storage::disk(config('image-style.filesystem'));

        $this->assertEquals(
            collect([
                'default' => 'image/jp2',
                'thumbnail' => 'image/jp2',
            ]),
            $this->imageStyle->pathsToJpeg2000(['default', 'thumbnail'], $this->filePaths['local-image'], relative: true)
                ->map(fn (string $path): string => $styleFilesystem->mimetype($path))
        );
    }

    /**
     * Tests if pathsToHeic() creates styled images in HEIC format and returns
     * the paths.
     */
    public function test_paths_to_heic_styles_image(): void
    {
        /** @var \Illuminate\Filesystem\FilesystemAdapter $styleFilesystem */
        $styleFilesystem = Storage::disk(config('image-style.filesystem'));

        $this->assertEquals(
            collect([
                'default' => 'image/heic',
                'thumbnail' => 'image/heic',
            ]),
            $this->imageStyle->pathsToHeic(['default', 'thumbnail'], $this->filePaths['local-image'], relative: true)
                ->map(fn (string $path): string => $styleFilesystem->mimetype($path))
        );
    }

    /**
     * Tests if imageInformation() returns an ImageStyleImageInformation object
     * - containing the original image information - for unknown style.
     */
    public function test_image_information_unknown_style(): void
    {
        $this->assertEquals(
            new ImageStyleImageInformation('/storage/images/image.jpg', 5, 5, 'image/jpeg'),
            $this->imageStyle->imageInformation('unknown', $this->filePaths['local-image'])
        );
    }

    /**
     * Tests if imageInformation() returns an ImageStyleImageInformation object
     * - containing the Storage::url() without any other information - for
     * missing image.
     */
    public function test_image_information_missing_image(): void
    {
        $this->assertEquals(
            new ImageStyleImageInformation('/storage/missing-image.jpg'),
            $this->imageStyle->imageInformation('default', 'missing-image.jpg')
        );
    }

    /**
     * Tests if imageInformation() returns an ImageStyleImageInformation object
     * - containing the Storage::url() without any other information - for
     * unknown style and missing image.
     */
    public function test_image_information_unknown_style_missing_image(): void
    {
        $this->assertEquals(
            new ImageStyleImageInformation('/storage/missing-image.jpg'),
            $this->imageStyle->imageInformation('unknown', 'missing-image.jpg')
        );
    }

    /**
     * Tests if imageInformation() creates a styled image and returns an
     * ImageStyleImageInformation object, containing the styled image
     * information.
     */
    public function test_image_information_style_image(): void
    {
        $path = $this->filePaths['local-image'];

        /** @var \Illuminate\Filesystem\FilesystemAdapter $styleFilesystem */
        $styleFilesystem = Storage::disk(config('image-style.filesystem'));

        $this->assertEquals(
            new ImageStyleImageInformation(
                $styleFilesystem->url('').'styles/default/'.$path, 5, 5, 'image/jpeg'
            ),
            $this->imageStyle->imageInformation('default', $path)
        );
    }

    /**
     * Tests if imageInformation() returns an ImageStyleImageInformation object
     * - containing the Storage::url() without any other information - for
     * invalid file.
     */
    public function test_image_information_style_invalid_file(): void
    {
        $path = $this->filePaths['local-document'];

        /** @var \Illuminate\Filesystem\FilesystemAdapter $localFilesystem */
        $localFilesystem = Storage::disk('local');

        $this->assertEquals(
            new ImageStyleImageInformation($localFilesystem->url($path)),
            $this->imageStyle->imageInformation('default', $path)
        );
    }

    /**
     * Tests if imageInformation(), with parameters, returns an
     * ImageStyleImageInformation object - containing the original image
     * information and the given parameters - for unknown style.
     */
    public function test_image_information_unknown_style_parameters(): void
    {
        $this->assertEquals(
            new ImageStyleImageInformation('/storage/images/image.jpg', 5, 5, 'image/jpeg', 'parameters'),
            $this->imageStyle->imageInformation('unknown', $this->filePaths['local-image'], informationParameters: 'parameters')
        );
    }

    /**
     * Tests if imageInformation(), with parameters, returns an
     * ImageStyleImageInformation object - containing the Storage::url() and the
     * given parameters without any other information - for missing image.
     */
    public function test_image_information_missing_image_parameters(): void
    {
        $this->assertEquals(
            new ImageStyleImageInformation('/storage/missing-image.jpg', parameters: 'parameters'),
            $this->imageStyle->imageInformation('default', 'missing-image.jpg', informationParameters: 'parameters')
        );
    }

    /**
     * Tests if imageInformation(), with parameters, returns an
     * ImageStyleImageInformation object - containing the Storage::url() and the
     * given parameters without any other information - for unknown style and
     * missing image.
     */
    public function test_image_information_unknown_style_missing_image_parameters(): void
    {
        $this->assertEquals(
            new ImageStyleImageInformation('/storage/missing-image.jpg', parameters: 'parameters'),
            $this->imageStyle->imageInformation('unknown', 'missing-image.jpg', informationParameters: 'parameters')
        );
    }

    /**
     * Tests if imageInformation(), with parameters, creates a styled image and
     * returns an ImageStyleImageInformation object, containing the styled image
     * information and the given parameters.
     */
    public function test_image_information_style_image_parameters(): void
    {
        $path = $this->filePaths['local-image'];

        /** @var \Illuminate\Filesystem\FilesystemAdapter $styleFilesystem */
        $styleFilesystem = Storage::disk(config('image-style.filesystem'));

        $this->assertEquals(
            new ImageStyleImageInformation(
                $styleFilesystem->url('').'styles/default/'.$path, 5, 5, 'image/jpeg', 'parameters'
            ),
            $this->imageStyle->imageInformation('default', $path, informationParameters: 'parameters')
        );
    }

    /**
     * Tests if imageInformation(), with parameters, returns an
     * ImageStyleImageInformation object - containing the Storage::url() and the
     * given parameters without any other information - for invalid file.
     */
    public function test_image_information_style_invalid_file_parameters(): void
    {
        $path = $this->filePaths['local-document'];

        /** @var \Illuminate\Filesystem\FilesystemAdapter $localFilesystem */
        $localFilesystem = Storage::disk('local');

        $this->assertEquals(
            new ImageStyleImageInformation($localFilesystem->url($path), parameters: 'parameters'),
            $this->imageStyle->imageInformation('default', $path, informationParameters: 'parameters')
        );
    }

    /**
     * Tests if imagesInformation() returns a collection of
     * ImageStyleImageInformation objects - containing the original image
     * information - for unknown styles.
     */
    public function test_images_information_unknown_styles(): void
    {
        $this->assertEquals(
            collect([
                'unknown' => new ImageStyleImageInformation('/storage/images/image.jpg', 5, 5, 'image/jpeg'),
                'unknown-2' => new ImageStyleImageInformation('/storage/images/image.jpg', 5, 5, 'image/jpeg'),
            ]),
            $this->imageStyle->imagesInformation(['unknown', 'unknown-2'], $this->filePaths['local-image'])
        );
    }

    /**
     * Tests if imagesInformation() returns a collection of
     * ImageStyleImageInformation objects - containing the Storage::url()
     * without any other information - for missing image.
     */
    public function test_images_information_missing_image(): void
    {
        $this->assertEquals(
            collect([
                'default' => new ImageStyleImageInformation('/storage/missing-image.jpg'),
                'thumbnail' => new ImageStyleImageInformation('/storage/missing-image.jpg'),
            ]),
            $this->imageStyle->imagesInformation(['default', 'thumbnail'], 'missing-image.jpg')
        );
    }

    /**
     * Tests if imagesInformation() returns a collection of
     * ImageStyleImageInformation objects - containing the Storage::url()
     * without any other information - for unknown styles and missing image.
     */
    public function test_images_information_unknown_styles_missing_image(): void
    {
        $this->assertEquals(
            collect([
                'unknown' => new ImageStyleImageInformation('/storage/missing-image.jpg'),
                'unknown-2' => new ImageStyleImageInformation('/storage/missing-image.jpg'),
            ]),
            $this->imageStyle->imagesInformation(['unknown', 'unknown-2'], 'missing-image.jpg')
        );
    }

    /**
     * Tests if imagesInformation() creates styled images and returns a
     * collection of ImageStyleImageInformation objects, containing the styled
     * images information.
     */
    public function test_images_information_styles_image(): void
    {
        $path = $this->filePaths['local-image'];

        /** @var \Illuminate\Filesystem\FilesystemAdapter $styleFilesystem */
        $styleFilesystem = Storage::disk(config('image-style.filesystem'));

        $styleFilesystemUrl = $styleFilesystem->url('');

        $this->assertEquals(
            collect([
                'default' => new ImageStyleImageInformation(
                    $styleFilesystemUrl.'styles/default/'.$path, 5, 5, 'image/jpeg'
                ),
                'thumbnail' => new ImageStyleImageInformation(
                    $styleFilesystemUrl.'styles/thumbnail/'.$path, 5, 5, 'image/jpeg'
                ),
            ]),
            $this->imageStyle->imagesInformation(['default', 'thumbnail'], $path)
        );
    }

    /**
     * Tests if imagesInformation() creates styled images by styles string and
     * returns a collection of ImageStyleImageInformation objects, containing
     * the styled images information.
     */
    public function test_images_information_string_styles_image(): void
    {
        $path = $this->filePaths['local-image'];

        /** @var \Illuminate\Filesystem\FilesystemAdapter $styleFilesystem */
        $styleFilesystem = Storage::disk(config('image-style.filesystem'));

        $styleFilesystemUrl = $styleFilesystem->url('');

        $this->assertEquals(
            collect([
                'default' => new ImageStyleImageInformation(
                    $styleFilesystemUrl.'styles/default/'.$path, 5, 5, 'image/jpeg'
                ),
                'thumbnail' => new ImageStyleImageInformation(
                    $styleFilesystemUrl.'styles/thumbnail/'.$path, 5, 5, 'image/jpeg'
                ),
            ]),
            $this->imageStyle->imagesInformation('default, thumbnail', $path)
        );
    }

    /**
     * Tests if imagesInformation() creates styled images by mixed
     * (known & unknown) styles and returns a collection of
     * ImageStyleImageInformation objects, containing the styled images
     * information.
     */
    public function test_images_information_mixed_styles_image(): void
    {
        $path = $this->filePaths['local-image'];

        /** @var \Illuminate\Filesystem\FilesystemAdapter $styleFilesystem */
        $styleFilesystem = Storage::disk(config('image-style.filesystem'));

        $styleFilesystemUrl = $styleFilesystem->url('');

        /** @var \Illuminate\Filesystem\FilesystemAdapter $localFilesystem */
        $localFilesystem = Storage::disk('local');

        $this->assertEquals(
            collect([
                'default' => new ImageStyleImageInformation(
                    $styleFilesystemUrl.'styles/default/'.$path, 5, 5, 'image/jpeg'
                ),
                'unknown' => new ImageStyleImageInformation(
                    $localFilesystem->url($path), 5, 5, 'image/jpeg'
                ),
                'thumbnail' => new ImageStyleImageInformation(
                    $styleFilesystemUrl.'styles/thumbnail/'.$path, 5, 5, 'image/jpeg'
                ),
            ]),
            $this->imageStyle->imagesInformation(['default', 'unknown', 'thumbnail'], $path)
        );
    }

    /**
     * Tests if imagesInformation() returns a collection of
     * ImageStyleImageInformation objects - containing the Storage::url()
     * without any other information - for invalid file.
     */
    public function test_images_information_styles_invalid_file(): void
    {
        $path = $this->filePaths['local-document'];

        /** @var \Illuminate\Filesystem\FilesystemAdapter $localFilesystem */
        $localFilesystem = Storage::disk('local');

        $stylePath = $localFilesystem->url($path);

        $this->assertEquals(
            collect([
                'default' => new ImageStyleImageInformation($stylePath),
                'thumbnail' => new ImageStyleImageInformation($stylePath),
            ]),
            $this->imageStyle->imagesInformation(['default', 'thumbnail'], $path)
        );
    }

    /**
     * Tests if imagesInformation(), with parameters, returns a collection of
     * ImageStyleImageInformation objects - containing the original image
     * information and the given parameters - for unknown styles.
     */
    public function test_images_information_unknown_styles_parameters(): void
    {
        $this->assertEquals(
            collect([
                'unknown' => new ImageStyleImageInformation('/storage/images/image.jpg', 5, 5, 'image/jpeg', 'unknown-parameters'),
                'unknown-2' => new ImageStyleImageInformation('/storage/images/image.jpg', 5, 5, 'image/jpeg', 'unknown-2-parameters'),
            ]),
            $this->imageStyle->imagesInformation(['unknown', 'unknown-2'], $this->filePaths['local-image'], informationParameters: [
                'unknown' => 'unknown-parameters',
                'unknown-2' => 'unknown-2-parameters',
            ])
        );
    }

    /**
     * Tests if imagesInformation(), with parameters, returns a collection of
     * ImageStyleImageInformation objects - containing the Storage::url() and
     * the given parameters without any other information - for missing image.
     */
    public function test_images_information_missing_image_parameters(): void
    {
        $this->assertEquals(
            collect([
                'default' => new ImageStyleImageInformation('/storage/missing-image.jpg', parameters: 'default-parameters'),
                'thumbnail' => new ImageStyleImageInformation('/storage/missing-image.jpg', parameters: 'thumbnail-parameters'),
            ]),
            $this->imageStyle->imagesInformation(['default', 'thumbnail'], 'missing-image.jpg', informationParameters: [
                'default' => 'default-parameters',
                'thumbnail' => 'thumbnail-parameters',
            ])
        );
    }

    /**
     * Tests if imagesInformation(), with parameters, returns a collection of
     * ImageStyleImageInformation objects - containing the Storage::url() and
     * the given parameters without any other information - for unknown styles
     * and missing image.
     */
    public function test_images_information_unknown_styles_missing_image_parameters(): void
    {
        $this->assertEquals(
            collect([
                'unknown' => new ImageStyleImageInformation('/storage/missing-image.jpg', parameters: 'unknown-parameters'),
                'unknown-2' => new ImageStyleImageInformation('/storage/missing-image.jpg', parameters: 'unknown-2-parameters'),
            ]),
            $this->imageStyle->imagesInformation(['unknown', 'unknown-2'], 'missing-image.jpg', informationParameters: [
                'unknown' => 'unknown-parameters',
                'unknown-2' => 'unknown-2-parameters',
            ])
        );
    }

    /**
     * Tests if imagesInformation(), with parameters, creates styled images and
     * returns a collection of ImageStyleImageInformation objects, containing
     * the styled images information and the given parameters.
     */
    public function test_images_information_styles_image_parameters(): void
    {
        $path = $this->filePaths['local-image'];

        /** @var \Illuminate\Filesystem\FilesystemAdapter $styleFilesystem */
        $styleFilesystem = Storage::disk(config('image-style.filesystem'));

        $styleFilesystemUrl = $styleFilesystem->url('');

        $this->assertEquals(
            collect([
                'default' => new ImageStyleImageInformation(
                    $styleFilesystemUrl.'styles/default/'.$path, 5, 5, 'image/jpeg', parameters: 'default-parameters'
                ),
                'thumbnail' => new ImageStyleImageInformation(
                    $styleFilesystemUrl.'styles/thumbnail/'.$path, 5, 5, 'image/jpeg', parameters: 'thumbnail-parameters'
                ),
            ]),
            $this->imageStyle->imagesInformation(['default', 'thumbnail'], $path, informationParameters: [
                'default' => 'default-parameters',
                'thumbnail' => 'thumbnail-parameters',
            ])
        );
    }

    /**
     * Tests if imagesInformation() with numeric key parameters creates styled
     * images and returns a collection of ImageStyleImageInformation
     * objects, containing the styled images information and the given
     * parameters.
     */
    public function test_images_information_styles_image_numeric_key_parameters(): void
    {
        $path = $this->filePaths['local-image'];

        /** @var \Illuminate\Filesystem\FilesystemAdapter $styleFilesystem */
        $styleFilesystem = Storage::disk(config('image-style.filesystem'));

        $styleFilesystemUrl = $styleFilesystem->url('');

        $this->assertEquals(
            collect([
                'default' => new ImageStyleImageInformation(
                    $styleFilesystemUrl.'styles/default/'.$path, 5, 5, 'image/jpeg'
                ),
                'thumbnail' => new ImageStyleImageInformation(
                    $styleFilesystemUrl.'styles/thumbnail/'.$path, 5, 5, 'image/jpeg', parameters: 'thumbnail-parameters'
                ),
            ]),
            $this->imageStyle->imagesInformation(['default', 'thumbnail'], $path, informationParameters: [
                1 => 'thumbnail-parameters',
            ])
        );
    }

    /**
     * Tests if imagesInformation(), with parameters, creates styled images by
     * styles string and returns a collection of ImageStyleImageInformation
     * objects, containing the styled images information and the given
     * parameters.
     */
    public function test_images_information_string_styles_image_parameters(): void
    {
        $path = $this->filePaths['local-image'];

        /** @var \Illuminate\Filesystem\FilesystemAdapter $styleFilesystem */
        $styleFilesystem = Storage::disk(config('image-style.filesystem'));

        $styleFilesystemUrl = $styleFilesystem->url('');

        $this->assertEquals(
            collect([
                'default' => new ImageStyleImageInformation(
                    $styleFilesystemUrl.'styles/default/'.$path, 5, 5, 'image/jpeg', parameters: 'default-parameters'
                ),
                'thumbnail' => new ImageStyleImageInformation(
                    $styleFilesystemUrl.'styles/thumbnail/'.$path, 5, 5, 'image/jpeg', parameters: 'thumbnail-parameters'
                ),
            ]),
            $this->imageStyle->imagesInformation('default, thumbnail', $path, informationParameters: [
                'default' => 'default-parameters',
                'thumbnail' => 'thumbnail-parameters',
            ])
        );
    }

    /**
     * Tests if imagesInformation(), with parameters, creates styled images by
     * mixed (known & unknown) styles and returns a collection of
     * ImageStyleImageInformation objects, containing the styled images
     * information and the given parameters.
     */
    public function test_images_information_mixed_styles_image_parameters(): void
    {
        $path = $this->filePaths['local-image'];

        /** @var \Illuminate\Filesystem\FilesystemAdapter $styleFilesystem */
        $styleFilesystem = Storage::disk(config('image-style.filesystem'));

        $styleFilesystemUrl = $styleFilesystem->url('');

        /** @var \Illuminate\Filesystem\FilesystemAdapter $localFilesystem */
        $localFilesystem = Storage::disk('local');

        $this->assertEquals(
            collect([
                'default' => new ImageStyleImageInformation(
                    $styleFilesystemUrl.'styles/default/'.$path, 5, 5, 'image/jpeg', parameters: 'default-parameters'
                ),
                'unknown' => new ImageStyleImageInformation(
                    $localFilesystem->url($path), 5, 5, 'image/jpeg', parameters: 'unknown-parameters'
                ),
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
     * Tests if imagesInformation(), with parameters, returns a collection of
     * ImageStyleImageInformation objects - containing the Storage::url() and
     * the given parameters without any other information - for invalid file.
     */
    public function test_images_information_styles_invalid_file_parameters(): void
    {
        $path = $this->filePaths['local-document'];

        /** @var \Illuminate\Filesystem\FilesystemAdapter $localFilesystem */
        $localFilesystem = Storage::disk('local');

        $stylePath = $localFilesystem->url($path);

        $this->assertEquals(
            collect([
                'default' => new ImageStyleImageInformation($stylePath, parameters: 'default-parameters'),
                'thumbnail' => new ImageStyleImageInformation($stylePath, parameters: 'thumbnail-parameters'),
            ]),
            $this->imageStyle->imagesInformation(['default', 'thumbnail'], $path, informationParameters: [
                'default' => 'default-parameters',
                'thumbnail' => 'thumbnail-parameters',
            ])
        );
    }

    /**
     * Tests if url() returns Storage::url() for unknown style.
     */
    public function test_url_unknown_style(): void
    {
        $this->assertEquals(
            '/storage/images/image.jpg',
            $this->imageStyle->url('unknown', $this->filePaths['local-image'])
        );
    }

    /**
     * Tests if url() returns Storage::url() for missing image.
     */
    public function test_url_missing_image(): void
    {
        $this->assertEquals(
            '/storage/missing-image.jpg',
            $this->imageStyle->url('default', 'missing-image.jpg')
        );
    }

    /**
     * Tests if url() returns Storage::url() for unknown style and missing
     * image.
     */
    public function test_url_unknown_style_missing_image(): void
    {
        $this->assertEquals(
            '/storage/missing-image.jpg',
            $this->imageStyle->url('unknown', 'missing-image.jpg')
        );
    }

    /**
     * Tests if url() creates a styled image and returns the URL.
     */
    public function test_url_style_image(): void
    {
        $path = $this->filePaths['local-image'];

        /** @var \Illuminate\Filesystem\FilesystemAdapter $styleFilesystem */
        $styleFilesystem = Storage::disk(config('image-style.filesystem'));

        $this->assertEquals(
            $styleFilesystem->url('').'styles/default/'.$path,
            $this->imageStyle->url('default', $path)
        );
    }

    /**
     * Tests if url() returns Storage::url() for invalid file.
     */
    public function test_url_style_invalid_file(): void
    {
        $path = $this->filePaths['local-document'];

        /** @var \Illuminate\Filesystem\FilesystemAdapter $localFilesystem */
        $localFilesystem = Storage::disk('local');

        $this->assertEquals(
            $localFilesystem->url($path),
            $this->imageStyle->url('default', $path)
        );
    }

    /**
     * Tests if urls() returns a collection of Storage::url() values for unknown
     * styles.
     */
    public function test_urls_unknown_styles(): void
    {
        $this->assertEquals(
            collect([
                'unknown' => '/storage/images/image.jpg',
                'unknown-2' => '/storage/images/image.jpg',
            ]),
            $this->imageStyle->urls(['unknown', 'unknown-2'], $this->filePaths['local-image'])
        );
    }

    /**
     * Tests if urls() returns a collection of Storage::url() values for missing
     * image.
     */
    public function test_urls_missing_image(): void
    {
        $this->assertEquals(
            collect([
                'default' => '/storage/missing-image.jpg',
                'thumbnail' => '/storage/missing-image.jpg',
            ]),
            $this->imageStyle->urls(['default', 'thumbnail'], 'missing-image.jpg')
        );
    }

    /**
     * Tests if urls() returns a collection of Storage::url() values for unknown
     * styles and missing image.
     */
    public function test_urls_unknown_styles_missing_image(): void
    {
        $this->assertEquals(
            collect([
                'unknown' => '/storage/missing-image.jpg',
                'unknown-2' => '/storage/missing-image.jpg',
            ]),
            $this->imageStyle->urls(['unknown', 'unknown-2'], 'missing-image.jpg')
        );
    }

    /**
     * Tests if urls() creates styled images and returns the URLs.
     */
    public function test_urls_styles_image(): void
    {
        $path = $this->filePaths['local-image'];

        /** @var \Illuminate\Filesystem\FilesystemAdapter $styleFilesystem */
        $styleFilesystem = Storage::disk(config('image-style.filesystem'));

        $styleFilesystemUrl = $styleFilesystem->url('');

        $this->assertEquals(
            collect([
                'default' => $styleFilesystemUrl.'styles/default/'.$path,
                'thumbnail' => $styleFilesystemUrl.'styles/thumbnail/'.$path,
            ]),
            $this->imageStyle->urls(['default', 'thumbnail'], $path)
        );
    }

    /**
     * Tests if urls() creates styled images by styles string and returns the
     * URLs.
     */
    public function test_urls_string_styles_image(): void
    {
        $path = $this->filePaths['local-image'];

        /** @var \Illuminate\Filesystem\FilesystemAdapter $styleFilesystem */
        $styleFilesystem = Storage::disk(config('image-style.filesystem'));

        $styleFilesystemUrl = $styleFilesystem->url('');

        $this->assertEquals(
            collect([
                'default' => $styleFilesystemUrl.'styles/default/'.$path,
                'thumbnail' => $styleFilesystemUrl.'styles/thumbnail/'.$path,
            ]),
            $this->imageStyle->urls('default, thumbnail', $path)
        );
    }

    /**
     * Tests if urls() creates styled images by mixed (known & unknown) styles
     * and returns the URLs.
     */
    public function test_urls_mixed_styles_image(): void
    {
        $path = $this->filePaths['local-image'];

        /** @var \Illuminate\Filesystem\FilesystemAdapter $styleFilesystem */
        $styleFilesystem = Storage::disk(config('image-style.filesystem'));

        $styleFilesystemUrl = $styleFilesystem->url('');

        /** @var \Illuminate\Filesystem\FilesystemAdapter $localFilesystem */
        $localFilesystem = Storage::disk('local');

        $this->assertEquals(
            collect([
                'default' => $styleFilesystemUrl.'styles/default/'.$path,
                'unknown' => $localFilesystem->url($path),
                'thumbnail' => $styleFilesystemUrl.'styles/thumbnail/'.$path,
            ]),
            $this->imageStyle->urls(['default', 'unknown', 'thumbnail'], $path)
        );
    }

    /**
     * Tests if urls() returns a collection of Storage::url() values for invalid
     * file.
     */
    public function test_urls_styles_invalid_file(): void
    {
        $path = $this->filePaths['local-document'];

        /** @var \Illuminate\Filesystem\FilesystemAdapter $localFilesystem */
        $localFilesystem = Storage::disk('local');

        $stylePath = $localFilesystem->url($path);

        $this->assertEquals(
            collect([
                'default' => $stylePath,
                'thumbnail' => $stylePath,
            ]),
            $this->imageStyle->urls(['default', 'thumbnail'], $path)
        );
    }

    /**
     * Tests if preview() throws an \InvalidArgumentException for unknown style.
     */
    public function test_preview_unknown_style(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->expectExceptionMessage('The given image style does not exist.');

        $this->imageStyle->preview('unknown');
    }

    /**
     * Tests if preview() returns a default image (preview.jpg) response for
     * missing image.
     */
    public function test_preview_missing_image(): void
    {
        $response = $this->imageStyle->preview('default', 'missing-image.jpg');

        $this->assertTrue(
            $response instanceof Response &&
            $response->headers->get('Content-Disposition') === 'inline; filename="preview.jpg"'
        );
    }

    /**
     * Tests if preview() throws an \InvalidArgumentException for unknown style
     * and missing image.
     */
    public function test_preview_unknown_style_missing_image(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->expectExceptionMessage('The given image style does not exist.');

        $this->imageStyle->preview('unknown', 'missing-image.jpg');
    }

    /**
     * Tests if preview() returns a default image (preview.jpg) response.
     */
    public function test_preview_style(): void
    {
        $response = $this->imageStyle->preview('default');

        $this->assertTrue(
            $response instanceof Response &&
            $response->headers->get('Content-Disposition') === 'inline; filename="preview.jpg"'
        );
    }

    /**
     * Tests if preview() returns a response for the given image.
     */
    public function test_preview_style_image(): void
    {
        $path = $this->filePaths['local-image'];

        $response = $this->imageStyle->preview('default', $path);

        $this->assertTrue(
            $response instanceof Response &&
            $response->headers->get('Content-Disposition') === 'inline; filename="'.basename($path).'"'
        );
    }

    /**
     * Tests if preview() returns a response for the given image at specific
     * disk.
     */
    public function test_preview_style_image_disk(): void
    {
        $path = $this->filePaths['public-image'];

        $response = $this->imageStyle->preview('default', $path, disk: 'public');

        $this->assertTrue(
            $response instanceof Response &&
            $response->headers->get('Content-Disposition') === 'inline; filename="'.basename($path).'"'
        );
    }

    /**
     * Tests if preview() returns a default image (preview.jpg) response for
     * invalid file.
     */
    public function test_preview_style_invalid_file(): void
    {
        $response = $this->imageStyle->preview('default', $this->filePaths['local-document']);

        $this->assertTrue(
            $response instanceof Response &&
            $response->headers->get('Content-Disposition') === 'inline; filename="preview.jpg"'
        );
    }
}
