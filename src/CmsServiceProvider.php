<?php

namespace Phpsa\FilamentHeadlessCms;

use Phpsa\FilamentHeadlessCms\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class CmsServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-headless-cms';

    public function configurePackage(Package $package): void
    {
        $package->name('filament-headless-cms')
            ->hasMigration('create_filament_headless_cms_table')
            ->hasCommand(InstallCommand::class)
            ->hasTranslations();
    }
}
