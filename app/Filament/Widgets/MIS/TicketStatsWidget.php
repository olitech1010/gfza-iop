<?php

namespace App\Filament\Widgets\MIS;

use App\Models\MisTicket;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class TicketStatsWidget extends Widget
{
    protected static string $view = 'filament.widgets.mis.ticket-stats-widget';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 5;

    public static function canView(): bool
    {
        $user = Auth::user();

        return $user?->hasRole('mis_support');
    }

    public function getViewData(): array
    {
        $openCount = MisTicket::where('status', 'open')->count();
        $inProgressCount = MisTicket::where('status', 'in_progress')->count();
        $resolvedToday = MisTicket::where('status', 'resolved')
            ->whereDate('updated_at', today())
            ->count();

        return [
            'stats' => [
                [
                    'label' => 'Open Tickets',
                    'value' => $openCount,
                    'color' => '#EF4444',
                    'icon' => 'exclamation',
                ],
                [
                    'label' => 'In Progress',
                    'value' => $inProgressCount,
                    'color' => '#F59E0B',
                    'icon' => 'clock',
                ],
                [
                    'label' => 'Resolved Today',
                    'value' => $resolvedToday,
                    'color' => '#10B981',
                    'icon' => 'check',
                ],
            ],
        ];
    }
}
