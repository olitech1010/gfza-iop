<?php

namespace App\Filament\Resources\WeeklyMenuResource\Pages;

use App\Filament\Resources\WeeklyMenuResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWeeklyMenus extends ListRecords
{
    protected static string $resource = WeeklyMenuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
