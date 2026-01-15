<?php

namespace App\Filament\Resources\MisAssetResource\Pages;

use App\Filament\Resources\MisAssetResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMisAsset extends EditRecord
{
    protected static string $resource = MisAssetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
