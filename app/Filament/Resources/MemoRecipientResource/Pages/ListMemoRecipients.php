<?php

namespace App\Filament\Resources\MemoRecipientResource\Pages;

use App\Filament\Resources\MemoRecipientResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMemoRecipients extends ListRecords
{
    protected static string $resource = MemoRecipientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
