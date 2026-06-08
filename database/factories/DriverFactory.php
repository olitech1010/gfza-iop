<?php

namespace Database\Factories;

use App\Models\Driver;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Driver>
 */
class DriverFactory extends Factory
{
    protected $model = Driver::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'license_number' => 'DL-'.$this->faker->unique()->numerify('#######'),
            'license_expiry' => $this->faker->dateTimeBetween('+1 month', '+3 years'),
            'license_class' => $this->faker->randomElement(['B', 'C', 'D', 'E']),
            'phone' => $this->faker->phoneNumber(),
            'status' => 'active',
        ];
    }

    /**
     * Set driver as inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn () => ['status' => 'inactive']);
    }

    /**
     * Set driver as on leave.
     */
    public function onLeave(): static
    {
        return $this->state(fn () => ['status' => 'on_leave']);
    }
}
