<?php

namespace App\Filament\Widgets\MIS;

use App\Models\MisTicket;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class OpenTicketsWidget extends Widget
{
    protected static string $view = 'filament.widgets.mis.open-tickets-widget';

    protected int|string|array $columnSpan = 1;

    protected static ?int $sort = 15;

    public static function canView(): bool
    {
        $user = Auth::user();

        return $user?->hasRole(['mis_support', 'super_admin']);
    }

    public function getViewData(): array
    {
        // Get open tickets needing attention
        $openTickets = MisTicket::where('status', 'open')
            ->with(['user', 'user.department'])
            ->oldest()
            ->take(5)
            ->get()
            ->map(function ($ticket) {
                return [
                    'id' => $ticket->id,
                    'title' => $ticket->title ?? $ticket->issue_description ?? 'Ticket #' . $ticket->id,
                    'userName' => $ticket->user?->name ?? 'Unknown',
                    'department' => $ticket->user?->department?->name ?? 'N/A',
                    'createdAt' => $ticket->created_at?->diffForHumans(),
                    'priority' => $ticket->priority ?? 'normal',
                ];
            });

        return [
            'openTickets' => $openTickets,
            'viewAllUrl' => '/admin/mis-tickets?tableFilters[status][value]=open',
        ];
    }
}
