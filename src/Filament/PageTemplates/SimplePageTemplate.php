<?php

namespace Phpsa\FilamentHeadlessCms\Filament\PageTemplates;

use App\Filament\PageBlocks\HelloBlock;
use Filament\Forms\Get;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Phpsa\FilamentHeadlessCms\Contracts\PageTemplate;
use Phpsa\FilamentHeadlessCms\Filament\Fields\Editor;
use Phpsa\FilamentHeadlessCms\Filament\Fields\FileUpload;
use Phpsa\FilamentHeadlessCms\Filament\PageBlocks\SimplePageBlock;
use Phpsa\FilamentHeadlessCms\Filament\Form\Components\PageBlockBuilder;

class SimplePageTemplate extends PageTemplate
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
            PageBlockBuilder::make('contentblock')->except(HelloBlock::getName()),
            Builder::make('content')
            ->blocks([

                Builder\Block::make('grid')
                ->schema([
                    Select::make('column_set')->options([
                        '7-1' => '7-1',
                        '6-2' => '6-2',
                        '5-3' => '5-3',
                        '4-4' => '4-4',
                        '3-5' => '3-5',
                        '2-6' => '2-6',
                        '1-7' => '1-7',
                    ])->live(),
                    Grid::make('grid')->columns(8)
                    ->schema([
                        Builder::make('content-left')->blocks([
                            Builder\Block::make('paragraph')
                    ->schema([
                        Textarea::make('content')
                            ->label('Paragraph')
                            ->required(),
                            ]),
                        ])->columnSpan(fn(Get $get) => match ($get('column_set')) {
                            '7-1' => 7,
                            '6-2' => 6,
                            '5-3' => 5,
                            '4-4' => 4,
                            '3-5' => 3,
                            '2-6' => 2,
                            '1-7' => 1,
                            default => 4
                        }),
                        Builder::make('content-right')->blocks([
                            Builder\Block::make('paragraph')
                    ->schema([
                        Textarea::make('content')
                            ->label('Paragraph')
                            ->required(),
                            ]),
                        ])->columnSpan(fn(Get $get) => match ($get('column_set')) {
                            '7-1' => 1,
                            '6-2' => 2,
                            '5-3' => 3,
                            '4-4' => 4,
                            '3-5' => 5,
                            '2-6' => 6,
                            '1-7' => 7,
                            default => 4
                        }),
                    ])

                ])
                ->columnSpanFull(),

                Builder\Block::make('heading')
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
                Builder\Block::make('paragraph')
                    ->schema([
                        Textarea::make('content')
                            ->label('Paragraph')
                            ->required(),
                    ]),
                Builder\Block::make('image')
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

        ];
    }

    public static function mutateData(array $data): array
    {
        return $data;
    }
}
