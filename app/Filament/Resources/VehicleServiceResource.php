<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VehicleServiceResource\Pages;
use App\Models\VehicleService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class VehicleServiceResource extends Resource
{
    protected static ?string $model = VehicleService::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static ?string $navigationGroup = 'Transport & Logistics';

    protected static ?string $navigationLabel = 'Fleet Care';

    protected static ?int $navigationSort = 5;

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user && $user->canAccessTransport();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Service Details')
                    ->schema([
                        Forms\Components\Select::make('vehicle_id')
                            ->label('Vehicle')
                            ->relationship('vehicle', 'registration_number')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->make} {$record->model} ({$record->registration_number})")
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('service_type')
                            ->options([
                                'routine' => 'Routine Service',
                                'repair' => 'Repair',
                                'inspection' => 'Inspection',
                                'tire_change' => 'Tyre Change',
                                'oil_change' => 'Oil Change',
                                'brake_service' => 'Brake Service',
                                'battery' => 'Battery',
                                'accident_repair' => 'Accident Repair',
                                'body_work' => 'Body Work',
                                'electrical' => 'Electrical',
                                'other' => 'Other',
                            ])
                            ->required(),
                        Forms\Components\DatePicker::make('service_date')
                            ->required()
                            ->default(now()),
                        Forms\Components\TextInput::make('mileage_at_service')
                            ->numeric()
                            ->suffix('km')
                            ->label('Mileage at Service'),
                        Forms\Components\Select::make('status')
                            ->options([
                                'scheduled' => 'Scheduled',
                                'in_progress' => 'In Progress',
                                'completed' => 'Completed',
                            ])
                            ->required()
                            ->default('scheduled'),
                        Forms\Components\TextInput::make('service_provider')
                            ->required()
                            ->maxLength(255)
                            ->label('Service Provider / Garage'),
                    ])->columns(2),

                Forms\Components\Section::make('Work Performed')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->required()
                            ->maxLength(65535)
                            ->label('Description of Work')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('parts_replaced')
                            ->maxLength(65535)
                            ->label('Parts Replaced')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Cost & Billing')
                    ->schema([
                        Forms\Components\TextInput::make('cost')
                            ->numeric()
                            ->prefix('GHS')
                            ->default(0)
                            ->required(),
                        Forms\Components\TextInput::make('invoice_number')
                            ->maxLength(255),
                        Forms\Components\Select::make('approved_by')
                            ->label('Approved By')
                            ->relationship('approvedByUser', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable(),
                    ])->columns(3),

                Forms\Components\Section::make('Next Service')
                    ->schema([
                        Forms\Components\DatePicker::make('next_service_date')
                            ->label('Next Service Date'),
                        Forms\Components\TextInput::make('next_service_mileage')
                            ->numeric()
                            ->suffix('km')
                            ->label('Next Service Mileage'),
                    ])->columns(2)->collapsible(),

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
                Tables\Columns\TextColumn::make('vehicle.registration_number')
                    ->label('Vehicle')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('service_type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'routine', 'oil_change', 'inspection' => 'info',
                        'repair', 'accident_repair', 'body_work' => 'danger',
                        'tire_change', 'brake_service' => 'warning',
                        'battery', 'electrical' => 'primary',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => str_replace('_', ' ', ucwords($state, '_'))),
                Tables\Columns\TextColumn::make('service_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('service_provider')
                    ->label('Provider')
                    ->limit(25)
                    ->searchable(),
                Tables\Columns\TextColumn::make('cost')
                    ->money('GHS')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'scheduled' => 'gray',
                        'in_progress' => 'warning',
                        'completed' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => str_replace('_', ' ', ucfirst($state))),
                Tables\Columns\TextColumn::make('next_service_date')
                    ->label('Next Service')
                    ->date()
                    ->color(fn (VehicleService $record): string => $record->next_service_date && $record->next_service_date->isPast() ? 'danger' : ($record->next_service_date && $record->next_service_date->diffInDays(now()) < 14 ? 'warning' : 'gray'))
                    ->toggleable(),
            ])
            ->defaultSort('service_date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('vehicle_id')
                    ->relationship('vehicle', 'registration_number')
                    ->label('Vehicle')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('service_type')
                    ->options([
                        'routine' => 'Routine',
                        'repair' => 'Repair',
                        'inspection' => 'Inspection',
                        'oil_change' => 'Oil Change',
                        'tire_change' => 'Tyre Change',
                        'brake_service' => 'Brake Service',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'scheduled' => 'Scheduled',
                        'in_progress' => 'In Progress',
                        'completed' => 'Completed',
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
            'index' => Pages\ListVehicleServices::route('/'),
            'create' => Pages\CreateVehicleService::route('/create'),
            'edit' => Pages\EditVehicleService::route('/{record}/edit'),
        ];
    }
}
