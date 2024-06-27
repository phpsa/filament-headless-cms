<?php

namespace Phpsa\FilamentHeadlessCms\Http\Controller;

use Illuminate\Contracts\View\View;
use Phpsa\FilamentHeadlessCms\Contracts\Renderer;
use Phpsa\FilamentHeadlessCms\Contracts\FilamentPage;

class FilamentPageController
{
    public function __construct(private readonly Renderer $renderer)
    {
    }

    public function show(FilamentPage $filamentPage): View
    {
        return $this->renderer->render($filamentPage);
    }
}
