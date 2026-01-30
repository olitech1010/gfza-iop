<?php

namespace App\Filament\Resources\AppraisalResource\Pages;

use App\Filament\Resources\AppraisalResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAppraisals extends ListRecords
{
    protected static string $resource = AppraisalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
