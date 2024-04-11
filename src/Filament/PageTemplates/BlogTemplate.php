<?php

namespace Phpsa\FilamentHeadlessCms\Filament\PageTemplates;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\MarkdownEditor;
use Phpsa\FilamentHeadlessCms\Contracts\PageTemplate;

class BlogTemplate implements PageTemplate
{
    public static function title(): string
    {
        return 'Simple Blog';
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
                    TextInput::make('author'),
                ]),
        ];
    }
}
