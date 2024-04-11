<?php

namespace Phpsa\FilamentHeadlessCms\Filament\Resources\PageResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Phpsa\FilamentHeadlessCms\FilamentHeadlessCms;

class CreateFilamentPage extends CreateRecord
{
    public static function getResource(): string
    {
        return FilamentHeadlessCms::getPlugin()->getResource();
    }
}
