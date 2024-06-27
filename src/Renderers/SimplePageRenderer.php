<?php

namespace Phpsa\FilamentHeadlessCms\Renderers;

use Illuminate\Contracts\View\View;
use Phpsa\FilamentHeadlessCms\Contracts\Renderer;
use Phpsa\FilamentHeadlessCms\Contracts\FilamentPage;

class SimplePageRenderer implements Renderer
{
    public function render(FilamentPage $filamentPage): View
    {

        $layout = config('filament-headless-cms.default_layout', 'layouts.app');

        return view($layout, ['page' => $filamentPage, 'data' => $filamentPage->data['content'] ?? []]);
    }
}
