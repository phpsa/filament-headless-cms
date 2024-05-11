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
use Throwable;

trait HasTemplate
{

     /**
     *
     * @return array{class:class-string<PageTemplate>, label:string}
     * @throws BindingResolutionException
     */
    public static function getCurrentTemplate(?string $key = null): mixed
    {
        $slug = self::getCurrentTemplateSlug();

        $templates = static::getTemplates()
            ->flip()
            ->mapWithKeys(
                fn($class, $label) => [
                    $class::getTemplateSlug() => [
                        'class'         => $class,
                        'label'         => $label,
                        'slug'          => $class::getTemplateSlug(),
                        'seo'           => $class::hasSeo(),
                        'publish_dates' => $class::hasPublishDates(),
                        'paginate'      => $class::$paginate,
                    ]
                ]
            );

        return $key ? $templates->get($slug, fn() => $templates->first())[$key] : $templates->get($slug, fn() => $templates->first());
    }

    public static function getCurrentTemplateSlug(): ?string
    {

        $slug = request()->query('cms_template');
        if (request()->routeIs('livewire.update')) {
            try {
                $slug = json_decode(request()->collect('components')->first()['snapshot'])->data->data[0]->template_slug;
            } catch (Throwable) {
                //hmmm
            }
        }

        return (request()->routeIs(static::getRouteBaseName() . '.edit'))
        ? parent::getEloquentQuery()->find(request()->record, ['template_slug'])->template_slug
        : $slug;
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
