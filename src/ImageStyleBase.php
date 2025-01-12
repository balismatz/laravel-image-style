<?php

namespace BalisMatz\ImageStyle;

use Intervention\Image\Interfaces\EncodedImageInterface;
use Intervention\Image\Interfaces\ImageInterface;

abstract class ImageStyleBase
{
    /**
     * Get the image modifications.
     */
    abstract public function modifications(ImageInterface $image, array $parameters): ImageInterface|EncodedImageInterface;

    /**
     * Get the image quality.
     *
     * @return int
     *             The image quality, from 0 to 100.
     */
    public function quality(array $parameters): int
    {
        return config('image-style.options.quality');
    }
}
