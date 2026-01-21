<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locations = [
            'Tema',
            'Labadi',
            'Labone',
            'Oyibi',
            'Lapaz',
            'Lake Side',
            'Adenta',
            'Madina',
            'Haatso',
            'Ashaley Botwe',
            'Dome',
            'Dansoman',
            'Osu',
            'Tseado',
        ];

        foreach ($locations as $name) {
            Location::firstOrCreate(['name' => $name]);
        }
    }
}
