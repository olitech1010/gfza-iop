<?php

namespace App\Filament\Widgets\Admin;

use App\Models\Department;
use App\Models\MisAsset;
use App\Models\MisTicket;
use App\Models\RoomBooking;
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
                    'label' => 'Total Users',
                    'value' => User::count(),
                    'color' => '#00c73f',
                    'icon' => 'users',
                ],
                [
                    'label' => 'Departments',
                    'value' => Department::count(),
                    'color' => '#3B82F6',
                    'icon' => 'building',
                ],
                [
                    'label' => 'Open Tickets',
                    'value' => MisTicket::whereIn('status', ['open', 'in_progress'])->count(),
                    'color' => '#F59E0B',
                    'icon' => 'ticket',
                ],
                [
                    'label' => 'Total Assets',
                    'value' => MisAsset::count(),
                    'color' => '#8B5CF6',
                    'icon' => 'computer',
                ],
                [
                    'label' => 'Today\'s Bookings',
                    'value' => RoomBooking::whereDate('start_time', today())->count(),
                    'color' => '#EC4899',
                    'icon' => 'calendar',
                ],
            ],
        ];
    }
}
