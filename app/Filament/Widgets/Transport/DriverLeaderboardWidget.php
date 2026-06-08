<?php

namespace App\Filament\Widgets\Transport;

use App\Models\Driver;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;

class DriverLeaderboardWidget extends Widget
{
    protected static string $view = 'filament.widgets.transport.driver-leaderboard-widget';

    protected int|string|array $columnSpan = 'full';

    /**
     * Get top-performing drivers (min 3 reviews, sorted by avg rating).
     */
    public function getTopDrivers(): Collection
    {
        return Driver::where('status', 'active')
            ->withCount('performanceReviews')
            ->having('performance_reviews_count', '>=', 1)
            ->get()
            ->map(function (Driver $driver) {
                return [
                    'id' => $driver->id,
                    'name' => $driver->name,
                    'avg_rating' => $driver->average_rating,
                    'total_reviews' => $driver->total_reviews,
                    'performance_status' => $driver->performance_status,
                    'manual_rating' => $driver->manual_rating,
                    'automatic_rating' => $driver->automatic_rating,
                ];
            })
            ->filter(fn ($d) => $d['avg_rating'] !== null)
            ->sortByDesc('avg_rating')
            ->values();
    }

    /**
     * Get flagged drivers (below 3.0 or restricted/not_recommended).
     */
    public function getFlaggedDrivers(): Collection
    {
        return $this->getTopDrivers()
            ->filter(fn ($d) => $d['avg_rating'] < 3.0 || $d['performance_status'] === 'needs_attention')
            ->values();
    }
}
