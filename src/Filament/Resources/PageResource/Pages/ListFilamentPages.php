<?php

namespace Phpsa\FilamentHeadlessCms\Filament\Resources\PageResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Phpsa\FilamentHeadlessCms\FilamentHeadlessCms;

class ListFilamentPages extends ListRecords
{
    public static function getResource(): string
    {
        return FilamentHeadlessCms::getPlugin()->getResource();
    }

    protected function getHeaderActions(): array
    {
        $url = static::getResource()::getUrl('create');

        return [
            CreateAction::make()->url($url)->label('New ' . static::getResource()::getCurrentTemplate()['label'] . ' Page'),
        ];
    }

    public function getTabs(): array
    {
        if (FilamentHeadlessCms::getPlugin()->getTemplateTabs() === false) {
            return [];
        }

        /** @var array<string> */
        $templates = self::getResource()::getTemplates();
        /** @var array<int|string, Tab>*/
        $tabs = collect()
        ->map(function ($template): Tab {
            return Tab::make($template)
                ->modifyQueryUsing(function (Builder $query) use ($template) {
                    return $query->where('template', $template);
                });
        })->sortKeys()->prepend(Tab::make('All'), 'all')->toArray();

        return $tabs;
    }
}
