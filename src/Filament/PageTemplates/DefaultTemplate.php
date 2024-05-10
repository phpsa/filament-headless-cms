<?php

namespace Phpsa\FilamentHeadlessCms\Filament\PageTemplates;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\Component;
use Phpsa\FilamentHeadlessCms\Contracts\PageTemplate;
use Phpsa\FilamentHeadlessCms\Filament\Fields\Editor;

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
