<?php
namespace Phpsa\FilamentHeadlessCms\Filament\Resources\PageResource\Traits;

use Filament\Forms\Get;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Phpsa\FilamentHeadlessCms\FilamentHeadlessCms;
use Camya\Filament\Forms\Components\TitleWithSlugInput;

trait HasSchemas
{

    public static function getPrimaryColumnSchema(): array
    {
        $slug = static::getCurrentTemplateSlug();

        return [
            Section::make([
                ...static::insertBeforePrimaryColumnSchema(),
                TitleWithSlugInput::make(
                    fieldTitle: 'title', // The name of the field in your model that stores the title.
                    fieldSlug: 'slug', // The name of the field in your model that will store the slug.
                    urlHost: FilamentHeadlessCms::getPlugin()->getSiteUrl() . '/' . $slug,
                )->columnSpanFull(),
                ...static::insertAfterPrimaryColumnSchema(),
            ])->columnSpanFull()
        ];
    }

    /**
     *
     * @return array<int, Group>
     */
    public static function getTemplateSchemas(): Grid
    {

        return Grid::make(1)->columnSpanFull()
            ->schema(fn (Get $get): array => static::getSectionColumnSchema('schema', $get('template')));
    }

    public static function getSideColumnSchemas(): Grid
    {

        return Grid::make(1)->columnSpanFull()
            ->schema(fn (Get $get): array => static::getSectionColumnSchema('sidebarSchema', $get('template')));
    }


    protected static function getSectionColumnSchema(string $section, ?string $template): array
    {
        return static::getTemplateClasses()
            ->mapWithKeys(fn ($class): array => [
                $class => [
                    Group::make($class::$section())->statePath('data.content')
                ]
            ])->get($template ?? '', []);
    }
     /**
     *
     * @return array<Component>
     */
    public static function insertBeforePrimaryColumnSchema(): array
    {
        return [Grid::make(1)->columnSpanFull()
            ->schema(fn (Get $get): array => static::getSectionColumnSchema('beforePrimaryColumnSchema', $get('template')))
        ];
    }

    /**
     *
     * @return array<Component>
     */
    public static function insertAfterPrimaryColumnSchema(): array
    {
        return [Grid::make(1)->columnSpanFull()
            ->schema(fn (Get $get): array => static::getSectionColumnSchema('afterPrimaryColumnSchema', $get('template')))
        ];
    }

    /**
     *
     * @return array<Component>
     */
    public static function insertBeforeSecondaryColumnSchema(): array
    {
        return [Grid::make(1)->columnSpanFull()
            ->schema(fn (Get $get): array => static::getSectionColumnSchema('beforeSecondaryColumnSchema', $get('template')))
        ];
    }

    /**
     *
     * @return array<Component>
     */
    public static function insertAfterSecondaryColumnSchema(): array
    {
        return [Grid::make(1)->columnSpanFull()
            ->schema(fn (Get $get): array => static::getSectionColumnSchema('afterSecondaryColumnSchema', $get('template')))
        ];
    }
}
