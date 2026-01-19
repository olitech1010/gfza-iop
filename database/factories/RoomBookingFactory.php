<?php

namespace Database\Factories;

use App\Models\ConferenceRoom;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RoomBooking>
 */
class RoomBookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startTime = fake()->dateTimeBetween('now', '+2 weeks');
        $endTime = (clone $startTime)->modify('+'.fake()->numberBetween(30, 120).' minutes');

        return [
            'conference_room_id' => ConferenceRoom::factory(),
            'user_id' => User::factory(),
            'title' => fake()->randomElement([
                'Team Standup',
                'Project Review',
                'Client Meeting',
                'Training Session',
                'Interview',
                'Budget Planning',
                'Strategy Discussion',
                'Department Meeting',
            ]),
            'description' => fake()->optional()->sentence(),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'status' => fake()->randomElement(['confirmed', 'confirmed', 'confirmed', 'cancelled']), // 75% confirmed
        ];
    }

    /**
     * Indicate that the booking is confirmed.
     */
    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'confirmed',
        ]);
    }

    /**
     * Indicate that the booking is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
        ]);
    }
}
