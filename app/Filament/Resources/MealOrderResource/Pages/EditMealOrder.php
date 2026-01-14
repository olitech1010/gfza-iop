<?php

namespace App\Filament\Resources\MealOrderResource\Pages;

use App\Filament\Resources\MealOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMealOrder extends EditRecord
{
    protected static string $resource = MealOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
