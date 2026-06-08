<?php

namespace App\Filament\Widgets;

use App\Models\VehicleRequisition;
use Filament\Widgets\Widget;

class PendingTripReviewWidget extends Widget
{
    protected static string $view = 'filament.widgets.pending-trip-review-widget';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 2;

    public static function canView(): bool
    {
        return static::getPendingCount() > 0;
    }

    public static function getPendingCount(): int
    {
        $userId = auth()->id();
        if (! $userId) {
            return 0;
        }

        return VehicleRequisition::where('requester_id', $userId)
            ->where('status', 'completed')
            ->whereDoesntHave('reviews', function ($q) {
                $q->where('review_type', 'passenger');
            })
            ->count();
    }

    public function getPendingTrips(): \Illuminate\Support\Collection
    {
        return VehicleRequisition::where('requester_id', auth()->id())
            ->where('status', 'completed')
            ->whereDoesntHave('reviews', function ($q) {
                $q->where('review_type', 'passenger');
            })
            ->with(['driver.user', 'vehicle'])
            ->orderByDesc('requested_date')
            ->limit(5)
            ->get();
    }
}
