<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VehicleResource\Pages;
use App\Models\Vehicle;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class VehicleResource extends Resource
{
    protected static ?string $model = Vehicle::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationGroup = 'Transport & Logistics';

    protected static ?string $navigationLabel = 'Fleet Registry';

    protected static ?int $navigationSort = 2;

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user && $user->canAccessTransport();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Vehicle Information')
                    ->schema([
                        Forms\Components\TextInput::make('registration_number')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->placeholder('e.g. GR-1234-21'),
                        Forms\Components\TextInput::make('make')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g. Toyota'),
                        Forms\Components\TextInput::make('model')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g. Land Cruiser'),
                        Forms\Components\TextInput::make('year')
                            ->numeric()
                            ->minValue(1990)
                            ->maxValue(now()->year + 1),
                        Forms\Components\Select::make('type')
                            ->options([
                                'sedan' => 'Sedan',
                                'suv' => 'SUV',
                                'pickup' => 'Pickup',
                                'bus' => 'Bus',
                                'van' => 'Van',
                                'motorcycle' => 'Motorcycle',
                                'other' => 'Other',
                            ])
                            ->required(),
                        Forms\Components\Select::make('fuel_type')
                            ->options([
                                'petrol' => 'Petrol',
                                'diesel' => 'Diesel',
                                'electric' => 'Electric',
                                'hybrid' => 'Hybrid',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('color')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('current_mileage')
                            ->numeric()
                            ->default(0)
                            ->suffix('km'),
                    ])->columns(2),

                Forms\Components\Section::make('Status & Compliance')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'available' => 'Available',
                                'in_use' => 'In Use',
                                'maintenance' => 'Under Maintenance',
                                'decommissioned' => 'Decommissioned',
                            ])
                            ->required()
                            ->default('available'),
                        Forms\Components\DatePicker::make('insurance_expiry')
                            ->label('Insurance Expiry Date'),
                        Forms\Components\DatePicker::make('roadworthy_expiry')
                            ->label('Roadworthy Expiry Date'),
                        Forms\Components\Select::make('assigned_driver_id')
                            ->label('Default Driver')
                            ->relationship('assignedDriver', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable(),
                    ])->columns(2),

                Forms\Components\Section::make('Notes')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ])->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('registration_number')
                    ->label('Reg. No.')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('make')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('model')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'available' => 'success',
                        'in_use' => 'info',
                        'maintenance' => 'warning',
                        'decommissioned' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => str_replace('_', ' ', ucfirst($state))),
                Tables\Columns\TextColumn::make('current_mileage')
                    ->label('Mileage')
                    ->numeric()
                    ->suffix(' km')
                    ->sortable(),
                Tables\Columns\TextColumn::make('insurance_expiry')
                    ->label('Insurance')
                    ->date()
                    ->color(fn (Vehicle $record): string => $record->insurance_expiry && $record->insurance_expiry->isPast() ? 'danger' : ($record->insurance_expiry && $record->insurance_expiry->diffInDays(now()) < 30 ? 'warning' : 'gray'))
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('roadworthy_expiry')
                    ->label('Roadworthy')
                    ->date()
                    ->color(fn (Vehicle $record): string => $record->roadworthy_expiry && $record->roadworthy_expiry->isPast() ? 'danger' : ($record->roadworthy_expiry && $record->roadworthy_expiry->diffInDays(now()) < 30 ? 'warning' : 'gray'))
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'available' => 'Available',
                        'in_use' => 'In Use',
                        'maintenance' => 'Under Maintenance',
                        'decommissioned' => 'Decommissioned',
                    ]),
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'sedan' => 'Sedan',
                        'suv' => 'SUV',
                        'pickup' => 'Pickup',
                        'bus' => 'Bus',
                        'van' => 'Van',
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
            'index' => Pages\ListVehicles::route('/'),
            'create' => Pages\CreateVehicle::route('/create'),
            'edit' => Pages\EditVehicle::route('/{record}/edit'),
        ];
    }
}
