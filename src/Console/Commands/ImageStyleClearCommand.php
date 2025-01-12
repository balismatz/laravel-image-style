<?php

namespace BalisMatz\ImageStyle\Console\Commands;

use BalisMatz\ImageStyle\ImageStyleManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'image-style:clear')]
class ImageStyleClearCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'image-style:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove the cached image style information';

    /**
     * Create a new image style clear command instance.
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
        Cache::store(config('image-style.cache'))->forget(
            $this->imageStyleManager->getCacheKey()
        );

        $this->components->info('Image style cache cleared successfully.');
    }
}
