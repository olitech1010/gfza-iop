<?php

namespace App\Filament\Resources\MealRequestResource\Pages;

use App\Filament\Resources\MealRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMealRequest extends EditRecord
{
    protected static string $resource = MealRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
