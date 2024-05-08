<?php

namespace Phpsa\FilamentHeadlessCms;

use Filament\Panel;
use Filament\Contracts\Plugin;
use Filament\Resources\Resource;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Phpsa\FilamentHeadlessCms\Models\FhcmsContent;
use Phpsa\FilamentHeadlessCms\Contracts\FilamentPage;
use Phpsa\FilamentHeadlessCms\Contracts\PageTemplate;
use Phpsa\FilamentHeadlessCms\Filament\Resources\PageResource;
use Phpsa\FilamentHeadlessCms\Filament\PageTemplates\BlogTemplate;
use Phpsa\FilamentHeadlessCms\Filament\PageTemplates\DefaultTemplate;
use Phpsa\FilamentHeadlessCms\Filament\PageTemplates\BlogCategoryTemplate;

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

    protected string $fileUploadFormField = FileUpload::class;

    protected string $editorFormField = RichEditor::class;

    protected array $apiMiddleware = ['api'];
    protected string $apiPrefix = 'api';

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
            BlogTemplate::class,
            BlogCategoryTemplate::class,
        ], true);

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
        $panel->resources([$this->resource]);
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
        return $this->siteUrl ?? config('app.frontend_url') ?? config('app.url');
    }

    public function generateUrl(string $slug): string
    {

        return url($this->siteUrl . '/' . $slug);
    }

    /**
     *
     * @param array<class-string<T>> $templates
     */
    public function setTemplates(array $templates, bool $flush = false): self
    {
        $this->templates = $flush ? collect($templates) : $this->templates->merge($templates);

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

    public function setUploadFormField(string $field): self
    {
        $this->fileUploadFormField = $field;

        return $this;
    }

    public function getUploadFormField(): string
    {
        return $this->fileUploadFormField;
    }

    public function setEditorFormField(string $field): self
    {
        $this->editorFormField = $field;

        return $this;
    }

    public function getEditorFormField(): string
    {
        return $this->editorFormField;
    }

    public function setApiMiddleware(array $middleware): self
    {
        $this->apiMiddleware = $middleware;

        return $this;
    }

    public function setApiPrefix(string $prefix): self
    {
        $this->apiPrefix = $prefix;

        return $this;
    }

    public function getApiMiddleware(): array
    {
        return $this->apiMiddleware;
    }

    public function getApiPrefix(): string
    {
        return $this->apiPrefix;
    }
}
