<?php

namespace Database\Seeders;

use App\Models\ConferenceRoom;
use App\Models\RoomBooking;
use App\Models\User;
use Illuminate\Database\Seeder;

class ConferenceRoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create the single conference room
        $room = ConferenceRoom::firstOrCreate(
            ['name' => 'Main Conference Room'],
            [
                'capacity' => 20,
                'location' => '3rd Floor, Block A',
                'has_projector' => true,
                'has_video_conference' => true,
                'is_active' => true,
            ]
        );

        // Create some sample bookings if users exist
        $users = User::take(5)->get();

        if ($users->isEmpty()) {
            return;
        }

        // Sample upcoming bookings
        $startDate = now()->addDay()->setTime(9, 0);

        foreach ($users->take(3) as $index => $user) {
            RoomBooking::factory()->create([
                'conference_room_id' => $room->id,
                'user_id' => $user->id,
                'start_time' => $startDate->copy()->addDays($index)->setTime(9 + ($index * 2), 0),
                'end_time' => $startDate->copy()->addDays($index)->setTime(10 + ($index * 2), 0),
                'status' => 'confirmed',
            ]);
        }
    }
}
