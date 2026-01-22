<?php

namespace App\Filament\Widgets\DeptHead;

use App\Models\MisTicket;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class DepartmentTicketsWidget extends Widget
{
    protected static string $view = 'filament.widgets.depthead.department-tickets-widget';

    protected int|string|array $columnSpan = 1;

    protected static ?int $sort = 25;

    public static function canView(): bool
    {
        $user = Auth::user();

        return $user?->hasRole('dept_head');
    }

    public function getViewData(): array
    {
        $user = Auth::user();
        $departmentId = $user->department_id;

        // Get recent tickets from department
        $recentTickets = MisTicket::whereHas('requester', function ($query) use ($departmentId) {
            $query->where('department_id', $departmentId);
        })
            ->with('requester')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($ticket) {
                return [
                    'id' => $ticket->id,
                    'title' => $ticket->subject ?? 'Ticket #'.$ticket->id,
                    'userName' => $ticket->requester?->name ?? 'Unknown',
                    'status' => $ticket->status,
                    'statusColor' => $this->getStatusColor($ticket->status),
                    'statusLabel' => $this->getStatusLabel($ticket->status),
                ];
            });

        return [
            'recentTickets' => $recentTickets,
            'viewAllUrl' => '/admin/mis-tickets',
        ];
    }

    protected function getStatusColor(string $status): string
    {
        return match ($status) {
            'open' => '#EF4444',
            'in_progress' => '#F59E0B',
            'resolved' => '#10B981',
            'referred' => '#8B5CF6',
            default => '#6B7280',
        };
    }

    protected function getStatusLabel(string $status): string
    {
        return match ($status) {
            'open' => 'Open',
            'in_progress' => 'In Progress',
            'resolved' => 'Resolved',
            'referred' => 'Referred',
            default => ucfirst($status),
        };
    }
}
