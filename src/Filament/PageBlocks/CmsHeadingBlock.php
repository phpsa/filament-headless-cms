<?php

namespace Phpsa\FilamentHeadlessCms\Filament\PageBlocks;

use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Builder\Block;
use Phpsa\FilamentHeadlessCms\Contracts\PageBlock;

class CmsHeadingBlock extends PageBlock
{

    public static function getBlockSchema(): Block
    {

        return Block::make('cms_heading_block')
            ->icon('heroicon-o-rectangle-stack')
            ->schema([
                Select::make('level')
                ->options([
                    'h1' => 'Heading 1',
                    'h2' => 'Heading 2',
                    'h3' => 'Heading 3',
                    'h4' => 'Heading 4',
                    'h5' => 'Heading 5',
                    'h6' => 'Heading 6',
                ])
                ->required()->columnSpan(['sm' => 1]),

                TextInput::make('content')
                        ->label('Heading')
                        ->live(onBlur: true)
                        ->required()
                        ->columnSpan(['md' => 2, 'lg' => 3, 'xl' => 4]),

            ])
            ->columns(['sm' => 1, 'md' => 3, 'lg' => 4, 'xl' => 5])
            ->label(function (?array $state): string {
                if (blank($state) || blank($state['content'])) {
                    return 'Heading';
                }

                return 'Heading: '.  $state['content'];
            });
    }

    public static function mutateData(array $data): array
    {
        return $data;
    }
}
