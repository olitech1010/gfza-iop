<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'license_number',
        'license_expiry',
        'license_class',
        'phone',
        'status',
    ];

    protected $casts = [
        'license_expiry' => 'date',
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function requisitions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(VehicleRequisition::class);
    }

    public function fuelLogs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(FuelLog::class);
    }

    public function auditTrips(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AuditTrip::class);
    }

    /**
     * Get the driver's name via user relationship.
     */
    public function getNameAttribute(): string
    {
        return $this->user->name ?? 'Unknown';
    }

    public function performanceReviews(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DriverTripReview::class);
    }

    /**
     * Overall average rating across all reviews.
     */
    public function getAverageRatingAttribute(): ?float
    {
        $avg = $this->performanceReviews()->whereNotNull('overall_rating')->avg('overall_rating');

        return $avg ? round($avg, 2) : null;
    }

    /**
     * Average rating for manual transmission trips.
     */
    public function getManualRatingAttribute(): ?float
    {
        $avg = $this->performanceReviews()->manual()->whereNotNull('overall_rating')->avg('overall_rating');

        return $avg ? round($avg, 2) : null;
    }

    /**
     * Average rating for automatic transmission trips.
     */
    public function getAutomaticRatingAttribute(): ?float
    {
        $avg = $this->performanceReviews()->automatic()->whereNotNull('overall_rating')->avg('overall_rating');

        return $avg ? round($avg, 2) : null;
    }

    /**
     * Total number of reviews for this driver.
     */
    public function getTotalReviewsAttribute(): int
    {
        return $this->performanceReviews()->count();
    }

    /**
     * Performance status based on average rating.
     */
    public function getPerformanceStatusAttribute(): string
    {
        $avg = $this->average_rating;

        if (is_null($avg)) {
            return 'no_reviews';
        }

        return match (true) {
            $avg >= 4.5 => 'excellent',
            $avg >= 3.5 => 'good',
            $avg >= 3.0 => 'average',
            default => 'needs_attention',
        };
    }
}
