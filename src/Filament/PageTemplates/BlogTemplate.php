<?php

namespace Phpsa\FilamentHeadlessCms\Filament\PageTemplates;

use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\SpatieTagsInput;
use Illuminate\Support\Facades\Storage;
use Phpsa\FilamentHeadlessCms\FilamentHeadlessCms;
use Phpsa\FilamentHeadlessCms\Contracts\FilamentPage;
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

    /**
     *
     * @return array<Component>
     */
    public static function afterSecondaryColumnSchema(): array
    {
        return [
            Select::make('category_id')
            ->label('category')
            ->native(false)
            ->searchable()
            ->getSearchResultsUsing(
                fn (string $search): array => FilamentHeadlessCms::getPlugin()->getModel()::where('title', 'like', "%{$search}%")
                    ->where('template_slug', 'blog-category')
                    ->limit(50)
                    ->pluck('title', 'id')->toArray()
            )
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
            Section::make('Featured Image')->columnSpan(1)
                ->schema([
                    FieldsFileUpload::make('featured_image'),
                ]),
           // FieldsFileUpload::make('featured_image')->directory('blog'),
        ];
    }

    public static function toApiResponse(array $data): array
    {

        $data['category'] = null;
        if (filled($data['category_id'] ?? null)) {
            $data['category'] = self::loadRelatedData($data['category_id']);
        }

        if (filled($data['author_id'] ?? null)) {
            $data['author'] = User::find($data['author_id'], ['id','name','email'])->toArray();
        }

        if (filled($data['featured_image'])) {
            $data['featured_image'] = Storage::disk(config('filament.default_filesystem_disk'))->url($data['featured_image']);
        }

        unset($data['category_id'], $data['author_id']);
        return $data;
    }
}
