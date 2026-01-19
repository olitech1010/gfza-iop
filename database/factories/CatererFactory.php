<?php

namespace Database\Factories;

use App\Models\Caterer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Caterer>
 */
class CatererFactory extends Factory
{
    protected $model = Caterer::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company().' Catering',
            'contact' => fake()->name(),
            'phone' => fake()->phoneNumber(),
            'is_active' => true,
        ];
    }
}
