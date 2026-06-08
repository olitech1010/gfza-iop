<?php

namespace App\Filament\Resources\DriverTripReviewResource\Pages;

use App\Filament\Resources\DriverTripReviewResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDriverTripReviews extends ListRecords
{
    protected static string $resource = DriverTripReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
