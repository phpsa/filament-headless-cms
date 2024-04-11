<?php

namespace Phpsa\FilamentHeadlessCms\Filament\PageTemplates;

use TypeError;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\MarkdownEditor;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Container\ContainerExceptionInterface;
use Phpsa\FilamentHeadlessCms\Contracts\PageTemplate;
use Illuminate\Contracts\Container\BindingResolutionException;

class DefaultTemplate implements PageTemplate
{
    public static function title(): string
    {
        return 'Default';
    }

    /**
     * @return array<int|string, Component>
     */
    public static function schema(): array
    {
        return [
            Section::make()
                ->schema([
                    MarkdownEditor::make('content')
                        ->label(__('Content')),
                ]),
        ];
    }
}
