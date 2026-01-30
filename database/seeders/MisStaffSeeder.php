<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MisStaffSeeder extends Seeder
{
    public function run(): void
    {
        $misDepartment = Department::firstOrCreate(['name' => 'Management Information System (MIS)']);

        // MIS Staff Members
        // Isaac is dept_head, others are staff + mis_support
        // Ladies: Data Entry / Data Analyst
        // Men: Technical Support

        $misStaff = [
            // HOD
            ['name' => 'Isaac Amoaku-Gyan', 'gender' => 'male', 'role' => 'dept_head', 'job_title' => 'Head of MIS'],
            
            // Female Staff - Data Entry / Data Analyst
            ['name' => 'Joy K. Sosu', 'gender' => 'female', 'role' => 'staff', 'job_title' => 'Data Analyst'],
            ['name' => 'Helena Agyekum', 'gender' => 'female', 'role' => 'staff', 'job_title' => 'Data Entry'],
            ['name' => 'Vida Georgina Darkwah', 'gender' => 'female', 'role' => 'staff', 'job_title' => 'Data Analyst'],
            ['name' => 'Ruth-Joy N. Dowuona', 'gender' => 'female', 'role' => 'staff', 'job_title' => 'Data Entry'],
            ['name' => 'Dorcas D. Amponsah', 'gender' => 'female', 'role' => 'staff', 'job_title' => 'Data Analyst'],
            ['name' => 'Rosemond Agyekum-Boateng', 'gender' => 'female', 'role' => 'staff', 'job_title' => 'Data Entry'],
            
            // Male Staff - Technical Support
            ['name' => 'Asward Alhassan', 'gender' => 'male', 'role' => 'staff', 'job_title' => 'Technical Support'],
            ['name' => 'Mohammed Bukari', 'gender' => 'male', 'role' => 'staff', 'job_title' => 'Technical Support'],
            ['name' => 'Nana Kojo Faibille', 'gender' => 'male', 'role' => 'staff', 'job_title' => 'Technical Support'],
            ['name' => 'Peniel M. Gbeku', 'gender' => 'male', 'role' => 'staff', 'job_title' => 'Technical Support'],
            ['name' => 'Richard Kojo Antwi', 'gender' => 'male', 'role' => 'staff', 'job_title' => 'Technical Support'],
            ['name' => 'Stephen Nii Nortey Anum', 'gender' => 'male', 'role' => 'staff', 'job_title' => 'Technical Support'],
            ['name' => 'Nana Boadi Ansah', 'gender' => 'male', 'role' => 'staff', 'job_title' => 'Technical Support'],
        ];

        // NSS IT Support Staff
        $nssStaff = [
            ['name' => 'Augustine Asante', 'gender' => 'male', 'job_title' => 'NSS, IT Support'],
            ['name' => 'Judith Annor', 'gender' => 'female', 'job_title' => 'NSS, IT Support'],
            ['name' => 'Clement Mensah', 'gender' => 'male', 'job_title' => 'NSS, IT Support'],
        ];

        $defaultPassword = Hash::make('00000000');

        // Create MIS Staff
        foreach ($misStaff as $staffData) {
            $names = $this->parseName($staffData['name']);
            
            $user = User::firstOrCreate(
                ['email' => $this->generateEmail($staffData['name'])],
                [
                    'name' => $staffData['name'],
                    'first_name' => $names['first_name'],
                    'last_name' => $names['last_name'],
                    'password' => $defaultPassword,
                    'department_id' => $misDepartment->id,
                    'job_title' => $staffData['job_title'],
                    'is_active' => true,
                ]
            );

            // Assign roles
            if ($staffData['role'] === 'dept_head') {
                $user->assignRole('dept_head');
            } else {
                $user->assignRole(['staff', 'mis_support']);
            }
        }

        // Create NSS Staff
        foreach ($nssStaff as $staffData) {
            $names = $this->parseName($staffData['name']);
            
            $user = User::firstOrCreate(
                ['email' => $this->generateEmail($staffData['name'])],
                [
                    'name' => $staffData['name'],
                    'first_name' => $names['first_name'],
                    'last_name' => $names['last_name'],
                    'password' => $defaultPassword,
                    'department_id' => $misDepartment->id,
                    'job_title' => $staffData['job_title'],
                    'is_nss' => true,
                    'is_active' => true,
                ]
            );

            $user->assignRole('staff');
        }
    }

    /**
     * Parse full name into first_name and last_name.
     */
    private function parseName(string $name): array
    {
        $parts = explode(' ', $name);
        $firstName = $parts[0];
        $lastName = end($parts);
        
        return [
            'first_name' => $firstName,
            'last_name' => $lastName,
        ];
    }

    /**
     * Generate email from full name.
     * Format: firstname.lastname@gfza.gov.gh
     */
    private function generateEmail(string $name): string
    {
        $parts = explode(' ', $name);
        $firstName = strtolower(preg_replace('/[^a-zA-Z]/', '', $parts[0]));
        $lastName = strtolower(preg_replace('/[^a-zA-Z]/', '', end($parts)));

        return "{$firstName}.{$lastName}@gfza.gov.gh";
    }
}

