<?php

namespace App\Filament\Resources\MealItemResource\Pages;

use App\Filament\Resources\MealItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMealItems extends ListRecords
{
    protected static string $resource = MealItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
