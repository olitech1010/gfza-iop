<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MealRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'weekly_menu_item_id',
        'is_nss',
        'amount_due',
        'is_paid',
        'paid_at',
        'paid_by',
        'is_served',
        'served_at',
        'served_by',
    ];

    protected function casts(): array
    {
        return [
            'is_nss' => 'boolean',
            'amount_due' => 'decimal:2',
            'is_paid' => 'boolean',
            'paid_at' => 'datetime',
            'is_served' => 'boolean',
            'served_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function weeklyMenuItem(): BelongsTo
    {
        return $this->belongsTo(WeeklyMenuItem::class);
    }

    public function paidByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    public function servedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'served_by');
    }

    /**
     * Get the meal item through the weekly menu item.
     */
    public function getMealItemAttribute(): ?MealItem
    {
        return $this->weeklyMenuItem?->mealItem;
    }

    /**
     * Get the day of week.
     */
    public function getDayOfWeekAttribute(): ?string
    {
        return $this->weeklyMenuItem?->day_of_week;
    }

    /**
     * Scope for unpaid requests (excluding NSS).
     */
    public function scopeUnpaid(Builder $query): Builder
    {
        return $query->where('is_paid', false)->where('is_nss', false);
    }

    /**
     * Scope for paid requests.
     */
    public function scopePaid(Builder $query): Builder
    {
        return $query->where('is_paid', true);
    }

    /**
     * Scope for served requests.
     */
    public function scopeServed(Builder $query): Builder
    {
        return $query->where('is_served', true);
    }

    /**
     * Scope for unserved requests.
     */
    public function scopeUnserved(Builder $query): Builder
    {
        return $query->where('is_served', false);
    }

    /**
     * Scope to filter by department.
     */
    public function scopeForDepartment(Builder $query, int $departmentId): Builder
    {
        return $query->whereHas('user', function (Builder $q) use ($departmentId) {
            $q->where('department_id', $departmentId);
        });
    }

    /**
     * Scope to filter by weekly menu.
     */
    public function scopeForWeeklyMenu(Builder $query, int $weeklyMenuId): Builder
    {
        return $query->whereHas('weeklyMenuItem', function (Builder $q) use ($weeklyMenuId) {
            $q->where('weekly_menu_id', $weeklyMenuId);
        });
    }

    /**
     * Mark request as paid.
     */
    public function markAsPaid(?int $paidBy = null): void
    {
        $this->update([
            'is_paid' => true,
            'paid_at' => now(),
            'paid_by' => $paidBy ?? auth()->id(),
        ]);
    }

    /**
     * Mark request as served.
     */
    public function markAsServed(?int $servedBy = null): void
    {
        $this->update([
            'is_served' => true,
            'served_at' => now(),
            'served_by' => $servedBy ?? auth()->id(),
        ]);
    }
}
