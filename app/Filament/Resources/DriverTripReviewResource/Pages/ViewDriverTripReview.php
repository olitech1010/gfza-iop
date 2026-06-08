<?php

namespace App\Filament\Resources\DriverTripReviewResource\Pages;

use App\Filament\Resources\DriverTripReviewResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDriverTripReview extends ViewRecord
{
    protected static string $resource = DriverTripReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
