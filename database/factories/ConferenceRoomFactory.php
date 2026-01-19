<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ConferenceRoom>
 */
class ConferenceRoomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Main Conference Room',
            'capacity' => 20,
            'location' => '3rd Floor, Block A',
            'has_projector' => true,
            'has_video_conference' => true,
            'is_active' => true,
        ];
    }
}
