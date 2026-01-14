<?php

namespace App\Filament\Resources\MealItemResource\Pages;

use App\Filament\Resources\MealItemResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMealItem extends EditRecord
{
    protected static string $resource = MealItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
