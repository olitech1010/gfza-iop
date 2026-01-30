<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // Core Setup
            RolesPermissionSeeder::class,
            LocationSeeder::class,
            DepartmentSeeder::class,
            
            // Users
            AdminUserSeeder::class,
            MisStaffSeeder::class,
            
            // Reference Data
            ConferenceRoomSeeder::class,
            MealManagementSeeder::class,
            
            // Sample Activity Data
            SampleActivitySeeder::class,
        ]);
    }
}

