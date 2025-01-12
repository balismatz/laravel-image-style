<?php

namespace BalisMatz\ImageStyle\Console\Commands;

use BalisMatz\ImageStyle\ImageStyleManager;
use BalisMatz\ImageStyle\Information\ImageStyleInformation;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Filesystem\FilesystemManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;

use function Laravel\Prompts\search;
use function Laravel\Prompts\select;

#[AsCommand(name: 'image-style:flush')]
class ImageStyleFlushCommand extends Command implements PromptsForMissingInput
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'image-style:flush';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Flush all images with style';

    /**
     * Create a new image style flush command instance.
     *
     * @return void
     */
    public function __construct(
        protected ImageStyleManager $imageStyleManager,
        protected FilesystemManager $filesystemManager,
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $directory = match ($style = $this->argument('style')) {
            'all' => 'styles',
            default => "styles/{$style}"
        };

        $disk = match ($disk = $this->argument('disk')) {
            'default' => config('image-style.filesystem'),
            'filesystems.default' => config($disk),
            default => $disk
        };

        if (! $this->filesystemManager->disk($disk)->deleteDirectory($directory)) {
            $this->components->error('There was an issue flushing images. Please try again.');

            return;
        }

        $this->components->info(match ($style) {
            'all' => sprintf('All image styles at %s disk flushed successfully.', $disk),
            default => sprintf('Image style "%s" at %s disk flushed successfully.', $style, $disk),
        });
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['style', InputArgument::REQUIRED, 'The style ID or "all" to flush all styles'],
            ['disk', InputArgument::REQUIRED, 'The disk that styled images exist'],
        ];
    }

    /**
     * Prompt for missing input arguments using the returned questions.
     *
     * @return array
     */
    protected function promptForMissingArgumentsUsing()
    {
        return [
            'style' => fn () => search(
                'Search for a style ID or "all styles" to flush all styles',
                fn (string $value): array => $value ? $this->imageStyleManager->all()
                    ->prepend(['id' => 'all styles'], 'all')
                    ->map(fn (ImageStyleInformation|array $styleInfo): string => data_get($styleInfo, 'id'))
                    ->filter(fn (string $styleId): bool => str_contains($styleId, mb_strtolower($value)))
                    ->all() : [],
                'E.g. all styles',
            ),
            'disk' => fn () => select(
                'Select the disk that styled images exist',
                collect(config('filesystems.disks'))
                    ->map(fn (array $item, string $key): string => $key)
                    ->prepend('default: application', 'filesystems.default')
                    ->prepend('default: image styles', 'default'),
            ),
        ];
    }
}
