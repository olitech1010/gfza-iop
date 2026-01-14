<?php

namespace App\Filament\Resources\MealOrderResource\Pages;

use App\Filament\Resources\MealOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMealOrders extends ListRecords
{
    protected static string $resource = MealOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
