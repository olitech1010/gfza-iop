<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MisAsset extends Model
{
    protected $fillable = [
        'asset_tag',
        'serial_number',
        'name',
        'type',
        'status',
        'purchase_date',
        'assigned_to_user_id',
        'notes',
    ];

    protected $casts = [
        'purchase_date' => 'date',
    ];

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }
}
