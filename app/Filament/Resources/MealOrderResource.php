<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MealOrderResource\Pages;
use App\Models\MealOrder;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MealOrderResource extends Resource
{
    protected static ?string $model = MealOrder::class;

    protected static ?string $navigationGroup = 'HR Operations';

    protected static ?string $navigationIcon = 'heroicon-o-cake';

    protected static ?string $navigationLabel = 'Staff Orders';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label('Staff Member'),
                Forms\Components\Select::make('served_meal_id')
                    ->relationship('servedMeal', 'date')
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->date->format('D, d M')} - {$record->mealItem->name}")
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label('Meal Selection'),
                Forms\Components\DateTimePicker::make('ordered_at')
                    ->default(now())
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options([
                        'ordered' => 'Ordered',
                        'collected' => 'Collected',
                        'cancelled' => 'Cancelled',
                    ])
                    ->default('ordered')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Staff')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('servedMeal.date')
                    ->label('Date')
                    ->date('D, d M')
                    ->sortable(),
                Tables\Columns\TextColumn::make('servedMeal.mealItem.name')
                    ->label('Meal')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'ordered',
                        'success' => 'collected',
                        'danger' => 'cancelled',
                    ]),
                Tables\Columns\TextColumn::make('ordered_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
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
            'index' => Pages\ListMealOrders::route('/'),
            'create' => Pages\CreateMealOrder::route('/create'),
            'edit' => Pages\EditMealOrder::route('/{record}/edit'),
        ];
    }
}
