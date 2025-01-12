<?php

namespace BalisMatz\ImageStyle\Information;

class ImageStyleImageInformation
{
    /**
     * Create a new image style image information instance.
     */
    public function __construct(
        public protected(set) string $url,
        public protected(set) ?int $height = null,
        public protected(set) ?int $width = null,
        public protected(set) ?string $mimetype = null,
        public protected(set) mixed $parameters = null,
    ) {}
}
