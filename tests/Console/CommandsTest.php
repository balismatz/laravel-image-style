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
     * Tests whether the cache command caches image style information.
     */
    public function test_cache_command_caches_image_style_information(): void
    {
        $this->artisan('image-style:cache')->assertSuccessful();

        $this->assertTrue(
            Cache::store(config('image-style.cache'))->has($this->imageStyleManager->getCacheKey())
        );
    }

    /**
     * Tests whether the clear command removes cached image style information.
     */
    public function test_clear_command_removes_cached_image_style_information(): void
    {
        $this->artisan('image-style:clear')->assertSuccessful();

        $this->assertFalse(
            Cache::store(config('image-style.cache'))->has($this->imageStyleManager->getCacheKey())
        );
    }

    /**
     * Tests whether the flush command flushes all styled images.
     */
    public function test_flush_command_flushes_all_styled_images(): void
    {
        $this->artisan('image-style:flush all default')->assertSuccessful();
    }

    /**
     * Tests whether the list command lists image style information.
     */
    public function test_list_command_lists_image_style_information(): void
    {
        $this->artisan('image-style:list')->assertSuccessful();
    }

    /**
     * Tests whether the optimize command caches image style information.
     */
    public function test_optimize_command_caches_image_style_information(): void
    {
        $this->artisan('optimize')->assertSuccessful();

        $this->assertTrue(
            Cache::store(config('image-style.cache'))->has($this->imageStyleManager->getCacheKey())
        );
    }

    /**
     * Tests whether the optimize clear command removes cached image style
     * information.
     */
    public function test_optimize_clear_command_removes_cached_image_style_information(): void
    {
        $this->artisan('optimize:clear')->assertSuccessful();

        $this->assertFalse(
            Cache::store(config('image-style.cache'))->has($this->imageStyleManager->getCacheKey())
        );
    }
}
