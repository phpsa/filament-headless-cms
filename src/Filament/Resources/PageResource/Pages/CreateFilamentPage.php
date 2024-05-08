<?php

namespace Phpsa\FilamentHeadlessCms\Filament\Resources\PageResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Phpsa\FilamentHeadlessCms\FilamentHeadlessCms;
use Filament\Support\Facades\FilamentView;

class CreateFilamentPage extends CreateRecord
{
    public static function getResource(): string
    {
        return FilamentHeadlessCms::getPlugin()->getResource();
    }


        /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
       // dd($data);
        return $data;
    }
}
