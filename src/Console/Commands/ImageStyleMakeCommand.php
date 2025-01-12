<?php

namespace BalisMatz\ImageStyle\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

#[AsCommand(name: 'image-style:make')]
class ImageStyleMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'image-style:make';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new image style';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Image Style';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        if ($this->option('id') || $this->option('help-text') || $this->option('active')) {
            return $this->resolveStubPath('/stubs/image-style.attribute.stub');
        }

        return $this->resolveStubPath('/stubs/image-style.stub');
    }

    /**
     * Resolve the fully-qualified path to the stub.
     *
     * @param  string  $stub
     * @return string
     */
    protected function resolveStubPath($stub)
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : __DIR__.'/../../..'.$stub;
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\\ImageStyles';
    }

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function buildClass($name)
    {
        $attributeParameters = collect($this->options())
            ->filter(function (mixed $value, string $key): bool {
                return $value && in_array($key, ['id', 'help-text', 'active'], true);
            })
            ->map(function (string $value, string $key): string {
                if ($key === 'help-text') {
                    $key = 'help';
                }

                return match ($key) {
                    'active' => $key.': '.($value === 'false' ? 'false' : 'true'),
                    default => "{$key}: '{$value}'"
                };
            })
            ->implode(', ');

        if ($attributeParameters) {
            return str_replace(
                '{{ attributeParameters }}',
                $attributeParameters,
                parent::buildClass($name)
            );
        }

        return parent::buildClass($name);
    }

    /**
     * Prompt for missing input arguments using the returned questions.
     *
     * @return array
     */
    protected function promptForMissingArgumentsUsing()
    {
        $prompt = parent::promptForMissingArgumentsUsing();

        $prompt['name'][1] = 'E.g. ThumbnailImageStyle';

        return $prompt;
    }

    /**
     * Interact further with the user if they were prompted for missing arguments.
     *
     * @return void
     */
    protected function afterPromptingForMissingArguments(InputInterface $input, OutputInterface $output)
    {
        if ($this->isReservedName($this->getNameInput()) || $this->didReceiveOptions($input)) {
            return;
        }

        collect([
            'id' => text('ID', 'E.g. thumbnail'),
            'help-text' => text('Help text', 'E.g. Resize image to 100px'),
            'active' => select('Status', [
                null => 'Default',
                'true' => 'Active',
                'false' => 'Disabled',
            ]),
        ])
            ->filter()
            ->each(fn (string $value, string $name) => $input->setOption($name, $value));
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['id', null, InputOption::VALUE_OPTIONAL, 'Set a custom ID'],
            ['help-text', null, InputOption::VALUE_OPTIONAL, 'Set a help text'],
            ['active', null, InputOption::VALUE_OPTIONAL, 'Set the status'],
        ];
    }
}
