<?php

namespace Phpsa\FilamentHeadlessCms;

use ReflectionClass;
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;
use Spatie\LaravelPackageTools\Package;
use Symfony\Component\Finder\SplFileInfo;
use Phpsa\FilamentHeadlessCms\Contracts\PageBlock;
use Phpsa\FilamentHeadlessCms\Commands\InstallCommand;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Phpsa\FilamentHeadlessCms\Commands\MakePageBlockCommand;
use Phpsa\FilamentHeadlessCms\Facades\FilamentCmsPageBlocks;

class CmsServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-headless-cms';

    public function configurePackage(Package $package): void
    {
        $package->name('filament-headless-cms')
            ->hasMigration('create_filament_headless_cms_table')
            ->hasCommand(InstallCommand::class)
            ->hasCommand(MakePageBlockCommand::class)
            ->hasRoute('api')
            ->hasViews()
            ->hasTranslations();
    }

    public function packageRegistered(): void
    {
        parent::packageRegistered();

        $this->app->scoped('filament-cms-page-blocks', function () {
            return new FilamentPageBlocksManager();
        });
    }

    public function bootingPackage(): void
    {
        $this->registerComponentsFromDirectory(
            PageBlock::class,
            [],
            app_path('Filament/PageBlocks'),
            'App\\Filament\\PageBlocks'
        );

        $this->registerComponentsFromDirectory(
            PageBlock::class,
            [],
            __DIR__ . '/Filament/PageBlocks',
            'Phpsa\\FilamentHeadlessCms\\Filament\\PageBlocks'
        );
    }

    protected function registerComponentsFromDirectory(string $baseClass, array $register, ?string $directory, ?string $namespace): void
    {
        if (blank($directory) || blank($namespace)) {
            return;
        }

        $filesystem = app(Filesystem::class);

        if ((! $filesystem->exists($directory)) && (! Str::of($directory)->contains('*'))) {
            return;
        }

        $namespace = Str::of($namespace);

        array_merge(
            $register,
            collect($filesystem->allFiles($directory))
                ->map(function (SplFileInfo $file) use ($namespace): string {
                    $variableNamespace = $namespace->contains('*') ? str_ireplace(
                        ['\\' . $namespace->before('*'), $namespace->after('*')],
                        ['', ''],
                        Str::of($file->getPath())
                            ->after(base_path())
                            ->replace(['/'], ['\\']),
                    ) : null;

                    return (string) $namespace
                        ->append('\\', $file->getRelativePathname())
                        ->replace('*', $variableNamespace)
                        ->replace(['/', '.php'], ['\\', '']);
                })
                ->filter(fn (string $class): bool => is_subclass_of($class, $baseClass) && (! (new ReflectionClass($class))->isAbstract()))
                ->each(fn (string $class) => FilamentCmsPageBlocks::register($class, $baseClass))
                ->all(),
        );
    }
}
