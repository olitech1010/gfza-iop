<?php

namespace App\Filament\Widgets\DeptHead;

use App\Models\User;
use App\Models\LeaveRequest;
use App\Models\MisTicket;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class DepartmentStatsWidget extends Widget
{
    protected static string $view = 'filament.widgets.depthead.department-stats-widget';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 5;

    public static function canView(): bool
    {
        $user = Auth::user();

        return $user?->hasRole(['dept_head', 'super_admin']);
    }

    public function getViewData(): array
    {
        $user = Auth::user();
        $departmentId = $user->department_id;

        // Get department info
        $department = $user->department;

        // Team stats
        $teamCount = User::where('department_id', $departmentId)
            ->where('is_active', true)
            ->count();

        // Pending leave approvals
        $pendingLeaves = LeaveRequest::whereHas('user', function ($query) use ($departmentId) {
            $query->where('department_id', $departmentId);
        })
            ->where('status', 'pending')
            ->whereNull('dept_head_approved_at')
            ->count();

        // Open tickets from department
        $openTickets = MisTicket::whereHas('requester', function ($query) use ($departmentId) {
            $query->where('department_id', $departmentId);
        })
            ->whereIn('status', ['open', 'in_progress'])
            ->count();

        return [
            'departmentName' => $department?->name ?? 'Your Department',
            'teamCount' => $teamCount,
            'pendingLeaves' => $pendingLeaves,
            'openTickets' => $openTickets,
        ];
    }
}
