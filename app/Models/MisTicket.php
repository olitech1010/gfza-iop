<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MisTicket extends Model
{
    protected $fillable = [
        'subject',
        'description',
        'status',
        'priority',
        'category',
        'user_id',
        'assigned_to_user_id',
        'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    public function requester()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assignedStaff()
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }
}
