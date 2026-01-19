<?php

namespace Database\Factories;

use App\Models\MealRequest;
use App\Models\User;
use App\Models\WeeklyMenuItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MealRequest>
 */
class MealRequestFactory extends Factory
{
    protected $model = MealRequest::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $isNss = fake()->boolean(20); // 20% chance of being NSS

        return [
            'user_id' => User::factory(),
            'weekly_menu_item_id' => WeeklyMenuItem::factory(),
            'is_nss' => $isNss,
            'amount_due' => $isNss ? 0 : 5.00,
            'is_paid' => $isNss ? false : fake()->boolean(70),
            'paid_at' => null,
            'paid_by' => null,
            'is_served' => false,
            'served_at' => null,
            'served_by' => null,
        ];
    }

    /**
     * Indicate that the request is paid.
     */
    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_paid' => true,
            'paid_at' => now(),
        ]);
    }

    /**
     * Indicate that the request is served.
     */
    public function served(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_served' => true,
            'served_at' => now(),
        ]);
    }

    /**
     * Indicate that the user is NSS.
     */
    public function nss(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_nss' => true,
            'amount_due' => 0,
        ]);
    }
}
