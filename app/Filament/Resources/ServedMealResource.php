<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServedMealResource\Pages;
use App\Filament\Resources\ServedMealResource\RelationManagers;
use App\Models\ServedMeal;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ServedMealResource extends Resource
{
    protected static ?string $model = ServedMeal::class;

    protected static ?string $navigationGroup = 'Meal Management';
    protected static ?string $navigationLabel = 'Daily Menu';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('date')
                    ->required()
                    ->native(false)
                    ->displayFormat('l, d M Y'),
                Forms\Components\Select::make('meal_item_id')
                    ->relationship('mealItem', 'name')
                    ->required()
                    ->preload()
                    ->searchable()
                    ->label('Meal Option'),
                Forms\Components\TextInput::make('max_orders')
                    ->numeric()
                    ->default(null)
                    ->helperText('Leave empty for unlimited'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->date('D, d M Y')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('mealItem.name')
                    ->label('Main Dish')
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_orders')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => $state ?? 'Unlimited'),
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
            'index' => Pages\ListServedMeals::route('/'),
            'create' => Pages\CreateServedMeal::route('/create'),
            'edit' => Pages\EditServedMeal::route('/{record}/edit'),
        ];
    }
}
