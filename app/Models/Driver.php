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
}
