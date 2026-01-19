<?php

namespace App\Filament\Resources\ConferenceRoomResource\Pages;

use App\Filament\Resources\ConferenceRoomResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListConferenceRooms extends ListRecords
{
    protected static string $resource = ConferenceRoomResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
