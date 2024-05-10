[![Latest Version on Packagist](https://img.shields.io/packagist/v/phpsa/filament-headless-cms.svg?style=flat-square)](https://packagist.org/packages/phpsa/filament-headless-cms)
[![Semantic Release](https://github.com/phpsa/filament-headless-cms/actions/workflows/release.yml/badge.svg)](https://github.com/phpsa/filament-headless-cms/actions/workflows/release.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/phpsa/filament-headless-cms.svg?style=flat-square)](https://packagist.org/packages/phpsa/filament-headless-cms)

# Filament Headless CMS

Starting point for a headless CMS for filament - notes on how to use with api / non-api below

## Installation


You can install the package via composer:

```bash
composer require phpsa/filament-headless-cms
php artisan filament-headless-cms::install
```

in the panel you are using

```php
public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('/admin')
            ...
            ->plugin(FilamentHeadlessCms::make())
```

## Usage
### Within Filament

After running the [Install Command](#installation), you will find a new Content Group and Sample Content Resources in your Filament Admin.

### Templates

This package comes with basic page and blog type template. By creating and selecting your own templates, you are able to fully
customize your pages.

To create your own Templates, extend the `Phpsa\FilamentHeadlessCms\Contracts\PageTemplate` abstract class:

```php
<?php

namespace App\Filament\PageTemplates;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\Component;
use Phpsa\FilamentHeadlessCms\Contracts\PageTemplate;
use Phpsa\FilamentHeadlessCms\Filament\Fields\Editor;

class CustomPageTemplate extends PageTemplate
{

    public static function title(): string
    {
        return 'Custom Page';
    }

    /**
     * @return array<int|string, Component>
     */
    public static function schema(): array
    {
        return [
            Section::make()
                ->schema([
                    Editor::make('content')
                    ->label('Content'),

                ])
        ];
    }
}
```

and register this in the panel provider: - you can choose to flush the sample templates or keep them with the flush param.
```php
 FilamentHeadlessCms::make()
                ->setTemplates([
                    GrapeTemplate::class
                ], flush: false),
```

This will now appear under the content area and will allow you to create / edit / delete.

### Template Functionality

being able to control the form in and of itself is great, but say you want to add something into the default areas:
we have you covered.

here are a few items that can be overwritten

```php
protected static int $sortOrder = 0; // set the order in nav
protected static bool $hasSeo = true; //no SEO, switch to false, why though?
protected static bool $publishDates = true; // if you do not need to publish on a date and it should just be... :-)

abstract public static function title(): string; //The value in here should be unique, it is the identifier for your template
abstract public static function schema(): array; // the actual form main body area, you can do whatever here, sections, tabs, wizards, builders etc.

public static function sidebarSchema(): array; //want to add another sidebar above the SEO?

//add area before and after the title area
public static function beforePrimaryColumnSchema(): array;
public static function afterPrimaryColumnSchema(): array;

// add area before and after the fields in the main sidebar
public static function beforeSecondaryColumnSchema(): array;
public static function afterSecondaryColumnSchema(): array;
 
// need to manipulate the data for your api response?
public static function toApiResponse(array $data): array
{
    if (filled($data['featured_image'])) {
        $data['featured_image'] = Storage::disk(config('filament.default_filesystem_disk'))->url($data['featured_image']);
    }

    return $data;
}
```

### Plugin Customisation
you can customise using the following methods
```php
FilamentHeadlessCms::make()
->setResource(ResourceFile::class) // in case you want to customize it, you can extend the PageResource::class
->setModel(Model::class) // woudl recomend extending FhcmsContent - must implement FilamentPage
->setNavigation([
        'group'       => 'Content',
        'icon'        => 'heroicon-o-document',
        'sort'        => null,
        'parent'      => null,
        'icon_active' => 'heroicon-s-document',
    ]);
->setSiteUrl('https://xxxx') //--> defaults to config('app.frontend_url') ?? config('app.url')
->setTemplates(array $templates, bool $flush = false) // add or completely replace templates
->setUploadFormField(FileUpload::class) // if using default templates or built in upload class -- make sure to use this file uploader
->setEditorFormField(Editor::class) // which rich / markdown form field editor to use - if using default templates
->setApiMiddleware(['api']) // defaults to ['api']
->setApiPrefix('api') // defaults to api
```

### Api Routes
* `api/fhcms/types` - will list the active template types 
* `api/fhcms/types?with_counts` - will list the active template types and count of published items.
* `fhcms/content-pages/{type}` - type - is the slug, will return paginated list
* `fhcms/content-pages/{type}/{slug}` - slug is the content item slug. - returns item in json format.


## Planned Features / Suggestions
* this is a new project and has room for growth and improvement, feel free to suggest enhancements or open pull requests.

***Currently Planned***
* Search intergration with Scout
* Improved internal references between templates.
* Improved testing


## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

- [Phpsa](https://github.com/phpsa)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
