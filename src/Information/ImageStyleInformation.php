<?php

namespace BalisMatz\ImageStyle\Information;

final readonly class ImageStyleInformation
{
    /**
     * Create a new image style information instance.
     */
    public function __construct(
        public string $class,
        public string $id,
        public ?string $help,
        public bool $active,
    ) {}
}
