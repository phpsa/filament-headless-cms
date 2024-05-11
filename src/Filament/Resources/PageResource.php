<?php

namespace Phpsa\FilamentHeadlessCms\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Navigation\NavigationItem;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Placeholder;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Forms\Components\DateTimePicker;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Resources\Pages\PageRegistration;
use Phpsa\FilamentHeadlessCms\FilamentHeadlessCms;
use Phpsa\FilamentHeadlessCms\Contracts\FilamentPage;
use Phpsa\FilamentHeadlessCms\Filament\Resources\PageResource\Traits\HasSchemas;
use Phpsa\FilamentHeadlessCms\Filament\Resources\PageResource\Traits\HasTemplate;
use Phpsa\FilamentHeadlessCms\Filament\Resources\PageResource\Pages\EditFilamentPage;
use Phpsa\FilamentHeadlessCms\Filament\Resources\PageResource\Pages\ListFilamentPages;
use Phpsa\FilamentHeadlessCms\Filament\Resources\PageResource\Pages\CreateFilamentPage;
use Phpsa\FilamentHeadlessCms\Filament\Resources\PageResource\Pages\ManageFilamentPages;

class PageResource extends Resource
{
    use HasTemplate;
    use HasSchemas;

    protected static ?string $recordRouteKeyName = 'id';
    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $currentCmsTemplate = null;




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
        return static::getCurrentTemplate()['label'] . ' pages';
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


    public static function getNavigationItems(): array
    {
        $contentTypes = static::getTemplates()->map(fn ($label, $class) =>
        NavigationItem::make($label)
            ->group(static::getNavigationGroup())
            ->parentItem(static::getNavigationParentItem())
            ->icon($class::getNavigationIcon())
            ->activeIcon($class::getActiveNavigationIcon())
            ->isActiveWhen(fn () =>
                request()->routeIs(static::getRouteBaseName() . '.*') &&
                request()->query('cms_template', static::getCurrentTemplateSlug()) === $class::getTemplateSlug())
            ->badge($class::getNavigationBadge(), color: $class::getNavigationBadgeColor())
            ->badgeTooltip($class::getNavigationBadgeTooltip())
            ->sort($class::getNavigationSort())
            ->url(static::getUrl('index', ['template' => $class::getTemplateSlug()])));
        return $contentTypes->toArray();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Split::make([
                    Group::make(
                        [
                            ...static::getPrimaryColumnSchema(),
                            static::getTemplateSchemas(),
                        ]
                    ),
                    Group::make(
                        [
                            Section::make(
                                [
                                    ...static::getSecondaryColumnSchema(),
                                ]
                            ),
                            static::getSideColumnSchemas(),
                            static::getSeoColumnSchema()
                        ]
                    )->grow(false),
                ])
                ->from('md')
                ->columnSpanFull(),
            ])
        ;
    }

    public static function table(Table $table): Table
    {
        $table = $table
            ->columns([

                TextColumn::make('title')
                    ->label(__('filament-headless-cms::pages.form.title'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('slug')
                    ->label(__('filament-headless-cms::pages.form.slug'))
            // ->icon('heroicon-o-external-link')
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
                    ->label(
                        __('filament-headless-cms::pages.form.published_at')
                    ),
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
            ])
            ->reorderable('sort_order', static::getCurrentTemplate('sortable'))
            ->reorderRecordsTriggerAction(
                fn (Action $action, bool $isReordering) => $action
                    ->button()->extraAttributes(['wire:currentcms' => 'template'])
            )
            ->paginated(static::getCurrentTemplate('paginate'));
        if (static::getCurrentTemplate('sortable')) {
            $table = $table->defaultSort('sort_order', 'asc');
        }
        return $table;
    }



    public static function getSecondaryColumnSchema(): array
    {

        $dateFields = static::getCurrentTemplate()['publish_dates'] ? [
            DateTimePicker::make('published_at')
        ->label(__('filament-headless-cms::pages.form.published_at'))
        ->displayFormat(__('filament-headless-cms::pages.dateFormat'))
        ->default(now()),

            DateTimePicker::make('published_until')

        ->label(__('filament-headless-cms::pages.form.published_until'))
        ->displayFormat(__('filament-headless-cms::pages.dateFormat')),

        ] : [
            Hidden::make('published_at')
        ->label(__('filament-headless-cms::pages.form.published_at'))
        ->default(now()),
        ];

        return [

            Group::make([
                Placeholder::make('created_at')
                    ->label(__('filament-headless-cms::pages.form.created_at'))
                    ->content(fn (?FilamentPage $record): string => $record?->created_at->diffForHumans() ?? '-'),

                Placeholder::make('updated_at')
                    ->label(__('filament-headless-cms::pages.form.updated_at'))
                    ->content(fn (?FilamentPage $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
            ])->columns(2),
            ...static::insertBeforeSecondaryColumnSchema(),
            Hidden::make('template')
        ->live()
        ->default(fn (): string => request()->query('template', static::getCurrentTemplate()['class'])),

            Hidden::make('template_slug')
        ->default(fn (): string =>  static::getCurrentTemplate()['slug']),

            ...$dateFields,

            ...static::insertAfterSecondaryColumnSchema(),
        ];
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
            'index'  => ListFilamentPages::route('/{template}'),
            'create' => CreateFilamentPage::route('/{template}/create'),
            'edit'   => EditFilamentPage::route('/{template}/{record:id}/edit'),
        ];
    }


    public static function getSeoColumnSchema(): Group
    {

        if (static::getCurrentTemplate()['seo'] === false) {
            return Group::make([]);
        }
        return
        Group::make([
            Section::make('SEO')
                        ->description('Enter SEO Details for the current content')
                        ->relationship(
                            'seo'
                        )
                        ->schema(
                            [
                                TextInput::make('title')
                                ->required()
                                ->maxLength(255)
                                ->columnSpanFull(),
                                TagsInput::make('keywords')
                             ->placeholder('Enter keywords')
                                ->columnSpanFull(),
                                Textarea::make('description')
                                ->maxLength(65535)
                                ->columnSpanFull(),
                                Select::make('robots')
                                        ->label('Follow')
                                        ->native(false)
                                        ->default('index, follow')
                                        ->options([
                                            'index, follow'       => 'Index and follow',
                                            'no index, follow'    => 'No index and follow',
                                            'index, no follow'    => 'Index and no follow',
                                            'no index, no follow' => 'No index and no follow',
                                        ]),
                            ]
                        )
        ])
        ;
    }


    public static function getEloquentQuery(): Builder
    {

        return parent::getEloquentQuery()->when(static::getCurrentTemplateSlug(), fn(Builder $builder) => $builder->where('template_slug', static::getCurrentTemplateSlug()));
    }


    public static function getUrl(string $name = 'index', array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?Model $tenant = null): string
    {

        if (blank($panel) || Filament::getPanel($panel)->hasTenancy()) {
            $parameters['tenant'] ??= ($tenant ?? Filament::getTenant());
        }

        $routeBaseName = static::getRouteBaseName(panel: $panel);

        $parameters['template'] = isset($parameters['template']) && filled($parameters['template']) ? $parameters['template'] : static::getCurrentTemplateSlug();

        return route("{$routeBaseName}.{$name}", $parameters, $isAbsolute);
    }
}
