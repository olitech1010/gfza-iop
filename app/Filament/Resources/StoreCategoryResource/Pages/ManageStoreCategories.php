<?php

namespace App\Filament\Resources\StoreCategoryResource\Pages;

use App\Filament\Resources\StoreCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageStoreCategories extends ManageRecords
{
    protected static string $resource = StoreCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
