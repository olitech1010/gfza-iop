<?php

namespace App\Filament\Resources\WeeklyMenuResource\Pages;

use App\Filament\Resources\WeeklyMenuResource;
use App\Models\WeeklyMenuItem;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWeeklyMenu extends EditRecord
{
    protected static string $resource = WeeklyMenuResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Load existing menu items for the checkbox lists
        $menuItems = WeeklyMenuItem::where('weekly_menu_id', $this->record->id)->get();

        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];

        foreach ($days as $day) {
            $data["{$day}_meals"] = $menuItems
                ->where('day_of_week', $day)
                ->pluck('meal_item_id')
                ->toArray();
        }

        return $data;
    }

    protected function afterSave(): void
    {
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];

        foreach ($days as $day) {
            $newMealIds = $this->data["{$day}_meals"] ?? [];
            $existingItems = WeeklyMenuItem::where('weekly_menu_id', $this->record->id)
                ->where('day_of_week', $day)
                ->get();

            // Delete items that are no longer selected
            foreach ($existingItems as $existingItem) {
                if (! in_array($existingItem->meal_item_id, $newMealIds)) {
                    $existingItem->delete();
                }
            }

            // Add new items
            $existingMealIds = $existingItems->pluck('meal_item_id')->toArray();
            foreach ($newMealIds as $mealItemId) {
                if (! in_array($mealItemId, $existingMealIds)) {
                    WeeklyMenuItem::create([
                        'weekly_menu_id' => $this->record->id,
                        'meal_item_id' => $mealItemId,
                        'day_of_week' => $day,
                    ]);
                }
            }
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('publish')
                ->label('Publish Menu')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn () => $this->record->status === 'draft')
                ->action(fn () => $this->record->update(['status' => 'published'])),

            Actions\Action::make('close')
                ->label('Close Menu')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->visible(fn () => $this->record->status === 'published')
                ->action(fn () => $this->record->update(['status' => 'closed'])),

            Actions\DeleteAction::make(),
        ];
    }
}
