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
        // Ensure MIS department exists
        $misDept = Department::firstOrCreate(
            ['name' => 'Management Information System (MIS)']
        );

        // Create or update super admin - will be preserved on re-seed
        $admin = User::updateOrCreate(
            ['email' => 'admin@gfza.gov.gh'],
            [
                'name' => 'System Administrator',
                'first_name' => 'System',
                'last_name' => 'Administrator',
                'password' => Hash::make('00000000'),
                'is_active' => true,
                'department_id' => $misDept->id,
                'staff_id' => 'GFZA/001/00',
                'job_title' => 'Super Admin',
            ]
        );

        // Ensure super_admin role is assigned
        if (!$admin->hasRole('super_admin')) {
            $admin->assignRole('super_admin');
        }
    }
}

