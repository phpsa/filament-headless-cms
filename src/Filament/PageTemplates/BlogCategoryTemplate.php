<?php

namespace Phpsa\FilamentHeadlessCms\Filament\PageTemplates;

use Illuminate\Support\Facades\Storage;
use Phpsa\FilamentHeadlessCms\Contracts\PageTemplate;
use Phpsa\FilamentHeadlessCms\Filament\Fields\FileUpload;

class BlogCategoryTemplate extends PageTemplate
{
    protected static bool $hasSeo = false;

    public static function title(): string
    {
        return 'Blog Category';
    }

    public static function schema(): array
    {
        return [];
    }

    public static function sidebarSchema(): array
    {
        return [
            FileUpload::make('featured_image'),
        ];
    }

    public static function toApiResponse(array $data): array
    {

        if (filled($data['featured_image'])) {
            $data['featured_image'] = Storage::disk(config('filament.default_filesystem_disk'))->url($data['featured_image']);
        }

        return $data;
    }
}
