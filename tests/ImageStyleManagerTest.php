<?php

namespace BalisMatz\ImageStyle\Tests;

use BalisMatz\ImageStyle\ImageStyleManager;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Once;

class ImageStyleManagerTest extends ImageStyleTestCase
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
     * Tests if styles can be detected.
     */
    public function test_styles_detection(): void
    {
        $this->assertEquals(9, $this->imageStyleManager->all()->count());
    }

    /**
     * Tests if styles information can be cached.
     */
    public function test_styles_cache(): void
    {
        $uncachedStyles = $this->imageStyleManager->all();

        $this->artisan('image-style:cache')->run();

        Once::flush();

        Cache::shouldReceive('get')
            ->once()
            ->with($this->imageStyleManager->getCacheKey())
            ->andReturn($uncachedStyles);

        $this->imageStyleManager->all();
    }

    /**
     * Tests if a style - without ID detection - can be assigned as "default".
     */
    public function test_style_default_id(): void
    {
        $style = $this->imageStyleManager->get('default');

        $this->assertEquals('App\ImageStyles\ImageStyle', $style?->class);
    }

    /**
     * Tests if a style can be assigned with it's custom ID.
     */
    public function test_style_custom_id(): void
    {
        $style = $this->imageStyleManager->get('custom-image-style-id');

        $this->assertEquals('App\ImageStyles\CustomIdImageStyle', $style?->class);
    }

    /**
     * Tests if the inactive style can not be returned (as an active one).
     */
    public function test_inactive_style(): void
    {
        $style = $this->imageStyleManager->get('App\ImageStyles\InactiveImageStyle');

        $this->assertEmpty($style);
    }

    /**
     * Tests if style's help text can be detected.
     */
    public function test_style_help_text(): void
    {
        $style = $this->imageStyleManager->get('thumbnail');

        $this->assertEquals('Resize image to 100px', $style?->help);
    }

    /**
     * Tests if style without "ImageStyle" suffix can be assigned with right ID.
     */
    public function test_style_without_suffix(): void
    {
        $style = $this->imageStyleManager->get('user-thumbnail');

        $this->assertEquals('App\ImageStyles\UserThumbnail', $style?->class);
    }

    /**
     * Tests if a style in directory level 1 can be assigned with the right ID.
     */
    public function test_style_directory_level_1(): void
    {
        $style = $this->imageStyleManager->get('App\ImageStyles\Posts\ThumbnailImageStyle');

        $this->assertEquals('posts-thumbnail', $style?->id);
    }

    /**
     * Tests if a style in directory level 2 can be assigned with the right ID.
     */
    public function test_style_directory_level_2(): void
    {
        $style = $this->imageStyleManager->get('App\ImageStyles\Posts\Show\ThumbnailImageStyle');

        $this->assertEquals('posts-show-thumbnail', $style?->id);
    }

    /**
     * Tests if a style in directory level 3 can be assigned with the right ID.
     */
    public function test_style_directory_level_3(): void
    {
        $style = $this->imageStyleManager->get('App\ImageStyles\Posts\Show\Gallery\ThumbnailImageStyle');

        $this->assertEquals('posts-show-gallery-thumbnail', $style?->id);
    }

    /**
     * Tests if style can not be detected when it's placed to unsupported directory.
     */
    public function test_style_unsupported_directory_level(): void
    {
        $style = $this->imageStyleManager->get('App\ImageStyles\Posts\Show\Gallery\Item\ThumbnailImageStyle');

        $this->assertEmpty($style);
    }

    /**
     * Tests if a style can be skipped when there are ID conflicts.
     */
    public function test_style_conflict(): void
    {
        $style = $this->imageStyleManager->get('posts-conflict');

        $this->assertNotEquals('App\ImageStyles\Posts\ConflictImageStyle', $style?->class);

        $this->assertEquals('App\ImageStyles\PostsConflictImageStyle', $style?->class);
    }
}
