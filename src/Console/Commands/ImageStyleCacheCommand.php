<?php

namespace BalisMatz\ImageStyle\Console\Commands;

use BalisMatz\ImageStyle\ImageStyleManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'image-style:cache')]
class ImageStyleCacheCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'image-style:cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache the image style information';

    /**
     * Create a new image style cache command instance.
     *
     * @return void
     */
    public function __construct(
        protected ImageStyleManager $imageStyleManager,
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->callSilent('image-style:clear');

        $styles = $this->imageStyleManager->all();

        if (! $styles->count()) {
            return $this->components->error("Your application doesn't have any image styles.");
        }

        Cache::store(config('image-style.cache'))->forever(
            $this->imageStyleManager->getCacheKey(),
            $styles
        );

        $this->components->info('Image style information cached successfully.');
    }
}
