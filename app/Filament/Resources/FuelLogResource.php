<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FuelLogResource\Pages;
use App\Models\FuelLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FuelLogResource extends Resource
{
    protected static ?string $model = FuelLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-fire';

    protected static ?string $navigationGroup = 'Transport & Logistics';

    protected static ?string $navigationLabel = 'Fuel Log';

    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Fuel Entry')
                    ->schema([
                        Forms\Components\Select::make('vehicle_id')
                            ->label('Vehicle')
                            ->relationship('vehicle', 'registration_number')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->make} {$record->model} ({$record->registration_number})")
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('driver_id')
                            ->label('Driver')
                            ->relationship('driver', 'id')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->user->name ?? 'Unknown')
                            ->searchable()
                            ->preload()
                            ->nullable(),
                        Forms\Components\DatePicker::make('fuel_date')
                            ->required()
                            ->default(now()),
                        Forms\Components\Select::make('fuel_type')
                            ->options([
                                'petrol' => 'Petrol',
                                'diesel' => 'Diesel',
                            ])
                            ->required()
                            ->default('petrol'),
                    ])->columns(2),

                Forms\Components\Section::make('Quantity & Cost')
                    ->schema([
                        Forms\Components\TextInput::make('litres')
                            ->numeric()
                            ->required()
                            ->suffix('L')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get) {
                                $litres = (float) $get('litres');
                                $cpl = (float) $get('cost_per_litre');
                                if ($litres && $cpl) {
                                    $set('total_cost', number_format($litres * $cpl, 2, '.', ''));
                                }
                            }),
                        Forms\Components\TextInput::make('cost_per_litre')
                            ->numeric()
                            ->required()
                            ->prefix('GHS')
                            ->label('Cost per Litre')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get) {
                                $litres = (float) $get('litres');
                                $cpl = (float) $get('cost_per_litre');
                                if ($litres && $cpl) {
                                    $set('total_cost', number_format($litres * $cpl, 2, '.', ''));
                                }
                            }),
                        Forms\Components\TextInput::make('total_cost')
                            ->numeric()
                            ->required()
                            ->prefix('GHS')
                            ->label('Total Cost'),
                        Forms\Components\TextInput::make('mileage_at_fill')
                            ->numeric()
                            ->suffix('km')
                            ->label('Mileage at Fill'),
                    ])->columns(2),

                Forms\Components\Section::make('Receipt')
                    ->schema([
                        Forms\Components\TextInput::make('station')
                            ->maxLength(255)
                            ->label('Fuel Station'),
                        Forms\Components\TextInput::make('receipt_number')
                            ->maxLength(255),
                    ])->columns(2)->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('fuel_date')
                    ->label('Date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('vehicle.registration_number')
                    ->label('Vehicle')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('driver.user.name')
                    ->label('Driver')
                    ->placeholder('—')
                    ->searchable(),
                Tables\Columns\TextColumn::make('fuel_type')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'petrol' ? 'info' : 'warning')
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                Tables\Columns\TextColumn::make('litres')
                    ->suffix(' L')
                    ->sortable(),
                Tables\Columns\TextColumn::make('cost_per_litre')
                    ->money('GHS')
                    ->label('Rate')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('total_cost')
                    ->money('GHS')
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('mileage_at_fill')
                    ->label('Mileage')
                    ->suffix(' km')
                    ->numeric()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('station')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('fuel_date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('vehicle_id')
                    ->relationship('vehicle', 'registration_number')
                    ->label('Vehicle')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('fuel_type')
                    ->options([
                        'petrol' => 'Petrol',
                        'diesel' => 'Diesel',
                    ]),
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

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFuelLogs::route('/'),
            'create' => Pages\CreateFuelLog::route('/create'),
            'edit' => Pages\EditFuelLog::route('/{record}/edit'),
        ];
    }
}
