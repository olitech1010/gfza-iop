<?php

namespace App\Filament\Resources\MemoResource\Pages;

use App\Filament\Resources\MemoResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateMemo extends CreateRecord
{
    protected static string $resource = MemoResource::class;
}
