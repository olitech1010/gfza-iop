<?php

namespace App\Filament\Resources\CatererResource\Pages;

use App\Filament\Resources\CatererResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCaterers extends ListRecords
{
    protected static string $resource = CatererResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
