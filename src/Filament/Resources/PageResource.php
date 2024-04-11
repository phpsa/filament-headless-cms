<?php

namespace Phpsa\FilamentHeadlessCms\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Illuminate\Support\Collection;
use Filament\Forms\Components\Card;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Navigation\NavigationGroup;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Placeholder;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Forms\Components\DateTimePicker;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Resources\Pages\PageRegistration;
use Phpsa\FilamentHeadlessCms\FilamentHeadlessCms;
use Phpsa\FilamentHeadlessCms\Contracts\FilamentPage;
use Phpsa\FilamentHeadlessCms\Contracts\PageTemplate;
use Illuminate\Contracts\Container\BindingResolutionException;
use Phpsa\FilamentHeadlessCms\Filament\Resources\PageResource\Pages\EditFilamentPage;
use Phpsa\FilamentHeadlessCms\Filament\Resources\PageResource\Pages\ListFilamentPages;
use Phpsa\FilamentHeadlessCms\Filament\Resources\PageResource\Pages\CreateFilamentPage;

class PageResource extends Resource
{

    protected static ?string $recordRouteKeyName = 'id';
    protected static ?string $recordTitleAttribute = 'title';

    public static function getModel(): string
    {
        return FilamentHeadlessCms::getPlugin()->getModel();
    }

    public static function getModelLabel(): string
    {
        return __('filament-headless-cms::pages.modelLabel');
    }

    public static function getPluralLabel(): string
    {
        return __('filament-headless-cms::pages.modelLabelPlural');
    }

    public static function getNavigationGroup(): ?string
    {
        return filled(FilamentHeadlessCms::getPlugin()->getNavigation('group')) ? (string) FilamentHeadlessCms::getPlugin()->getNavigation('group') : null;
    }

    public static function getNavigationSort(): ?int
    {
        return filled(FilamentHeadlessCms::getPlugin()->getNavigation('sort')) ? (int) FilamentHeadlessCms::getPlugin()->getNavigation('sort') : null;
    }

    public static function getNavigationIcon(): string
    {
        return (string)FilamentHeadlessCms::getPlugin()->getNavigation('icon');
    }

    public static function getNavigationParentItem(): ?string
    {
        return filled(FilamentHeadlessCms::getPlugin()->getNavigation('parent')) ? (string) FilamentHeadlessCms::getPlugin()->getNavigation('parent') : null;
    }

    public static function getActiveNavigationIcon(): string | Htmlable | null
    {
        return filled(FilamentHeadlessCms::getPlugin()->getNavigation('icon_active')) ? (string) FilamentHeadlessCms::getPlugin()->getNavigation('icon_active') : static::getNavigationIcon();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()
                    ->schema([
                        static::getPrimaryColumnSchema(),
                        ...static::getTemplateSchemas(),
                    ])
                    ->columnSpan(['lg' => 7]),

                static::getSecondaryColumnSchema(),

            ])
            ->columns([
                'sm' => 9,
                'lg' => null,
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(__('filament-headless-cms::pages.form.title'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('slug')
                    ->label(__('filament-headless-cms::pages.form.slug'))
                    ->icon('heroicon-o-external-link')
                    ->iconPosition('after')
                    ->getStateUsing(fn (FilamentPage $record) => $record->url)
                    ->searchable()
                    ->url(
                        url: fn (FilamentPage $record) => $record->url,
                        shouldOpenInNewTab: true
                    )
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('status')
                ->badge()
                    ->getStateUsing(fn (FilamentPage $record): string => $record->published_at?->isPast() && ($record->published_until?->isFuture() ?? true) ? __('filament-headless-cms::pages.status.published') : __('filament-headless-cms::pages.status.draft'))
                    ->colors([
                        'success' => __('filament-headless-cms::pages.status.published'),
                        'warning' => __('filament-headless-cms::pages.status.draft'),
                    ]),

                TextColumn::make('published_at')
                    ->label(__('filament-headless-cms::pages.form.published_at.label'))
                    ->dateTime(__('filament-headless-cms::pages.form.dateFormat')),
            ])
            ->filters([
                Filter::make('published_at')
                    ->form([
                        DateTimePicker::make('published_from')
                            ->label(__('filament-headless-cms::pages.form.published_at'))
                            ->placeholder(fn ($state): string => now()->subYear()->format(__('filament-headless-cms::pages.form.dateFormat'))),
                        DateTimePicker::make('published_until')
                            ->label(__('filament-headless-cms::pages.form.published_until'))
                            ->placeholder(fn ($state): string => now()->addYear()->format(__('filament-headless-cms::pages.form.dateFormat'))),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['published_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('published_at', '>=', $date),
                            )
                            ->when(
                                $data['published_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('published_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['published_from'] ?? null) {
                            $indicators['published_from'] = 'Published from '.Carbon::parse($data['published_at'])->toFormattedDateString();
                        }
                        if ($data['published_until'] ?? null) {
                            $indicators['published_until'] = 'Published until '.Carbon::parse($data['published_until'])->toFormattedDateString();
                        }

                        return $indicators;
                    }),
            ])
            ->actions([
                EditAction::make(),

                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getPrimaryColumnSchema(): Component
    {
        return Section::make()
            ->columns(2)
            ->schema([
                ...static::insertBeforePrimaryColumnSchema(),
                TextInput::make('title')
                    ->label(__('filament-headless-cms::pages.form.title'))
                    ->columnSpan(1)
                    ->required()
                    ->lazy()
                    ->afterStateUpdated(fn (string $context, $state, callable $set) => $context === 'create' ? $set('slug', Str::slug($state)) : null),

                TextInput::make('slug')
                    ->label(__('filament-headless-cms::pages.form.slug'))
                    ->columnSpan(1)
                    ->required()
                    ->unique(FilamentPage::class, 'slug', ignoreRecord: true),
                ...static::insertAfterPrimaryColumnSchema(),
            ]);
    }

    public static function getSecondaryColumnSchema(): Component
    {
        return Section::make()
            ->schema([
                ...static::insertBeforeSecondaryColumnSchema(),
                Select::make('template')
                    ->live()
                    ->afterStateUpdated(fn (string $context, $state, callable $set) => $set('data.templateName', Str::snake(self::getTemplateName($state))))
                    ->afterStateHydrated(fn (string $context, $state, callable $set) => $set('data.templateName', Str::snake(self::getTemplateName($state))))
                    ->options(static::getTemplates()),

                Hidden::make('data.templateName')
                    ->reactive(),

                DateTimePicker::make('published_at')
                    ->label(__('filament-headless-cms::pages.form.published_at'))
                    ->displayFormat(__('filament-headless-cms::pages.dateFormat'))
                    ->default(now()),

                DateTimePicker::make('published_until')
                    ->label(__('filament-headless-cms::pages.form.published_until'))
                    ->displayFormat(__('filament-headless-cms::pages.dateFormat')),

                Placeholder::make('created_at')
                    ->label(__('filament-headless-cms::pages.form.created_at'))
                    ->hidden(fn (?FilamentPage $record) => $record === null)
                    ->content(fn (FilamentPage $record): string => $record->created_at->diffForHumans()),

                Placeholder::make('updated_at')
                    ->label(__('filament-headless-cms::pages.form.updated_at'))
                    ->hidden(fn (?FilamentPage $record) => $record === null)
                    ->content(fn (FilamentPage $record): string => $record->updated_at?->diffForHumans() ?? '-'),
                ...static::insertAfterSecondaryColumnSchema(),
            ])
            ->columnSpan(['lg' => 2]);
    }

    /**
     *
     * @return array<Component>
     */
    public static function insertBeforePrimaryColumnSchema(): array
    {
        return [];
    }

    /**
     *
     * @return array<Component>
     */
    public static function insertAfterPrimaryColumnSchema(): array
    {
        return [];
    }

    /**
     *
     * @return array<Component>
     */
    public static function insertBeforeSecondaryColumnSchema(): array
    {
        return [];
    }

    /**
     *
     * @return array<Component>
     */
    public static function insertAfterSecondaryColumnSchema(): array
    {
        return [];
    }

    /**
     * @return Collection<int, class-string<PageTemplate>>
     */
    public static function getTemplateClasses(): Collection
    {
        return FilamentHeadlessCms::getPlugin()->getTemplates();
    }

    /**
     * @return Collection<class-string<PageTemplate>, string>
     */
    public static function getTemplates(): Collection
    {
        /** @phpstan-ignore-next-line */
        return static::getTemplateClasses()
            ->mapWithKeys(
                fn ($class): array => [$class => $class::title()]
            );
    }

    public static function getTemplateName(string $class): string
    {
        return Str::of($class)->afterLast('\\')->snake()->toString();
    }

    /**
     *
     * @return array<int, Group>
     */
    public static function getTemplateSchemas(): array
    {
         /** @phpstan-ignore-next-line */
        return static::getTemplateClasses()
            ->map(fn ($class): Group => Group::make($class::schema())
                ->afterStateHydrated(fn ($component, $state) => $component->getChildComponentContainer()->fill($state))
                ->statePath('data.content')
                ->visible(fn ($get) => $get('data.template') === $class))
            ->toArray();
    }

    /**
     *
     * @param array{template:string, temp_content: array<string,array<string,mixed>>} $data
     * @return array{template:string, "data.content":array<string,mixed>}
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['data.content'] = $data['temp_content'][static::getTemplateName($data['template'])];
        unset($data['temp_content']);

        return $data;
    }

     /**
     *
     * @param array{template:string, temp_content: array<string,array<string,mixed>>} $data
     * @return array{template:string, "data.content":array<string,mixed>}
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $this->mutateFormDataBeforeFill($data);
    }

     /**
     *
     * @return array<string, PageRegistration>
     */
    public static function getPages(): array
    {
        return [
            'index'  => ListFilamentPages::route('/'),
            'create' => CreateFilamentPage::route('/create'),
            'edit'   => EditFilamentPage::route('/{record:id}/edit'),
        ];
    }


    // public static function getNavigationItems(): array
    // {

    //     return NavigationGroup::make()
    //     ->label(static::getNavigationLabel())
    //     ->parentItem(static::getNavigationGroup());

    //     $initial = parent::getNavigationItems();

    //     \Filament\Navigation\NavigationItem::class

    //     return $initial;

    //     /**
    //      *  return [
    //         \Filament\Navigation\NavigationItem::make(static::getNavigationLabel())
    //             ->group(static::getNavigationGroup())
    //             ->parentItem(static::getNavigationParentItem())
    //             ->icon(static::getNavigationIcon())
    //             ->activeIcon(static::getActiveNavigationIcon())
    //             ->isActiveWhen(fn () => request()->routeIs(static::getRouteBaseName() . '.*'))
    //             ->badge(static::getNavigationBadge(), color: static::getNavigationBadgeColor())
    //             ->badgeTooltip(static::getNavigationBadgeTooltip())
    //             ->sort(static::getNavigationSort())
    //             ->url(static::getNavigationUrl()),
    //     ];
    //      */
    // }
}
