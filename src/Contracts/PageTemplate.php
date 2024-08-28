<?php

namespace Phpsa\FilamentHeadlessCms\Contracts;

use Closure;
use Illuminate\Support\Str;
use Filament\Forms\Components\Component;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Cache;
use Phpsa\FilamentHeadlessCms\FilamentHeadlessCms;

abstract class PageTemplate
{

    public static bool|array|Closure $paginate = true;

    public static bool $sortable = false;

    public static bool $simplePage = false;

    protected static int $sortOrder = 0;

    protected static bool $hasSeo = true;

    protected static bool $publishDates = true;

    protected static ?string $publicPath = null;

    abstract public static function title(): string;

    public static function mutateData(array $data): array
    {
        return $data;
    }

    /**
     * @return array<int|string, Component>
     */
    abstract public static function schema(): array;

    public static function getTemplateSlug(): string
    {
        return Str::slug(static::title());
    }

    public static function getPublicPath(): string
    {
        return str(static::$publicPath ?? Str::slug(static::title()))
            ->whenStartsWith('/', fn ($st) => $st, fn($st) => $st->prepend('/'))
            ->whenEndsWith('/', fn ($st) => $st, fn($st) => $st->append('/'))
            ->__toString();
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

        /** @var array */
        $page = Cache::remember('phpsa-filament-headless-cms-page-' . $record->id, now()->addDay(), function () use ($record): array {
            $content = $record->data['content'];
            $data = $record->toArray();
            $data['content'] = static::mutateData($content);

            unset($data['seo']['fhcms_contents_id'], $data['data'], $data['template'], $data['template_slug'], $data['id'], $data['deleted_at']);
            ksort($data);
            return $data;
        });

        throw_unless(is_array($page) && filled($page));

        return $page;
    }

    public function toSearchableArray(FilamentPage $record): array
    {
        return $this->apiTransform($record);
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
