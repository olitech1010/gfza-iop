<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WeeklyMenuItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'weekly_menu_id',
        'meal_item_id',
        'day_of_week',
    ];

    public function weeklyMenu(): BelongsTo
    {
        return $this->belongsTo(WeeklyMenu::class);
    }

    public function mealItem(): BelongsTo
    {
        return $this->belongsTo(MealItem::class);
    }

    public function requests(): HasMany
    {
        return $this->hasMany(MealRequest::class);
    }

    /**
     * Get the date for this menu item.
     */
    public function getDateAttribute(): ?\Carbon\Carbon
    {
        $weekStart = $this->weeklyMenu->week_start;
        $dayIndex = match ($this->day_of_week) {
            'monday' => 0,
            'tuesday' => 1,
            'wednesday' => 2,
            'thursday' => 3,
            'friday' => 4,
            default => 0,
        };

        return $weekStart->copy()->addDays($dayIndex);
    }

    /**
     * Get display name with day.
     */
    public function getDisplayNameAttribute(): string
    {
        return ucfirst($this->day_of_week).' - '.$this->mealItem->name;
    }

    /**
     * Get total requests for this menu item.
     */
    public function getTotalRequestsAttribute(): int
    {
        return $this->requests()->count();
    }
}
