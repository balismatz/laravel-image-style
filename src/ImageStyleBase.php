<?php

namespace BalisMatz\ImageStyle;

use Illuminate\Image\Image;

abstract class ImageStyleBase
{
    /**
     * The image manipulations.
     *
     * @see https://laravel.com/docs/13.x/images#manipulating-images
     */
    abstract public function manipulations(Image $image, mixed $parameters): Image;
}
