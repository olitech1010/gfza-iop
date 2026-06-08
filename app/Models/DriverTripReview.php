<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverTripReview extends Model
{
    use HasFactory;

    /**
     * Rating fields used for admin reviews.
     */
    public const ADMIN_RATINGS = [
        'vehicle_condition',
        'cleanliness',
        'fuel_efficiency',
        'timeliness',
        'rule_compliance',
    ];

    /**
     * Rating fields used for passenger reviews.
     */
    public const PASSENGER_RATINGS = [
        'punctuality',
        'driving_quality',
        'professionalism',
        'safety_feeling',
        'overall_satisfaction',
    ];

    /**
     * Human-readable descriptions for each admin rating category.
     *
     * @var array<string, string>
     */
    public const ADMIN_RATING_LABELS = [
        'vehicle_condition' => 'Was the vehicle returned without damage? Any new scratches, dents, or mechanical issues?',
        'cleanliness' => 'Interior and exterior cleanliness of the vehicle on return.',
        'fuel_efficiency' => 'Was fuel consumption reasonable for the distance? No unnecessary detours?',
        'timeliness' => 'Was the vehicle returned on schedule? Any unexplained delays?',
        'rule_compliance' => 'Followed speed limits, route, no unauthorized passengers, proper documentation?',
    ];

    /**
     * Human-readable descriptions for each passenger rating category.
     *
     * @var array<string, string>
     */
    public const PASSENGER_RATING_LABELS = [
        'punctuality' => 'Did the driver arrive on time for pickup and depart as scheduled?',
        'driving_quality' => 'Was the driving smooth and controlled? No harsh braking, speeding, or reckless behavior?',
        'professionalism' => 'Was the driver courteous, helpful, and professional in conduct?',
        'safety_feeling' => 'Did you feel safe throughout the entire trip?',
        'overall_satisfaction' => 'Overall, would you want this driver for your next trip?',
    ];

    protected $fillable = [
        'driver_id',
        'vehicle_id',
        'vehicle_requisition_id',
        'audit_trip_id',
        'reviewed_by',
        'review_type',
        'review_date',
        'transmission_used',
        // Admin ratings
        'vehicle_condition',
        'cleanliness',
        'fuel_efficiency',
        'timeliness',
        'rule_compliance',
        // Passenger ratings
        'punctuality',
        'driving_quality',
        'professionalism',
        'safety_feeling',
        'overall_satisfaction',
        // Computed
        'overall_rating',
        // Admin qualitative
        'damage_severity',
        'damage_notes',
        'incidents',
        'mechanical_issues',
        'recommendation',
        // Passenger qualitative
        'compliments',
        'complaints',
        // Shared
        'comments',
    ];

    protected function casts(): array
    {
        return [
            'review_date' => 'date',
            'overall_rating' => 'decimal:2',
        ];
    }

    /**
     * Auto-compute overall_rating as the average of applicable rating fields.
     */
    protected static function booted(): void
    {
        static::saving(function (DriverTripReview $review) {
            $fields = $review->review_type === 'admin'
                ? self::ADMIN_RATINGS
                : self::PASSENGER_RATINGS;

            $ratings = collect($fields)
                ->map(fn (string $field) => $review->{$field})
                ->filter(fn ($value) => ! is_null($value) && $value > 0);

            $review->overall_rating = $ratings->isNotEmpty()
                ? round($ratings->avg(), 2)
                : null;
        });
    }

    public function driver(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function vehicle(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function vehicleRequisition(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(VehicleRequisition::class);
    }

    public function auditTrip(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(AuditTrip::class);
    }

    public function reviewer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Scope to only admin reviews.
     */
    public function scopeAdmin(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('review_type', 'admin');
    }

    /**
     * Scope to only passenger reviews.
     */
    public function scopePassenger(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('review_type', 'passenger');
    }

    /**
     * Scope to reviews for a specific driver.
     */
    public function scopeForDriver(\Illuminate\Database\Eloquent\Builder $query, int $driverId): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('driver_id', $driverId);
    }

    /**
     * Scope to reviews for manual transmission vehicles.
     */
    public function scopeManual(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('transmission_used', 'manual');
    }

    /**
     * Scope to reviews for automatic transmission vehicles.
     */
    public function scopeAutomatic(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('transmission_used', 'automatic');
    }
}
