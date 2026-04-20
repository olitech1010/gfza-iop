<?php

namespace App\Filament\Resources\VehicleRequisitionResource\Pages;

use App\Filament\Resources\VehicleRequisitionResource;
use App\Models\VehicleRequisition;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewVehicleRequisition extends ViewRecord
{
    protected static string $resource = VehicleRequisitionResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Request Details')
                    ->schema([
                        Infolists\Components\TextEntry::make('reference_number')
                            ->label('Reference #')
                            ->weight('bold'),
                        Infolists\Components\TextEntry::make('status')
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
                            ->formatStateUsing(fn (string $state): string => str_replace('_', ' ', ucwords($state, '_'))),
                        Infolists\Components\TextEntry::make('requester.name')
                            ->label('Requested By'),
                        Infolists\Components\TextEntry::make('department.name'),
                        Infolists\Components\TextEntry::make('destination'),
                        Infolists\Components\TextEntry::make('purpose')
                            ->columnSpanFull(),
                        Infolists\Components\TextEntry::make('requested_date')
                            ->date(),
                        Infolists\Components\TextEntry::make('requested_time'),
                        Infolists\Components\TextEntry::make('return_date')
                            ->date(),
                        Infolists\Components\TextEntry::make('number_of_passengers'),
                    ])->columns(2),

                Infolists\Components\Section::make('Assignment (Head of Drivers)')
                    ->schema([
                        Infolists\Components\TextEntry::make('vehicle.registration_number')
                            ->label('Vehicle')
                            ->placeholder('Not assigned'),
                        Infolists\Components\TextEntry::make('driver.user.name')
                            ->label('Driver')
                            ->placeholder('Not assigned'),
                        Infolists\Components\TextEntry::make('assignedByUser.name')
                            ->label('Assigned By')
                            ->placeholder('—'),
                        Infolists\Components\TextEntry::make('assigned_at')
                            ->label('Assigned At')
                            ->dateTime()
                            ->placeholder('—'),
                    ])->columns(2)
                    ->visible(fn (VehicleRequisition $record): bool => $record->vehicle_id !== null),

                Infolists\Components\Section::make('Approvals')
                    ->schema([
                        Infolists\Components\TextEntry::make('transportApprover.name')
                            ->label('Transport Approved By')
                            ->placeholder('Pending'),
                        Infolists\Components\TextEntry::make('transport_approved_at')
                            ->dateTime()
                            ->placeholder('—'),
                        Infolists\Components\TextEntry::make('adminApprover.name')
                            ->label('Admin Approved By')
                            ->placeholder('Pending'),
                        Infolists\Components\TextEntry::make('admin_approved_at')
                            ->dateTime()
                            ->placeholder('—'),
                    ])->columns(2)
                    ->visible(fn (VehicleRequisition $record): bool => $record->transport_approved_by !== null),

                Infolists\Components\Section::make('Trip Log')
                    ->schema([
                        Infolists\Components\TextEntry::make('start_mileage')
                            ->suffix(' km')
                            ->placeholder('—'),
                        Infolists\Components\TextEntry::make('end_mileage')
                            ->suffix(' km')
                            ->placeholder('—'),
                        Infolists\Components\TextEntry::make('tripDistance')
                            ->label('Distance Covered')
                            ->suffix(' km')
                            ->placeholder('—'),
                        Infolists\Components\TextEntry::make('fuel_used')
                            ->suffix(' litres')
                            ->placeholder('—'),
                        Infolists\Components\TextEntry::make('departure_time')
                            ->dateTime()
                            ->placeholder('—'),
                        Infolists\Components\TextEntry::make('arrival_time')
                            ->dateTime()
                            ->placeholder('—'),
                        Infolists\Components\TextEntry::make('trip_notes')
                            ->columnSpanFull()
                            ->placeholder('—'),
                    ])->columns(2)
                    ->visible(fn (VehicleRequisition $record): bool => $record->start_mileage !== null),

                Infolists\Components\Section::make('Rejection')
                    ->schema([
                        Infolists\Components\TextEntry::make('rejection_reason')
                            ->columnSpanFull(),
                    ])
                    ->visible(fn (VehicleRequisition $record): bool => $record->status === 'rejected'),
            ]);
    }
}
