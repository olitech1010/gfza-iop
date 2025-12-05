<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ['name' => 'Management Information Systems', 'description' => 'IT Support and Infrastructure'],
            ['name' => 'Human Resources', 'description' => 'Employee Relations and Recruitment'],
            ['name' => 'Finance', 'description' => 'Accounting and Payroll'],
            ['name' => 'Administration', 'description' => 'General Administration'],
            ['name' => 'Legal', 'description' => 'Legal Affairs'],
        ];

        foreach ($departments as $dept) {
            Department::create($dept);
        }
    }
}
