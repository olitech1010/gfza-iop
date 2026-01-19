<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class WeeklyMenu extends Model
{
    use HasFactory;

    protected $fillable = [
        'caterer_id',
        'week_start',
        'week_end',
        'week_label',
        'available_days',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'week_start' => 'date',
            'week_end' => 'date',
            'available_days' => 'array',
        ];
    }

    public function caterer(): BelongsTo
    {
        return $this->belongsTo(Caterer::class);
    }

    public function menuItems(): HasMany
    {
        return $this->hasMany(WeeklyMenuItem::class);
    }

    public function requests(): HasManyThrough
    {
        return $this->hasManyThrough(MealRequest::class, WeeklyMenuItem::class);
    }

    /**
     * Scope to get only published menus.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope to get the menu for the current week.
     */
    public function scopeForCurrentWeek(Builder $query): Builder
    {
        $today = now();

        return $query->where('week_start', '<=', $today)
            ->where('week_end', '>=', $today);
    }

    /**
     * Scope for current published menu (staff can select).
     */
    public function scopeCurrent(Builder $query): Builder
    {
        return $query->published()->forCurrentWeek();
    }

    /**
     * Scope for past menus.
     */
    public function scopePast(Builder $query): Builder
    {
        return $query->where('week_end', '<', now());
    }

    /**
     * Get available days or default to Mon-Fri.
     */
    public function getAvailableDaysListAttribute(): array
    {
        return $this->available_days ?? ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
    }

    /**
     * Get menu items grouped by day (only available days).
     */
    public function getMenuByDay(): array
    {
        $grouped = [];
        $days = $this->available_days_list;

        foreach ($days as $day) {
            $grouped[$day] = $this->menuItems()
                ->where('day_of_week', $day)
                ->with('mealItem')
                ->get();
        }

        return $grouped;
    }

    /**
     * Get total meal requests for this menu.
     */
    public function getTotalRequestsAttribute(): int
    {
        return $this->requests()->count();
    }

    /**
     * Get staff requests count (non-NSS).
     */
    public function getStaffRequestsCountAttribute(): int
    {
        return $this->requests()->where('is_nss', false)->count();
    }

    /**
     * Get NSS requests count.
     */
    public function getNssRequestsCountAttribute(): int
    {
        return $this->requests()->where('is_nss', true)->count();
    }

    /**
     * Get total paid amount.
     */
    public function getTotalPaidAttribute(): float
    {
        return $this->requests()
            ->where('is_paid', true)
            ->where('is_nss', false)
            ->sum('amount_due');
    }

    /**
     * Get total pending amount.
     */
    public function getTotalPendingAttribute(): float
    {
        return $this->requests()
            ->where('is_paid', false)
            ->where('is_nss', false)
            ->sum('amount_due');
    }

    /**
     * Check if menu is current (can accept requests).
     */
    public function getIsCurrentAttribute(): bool
    {
        return $this->status === 'published'
            && $this->week_start <= now()
            && $this->week_end >= now();
    }
}
