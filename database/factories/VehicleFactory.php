<?php

namespace Database\Factories;

use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Vehicle>
 */
class VehicleFactory extends Factory
{
    protected $model = Vehicle::class;

    public function definition(): array
    {
        return [
            'registration_number' => strtoupper($this->faker->unique()->bothify('??-####-##')),
            'make' => $this->faker->randomElement(['Toyota', 'Nissan', 'Hyundai', 'Kia', 'Ford']),
            'model' => $this->faker->randomElement(['Land Cruiser', 'Hilux', 'Corolla', 'Patrol', 'Tucson']),
            'year' => $this->faker->numberBetween(2015, 2026),
            'type' => $this->faker->randomElement(['sedan', 'suv', 'pickup', 'bus', 'van']),
            'fuel_type' => $this->faker->randomElement(['petrol', 'diesel']),
            'transmission' => $this->faker->randomElement(['manual', 'automatic']),
            'color' => $this->faker->safeColorName(),
            'current_mileage' => $this->faker->numberBetween(10000, 200000),
            'status' => 'available',
        ];
    }

    /**
     * Set vehicle as manual transmission.
     */
    public function manual(): static
    {
        return $this->state(fn () => ['transmission' => 'manual']);
    }

    /**
     * Set vehicle as automatic transmission.
     */
    public function automatic(): static
    {
        return $this->state(fn () => ['transmission' => 'automatic']);
    }
}
