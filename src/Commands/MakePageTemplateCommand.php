<?php

namespace Phpsa\FilamentHeadlessCms\Commands;

use Filament\Support\Commands\Concerns\CanIndentStrings;
use Filament\Support\Commands\Concerns\CanManipulateFiles;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

use function Laravel\Prompts\text;

class MakepageTemplateCommand extends Command
{
    use CanIndentStrings;
    use CanManipulateFiles;

    protected $signature = 'make:fhcms-page-template {name?} {--F|force}';

    protected $description = 'Create a new filament cms page template';

    public function handle(): int
    {
        $pageTemplate = (string) Str::of($this->argument('name') ?? text(label: 'Name (e.g. `BlogTemplate`)', required: true))
            ->trim('/')
            ->trim('\\')
            ->trim(' ')
            ->replace('/', '\\');

        $pageTemplateClass = (string) Str::of($pageTemplate)->afterLast('\\');

        $pageTemplateNamespace = Str::of($pageTemplate)->contains('\\') ?
        (string) Str::of($pageTemplate)->beforeLast('\\') : '';

        $label = Str::of($pageTemplate)
            ->beforeLast('Template')
            ->explode('\\')
            ->map(fn ($segment) => Str::title($segment))
            ->implode(': ');

        $shortName = Str::of($pageTemplate)
            ->beforeLast('Template')
            ->explode('\\')
            ->map(fn ($segment) => Str::kebab($segment))
            ->implode('.');

        // $view = Str::of($pageTemplate)
        //     ->beforeLast('Template')
        //     ->prepend('components\\page-blocks\\')
        //     ->explode('\\')
        //     ->map(fn ($segment) => Str::kebab($segment))
        //     ->implode('.');

        $path = app_path(
            (string) Str::of($pageTemplate)
                ->prepend('Filament\\PageTemplates\\')
                ->replace('\\', '/')
                ->append('.php'),
        );

        // $viewPath = resource_path(
        //     (string) Str::of($view)
        //         ->replace('.', '/')
        //         ->prepend('views/')
        //         ->append('.blade.php'),
        // );

        $files = [
            $path,
     //       $viewPath
        ];

        if (! $this->option('force') && $this->checkForCollision($files)) {
            return static::INVALID;
        }

        $this->copyStubToApp('pageTemplate', $path, [
            'class'     => $pageTemplateClass,
            'namespace' => 'App\\Filament\\PageTemplates' . ($pageTemplateNamespace !== '' ? "\\{$pageTemplateNamespace}" : ''),
            'label'     => $label,
            'shortName' => $shortName,
        ]);

     //   $this->copyStubToApp('pageTemplateView', $viewPath);

        $this->info("Successfully created {$pageTemplate}!");

        return static::SUCCESS;
    }
}
