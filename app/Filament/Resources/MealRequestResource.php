<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MealRequestResource\Pages;
use App\Models\MealRequest;
use App\Models\WeeklyMenu;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class MealRequestResource extends Resource
{
    protected static ?string $model = MealRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Meal Management';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Meal Request';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Request Information')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Staff Member')
                            ->relationship('user', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->native(false),

                        Forms\Components\Select::make('weekly_menu_item_id')
                            ->label('Meal Selection')
                            ->relationship(
                                'weeklyMenuItem',
                                'id',
                                fn (Builder $query) => $query->with(['mealItem', 'weeklyMenu'])
                            )
                            ->getOptionLabelFromRecordUsing(fn ($record) => ucfirst($record->day_of_week).' - '.$record->mealItem->name)
                            ->required()
                            ->searchable()
                            ->preload()
                            ->native(false),

                        Forms\Components\Toggle::make('is_nss')
                            ->label('NSS Personnel')
                            ->helperText('NSS personnel do not pay for meals'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Payment & Serving')
                    ->schema([
                        Forms\Components\TextInput::make('amount_due')
                            ->label('Amount Due (GHS)')
                            ->numeric()
                            ->default(5.00)
                            ->prefix('GHS')
                            ->disabled(fn (callable $get) => $get('is_nss')),

                        Forms\Components\Toggle::make('is_paid')
                            ->label('Paid')
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $set('paid_at', now()->toDateTimeString());
                                } else {
                                    $set('paid_at', null);
                                }
                            }),

                        Forms\Components\DateTimePicker::make('paid_at')
                            ->label('Paid At')
                            ->disabled(),

                        Forms\Components\Toggle::make('is_served')
                            ->label('Served')
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $set('served_at', now()->toDateTimeString());
                                } else {
                                    $set('served_at', null);
                                }
                            }),

                        Forms\Components\DateTimePicker::make('served_at')
                            ->label('Served At')
                            ->disabled(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Staff Member')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('user.department.name')
                    ->label('Department')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('weeklyMenuItem.day_of_week')
                    ->label('Day')
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('weeklyMenuItem.mealItem.name')
                    ->label('Meal')
                    ->limit(30)
                    ->searchable(),

                Tables\Columns\IconColumn::make('is_nss')
                    ->label('NSS')
                    ->boolean()
                    ->trueIcon('heroicon-o-academic-cap')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('warning'),

                Tables\Columns\TextColumn::make('amount_due')
                    ->label('Amount')
                    ->money('GHS')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_paid')
                    ->label('Paid')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\IconColumn::make('is_served')
                    ->label('Served')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('warning'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Requested')
                    ->dateTime('M j, g:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('department')
                    ->label('Department')
                    ->relationship('user.department', 'name'),

                Tables\Filters\SelectFilter::make('weekly_menu')
                    ->label('Weekly Menu')
                    ->options(fn () => WeeklyMenu::orderBy('week_start', 'desc')->pluck('week_label', 'id'))
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'],
                            fn (Builder $q, $value): Builder => $q->whereHas('weeklyMenuItem', fn ($q2) => $q2->where('weekly_menu_id', $value))
                        );
                    }),

                Tables\Filters\TernaryFilter::make('is_nss')
                    ->label('NSS Personnel'),

                Tables\Filters\TernaryFilter::make('is_paid')
                    ->label('Payment Status'),

                Tables\Filters\TernaryFilter::make('is_served')
                    ->label('Serving Status'),
            ])
            ->actions([
                Tables\Actions\Action::make('mark_paid')
                    ->label('Mark Paid')
                    ->icon('heroicon-o-currency-dollar')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (MealRequest $record): bool => ! $record->is_paid && ! $record->is_nss)
                    ->action(fn (MealRequest $record) => $record->markAsPaid()),

                Tables\Actions\Action::make('mark_served')
                    ->label('Mark Served')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (MealRequest $record): bool => ! $record->is_served)
                    ->action(fn (MealRequest $record) => $record->markAsServed()),

                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('bulk_mark_paid')
                        ->label('Mark as Paid')
                        ->icon('heroicon-o-currency-dollar')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (Collection $records): void {
                            $records->each(function (MealRequest $record) {
                                if (! $record->is_nss && ! $record->is_paid) {
                                    $record->markAsPaid();
                                }
                            });
                        }),

                    Tables\Actions\BulkAction::make('bulk_mark_served')
                        ->label('Mark as Served')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (Collection $records): void {
                            $records->each(function (MealRequest $record) {
                                if (! $record->is_served) {
                                    $record->markAsServed();
                                }
                            });
                        }),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMealRequests::route('/'),
            'create' => Pages\CreateMealRequest::route('/create'),
            'edit' => Pages\EditMealRequest::route('/{record}/edit'),
        ];
    }
}
