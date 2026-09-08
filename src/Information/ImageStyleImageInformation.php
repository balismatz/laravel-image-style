<?php

namespace BalisMatz\ImageStyle\Information;

final readonly class ImageStyleImageInformation
{
    /**
     * Create a new image style image information instance.
     */
    public function __construct(
        public string $url,
        public ?int $height = null,
        public ?int $width = null,
        public ?string $mimetype = null,
        public mixed $parameters = null,
    ) {}
}
