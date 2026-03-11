<?php

namespace App\Filament\Resources\StoreTransactionResource\Pages;

use App\Filament\Resources\StoreTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStoreTransactions extends ListRecords
{
    protected static string $resource = StoreTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No create action, items are received/issued from the Items Resource
        ];
    }
}
