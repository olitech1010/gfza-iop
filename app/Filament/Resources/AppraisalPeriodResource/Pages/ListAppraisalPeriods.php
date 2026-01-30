<?php

namespace App\Filament\Resources\AppraisalPeriodResource\Pages;

use App\Filament\Resources\AppraisalPeriodResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAppraisalPeriods extends ListRecords
{
    protected static string $resource = AppraisalPeriodResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
