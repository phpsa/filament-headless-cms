<?php

namespace Phpsa\FilamentHeadlessCms\Contracts;

use Filament\Forms\Components\Component;

interface PageTemplate
{
    public static function title(): string;

    /**
     * @return array<int|string, Component>
     */
    public static function schema(): array;
}
