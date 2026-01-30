<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            'Office of the Chief Executive Officer',
            'Legal',
            'Internal Audit',
            'Human Resource',
            'Accounts',
            'Audits',
            'Monitoring',
            'Business Development and Research (BDR)',
            'Stores',
            'Corporate Affairs',
            'Administration',
            'Registry',
            'Procurement',
            'Marketing',
            'Media',
            'Compliance',
            'Management Information System (MIS)',
        ];

        foreach ($departments as $name) {
            Department::firstOrCreate(['name' => $name]);
        }
    }
}
