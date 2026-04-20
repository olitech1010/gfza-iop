<?php

namespace App\Filament\Resources\VehicleServiceResource\Pages;

use App\Filament\Resources\VehicleServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVehicleService extends EditRecord
{
    protected static string $resource = VehicleServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
