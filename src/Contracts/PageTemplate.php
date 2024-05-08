<?php

namespace Phpsa\FilamentHeadlessCms\Contracts;

use Illuminate\Support\Str;
use Filament\Forms\Components\Component;
use Illuminate\Contracts\Support\Htmlable;
use Phpsa\FilamentHeadlessCms\FilamentHeadlessCms;

abstract class PageTemplate
{

    protected static int $sortOrder = 0;

    protected static bool $hasSeo = true;

    protected static bool $publishDates = true;

    abstract public static function title(): string;

    /**
     * @return array<int|string, Component>
     */
    abstract public static function schema(): array;

    public static function getTemplateSlug(): string
    {
        return Str::slug(static::title());
    }

    public static function getNavigationIcon(): string
    {
        return (string)FilamentHeadlessCms::getPlugin()->getNavigation('icon');
    }

    public static function getActiveNavigationIcon(): string | Htmlable | null
    {
        return filled(FilamentHeadlessCms::getPlugin()->getNavigation('icon_active')) ? (string) FilamentHeadlessCms::getPlugin()->getNavigation('icon_active') : static::getNavigationIcon();
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
        return [];
    }

    public static function beforePrimaryColumnSchema(): array
    {
        return [];
    }

    public static function afterPrimaryColumnSchema(): array
    {
        return [];
    }

    public static function sidebarSchema(): array
    {
        return [];
    }

    public static function getNavigationBadge(): ?string
    {
        return null;
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return null;
    }

    public static function getNavigationBadgeColor(): string | array | null
    {
        return null;
    }

    public static function getNavigationSort(): ?int
    {
        return  static::$sortOrder;
    }

    public static function hasSeo(): bool
    {
        return static::$hasSeo;
    }

    public static function hasPublishDates(): bool
    {
        return static::$publishDates;
    }

    public static function apiTransform(FilamentPage $record): array
    {
       // return $data->toArray();
        $content = $record->data['content'];
        $data = $record->toArray();
        $data['content'] = static::toApiResponse($content);

        unset($data['seo']['fhcms_contents_id'], $data['data'], $data['template'], $data['template_slug'], $data['id'], $data['deleted_at']);
        ksort($data);
        return $data;
    }

    public static function toApiResponse(array $data): array
    {
        return $data;
    }

    protected static function loadRelatedData($id): array
    {
        $record = FilamentHeadlessCms::getPlugin()
            ->getModel()::wherePublished()
            ->find($id);

        if (blank($record)) {
            return null;
        }

        $template = $record->template;
        return $template::apiTransform($record);
    }
}
