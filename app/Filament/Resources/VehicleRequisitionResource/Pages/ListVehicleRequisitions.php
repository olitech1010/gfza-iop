<?php

namespace App\Filament\Resources\VehicleRequisitionResource\Pages;

use App\Filament\Resources\VehicleRequisitionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVehicleRequisitions extends ListRecords
{
    protected static string $resource = VehicleRequisitionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('New Requisition'),
        ];
    }
}
