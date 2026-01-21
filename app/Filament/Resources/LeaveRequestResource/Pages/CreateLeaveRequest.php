<?php

namespace App\Filament\Resources\LeaveRequestResource\Pages;

use App\Filament\Resources\LeaveRequestResource;
use Carbon\Carbon;
use Filament\Resources\Pages\CreateRecord;

class CreateLeaveRequest extends CreateRecord
{
    protected static string $resource = LeaveRequestResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Ensure user_id is set to current user if not provided
        if (empty($data['user_id'])) {
            $data['user_id'] = auth()->id();
        }

        // Calculate days_requested if dates are set
        if (! empty($data['start_date']) && ! empty($data['end_date'])) {
            $start = Carbon::parse($data['start_date']);
            $end = Carbon::parse($data['end_date']);
            $data['days_requested'] = $start->diffInDays($end) + 1;
        }

        return $data;
    }
}
