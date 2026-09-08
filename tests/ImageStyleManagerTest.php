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
     * Tests whether all image styles can be detected.
     */
    public function test_all_styles_can_be_detected(): void
    {
        $this->assertEquals(9, $this->imageStyleManager->all()->count());
    }

    /**
     * Tests whether image style information can be cached.
     */
    public function test_style_information_can_be_cached(): void
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
     * Tests whether an image style without a valid ID gets the ID "default".
     */
    public function test_invalid_style_id_gets_default_id(): void
    {
        $style = $this->imageStyleManager->get('default');

        $this->assertEquals('App\ImageStyles\ImageStyle', $style?->class);
    }

    /**
     * Tests whether an image style can have a custom ID.
     */
    public function test_style_can_have_custom_id(): void
    {
        $style = $this->imageStyleManager->get('custom-image-style-id');

        $this->assertEquals('App\ImageStyles\CustomIdImageStyle', $style?->class);
    }

    /**
     * Tests whether an inactive image style is not returned.
     */
    public function test_inactive_style_is_not_returned(): void
    {
        $style = $this->imageStyleManager->get('App\ImageStyles\InactiveImageStyle');

        $this->assertEmpty($style);
    }

    /**
     * Tests whether an image style's help text can be detected.
     */
    public function test_style_help_text_can_be_detected(): void
    {
        $style = $this->imageStyleManager->get('thumbnail');

        $this->assertEquals('Resize image to 100px', $style?->help);
    }

    /**
     * Tests whether an image style without the "ImageStyle" suffix gets the
     * correct ID.
     */
    public function test_style_without_suffix_gets_correct_id(): void
    {
        $style = $this->imageStyleManager->get('user-thumbnail');

        $this->assertEquals('App\ImageStyles\UserThumbnail', $style?->class);
    }

    /**
     * Tests whether an image style at directory level 1 gets the correct ID.
     */
    public function test_style_at_directory_level_1_gets_correct_id(): void
    {
        $style = $this->imageStyleManager->get('App\ImageStyles\Post\ThumbnailImageStyle');

        $this->assertEquals('post-thumbnail', $style?->id);
    }

    /**
     * Tests whether an image style at directory level 2 gets the correct ID.
     */
    public function test_style_at_directory_level_2_gets_correct_id(): void
    {
        $style = $this->imageStyleManager->get('App\ImageStyles\Post\Show\ThumbnailImageStyle');

        $this->assertEquals('post-show-thumbnail', $style?->id);
    }

    /**
     * Tests whether an image style at directory level 3 gets the correct ID.
     */
    public function test_style_at_directory_level_3_gets_correct_id(): void
    {
        $style = $this->imageStyleManager->get('App\ImageStyles\Post\Show\Gallery\ThumbnailImageStyle');

        $this->assertEquals('post-show-gallery-thumbnail', $style?->id);
    }

    /**
     * Tests whether an image style is not detected when it is placed in an
     * unsupported directory.
     */
    public function test_style_in_unsupported_directory_level_is_not_detected(): void
    {
        $style = $this->imageStyleManager->get('App\ImageStyles\Post\Show\Gallery\Item\ThumbnailImageStyle');

        $this->assertEmpty($style);
    }

    /**
     * Tests whether an image style is skipped when its ID conflicts with
     * another image style ID.
     */
    public function test_style_with_conflicting_id_is_skipped(): void
    {
        $style = $this->imageStyleManager->get('post-conflict');

        $this->assertNotEquals('App\ImageStyles\Post\ConflictImageStyle', $style?->class);

        $this->assertEquals('App\ImageStyles\PostConflictImageStyle', $style?->class);
    }
}
