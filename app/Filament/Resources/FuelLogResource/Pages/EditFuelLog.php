<?php

namespace App\Filament\Resources\FuelLogResource\Pages;

use App\Filament\Resources\FuelLogResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFuelLog extends EditRecord
{
    protected static string $resource = FuelLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
