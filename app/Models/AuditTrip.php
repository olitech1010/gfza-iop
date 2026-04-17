<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditTrip extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_name',
        'audit_type',
        'schedule_type',
        'region',
        'sequence_number',
        'company_name',
        'scheduled_date',
        'start_date',
        'end_date',
        'team_members',
        'status',
        'notes',
        'vehicle_id',
        'driver_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
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
