<?php

namespace App\Filament\Widgets\HR;

use App\Models\Department;
use App\Models\User;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class HrOverviewStatsWidget extends Widget
{
    protected static string $view = 'filament.widgets.hr.hr-overview-stats-widget';

    protected int|string|array $columnSpan = 1;

    protected static ?int $sort = 6;

    public static function canView(): bool
    {
        $user = Auth::user();

        return $user?->hasRole('hr_manager');
    }

    public function getViewData(): array
    {
        $totalStaff = User::count();
        $activeStaff = User::where('is_active', true)->count();
        $nssPersonnel = User::where('is_nss', true)->where('is_active', true)->count();
        $departments = Department::count();

        return [
            'stats' => [
                [
                    'label' => 'Total Staff',
                    'value' => $totalStaff,
                    'color' => '#00c73f',
                    'bgColor' => '#E6F4EA',
                ],
                [
                    'label' => 'Active Staff',
                    'value' => $activeStaff,
                    'color' => '#1a73e8',
                    'bgColor' => '#E8F0FE',
                ],
                [
                    'label' => 'NSS Personnel',
                    'value' => $nssPersonnel,
                    'color' => '#7c3aed',
                    'bgColor' => '#EDE7F6',
                ],
                [
                    'label' => 'Departments',
                    'value' => $departments,
                    'color' => '#e65100',
                    'bgColor' => '#FFF4E5',
                ],
            ],
        ];
    }
}
