<?php

namespace Phpsa\FilamentHeadlessCms\Filament\PageTemplates;

use TypeError;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\MarkdownEditor;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Container\ContainerExceptionInterface;
use Phpsa\FilamentHeadlessCms\Contracts\PageTemplate;
use Illuminate\Contracts\Container\BindingResolutionException;

class DefaultTemplate extends PageTemplate
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
                    Builder::make('content2')
        ->blocks([
            Block::make('heading')
            ->schema([
                TextInput::make('content')
                ->label('Heading')
                ->required(),
                Select::make('level')
                ->options([
                    'h1' => 'Heading 1',
                    'h2' => 'Heading 2',
                    'h3' => 'Heading 3',
                    'h4' => 'Heading 4',
                    'h5' => 'Heading 5',
                    'h6' => 'Heading 6',
                ])
                ->required(),
            ])
                    ->columns(2),
            Block::make('paragraph')
                    ->schema([
                        Textarea::make('content')
                    ->label('Paragraph')
                    ->required(),
                    ]),
            Block::make('image')
                    ->schema([
                        FileUpload::make('url')
                    ->label('Image')
                    ->image()
                    ->required(),
                        TextInput::make('alt')
                    ->label('Alt text')
                    ->required(),
                    ]),
        ])
                ]),
        ];
    }
}
