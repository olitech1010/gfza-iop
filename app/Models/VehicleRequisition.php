<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleRequisition extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_number',
        'requester_id',
        'department_id',
        'destination',
        'purpose',
        'requested_date',
        'requested_time',
        'return_date',
        'number_of_passengers',
        'status',
        'rejection_reason',
        // Assignment (Head of Drivers)
        'vehicle_id',
        'driver_id',
        'assigned_by',
        'assigned_at',
        // Approvals
        'transport_approved_by',
        'transport_approved_at',
        'admin_approved_by',
        'admin_approved_at',
        // Trip log (Head of Drivers)
        'start_mileage',
        'end_mileage',
        'departure_time',
        'arrival_time',
        'fuel_used',
        'trip_notes',
    ];

    protected $casts = [
        'requested_date' => 'date',
        'return_date' => 'date',
        'assigned_at' => 'datetime',
        'transport_approved_at' => 'datetime',
        'admin_approved_at' => 'datetime',
        'departure_time' => 'datetime',
        'arrival_time' => 'datetime',
        'fuel_used' => 'decimal:2',
    ];

    /**
     * Auto-generate reference number on creation.
     */
    protected static function booted(): void
    {
        static::creating(function (VehicleRequisition $requisition) {
            if (empty($requisition->reference_number)) {
                $year = now()->format('Y');
                $last = static::whereYear('created_at', $year)->max('id') ?? 0;
                $requisition->reference_number = 'VR-'.$year.'-'.str_pad($last + 1, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function requester(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function department(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function vehicle(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function assignedByUser(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function transportApprover(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'transport_approved_by');
    }

    public function adminApprover(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_approved_by');
    }

    /**
     * Calculate trip distance from mileage.
     */
    public function getTripDistanceAttribute(): ?int
    {
        if ($this->start_mileage && $this->end_mileage) {
            return $this->end_mileage - $this->start_mileage;
        }

        return null;
    }

    public function reviews(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DriverTripReview::class, 'vehicle_requisition_id');
    }

    /**
     * Check if admin review has been submitted for this trip.
     */
    public function hasAdminReview(): bool
    {
        return $this->reviews()->where('review_type', 'admin')->exists();
    }

    /**
     * Check if passenger review has been submitted for this trip.
     */
    public function hasPassengerReview(): bool
    {
        return $this->reviews()->where('review_type', 'passenger')->exists();
    }

    /**
     * Check if this completed trip needs any reviews.
     */
    public function needsReview(): bool
    {
        return $this->status === 'completed'
            && (! $this->hasAdminReview() || ! $this->hasPassengerReview());
    }
}
