<?php

namespace App\Filament\Widgets\Admin;

use App\Models\Department;
use App\Models\MisTicket;
use App\Models\User;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class SystemOverviewWidget extends Widget
{
    protected static string $view = 'filament.widgets.admin.system-overview-widget';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 5;

    public static function canView(): bool
    {
        $user = Auth::user();

        return $user?->hasRole('super_admin');
    }

    public function getViewData(): array
    {
        return [
            'stats' => [
                [
                    'label' => 'Total Staff',
                    'value' => User::count(),
                    'color' => '#00c73f',
                    'bgColor' => '#E6F4EA',
                ],
                [
                    'label' => 'Departments',
                    'value' => Department::count(),
                    'color' => '#1a73e8',
                    'bgColor' => '#E8F0FE',
                ],
                [
                    'label' => 'Open Tickets',
                    'value' => MisTicket::where('status', 'open')->count(),
                    'color' => '#ea4335',
                    'bgColor' => '#FDECEA',
                ],
                [
                    'label' => 'In Progress',
                    'value' => MisTicket::where('status', 'in_progress')->count(),
                    'color' => '#e65100',
                    'bgColor' => '#FFF4E5',
                ],
                [
                    'label' => 'Resolved Today',
                    'value' => MisTicket::where('status', 'resolved')
                        ->whereDate('resolved_at', today())
                        ->count(),
                    'color' => '#34a853',
                    'bgColor' => '#E6F4EA',
                ],
            ],
        ];
    }
}
