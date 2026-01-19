<?php

namespace App\Filament\Resources\WeeklyMenuResource\Pages;

use App\Filament\Resources\WeeklyMenuResource;
use App\Models\WeeklyMenuItem;
use Filament\Resources\Pages\CreateRecord;

class CreateWeeklyMenu extends CreateRecord
{
    protected static string $resource = WeeklyMenuResource::class;

    protected function afterCreate(): void
    {
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];

        foreach ($days as $day) {
            $mealIds = $this->data["{$day}_meals"] ?? [];

            foreach ($mealIds as $mealItemId) {
                WeeklyMenuItem::create([
                    'weekly_menu_id' => $this->record->id,
                    'meal_item_id' => $mealItemId,
                    'day_of_week' => $day,
                ]);
            }
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }
}
