<?php

namespace App\Filament\Resources\NssAttendanceResource\Pages;

use App\Filament\Resources\NssAttendanceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditNssAttendance extends EditRecord
{
    protected static string $resource = NssAttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
