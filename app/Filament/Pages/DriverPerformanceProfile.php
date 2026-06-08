<?php

namespace App\Filament\Pages;

use App\Models\Driver;
use App\Models\DriverTripReview;
use Filament\Pages\Page;
use Illuminate\Support\Collection;

class DriverPerformanceProfile extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationGroup = 'Transport & Logistics';

    protected static ?string $navigationLabel = 'Driver Performance';

    protected static ?int $navigationSort = 8;

    protected static string $view = 'filament.pages.driver-performance-profile';

    protected static bool $shouldRegisterNavigation = false;

    public ?Driver $driver = null;

    public function mount(int $driverId): void
    {
        $this->driver = Driver::with('user')->findOrFail($driverId);
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user && $user->canAccessTransport();
    }

    public static function getSlug(): string
    {
        return 'driver-performance/{driverId}';
    }

    public function getTitle(): string
    {
        return 'Driver Performance — '.($this->driver?->name ?? 'Unknown');
    }

    /**
     * Get transmission competency breakdown.
     *
     * @return array{manual: array, automatic: array}
     */
    public function getTransmissionCompetency(): array
    {
        $result = [];
        foreach (['manual', 'automatic'] as $type) {
            $reviews = $this->driver->performanceReviews()
                ->where('transmission_used', $type)
                ->whereNotNull('overall_rating')
                ->get();

            $avgRating = $reviews->avg('overall_rating');
            $topRecommendation = $reviews->where('review_type', 'admin')
                ->whereNotNull('recommendation')
                ->countBy('recommendation')
                ->sortDesc()
                ->keys()
                ->first();

            $result[$type] = [
                'avg' => $avgRating ? round($avgRating, 1) : null,
                'count' => $reviews->count(),
                'recommendation' => $topRecommendation,
            ];
        }

        return $result;
    }

    /**
     * Get average per admin rating category.
     *
     * @return array<string, float|null>
     */
    public function getAdminBreakdown(): array
    {
        $reviews = $this->driver->performanceReviews()->admin()->get();
        $result = [];
        foreach (DriverTripReview::ADMIN_RATINGS as $field) {
            $values = $reviews->pluck($field)->filter();
            $result[$field] = $values->isNotEmpty() ? round($values->avg(), 1) : null;
        }

        return $result;
    }

    /**
     * Get average per passenger rating category.
     *
     * @return array<string, float|null>
     */
    public function getPassengerBreakdown(): array
    {
        $reviews = $this->driver->performanceReviews()->passenger()->get();
        $result = [];
        foreach (DriverTripReview::PASSENGER_RATINGS as $field) {
            $values = $reviews->pluck($field)->filter();
            $result[$field] = $values->isNotEmpty() ? round($values->avg(), 1) : null;
        }

        return $result;
    }

    /**
     * Get incidents from admin reviews.
     */
    public function getIncidents(): Collection
    {
        return $this->driver->performanceReviews()
            ->admin()
            ->where(function ($q) {
                $q->whereNotNull('incidents')
                    ->orWhere('damage_severity', '!=', 'none');
            })
            ->orderByDesc('review_date')
            ->limit(10)
            ->get();
    }

    /**
     * Get recent reviews.
     */
    public function getRecentReviews(): Collection
    {
        return $this->driver->performanceReviews()
            ->with(['vehicle', 'reviewer'])
            ->orderByDesc('review_date')
            ->limit(15)
            ->get();
    }

    /**
     * Get summary stats.
     *
     * @return array{totalTrips: int, totalReviews: int, adminReviews: int, passengerReviews: int, avgRating: float|null, since: string|null}
     */
    public function getSummaryStats(): array
    {
        $reviews = $this->driver->performanceReviews;

        return [
            'totalTrips' => $this->driver->requisitions()->count() + $this->driver->auditTrips()->count(),
            'totalReviews' => $reviews->count(),
            'adminReviews' => $reviews->where('review_type', 'admin')->count(),
            'passengerReviews' => $reviews->where('review_type', 'passenger')->count(),
            'avgRating' => $this->driver->average_rating,
            'since' => $this->driver->created_at?->format('M Y'),
        ];
    }
}
