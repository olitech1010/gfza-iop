<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FuelLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'driver_id',
        'fuel_date',
        'fuel_type',
        'litres',
        'cost_per_litre',
        'total_cost',
        'mileage_at_fill',
        'station',
        'receipt_number',
    ];

    protected $casts = [
        'fuel_date' => 'date',
        'litres' => 'decimal:2',
        'cost_per_litre' => 'decimal:2',
        'total_cost' => 'decimal:2',
    ];

    public function vehicle(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }
}
