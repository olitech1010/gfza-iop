<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Department;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure an MIS department exists for the admin
        $misDept = Department::firstOrCreate(
            ['code' => 'MIS'],
            ['name' => 'Management Information Systems']
        );

        User::updateOrCreate(
            ['email' => 'admin@gfza.gov.gh'],
            [
                'name' => 'System Administrator',
                'first_name' => 'System',
                'last_name' => 'Admin',
                'password' => Hash::make('password'),
                'is_active' => true,
                'department_id' => $misDept->id,
                'staff_id' => 'ADMIN001',
                'job_title' => 'Super Admin',
            ]
        );
    }
}
