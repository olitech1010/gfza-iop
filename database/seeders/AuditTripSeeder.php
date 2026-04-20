<?php

namespace Database\Seeders;

use App\Models\AuditTrip;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class AuditTripSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedFile(
            base_path('docs/transport department/Ghana_Free_Zones_Internal_Schedule.csv'),
            'internal'
        );

        $this->seedFile(
            base_path('docs/transport department/Ghana_Free_Zones_External_Schedule.csv'),
            'external'
        );
    }

    private function seedFile(string $path, string $scheduleType): void
    {
        if (!File::exists($path)) {
            $this->command->warn("File not found: {$path}");
            return;
        }

        $content = File::get($path);
        $lines = explode("\n", $content);
        $header = str_getcsv(array_shift($lines));
        $isExternal = $scheduleType === 'external';

        $imported = 0;
        $skipped = 0;

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            $row = str_getcsv($line);

            if ($isExternal) {
                // External: Team, Type, Region, No., Activity/Company, Date, Team Members
                $team = trim($row[0] ?? '');
                $type = strtolower(trim($row[1] ?? ''));
                $region = trim($row[2] ?? '');
                $seqNum = trim($row[3] ?? '');
                $company = trim($row[4] ?? '');
                $date = trim($row[5] ?? '');
                $members = trim($row[6] ?? '');
            } else {
                // Internal: Team, Type, No., Company Name, Date, Team Members
                $team = trim($row[0] ?? '');
                $type = strtolower(trim($row[1] ?? ''));
                $seqNum = trim($row[2] ?? '');
                $company = trim($row[3] ?? '');
                $date = trim($row[4] ?? '');
                $members = trim($row[5] ?? '');
                $region = null;
            }

            // Skip rows without a sequence number (weekends, depart/return, summaries)
            if (empty($seqNum) || !is_numeric($seqNum)) {
                $skipped++;
                continue;
            }

            // Skip if company is empty
            if (empty($company)) {
                $skipped++;
                continue;
            }

            AuditTrip::firstOrCreate(
                [
                    'team_name' => strtoupper($team),
                    'company_name' => $company,
                    'schedule_type' => $scheduleType,
                ],
                [
                    'audit_type' => $type === 'compliance' ? 'compliance' : 'monitoring',
                    'region' => $region,
                    'sequence_number' => (int) $seqNum,
                    'scheduled_date' => $date,
                    'team_members' => $members,
                    'status' => 'scheduled',
                ]
            );

            $imported++;
        }

        $this->command->info("[$scheduleType] Imported: {$imported}, Skipped: {$skipped}");
    }
}
