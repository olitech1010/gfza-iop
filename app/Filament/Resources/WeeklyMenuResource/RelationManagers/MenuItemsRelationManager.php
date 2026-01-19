<?php

namespace App\Filament\Resources\WeeklyMenuResource\RelationManagers;

use App\Models\MealItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class MenuItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'menuItems';

    protected static ?string $title = 'Menu Items';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('meal_item_id')
                    ->label('Meal')
                    ->options(MealItem::where('is_active', true)->pluck('name', 'id'))
                    ->required()
                    ->searchable()
                    ->native(false),

                Forms\Components\Select::make('day_of_week')
                    ->label('Day')
                    ->options([
                        'monday' => 'Monday',
                        'tuesday' => 'Tuesday',
                        'wednesday' => 'Wednesday',
                        'thursday' => 'Thursday',
                        'friday' => 'Friday',
                    ])
                    ->required()
                    ->native(false),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('day_of_week')
                    ->label('Day')
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'monday' => 'info',
                        'tuesday' => 'success',
                        'wednesday' => 'warning',
                        'thursday' => 'danger',
                        'friday' => 'primary',
                        default => 'secondary',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('mealItem.name')
                    ->label('Meal')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('requests_count')
                    ->label('Requests')
                    ->counts('requests')
                    ->sortable(),
            ])
            ->defaultSort('day_of_week')
            ->filters([
                Tables\Filters\SelectFilter::make('day_of_week')
                    ->label('Day')
                    ->options([
                        'monday' => 'Monday',
                        'tuesday' => 'Tuesday',
                        'wednesday' => 'Wednesday',
                        'thursday' => 'Thursday',
                        'friday' => 'Friday',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
