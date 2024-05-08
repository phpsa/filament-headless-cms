<?php

namespace Phpsa\FilamentHeadlessCms\Filament\PageTemplates;

use Filament\Forms\Components\FileUpload;
use Phpsa\FilamentHeadlessCms\Contracts\PageTemplate;

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
}
