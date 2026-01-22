<?php

namespace App\Filament\Widgets\HR;

use App\Models\LeaveRequest;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class PendingLeavesWidget extends Widget
{
    protected static string $view = 'filament.widgets.hr.pending-leaves-widget';

    protected int|string|array $columnSpan = 1;

    protected static ?int $sort = 15;

    public static function canView(): bool
    {
        $user = Auth::user();

        return $user?->hasRole('hr_manager');
    }

    public function getViewData(): array
    {
        // Get leaves pending HR approval (already approved by dept head)
        $pendingLeaves = LeaveRequest::where('status', 'pending')
            ->whereNotNull('dept_head_approved_at')
            ->whereNull('hr_approved_at')
            ->with(['user', 'user.department'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($leave) {
                return [
                    'id' => $leave->id,
                    'userName' => $leave->user?->name ?? 'Unknown',
                    'department' => $leave->user?->department?->name ?? 'N/A',
                    'startDate' => $leave->start_date->format('M j'),
                    'endDate' => $leave->end_date->format('M j'),
                    'days' => $leave->days_requested,
                ];
            });

        $totalPending = LeaveRequest::where('status', 'pending')
            ->whereNotNull('dept_head_approved_at')
            ->whereNull('hr_approved_at')
            ->count();

        return [
            'pendingLeaves' => $pendingLeaves,
            'totalPending' => $totalPending,
            'viewAllUrl' => '/admin/leave-requests',
        ];
    }
}
