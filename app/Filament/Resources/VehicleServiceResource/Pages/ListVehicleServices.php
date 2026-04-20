<?php

namespace App\Filament\Resources\VehicleServiceResource\Pages;

use App\Filament\Resources\VehicleServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVehicleServices extends ListRecords
{
    protected static string $resource = VehicleServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Log Service'),
        ];
    }
}
