<?php

namespace App\Filament\Resources\ConferenceRoomResource\Pages;

use App\Filament\Resources\ConferenceRoomResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditConferenceRoom extends EditRecord
{
    protected static string $resource = ConferenceRoomResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
