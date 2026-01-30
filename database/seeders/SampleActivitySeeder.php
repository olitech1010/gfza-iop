<?php

namespace Database\Seeders;

use App\Models\Appraisal;
use App\Models\AppraisalPeriod;
use App\Models\AppraisalTarget;
use App\Models\CompetencyScore;
use App\Models\ConferenceRoom;
use App\Models\Department;
use App\Models\LeaveRequest;
use App\Models\Memo;
use App\Models\MisTicket;
use App\Models\NssAttendance;
use App\Models\RoomBooking;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SampleActivitySeeder extends Seeder
{
    public function run(): void
    {
        $misDepartment = Department::where('name', 'like', '%MIS%')->first();
        $misUsers = User::where('department_id', $misDepartment?->id)->get();
        $hrDepartment = Department::where('name', 'like', '%Human Resource%')->first();

        if ($misUsers->isEmpty()) {
            $this->command->warn('No MIS users found. Run MisStaffSeeder first.');
            return;
        }

        $this->seedAttendance($misUsers);
        $this->seedLeaveRequests($misUsers);
        $this->seedMisTickets($misUsers);
        $this->seedMemos($misUsers);
        $this->seedRoomBookings($misUsers);
        $this->seedAppraisals($misUsers);
    }

    /**
     * Seed attendance records for the past 5 working days.
     */
    private function seedAttendance($users): void
    {
        $workingDays = collect();
        $date = Carbon::now();

        // Get last 5 working days (Mon-Fri)
        while ($workingDays->count() < 5) {
            $date = $date->subDay();
            if ($date->isWeekday()) {
                $workingDays->push($date->copy());
            }
        }

        foreach ($users as $user) {
            foreach ($workingDays as $day) {
                // Random 80% attendance rate
                if (rand(1, 100) > 80) {
                    continue; // Skip (absent)
                }

                $checkInHour = rand(7, 9);
                $checkInMinute = rand(0, 59);
                $isLate = $checkInHour >= 9; // Late if after 9:00 AM

                $checkOutHour = rand(16, 18);
                $checkOutMinute = rand(0, 59);

                NssAttendance::create([
                    'user_id' => $user->id,
                    'date' => $day->toDateString(),
                    'check_in_time' => sprintf('%02d:%02d:00', $checkInHour, $checkInMinute),
                    'check_out_time' => sprintf('%02d:%02d:00', $checkOutHour, $checkOutMinute),
                    'status' => $isLate ? 'late' : 'present',
                    'check_in_method' => 'manual',
                    'check_out_method' => 'manual',
                ]);
            }
        }
    }

    /**
     * Seed leave requests with various statuses.
     */
    private function seedLeaveRequests($users): void
    {
        $statuses = ['pending', 'dept_head_approved', 'hr_approved', 'rejected'];
        $reasons = [
            'Annual leave for family vacation',
            'Medical appointment',
            'Personal emergency',
            'Family event',
            'Training/Conference attendance',
        ];

        $selectedUsers = $users->random(min(4, $users->count()));

        foreach ($selectedUsers as $index => $user) {
            $startDate = Carbon::now()->addDays(rand(3, 14));
            $daysRequested = rand(1, 5);

            LeaveRequest::create([
                'user_id' => $user->id,
                'start_date' => $startDate,
                'end_date' => $startDate->copy()->addDays($daysRequested - 1),
                'days_requested' => $daysRequested,
                'reason' => $reasons[array_rand($reasons)],
                'status' => $statuses[$index % count($statuses)],
            ]);
        }
    }

    /**
     * Seed MIS support tickets.
     */
    private function seedMisTickets($users): void
    {
        $categories = ['hardware', 'software', 'network', 'account'];
        $priorities = ['low', 'medium', 'high', 'critical'];
        $statuses = ['open', 'in_progress', 'resolved'];

        $subjects = [
            'hardware' => ['Printer not working', 'Computer won\'t turn on', 'Monitor display issue', 'Keyboard malfunction'],
            'software' => ['MS Office not responding', 'Email not syncing', 'Browser crashing', 'System running slow'],
            'network' => ['Internet connection down', 'Cannot access shared folder', 'VPN not connecting', 'Slow network speed'],
            'account' => ['Password reset needed', 'Account locked', 'New user setup', 'Permission request'],
        ];

        $misSupport = $users->filter(fn($u) => $u->hasRole('mis_support'))->first() ?? $users->first();

        for ($i = 0; $i < 10; $i++) {
            $category = $categories[array_rand($categories)];
            $status = $statuses[array_rand($statuses)];
            $subject = $subjects[$category][array_rand($subjects[$category])];

            MisTicket::create([
                'subject' => $subject,
                'description' => "User reported: {$subject}. Needs immediate attention.",
                'status' => $status,
                'priority' => $priorities[array_rand($priorities)],
                'category' => $category,
                'user_id' => $users->random()->id,
                'assigned_to_user_id' => rand(0, 1) ? $misSupport->id : null,
                'resolved_at' => $status === 'resolved' ? Carbon::now()->subDays(rand(0, 3)) : null,
            ]);
        }
    }

    /**
     * Seed internal memos.
     */
    private function seedMemos($users): void
    {
        $memos = [
            [
                'title' => 'Staff Meeting Reminder',
                'body' => 'This is to remind all staff that there will be a mandatory staff meeting on Friday at 10:00 AM in the main conference room. Attendance is required.',
                'status' => 'published',
            ],
            [
                'title' => 'Network Maintenance Notice',
                'body' => 'Please be informed that there will be scheduled network maintenance this weekend. Services may be intermittent between 10:00 PM Saturday and 6:00 AM Sunday.',
                'status' => 'published',
            ],
            [
                'title' => 'New Leave Policy Update (Draft)',
                'body' => 'Draft memo regarding updates to the leave policy. Pending approval from management.',
                'status' => 'draft',
            ],
        ];

        $author = $users->first();

        foreach ($memos as $memoData) {
            $memo = Memo::create([
                'title' => $memoData['title'],
                'body' => $memoData['body'],
                'status' => $memoData['status'],
                'created_by' => $author->id,
                'published_at' => $memoData['status'] === 'published' ? Carbon::now()->subDays(rand(1, 5)) : null,
            ]);

            // Attach all MIS users as recipients for published memos
            if ($memoData['status'] === 'published') {
                $memo->recipients()->attach($users->pluck('id')->toArray());
            }
        }
    }

    /**
     * Seed room bookings.
     */
    private function seedRoomBookings($users): void
    {
        $rooms = ConferenceRoom::all();
        if ($rooms->isEmpty()) {
            return;
        }

        $purposes = [
            'Team standup meeting',
            'Project planning session',
            'Client presentation',
            'Training session',
            'Department review',
        ];

        for ($i = 0; $i < 5; $i++) {
            $date = Carbon::now()->addDays(rand(1, 10));
            $startHour = rand(9, 14);
            $duration = rand(1, 3);

            RoomBooking::create([
                'conference_room_id' => $rooms->random()->id,
                'user_id' => $users->random()->id,
                'title' => $purposes[array_rand($purposes)],
                'start_time' => $date->copy()->setHour($startHour)->setMinute(0)->setSecond(0),
                'end_time' => $date->copy()->setHour($startHour + $duration)->setMinute(0)->setSecond(0),
                'status' => 'confirmed',
            ]);
        }
    }

    /**
     * Seed appraisals in different workflow states.
     */
    private function seedAppraisals($users): void
    {
        // Create or get an active appraisal period
        $period = AppraisalPeriod::firstOrCreate(
            ['is_active' => true],
            [
                'title' => 'Annual Review 2025-2026',
                'start_date' => Carbon::create(2025, 7, 1),
                'end_date' => Carbon::create(2026, 6, 30),
                'is_active' => true,
            ]
        );

        $hod = $users->filter(fn($u) => $u->hasRole('dept_head'))->first();
        if (!$hod) {
            $hod = $users->first();
        }

        $staffUsers = $users->reject(fn($u) => $u->id === $hod->id)->take(3);

        $statuses = ['goal_setting', 'hod_review', 'completed'];

        foreach ($staffUsers as $index => $user) {
            $status = $statuses[$index] ?? 'goal_setting';

            $appraisal = Appraisal::create([
                'user_id' => $user->id,
                'hod_id' => $hod->id,
                'appraisal_period_id' => $period->id,
                'current_grade' => 'Grade ' . rand(5, 12),
                'job_title' => $user->job_title ?? 'Staff',
                'date_appointed_present_grade' => Carbon::now()->subYears(rand(1, 5)),
                'status' => $status,
                'final_score' => $status === 'completed' ? rand(30, 50) / 10 : 0,
                'promotion_verdict' => $status === 'completed' ? 'suitable' : null,
            ]);

            // Add targets
            $targets = [
                ['objective' => 'Complete monthly reports on time', 'target_criteria' => '100% on-time submission'],
                ['objective' => 'Improve response time to tickets', 'target_criteria' => 'Average response < 2 hours'],
                ['objective' => 'Participate in training programs', 'target_criteria' => 'Complete 2 training modules'],
            ];

            foreach ($targets as $target) {
                $targetData = [
                    'appraisal_id' => $appraisal->id,
                    'objective' => $target['objective'],
                    'target_criteria' => $target['target_criteria'],
                ];

                // Only add scores for non-goal_setting status
                if ($status !== 'goal_setting') {
                    $targetData['manager_score'] = rand(3, 5);
                    $targetData['remarks'] = 'Good progress shown.';
                }

                AppraisalTarget::create($targetData);
            }

            // Add competency scores for hod_review and completed
            if ($status !== 'goal_setting') {
                foreach (CompetencySeeder::CORE_COMPETENCIES as $comp) {
                    CompetencyScore::create([
                        'appraisal_id' => $appraisal->id,
                        'competency_type' => 'core',
                        'competency_name' => $comp,
                        'manager_score' => rand(3, 5),
                        'weight_factor' => 0.30,
                    ]);
                }

                foreach (CompetencySeeder::NON_CORE_COMPETENCIES as $comp) {
                    CompetencyScore::create([
                        'appraisal_id' => $appraisal->id,
                        'competency_type' => 'non_core',
                        'competency_name' => $comp,
                        'manager_score' => rand(3, 5),
                        'weight_factor' => 0.10,
                    ]);
                }
            }
        }
    }
}
