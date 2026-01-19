<?php

namespace Database\Seeders;

use App\Models\Caterer;
use App\Models\MealItem;
use Illuminate\Database\Seeder;

class MealManagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Caterers
        $caterers = [
            ['name' => 'AL GRAY AL HANNAH', 'contact' => 'Hannah Gray', 'phone' => '0241234567', 'is_active' => true],
            ['name' => 'GOLDEN SPOON CATERING', 'contact' => 'Kofi Mensah', 'phone' => '0201234567', 'is_active' => true],
            ['name' => "MAMA'S KITCHEN", 'contact' => 'Ama Serwaa', 'phone' => '0551234567', 'is_active' => true],
        ];

        foreach ($caterers as $caterer) {
            Caterer::firstOrCreate(['name' => $caterer['name']], $caterer);
        }

        // Create Meal Items (real Ghanaian meals)
        $meals = [
            // Monday meals
            'Plain Rice with Sausage and Gizzard Vegetable Stew',
            'EBA with Agushie Stew, Tuna and Meat',
            'Rice Balls with Groundnut Soup, Meat and Fish',

            // Tuesday meals
            'Waakye with Meat and Egg',
            'Yam Mpotompoto with Fish and Egg',
            'Fufu with Goat Light Soup',

            // Wednesday meals
            'Fried Rice with Chicken and Fried Egg',
            'Yam with Kontomire Abomu with Fish & Egg',
            'Kenkey (Corn & Millet) with Red Fish',
            'Konkontey with Palmnut Soup, Meat and Fish',

            // Thursday meals
            'Jollof Rice with Chicken and Egg',
            'Banku with Okro Soup, Meat and Tuna',
            'Acheke with Fish and Egg',

            // Friday meals
            'Fried Yam with Pepper Sauce and Fish',
            'Beans Stew with Fried Plantain',
            'TZ with Ayoyo Soup and Meat',
            'Red Red with Plantain and Fish',

            // Additional meals
            'Ampesi with Kontomire Stew',
            'Ga Kenkey with Shito and Fried Fish',
            'Hausa Koko with Koose',
            'Tuozaafi with Ayoyo Soup',
        ];

        foreach ($meals as $mealName) {
            MealItem::firstOrCreate(
                ['name' => $mealName],
                [
                    'description' => null,
                    'is_active' => true,
                ]
            );
        }

        $this->command->info('Created '.count($caterers).' caterers and '.count($meals).' meal items.');
    }
}
