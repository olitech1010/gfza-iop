<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'conference_room_id',
        'user_id',
        'title',
        'description',
        'start_time',
        'end_time',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
        ];
    }

    public function conferenceRoom(): BelongsTo
    {
        return $this->belongsTo(ConferenceRoom::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to find overlapping bookings for conflict detection.
     */
    public function scopeOverlapping(Builder $query, int $roomId, string $startTime, string $endTime, ?int $excludeId = null): Builder
    {
        return $query->where('conference_room_id', $roomId)
            ->where('status', '!=', 'cancelled')
            ->where(function (Builder $q) use ($startTime, $endTime) {
                $q->where(function (Builder $inner) use ($startTime) {
                    // New booking starts during existing booking
                    $inner->where('start_time', '<=', $startTime)
                        ->where('end_time', '>', $startTime);
                })->orWhere(function (Builder $inner) use ($endTime) {
                    // New booking ends during existing booking
                    $inner->where('start_time', '<', $endTime)
                        ->where('end_time', '>=', $endTime);
                })->orWhere(function (Builder $inner) use ($startTime, $endTime) {
                    // New booking completely contains existing booking
                    $inner->where('start_time', '>=', $startTime)
                        ->where('end_time', '<=', $endTime);
                });
            })
            ->when($excludeId, fn (Builder $q) => $q->where('id', '!=', $excludeId));
    }

    /**
     * Check if this booking overlaps with any existing bookings.
     */
    public function hasConflict(): bool
    {
        return self::overlapping(
            $this->conference_room_id,
            $this->start_time->toDateTimeString(),
            $this->end_time->toDateTimeString(),
            $this->id
        )->exists();
    }

    /**
     * Get formatted duration for display.
     */
    public function getDurationAttribute(): string
    {
        $diff = $this->start_time->diff($this->end_time);

        if ($diff->h > 0 && $diff->i > 0) {
            return "{$diff->h}h {$diff->i}m";
        } elseif ($diff->h > 0) {
            return "{$diff->h} hour".($diff->h > 1 ? 's' : '');
        } else {
            return "{$diff->i} minutes";
        }
    }
}


