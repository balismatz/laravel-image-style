<?php

namespace BalisMatz\ImageStyle\Console\Commands;

use BalisMatz\ImageStyle\ImageStyleManager;
use BalisMatz\ImageStyle\Information\ImageStyleInformation;
use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Terminal;

#[AsCommand(name: 'image-style:list')]
class ImageStyleListCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'image-style:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all registered image styles';

    /**
     * Create a new image style list command instance.
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
        $styles = $this->imageStyleManager->all();

        if (! $styles->count()) {
            return $this->components->error("Your application doesn't have any image styles.");
        }

        $terminalWidth = (new Terminal)->getWidth();

        $stylesSort = match ($sort = $this->option('sort')) {
            'active', 'class', 'help', 'id' => $sort,
            default => 'id'
        };

        $styles = match ($stylesSort) {
            'active' => $styles->sortBy($stylesSort, descending: true),
            default => $styles->sortBy($stylesSort, SORT_NATURAL)
        };

        $lastStyleId = $styles->keys()->last();

        $styles->each(function (ImageStyleInformation $style, string $styleId) use ($terminalWidth, $lastStyleId) {
            $styleStatus = $style->active;

            $styleStatusText = match ($styleStatus) {
                false => 'Inactive',
                default => 'Active'
            };

            $this->line(
                sprintf(
                    '<fg=blue>%s</> %s <fg=%s>%s</>',
                    $styleId,
                    str_repeat(
                        '.',
                        max($terminalWidth - mb_strlen($styleId) - mb_strlen($styleStatusText) - 2, 0)
                    ),
                    match ($styleStatus) {
                        false => 'red',
                        default => 'green'
                    },
                    $styleStatusText
                )
            );

            $this->line(
                sprintf('<fg=#6C7280>class:</> %s', $style->class)
            );

            if ($styleHelp = $style->help) {
                $this->line(
                    sprintf('<fg=#6C7280>help:</>  %s', $styleHelp)
                );
            }

            if ($styleId !== $lastStyleId) {
                $this->newLine();
            }
        });
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['sort', null, InputOption::VALUE_OPTIONAL, 'The image style information key (id, class, help, active) to sort by', 'id'],
        ];
    }
}
