<?php

namespace Database\Factories;

use App\Models\Caterer;
use App\Models\WeeklyMenu;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WeeklyMenu>
 */
class WeeklyMenuFactory extends Factory
{
    protected $model = WeeklyMenu::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $weekStart = fake()->dateTimeBetween('now', '+4 weeks');
        // Ensure it's a Monday
        $weekStart = \Carbon\Carbon::parse($weekStart)->startOfWeek();
        $weekEnd = $weekStart->copy()->addDays(4);

        return [
            'caterer_id' => Caterer::factory(),
            'week_start' => $weekStart,
            'week_end' => $weekEnd,
            'week_label' => $weekStart->format('jS').' - '.$weekEnd->format('jS F Y'),
            'status' => 'draft',
        ];
    }

    /**
     * Indicate that the menu is published.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'published',
        ]);
    }

    /**
     * Indicate that the menu is closed.
     */
    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'closed',
        ]);
    }
}
