<?php

namespace Phpsa\FilamentHeadlessCms;

use Filament\Panel;
use Filament\Contracts\Plugin;
use Filament\Resources\Resource;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Phpsa\FilamentHeadlessCms\Models\FhcmsContent;
use Phpsa\FilamentHeadlessCms\Contracts\FilamentPage;
use Phpsa\FilamentHeadlessCms\Contracts\PageTemplate;
use Phpsa\FilamentHeadlessCms\Filament\Resources\PageResource;
use Phpsa\FilamentHeadlessCms\Filament\PageTemplates\BlogTemplate;
use Phpsa\FilamentHeadlessCms\Filament\PageTemplates\DefaultTemplate;

/**
 * @template R of Resource
 * @template M of Model&FilamentPage
 * @template T of PageTemplate
 *
 * @package Phpsa\FilamentHeadlessCms
 */
class FilamentHeadlessCms implements Plugin
{

    /**
     *
     * @var class-string<R>
     */
    protected string $resource = PageResource::class;

    /**
     *
     * @var class-string<M>
     */
    protected string $model = FhcmsContent::class;

    protected ?string $siteUrl = null;

    /**
     *
     * @var Collection<int,class-string<T>>
     */
    protected Collection $templates;


    protected bool $templateTabs = false;

    /**
     *
     * @var array{group:?string,icon:string,sort:?int,parent:?string,icon_active:?string}
     */
    protected array $navigation = [
        'group'       => 'Content',
        'icon'        => 'heroicon-o-document',
        'sort'        => null,
        'parent'      => null,
        'icon_active' => 'heroicon-s-document',
    ];

    public function boot(Panel $panel): void
    {
    }

    /**
     * @param array<string, mixed> $options
     */
    public static function make(array $options = []): self
    {
        $instance = new self();

        $instance->setTemplates([
            DefaultTemplate::class,
            BlogTemplate::class
        ]);

        foreach ($options as $value => $option) {
            $call = 'set'.ucfirst($value);
            if (method_exists($instance, $call)) {
                $instance->$call($option);
            }
        }

        return $instance;
    }

    public static function getPlugin(): self
    {
        /** @phpstan-ignore-next-line */
        return filament('filament-headless-cms');
    }

    public function getId(): string
    {
        return 'filament-headless-cms';
    }

    public function register(Panel $panel): void
    {
    }


    /**
     *
     * @return class-string<R>
     */
    public function getResource(): string
    {
        return $this->resource;
    }

    /**
     *
     * @param class-string<R> $resource
     */
    public function setResource(string $resource): self
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     *
     * @return class-string<M>
     */
    public function getModel():string
    {
        return $this->model;
    }

    /**
     *
     * @param class-string<M> $model
     */
    public function setModel(string $model): self
    {
        $this->model = $model;

        return $this;
    }

    /**
     *
     * @param array{group:?string,icon:string,sort:?int,parent:?string,icon_active:?string} $navigation
     * @return FilamentHeadlessCms
     */
    public function setNavigation(array $navigation): self
    {
        $this->navigation = $navigation;

        return $this;
    }

    /**
     *
     * @return ($key is null ? array{group:?string,icon:string,sort:?int,parent:?string,icon_active:?string} : string|null)
     *
     */
    public function getNavigation(?string $key = null): array|string|int|null
    {
        return $key ? ($this->navigation[$key] ?? null) : $this->navigation;
    }

    public function setSiteUrl(string $siteUrl): self
    {
        $this->siteUrl = $siteUrl;

        return $this;
    }

    public function getSiteUrl(): string
    {
        /** @phpstan-ignore-next-line */
        return $this->siteUrl ?? config('app.url');
    }

    public function generateUrl(string $slug): string
    {

        return url($this->siteUrl . '/' . $slug);
    }

    /**
     *
     * @param array<class-string<T>> $templates
     */
    public function setTemplates(array $templates): self
    {
        $this->templates = collect($templates);

        return $this;
    }

    /**
     *
     * @return Collection<class-string<T>>
     */
    public function getTemplates(): Collection
    {
        return $this->templates;
    }

    public function setTemplateTabs(bool $enabled = true): self
    {
        $this->templateTabs = $enabled;

        return $this;
    }

    public function getTemplateTabs(): bool
    {
        return $this->templateTabs;
    }
}
