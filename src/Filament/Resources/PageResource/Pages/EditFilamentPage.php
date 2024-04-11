<?php

namespace Phpsa\FilamentHeadlessCms\Filament\Resources\PageResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Phpsa\FilamentHeadlessCms\FilamentHeadlessCms;

class EditFilamentPage extends EditRecord
{
    public static function getResource(): string
    {
        return FilamentHeadlessCms::getPlugin()->getResource();
    }

    protected function getActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
