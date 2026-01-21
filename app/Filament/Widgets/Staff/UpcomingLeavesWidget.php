<?php

namespace App\Filament\Widgets\Staff;

use App\Models\LeaveRequest;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class UpcomingLeavesWidget extends Widget
{
    protected static string $view = 'filament.widgets.staff.upcoming-leaves-widget';

    protected int|string|array $columnSpan = 1;

    protected static ?int $sort = 30;

    public function getViewData(): array
    {
        $user = Auth::user();
        $today = now()->startOfDay();

        // Get upcoming approved leaves
        $upcomingLeaves = LeaveRequest::where('user_id', $user->id)
            ->where('status', 'approved')
            ->where('start_date', '>=', $today)
            ->orderBy('start_date')
            ->take(3)
            ->get()
            ->map(function ($leave) {
                return [
                    'id' => $leave->id,
                    'start_date' => $leave->start_date->format('M j'),
                    'end_date' => $leave->end_date->format('M j'),
                    'days' => $leave->days_requested,
                ];
            });

        // Get pending leave requests
        $pendingCount = LeaveRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->count();

        return [
            'upcomingLeaves' => $upcomingLeaves,
            'pendingCount' => $pendingCount,
            'requestUrl' => '/admin/leave-requests/create',
        ];
    }
}
