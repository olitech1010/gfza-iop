<?php

namespace App\Filament\Pages\Meals;

use App\Models\MealRequest;
use App\Models\WeeklyMenu;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class MealSummary extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationGroup = 'Meals';

    protected static ?int $navigationSort = 6;

    protected static ?string $navigationLabel = 'Summary & Reports';

    protected static string $view = 'filament.pages.meals.meal-summary';

    public ?string $selectedWeek = null;

    public function mount(): void
    {
        $currentMenu = WeeklyMenu::current()->first();
        $this->selectedWeek = $currentMenu?->id;
    }

    public function getWeekOptions(): array
    {
        return WeeklyMenu::orderBy('week_start', 'desc')
            ->limit(10)
            ->get()
            ->mapWithKeys(fn ($menu) => [$menu->id => $menu->week_label ?? $menu->week_start->format('M j, Y')])
            ->toArray();
    }

    public function getOverviewStats(): array
    {
        $query = MealRequest::query();

        if ($this->selectedWeek) {
            $query->whereHas('weeklyMenuItem', fn ($q) => $q->where('weekly_menu_id', $this->selectedWeek));
        }

        return [
            'total_meals' => $query->count(),
            'total_staff' => (clone $query)->where('is_nss', false)->count(),
            'total_nss' => (clone $query)->where('is_nss', true)->count(),
            'total_paid' => (clone $query)->where('is_paid', true)->sum('amount_due'),
            'total_pending' => (clone $query)->where('is_paid', false)->where('is_nss', false)->sum('amount_due'),
            'total_served' => (clone $query)->where('is_served', true)->count(),
            'total_unserved' => (clone $query)->where('is_served', false)->count(),
        ];
    }

    public function getDepartmentBreakdown(): array
    {
        $query = MealRequest::query()
            ->join('users', 'meal_requests.user_id', '=', 'users.id')
            ->join('departments', 'users.department_id', '=', 'departments.id')
            ->select(
                'departments.name as department',
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN meal_requests.is_nss = 0 THEN 1 ELSE 0 END) as staff'),
                DB::raw('SUM(CASE WHEN meal_requests.is_nss = 1 THEN 1 ELSE 0 END) as nss'),
                DB::raw('SUM(CASE WHEN meal_requests.is_paid = 1 THEN meal_requests.amount_due ELSE 0 END) as paid'),
                DB::raw('SUM(CASE WHEN meal_requests.is_paid = 0 AND meal_requests.is_nss = 0 THEN meal_requests.amount_due ELSE 0 END) as pending'),
                DB::raw('SUM(CASE WHEN meal_requests.is_served = 1 THEN 1 ELSE 0 END) as served')
            )
            ->groupBy('departments.id', 'departments.name')
            ->orderBy('departments.name');

        if ($this->selectedWeek) {
            $query->whereHas('weeklyMenuItem', fn ($q) => $q->where('weekly_menu_id', $this->selectedWeek));
        }

        return $query->get()->toArray();
    }

    public function getSelectedMenu(): ?WeeklyMenu
    {
        if (! $this->selectedWeek) {
            return null;
        }

        return WeeklyMenu::with('caterer')->find($this->selectedWeek);
    }
}
