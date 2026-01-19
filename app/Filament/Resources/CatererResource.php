<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CatererResource\Pages;
use App\Models\Caterer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CatererResource extends Resource
{
    protected static ?string $model = Caterer::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $navigationGroup = 'Meal Management';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Caterer Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Caterer Name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., AL GRAY AL HANNAH')
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('contact')
                            ->label('Contact Person')
                            ->maxLength(255)
                            ->placeholder('Contact person name'),

                        Forms\Components\TextInput::make('phone')
                            ->label('Phone Number')
                            ->tel()
                            ->maxLength(255)
                            ->placeholder('e.g., 0241234567'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->helperText('Inactive caterers will not appear in menu creation'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Caterer Name')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('contact')
                    ->label('Contact Person')
                    ->searchable(),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Phone')
                    ->searchable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                Tables\Columns\TextColumn::make('weekly_menus_count')
                    ->label('Menus')
                    ->counts('weeklyMenus')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status'),
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
            'index' => Pages\ListCaterers::route('/'),
            'create' => Pages\CreateCaterer::route('/create'),
            'edit' => Pages\EditCaterer::route('/{record}/edit'),
        ];
    }
}
