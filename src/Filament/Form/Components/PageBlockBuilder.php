<?php

namespace Phpsa\FilamentHeadlessCms\Filament\Form\Components;

use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Builder;
use Illuminate\Support\Arr;
use Phpsa\FilamentHeadlessCms\Facades\FilamentCmsPageBlocks;

class PageBlockBuilder extends Builder
{
//    protected string $view = 'filament-headless-cms::components.page-builder';

    protected function setUp(): void
    {
        parent::setUp();

        $this->blocks(FilamentCmsPageBlocks::getPageBlocks());

        $this->label(fn () => new HtmlString('<h1 class="fi-header-heading text-xl font-bold tracking-tight text-gray-950 dark:text-white sm:text-2xl">Page Blocks</h1>'));
      //  $this->label('Page Blocks');

        $this->addActionLabel('Add Block');
        $this->cloneable();

        $this->blockPickerColumns(['md' => 1, 'lg' => 2, 'xl' => 3]);

        $this->mutateDehydratedStateUsing(static function (?array $state): array {
            if (! is_array($state)) {
                return array_values([]);
            }

            $registerPageBlockNames = array_keys(FilamentCmsPageBlocks::getPageBlocksRaw());

            return collect($state)
                ->filter(fn (array $block) => in_array($block['type'], $registerPageBlockNames, true))
                ->values()
                ->toArray();
        });
    }

    public function only(string ...$only): self
    {
        $this->childComponents = Arr::only($this->childComponents, $only);
        return $this;
    }

    public function except(string ...$only): self
    {
        $this->childComponents = Arr::except($this->childComponents, $only);
        return $this;
    }
}
