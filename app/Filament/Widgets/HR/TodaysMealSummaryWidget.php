<?php

namespace App\Filament\Widgets\HR;

use App\Models\MealRequest;
use App\Models\WeeklyMenu;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class TodaysMealSummaryWidget extends Widget
{
    protected static string $view = 'filament.widgets.hr.todays-meal-summary-widget';

    protected int|string|array $columnSpan = 1;

    protected static ?int $sort = 10;

    public static function canView(): bool
    {
        $user = Auth::user();

        return $user?->hasRole(['hr_manager', 'super_admin']);
    }

    public function getViewData(): array
    {
        $today = now();
        $dayOfWeek = strtolower($today->format('l'));

        // Find the current week's menu
        $currentMenu = WeeklyMenu::where('week_start', '<=', $today)
            ->where('week_end', '>=', $today)
            ->first();

        $totalOrders = 0;
        $servedCount = 0;
        $pendingCount = 0;
        $mealBreakdown = collect();

        if ($currentMenu) {
            // Get today's meal requests
            $todaysRequests = MealRequest::whereHas('weeklyMenuItem', function ($query) use ($currentMenu, $dayOfWeek) {
                $query->where('weekly_menu_id', $currentMenu->id)
                    ->where('day_of_week', $dayOfWeek);
            })
                ->with('weeklyMenuItem.mealItem')
                ->get();

            $totalOrders = $todaysRequests->count();
            $servedCount = $todaysRequests->where('is_served', true)->count();
            $pendingCount = $totalOrders - $servedCount;

            // Breakdown by meal item
            $mealBreakdown = $todaysRequests
                ->groupBy(fn($req) => $req->weeklyMenuItem?->mealItem?->name ?? 'Unknown')
                ->map(fn($group) => $group->count())
                ->sortDesc()
                ->take(5);
        }

        return [
            'totalOrders' => $totalOrders,
            'servedCount' => $servedCount,
            'pendingCount' => $pendingCount,
            'mealBreakdown' => $mealBreakdown,
            'summaryUrl' => '/admin/meals/meal-summary',
        ];
    }
}
