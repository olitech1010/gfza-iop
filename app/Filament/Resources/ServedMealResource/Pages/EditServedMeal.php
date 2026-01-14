<?php

namespace App\Filament\Resources\ServedMealResource\Pages;

use App\Filament\Resources\ServedMealResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditServedMeal extends EditRecord
{
    protected static string $resource = ServedMealResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
