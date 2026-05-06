<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VehicleRequisitionResource\Pages;
use App\Models\Driver;
use App\Models\Vehicle;
use App\Models\VehicleRequisition;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class VehicleRequisitionResource extends Resource
{
    protected static ?string $model = VehicleRequisition::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Transport & Logistics';

    protected static ?string $navigationLabel = 'Vehicle Requisitions';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Request Details')
                    ->schema([
                        Forms\Components\Select::make('requester_id')
                            ->label('Requested By')
                            ->relationship('requester', 'name')
                            ->default(auth()->id())
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('department_id')
                            ->label('Department')
                            ->relationship('department', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\TextInput::make('destination')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('purpose')
                            ->required()
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        Forms\Components\DatePicker::make('requested_date')
                            ->required()
                            ->default(now())
                            ->label('Date Needed'),
                        Forms\Components\TimePicker::make('requested_time')
                            ->label('Preferred Departure Time'),
                        Forms\Components\DatePicker::make('return_date')
                            ->label('Expected Return Date'),
                        Forms\Components\TextInput::make('number_of_passengers')
                            ->numeric()
                            ->default(1)
                            ->minValue(1)
                            ->maxValue(50),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference_number')
                    ->label('Ref #')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('requester.name')
                    ->label('Requested By')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('department.name')
                    ->label('Department')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('destination')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('requested_date')
                    ->label('Date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('vehicle.registration_number')
                    ->label('Vehicle')
                    ->placeholder('—')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('driver.user.name')
                    ->label('Driver')
                    ->placeholder('—')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'vehicle_assigned' => 'info',
                        'transport_approved' => 'primary',
                        'admin_approved' => 'success',
                        'in_progress' => 'warning',
                        'completed' => 'success',
                        'rejected' => 'danger',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Pending',
                        'vehicle_assigned' => 'Vehicle Assigned',
                        'transport_approved' => 'Transport Approved',
                        'admin_approved' => 'Admin Approved',
                        'in_progress' => 'In Progress',
                        'completed' => 'Completed',
                        'rejected' => 'Rejected',
                        'cancelled' => 'Cancelled',
                        default => ucfirst($state),
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'vehicle_assigned' => 'Vehicle Assigned',
                        'transport_approved' => 'Transport Approved',
                        'admin_approved' => 'Admin Approved',
                        'in_progress' => 'In Progress',
                        'completed' => 'Completed',
                        'rejected' => 'Rejected',
                        'cancelled' => 'Cancelled',
                    ]),
                Tables\Filters\SelectFilter::make('department_id')
                    ->relationship('department', 'name')
                    ->label('Department'),
            ])
            ->actions([
                // Head of Transport: Assign Vehicle & Driver
                Tables\Actions\Action::make('assign')
                    ->label('Assign Vehicle')
                    ->icon('heroicon-o-truck')
                    ->color('info')
                    ->visible(fn (VehicleRequisition $record): bool => $record->status === 'pending')
                    ->form([
                        Forms\Components\Select::make('vehicle_id')
                            ->label('Vehicle')
                            ->options(Vehicle::where('status', 'available')->get()->mapWithKeys(fn (Vehicle $v) => [$v->id => "{$v->make} {$v->model} ({$v->registration_number})"]))
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('driver_id')
                            ->label('Driver')
                            ->options(Driver::where('status', 'active')->with('user')->get()->mapWithKeys(fn (Driver $d) => [$d->id => $d->user->name." ({$d->license_number})"]))
                            ->searchable()
                            ->required(),
                    ])
                    ->action(function (VehicleRequisition $record, array $data): void {
                        $record->update([
                            'vehicle_id' => $data['vehicle_id'],
                            'driver_id' => $data['driver_id'],
                            'assigned_by' => auth()->id(),
                            'assigned_at' => now(),
                            'status' => 'vehicle_assigned',
                        ]);
                        Notification::make()->success()->title('Vehicle & driver assigned.')->send();
                    }),

                // Head of Transport: Approve
                Tables\Actions\Action::make('transport_approve')
                    ->label('Transport Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('primary')
                    ->visible(fn (VehicleRequisition $record): bool => $record->status === 'vehicle_assigned')
                    ->requiresConfirmation()
                    ->action(function (VehicleRequisition $record): void {
                        $record->update([
                            'transport_approved_by' => auth()->id(),
                            'transport_approved_at' => now(),
                            'status' => 'transport_approved',
                        ]);
                        Notification::make()->success()->title('Transport approval granted.')->send();
                    }),

                // Admin Director: Final Approve
                Tables\Actions\Action::make('admin_approve')
                    ->label('Admin Approve')
                    ->icon('heroicon-o-shield-check')
                    ->color('success')
                    ->visible(fn (VehicleRequisition $record): bool => $record->status === 'transport_approved')
                    ->requiresConfirmation()
                    ->action(function (VehicleRequisition $record): void {
                        $record->update([
                            'admin_approved_by' => auth()->id(),
                            'admin_approved_at' => now(),
                            'status' => 'admin_approved',
                        ]);
                        Notification::make()->success()->title('Admin approval granted. Trip can begin.')->send();
                    }),

                // Head of Transport: Start Trip
                Tables\Actions\Action::make('start_trip')
                    ->label('Start Trip')
                    ->icon('heroicon-o-play')
                    ->color('warning')
                    ->visible(fn (VehicleRequisition $record): bool => $record->status === 'admin_approved')
                    ->form([
                        Forms\Components\TextInput::make('start_mileage')
                            ->label('Start Mileage (km)')
                            ->numeric()
                            ->required(),
                        Forms\Components\DateTimePicker::make('departure_time')
                            ->label('Departure Time')
                            ->required()
                            ->default(now()),
                    ])
                    ->action(function (VehicleRequisition $record, array $data): void {
                        $record->update([
                            'start_mileage' => $data['start_mileage'],
                            'departure_time' => $data['departure_time'],
                            'status' => 'in_progress',
                        ]);

                        // Update vehicle status
                        if ($record->vehicle) {
                            $record->vehicle->update(['status' => 'in_use']);
                        }

                        Notification::make()->success()->title('Trip started.')->send();
                    }),

                // Head of Transport: Complete Trip
                Tables\Actions\Action::make('complete_trip')
                    ->label('Complete Trip')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn (VehicleRequisition $record): bool => $record->status === 'in_progress')
                    ->form([
                        Forms\Components\TextInput::make('end_mileage')
                            ->label('End Mileage (km)')
                            ->numeric()
                            ->required(),
                        Forms\Components\DateTimePicker::make('arrival_time')
                            ->label('Arrival Time')
                            ->required()
                            ->default(now()),
                        Forms\Components\TextInput::make('fuel_used')
                            ->label('Fuel Used (litres)')
                            ->numeric()
                            ->step(0.01),
                        Forms\Components\Textarea::make('trip_notes')
                            ->label('Trip Notes'),
                    ])
                    ->action(function (VehicleRequisition $record, array $data): void {
                        $record->update([
                            'end_mileage' => $data['end_mileage'],
                            'arrival_time' => $data['arrival_time'],
                            'fuel_used' => $data['fuel_used'] ?? null,
                            'trip_notes' => $data['trip_notes'] ?? null,
                            'status' => 'completed',
                        ]);

                        // Update vehicle mileage and status
                        if ($record->vehicle) {
                            $record->vehicle->update([
                                'current_mileage' => $data['end_mileage'],
                                'status' => 'available',
                            ]);
                        }

                        Notification::make()->success()->title('Trip completed. Vehicle returned.')->send();
                    }),

                // Reject (available at multiple stages)
                Tables\Actions\Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (VehicleRequisition $record): bool => in_array($record->status, ['pending', 'vehicle_assigned', 'transport_approved']))
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->required()
                            ->label('Reason for Rejection'),
                    ])
                    ->requiresConfirmation()
                    ->action(function (VehicleRequisition $record, array $data): void {
                        $record->update([
                            'rejection_reason' => $data['rejection_reason'],
                            'status' => 'rejected',
                        ]);
                        Notification::make()->danger()->title('Requisition rejected.')->send();
                    }),

                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVehicleRequisitions::route('/'),
            'create' => Pages\CreateVehicleRequisition::route('/create'),
            'view' => Pages\ViewVehicleRequisition::route('/{record}'),
        ];
    }
}
