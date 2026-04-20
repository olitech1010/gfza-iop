<?php

namespace App\Filament\Resources\AuditTripResource\Pages;

use App\Filament\Resources\AuditTripResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAuditTrip extends EditRecord
{
    protected static string $resource = AuditTripResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
