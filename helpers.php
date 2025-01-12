<?php

use BalisMatz\ImageStyle\ImageStyle;

if (! function_exists('imageStyle')) {
    /**
     * Get the image style.
     */
    function imageStyle(): ImageStyle
    {
        return app(ImageStyle::class);
    }
}
