<?php

namespace Phpsa\FilamentHeadlessCms\Contracts;

use Illuminate\Http\Response;
use Illuminate\Contracts\View\View;
use Phpsa\FilamentHeadlessCms\Contracts\FilamentPage;

interface Renderer
{
    public function render(FilamentPage $filamentPage): Response|View;
}
