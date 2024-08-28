<?php

namespace Phpsa\FilamentHeadlessCms\Filament\PageBlocks;

use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Builder\Block;
use Phpsa\FilamentHeadlessCms\Contracts\PageBlock;
use Phpsa\FilamentHeadlessCms\Filament\Fields\Editor;

class CmsRichTextBlock extends PageBlock
{

    public static function getBlockSchema(): Block
    {

        return Block::make('cms_rich_text_block')
            ->icon('heroicon-o-rectangle-stack')
            ->schema([
                Editor::make('content')
                    ->label('Content')

            ])
            ->preview('filament-headless-cms::components.page-builder.preview.heading')
            ->columns(1)
            ->label('Rich Text');
    }

    public static function mutateData(array $data): array
    {
        return $data;
    }
}
