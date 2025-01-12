<?php

namespace BalisMatz\ImageStyle\Information;

class ImageStyleInformation
{
    /**
     * Create a new image style information instance.
     */
    public function __construct(
        public protected(set) string $class,
        public protected(set) string $id,
        public protected(set) ?string $help,
        public protected(set) bool $active,
    ) {}
}
