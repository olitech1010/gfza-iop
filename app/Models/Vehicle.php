<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'registration_number',
        'make',
        'model',
        'year',
        'type',
        'fuel_type',
        'transmission',
        'color',
        'current_mileage',
        'status',
        'insurance_expiry',
        'roadworthy_expiry',
        'assigned_driver_id',
        'notes',
    ];

    protected $casts = [
        'insurance_expiry' => 'date',
        'roadworthy_expiry' => 'date',
    ];

    public function assignedDriver(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_driver_id');
    }

    public function requisitions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(VehicleRequisition::class);
    }

    public function services(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(VehicleService::class);
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
     * Get the full display name: Make Model (Reg#)
     */
    public function getDisplayNameAttribute(): string
    {
        return "{$this->make} {$this->model} ({$this->registration_number})";
    }
}
