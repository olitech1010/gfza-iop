<?php

namespace App\Filament\Widgets\Staff;

use App\Models\MisTicket;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class MyTicketsWidget extends Widget
{
    protected static string $view = 'filament.widgets.staff.my-tickets-widget';

    protected int|string|array $columnSpan = 1;

    protected static ?int $sort = 40;

    public function getViewData(): array
    {
        $user = Auth::user();

        // Get recent tickets
        $recentTickets = MisTicket::where('user_id', $user->id)
            ->latest()
            ->take(4)
            ->get()
            ->map(function ($ticket) {
                return [
                    'id' => $ticket->id,
                    'title' => $ticket->subject ?? 'Ticket #'.$ticket->id,
                    'status' => $ticket->status,
                    'statusColor' => $this->getStatusColor($ticket->status),
                    'created_at' => $ticket->created_at?->diffForHumans(),
                ];
            });

        // Count by status
        $openCount = MisTicket::where('user_id', $user->id)
            ->where('status', 'open')
            ->count();

        $inProgressCount = MisTicket::where('user_id', $user->id)
            ->where('status', 'in_progress')
            ->count();

        return [
            'recentTickets' => $recentTickets,
            'openCount' => $openCount,
            'inProgressCount' => $inProgressCount,
            'createUrl' => '/admin/mis-tickets/create',
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
}
