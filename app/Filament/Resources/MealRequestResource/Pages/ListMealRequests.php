<?php

namespace App\Filament\Resources\MealRequestResource\Pages;

use App\Filament\Resources\MealRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMealRequests extends ListRecords
{
    protected static string $resource = MealRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
