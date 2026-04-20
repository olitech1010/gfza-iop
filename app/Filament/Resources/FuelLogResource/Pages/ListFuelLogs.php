<?php

namespace App\Filament\Resources\FuelLogResource\Pages;

use App\Filament\Resources\FuelLogResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFuelLogs extends ListRecords
{
    protected static string $resource = FuelLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Log Fuel'),
        ];
    }
}
