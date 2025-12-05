<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'Administrator', 'slug' => 'admin'],
            ['name' => 'HR Manager', 'slug' => 'hr'],
            ['name' => 'MIS Manager', 'slug' => 'mis'],
            ['name' => 'Director', 'slug' => 'director'],
            ['name' => 'Staff', 'slug' => 'staff'],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
