<?php
namespace Phpsa\FilamentHeadlessCms\Concerns;

use Closure;
use Filament\Panel;
use Phpsa\FilamentHeadlessCms\Filament\Form\Components\PageBlockBuilder;

trait HasPageBlockBuilder
{

    protected bool|Closure|null $shouldBeCollapsible = true;
    protected bool|Closure|null $shouldBeCollapsed = null;

    public function shouldBeCollapsible(): bool
    {
        return $this->evaluate($this->shouldBeCollapsible) ?? false;
    }

    public function collapsible(bool|Closure|null $condition = false): static
    {
        $this->shouldBeCollapsible = $condition;

        return $this;
    }

    public function shouldBeCollapsed(): bool
    {
        return $this->evaluate($this->shouldBeCollapsed) ?? false;
    }

    public function collapsed(bool|Closure|null $condition = false): static
    {
        $this->shouldBeCollapsed = $condition;

        return $this;
    }


    public function bootHasPageBlockBuilder(Panel $panel): void
    {
        PageBlockBuilder::configureUsing(function (PageBlockBuilder $builder) {
            $builder->collapsible($this->shouldBeCollapsible())
                ->collapsed($this->shouldBeCollapsed());
        });
    }
}
