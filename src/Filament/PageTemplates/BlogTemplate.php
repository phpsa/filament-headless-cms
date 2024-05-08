<?php

namespace Phpsa\FilamentHeadlessCms\Filament\PageTemplates;

use App\Models\User;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\SpatieTagsInput;
use Phpsa\FilamentHeadlessCms\FilamentHeadlessCms;
use Phpsa\FilamentHeadlessCms\Contracts\PageTemplate;
use Phpsa\FilamentHeadlessCms\Filament\Fields\Editor;
use Phpsa\FilamentHeadlessCms\Filament\Fields\FileUpload as FieldsFileUpload;

class BlogTemplate extends PageTemplate
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
            Section::make('Primary Content')
                ->schema([
                    Textarea::make('blurb')
                        ->label('Blurb')
                        ->required()
                        ->minLength(2)
                        ->maxLength(1024)
                        ->rows(3),

                    Editor::make('content')
                        ->label('Content'),
                ]),

        ];
    }

    public static function beforePrimaryColumnSchema(): array
    {
        return [ ];
    }

    public static function afterPrimaryColumnSchema(): array
    {
        return [ ];
    }

     /**
     *
     * @return array<Component>
     */
    public static function beforeSecondaryColumnSchema(): array
    {
        return [];
    }
    /**
     *
     * @return array<Component>
     */
    public static function afterSecondaryColumnSchema(): array
    {
        return [
            Select::make('category_id')
            ->native(false)
            ->searchable()
            ->getSearchResultsUsing(fn (string $search): array => FilamentHeadlessCms::getPlugin()->getModel()::where('title', 'like', "%{$search}%")->limit(50)->pluck('title', 'id')->toArray())
            ->getOptionLabelUsing(fn ($value): ?string => FilamentHeadlessCms::getPlugin()->getModel()::find($value)?->title),

            Select::make('author_id')
                ->native(false)
                ->searchable()
                ->getSearchResultsUsing(fn (string $search): array => User::where('name', 'like', "%{$search}%")->limit(50)->pluck('name', 'id')->toArray())
                ->getOptionLabelUsing(fn ($value): ?string => User::find($value)?->name)
                ->default(auth()->id()),
            SpatieTagsInput::make('tags')
            ->type('tags')
        ];
    }

    public static function sidebarSchema(): array
    {
        return [
            FieldsFileUpload::make('featured_image')->directory('blog')->image(),
        ];
    }
}
