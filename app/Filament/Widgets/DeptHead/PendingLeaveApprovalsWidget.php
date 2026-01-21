<?php

namespace App\Filament\Widgets\DeptHead;

use App\Models\LeaveRequest;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class PendingLeaveApprovalsWidget extends Widget
{
    protected static string $view = 'filament.widgets.depthead.pending-leave-approvals-widget';

    protected int|string|array $columnSpan = 1;

    protected static ?int $sort = 15;

    public static function canView(): bool
    {
        $user = Auth::user();

        return $user?->hasRole(['dept_head', 'super_admin']);
    }

    public function getViewData(): array
    {
        $user = Auth::user();
        $departmentId = $user->department_id;

        // Get pending leave requests from department
        $pendingLeaves = LeaveRequest::whereHas('user', function ($query) use ($departmentId) {
            $query->where('department_id', $departmentId);
        })
            ->where('status', 'pending')
            ->whereNull('dept_head_approved_at')
            ->with('user')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($leave) {
                return [
                    'id' => $leave->id,
                    'userName' => $leave->user?->name ?? 'Unknown',
                    'startDate' => $leave->start_date->format('M j'),
                    'endDate' => $leave->end_date->format('M j'),
                    'days' => $leave->days_requested,
                    'reason' => $leave->reason,
                ];
            });

        return [
            'pendingLeaves' => $pendingLeaves,
            'viewAllUrl' => '/admin/leave-requests?tableFilters[status][value]=pending',
        ];
    }
}
