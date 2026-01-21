<?php

namespace App\Filament\Widgets\Staff;

use App\Models\MealRequest;
use App\Models\WeeklyMenu;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class TodaysMealWidget extends Widget
{
    protected static string $view = 'filament.widgets.staff.todays-meal-widget';

    protected int|string|array $columnSpan = 1;

    protected static ?int $sort = 10;

    public function getViewData(): array
    {
        $user = Auth::user();
        $today = now();
        $dayOfWeek = strtolower($today->format('l')); // monday, tuesday, etc.

        // Find the current week's menu
        $currentMenu = WeeklyMenu::where('week_start', '<=', $today)
            ->where('week_end', '>=', $today)
            ->first();

        $todaysMeal = null;
        $hasSelectedMeal = false;
        $mealName = null;
        $isServed = false;

        if ($currentMenu) {
            // Find today's meal request for this user
            $todaysMeal = MealRequest::where('user_id', $user->id)
                ->whereHas('weeklyMenuItem', function ($query) use ($currentMenu, $dayOfWeek) {
                    $query->where('weekly_menu_id', $currentMenu->id)
                        ->where('day_of_week', $dayOfWeek);
                })
                ->with('weeklyMenuItem.mealItem')
                ->first();

            if ($todaysMeal) {
                $hasSelectedMeal = true;
                $mealName = $todaysMeal->weeklyMenuItem?->mealItem?->name ?? 'Meal Selected';
                $isServed = $todaysMeal->is_served;
            }
        }

        return [
            'hasSelectedMeal' => $hasSelectedMeal,
            'mealName' => $mealName,
            'isServed' => $isServed,
            'selectUrl' => '/admin/meals/staff-meal-selection',
        ];
    }
}
