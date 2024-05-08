<?php
namespace Phpsa\FilamentHeadlessCms\Filament\Fields;

use Phpsa\FilamentHeadlessCms\FilamentHeadlessCms;

class Editor
{
    public static function make(...$args)
    {
        $class = FilamentHeadlessCms::getPlugin()->getEditorFormField();
        return $class::make(...$args);
    }
}
