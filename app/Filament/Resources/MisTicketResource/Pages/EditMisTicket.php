<?php

namespace App\Filament\Resources\MisTicketResource\Pages;

use App\Filament\Resources\MisTicketResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMisTicket extends EditRecord
{
    protected static string $resource = MisTicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
