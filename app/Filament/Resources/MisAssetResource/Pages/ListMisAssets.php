<?php

namespace App\Filament\Resources\MisAssetResource\Pages;

use App\Filament\Resources\MisAssetResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMisAssets extends ListRecords
{
    protected static string $resource = MisAssetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
