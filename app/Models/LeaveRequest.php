<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    protected $fillable = [
        'user_id',
        'start_date',
        'end_date',
        'days_requested',
        'reason',
        'status',
        'dept_head_approved_at',
        'dept_head_id',
        'hr_approved_at',
        'hr_id',
        'rejection_reason',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'dept_head_approved_at' => 'datetime',
        'hr_approved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function deptHead()
    {
        return $this->belongsTo(User::class, 'dept_head_id');
    }

    public function hr()
    {
        return $this->belongsTo(User::class, 'hr_id');
    }
}
