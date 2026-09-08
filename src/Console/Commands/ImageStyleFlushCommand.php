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
    protected $description = 'Flush all styled images';

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
    public function handle(): void
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
            $this->components->error('Unable to flush images. Please try again.');

            return;
        }

        $this->components->info(match ($style) {
            'all' => sprintf('All image styles on the "%s" disk were flushed successfully.', $disk),
            default => sprintf('Image style "%s" on the "%s" disk was flushed successfully.', $style, $disk),
        });
    }

    /**
     * Get the console command arguments.
     */
    protected function getArguments(): array
    {
        return [
            ['style', InputArgument::REQUIRED, 'The style ID or "all" to flush all styles'],
            ['disk', InputArgument::REQUIRED, 'The disk where styled images exist'],
        ];
    }

    /**
     * Prompt for missing input arguments using the returned questions.
     */
    protected function promptForMissingArgumentsUsing(): array
    {
        /** @var array<string, mixed> $disks */
        $disks = config('filesystems.disks');

        return [
            'style' => fn () => search(
                'Search for a style ID or type "all styles" to flush all styles',
                fn (string $value): array => $value ? $this->imageStyleManager->all()
                    /** @phpstan-ignore argument.type */
                    ->prepend(['id' => 'all styles'], 'all')
                    ->map(fn (ImageStyleInformation|array $styleInfo): string => data_get($styleInfo, 'id'))
                    ->filter(fn (string $styleId): bool => str_contains($styleId, mb_strtolower($value)))
                    ->all() : [],
                'E.g. all styles',
            ),
            'disk' => fn () => select(
                'Select the disk where styled images exist',
                collect($disks)
                    ->map(fn (array $item, string $key): string => $key)
                    ->prepend('default: application', 'filesystems.default')
                    ->prepend('default: image styles', 'default')
                    ->all(),
            ),
        ];
    }
}
