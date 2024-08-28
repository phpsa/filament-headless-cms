<?php
namespace Phpsa\FilamentHeadlessCms\Filament\Resources\PageResource\Traits;

use Filament\Forms\Get;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Phpsa\FilamentHeadlessCms\FilamentHeadlessCms;
use Phpsa\FilamentHeadlessCms\Contracts\PageTemplate;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Sleep;
use Throwable;

trait HasTemplate
{

     /**
     *
     * @return array{class:class-string<PageTemplate>, label:string}
     * @throws BindingResolutionException
     */
    public static function getCurrentTemplate(?string $key = null, ?string $slug = null): mixed
    {
        $slug ??= self::getCurrentTemplateSlug();

        $templates = static::getTemplates()
            ->flip()
            ->mapWithKeys(
                fn($class, $label) => [
                    $class::getTemplateSlug() => [
                        'path'          => $class::getPublicPath(),
                        'class'         => $class,
                        'label'         => $label,
                        'slug'          => $class::getTemplateSlug(),
                        'seo'           => $class::hasSeo(),
                        'publish_dates' => $class::hasPublishDates(),
                        'paginate'      => $class::$paginate,
                        'sortable'      => $class::$sortable,
                    ]
                ]
            );

        return $key ? $templates->get($slug, fn() => $templates->first())[$key] : $templates->get($slug, fn() => $templates->first());
    }

    public static function getCurrentTemplateSlug(): ?string
    {
        if (request()->routeIs('livewire.update')) {
            $old = json_decode(request()->collect('components')->first()['snapshot'])->memo;
            $route = app('router')->getRoutes()->match(app('request')->create($old->path, $old->method));
            return $route->parameter('template');
        }
        return request()->route('template');
    }

    /**
     * @return Collection<int, class-string<PageTemplate>>
     */
    public static function getTemplateClasses(): Collection
    {
        return FilamentHeadlessCms::getPlugin()->getTemplates();
    }

    /**
     * @return Collection<class-string<PageTemplate>, string>
     */
    public static function getTemplates(): Collection
    {
        /** @phpstan-ignore-next-line */
        return static::getTemplateClasses()
            ->mapWithKeys(
                fn ($class): array => [$class => $class::title()]
            );
    }

    public static function getTemplateName(string $class): string
    {
        return Str::of($class)->afterLast('\\')->snake()->toString();
    }
}
