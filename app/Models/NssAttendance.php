<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NssAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'check_in_time',
        'check_out_time',
        'status',
        'check_in_method',
        'check_out_method',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'check_in_time' => 'datetime:H:i:s',
            'check_out_time' => 'datetime:H:i:s',
        ];
    }

    /**
     * Get the user that owns this attendance record.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to filter by today's date.
     */
    public function scopeToday($query)
    {
        return $query->where('date', today());
    }

    /**
     * Scope to filter by current month.
     */
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('date', now()->month)
            ->whereYear('date', now()->year);
    }

    /**
     * Scope to filter by current week (Monday to Sunday).
     */
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('date', [
            now()->startOfWeek(),
            now()->endOfWeek(),
        ]);
    }

    /**
     * Scope to filter by a specific week starting from given date.
     */
    public function scopeForWeek($query, $startDate)
    {
        $start = \Carbon\Carbon::parse($startDate)->startOfWeek();
        $end = $start->copy()->endOfWeek();

        return $query->whereBetween('date', [$start, $end]);
    }

    /**
     * Scope to filter late arrivals.
     */
    public function scopeLate($query)
    {
        return $query->where('status', 'late');
    }

    /**
     * Scope to filter present (on time).
     */
    public function scopePresent($query)
    {
        return $query->where('status', 'present');
    }

    /**
     * Scope to filter absent.
     */
    public function scopeAbsent($query)
    {
        return $query->where('status', 'absent');
    }

    /**
     * Scope for records without check-out.
     */
    public function scopePendingCheckout($query)
    {
        return $query->whereNotNull('check_in_time')
            ->whereNull('check_out_time');
    }

    /**
     * Check if this attendance was late.
     */
    public function isLate(): bool
    {
        return $this->status === 'late';
    }

    /**
     * Calculate working hours for this attendance.
     */
    public function getWorkingHoursAttribute(): ?float
    {
        if (! $this->check_in_time || ! $this->check_out_time) {
            return null;
        }

        $checkIn = Carbon::parse($this->check_in_time);
        $checkOut = Carbon::parse($this->check_out_time);

        return round($checkIn->diffInMinutes($checkOut) / 60, 2);
    }

    /**
     * Get formatted check-in time.
     */
    public function getFormattedCheckInAttribute(): ?string
    {
        return $this->check_in_time
            ? Carbon::parse($this->check_in_time)->format('h:i A')
            : null;
    }

    /**
     * Get formatted check-out time.
     */
    public function getFormattedCheckOutAttribute(): ?string
    {
        return $this->check_out_time
            ? Carbon::parse($this->check_out_time)->format('h:i A')
            : null;
    }
}
