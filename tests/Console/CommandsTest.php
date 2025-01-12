<?php

namespace BalisMatz\ImageStyle\Tests\Console;

use BalisMatz\ImageStyle\ImageStyleManager;
use BalisMatz\ImageStyle\Tests\ImageStyleTestCase;
use Illuminate\Support\Facades\Cache;

class CommandsTest extends ImageStyleTestCase
{
    /**
     * The image style manager.
     */
    protected ImageStyleManager $imageStyleManager;

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        $this->afterApplicationCreated(function () {
            $this->imageStyleManager = $this->app->make(ImageStyleManager::class);
        });

        parent::setUp();
    }

    /**
     * Tests the cache command.
     */
    public function test_cache(): void
    {
        $this->artisan('image-style:cache')->assertSuccessful();

        $this->assertTrue(
            Cache::has($this->imageStyleManager->getCacheKey())
        );
    }

    /**
     * Tests the clear command.
     */
    public function test_clear(): void
    {
        $this->artisan('image-style:clear')->assertSuccessful();

        $this->assertFalse(
            Cache::has($this->imageStyleManager->getCacheKey())
        );
    }

    /**
     * Tests the very basic functionality of flush command.
     */
    public function test_flush(): void
    {
        $this->artisan('image-style:flush all default')->assertSuccessful();
    }

    /**
     * Tests the very basic functionality of list command.
     */
    public function test_list(): void
    {
        $this->artisan('image-style:list')->assertSuccessful();
    }
}
