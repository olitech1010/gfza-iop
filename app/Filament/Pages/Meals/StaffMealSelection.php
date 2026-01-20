<?php

namespace App\Filament\Pages\Meals;

use App\Models\MealRequest;
use App\Models\WeeklyMenu;
use App\Models\WeeklyMenuItem;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class StaffMealSelection extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationGroup = 'Meal Management';

    protected static ?int $navigationSort = 0;

    protected static ?string $navigationLabel = 'My Meal Selection';

    protected static string $view = 'filament.pages.meals.staff-meal-selection';

    public ?WeeklyMenu $currentMenu = null;

    public array $selections = [];

    public array $existingRequests = [];

    public function mount(): void
    {
        $this->currentMenu = WeeklyMenu::current()->with(['menuItems.mealItem', 'caterer'])->first();

        if ($this->currentMenu) {
            // Load existing requests for current user
            $this->existingRequests = MealRequest::where('user_id', Auth::id())
                ->whereHas('weeklyMenuItem', fn ($q) => $q->where('weekly_menu_id', $this->currentMenu->id))
                ->with('weeklyMenuItem')
                ->get()
                ->keyBy(fn ($r) => $r->weeklyMenuItem->day_of_week)
                ->toArray();

            // Pre-fill selections
            foreach ($this->existingRequests as $day => $request) {
                $this->selections[$day] = $request['weekly_menu_item_id'];
            }
        }
    }

    public function getMenuByDay(): array
    {
        if (! $this->currentMenu) {
            return [];
        }

        return $this->currentMenu->getMenuByDay();
    }

    public function submitSelections(): void
    {
        $user = Auth::user();
        $isNss = $user->is_nss ?? false;

        foreach ($this->selections as $day => $menuItemId) {
            if (! $menuItemId) {
                continue;
            }

            $menuItem = WeeklyMenuItem::find($menuItemId);
            if (! $menuItem) {
                continue;
            }

            // Check if already exists
            $existing = MealRequest::where('user_id', $user->id)
                ->where('weekly_menu_item_id', $menuItemId)
                ->first();

            if (! $existing) {
                MealRequest::create([
                    'user_id' => $user->id,
                    'weekly_menu_item_id' => $menuItemId,
                    'is_nss' => $isNss,
                    'amount_due' => $isNss ? 0 : 5.00,
                    'is_paid' => false,
                    'is_served' => false,
                ]);
            }
        }

        Notification::make()
            ->title('Meal selections saved!')
            ->success()
            ->send();

        $this->mount(); // Refresh data
    }

    public function getSelectedMealsPreview(): array
    {
        $preview = [];

        foreach ($this->selections as $day => $menuItemId) {
            if ($menuItemId) {
                $menuItem = WeeklyMenuItem::with('mealItem')->find($menuItemId);
                if ($menuItem) {
                    $preview[$day] = $menuItem->mealItem->name;
                }
            }
        }

        return $preview;
    }
}
