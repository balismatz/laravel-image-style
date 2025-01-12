<?php

namespace BalisMatz\ImageStyle\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class ImageStyle
{
    /**
     * Create a new image style attribute instance.
     */
    public function __construct(
        public ?string $id = null,
        public ?string $help = null,
        public bool $active = true,
    ) {}
}
