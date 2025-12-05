<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin User
        $adminRole = Role::where('slug', 'admin')->first();
        $misDept = Department::where('name', 'Management Information Systems')->first();

        $admin = User::create([
            'name' => 'System Administrator',
            'email' => 'admin@gfzaiop.com',
            'password' => Hash::make('password'),
            'department_id' => $misDept?->id,
            'is_active' => true,
        ]);
        $admin->roles()->attach($adminRole);

        // Create HR User
        $hrRole = Role::where('slug', 'hr')->first();
        $hrDept = Department::where('name', 'Human Resources')->first();

        $hr = User::create([
            'name' => 'HR Manager',
            'email' => 'hr@gfzaiop.com',
            'password' => Hash::make('password'),
            'department_id' => $hrDept?->id,
            'is_active' => true,
        ]);
        $hr->roles()->attach($hrRole);

        // Create Staff User
        $staffRole = Role::where('slug', 'staff')->first();
        $financeDept = Department::where('name', 'Finance')->first();

        $staff = User::create([
            'name' => 'John Doe',
            'email' => 'jdoe@gfzaiop.com',
            'password' => Hash::make('password'),
            'department_id' => $financeDept?->id,
            'is_active' => true,
        ]);
        $staff->roles()->attach($staffRole);
    }
}
