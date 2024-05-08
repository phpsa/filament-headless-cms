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
use Phpsa\FilamentHeadlessCms\Filament\Fields\Editor;
use Illuminate\Contracts\Container\BindingResolutionException;

class DefaultTemplate extends PageTemplate
{
    protected static bool $publishDates = false;

    public static function title(): string
    {
        return 'Simple Page';
    }

    /**
     * @return array<int|string, Component>
     */
    public static function schema(): array
    {
        return [
            Section::make()
                ->schema([
                    Editor::make('content')
                    ->label('Content'),
                ])
        ];
    }
}
