<?php
namespace Phpsa\FilamentHeadlessCms\Filament\Fields;

use Phpsa\FilamentHeadlessCms\FilamentHeadlessCms;

class FileUpload
{
    public static function make(...$args)
    {
        $class = FilamentHeadlessCms::getPlugin()->getUploadFormField();
        return $class::make(...$args);
    }
}
