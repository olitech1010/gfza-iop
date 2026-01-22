<?php

namespace App\Filament\Widgets\HR;

use App\Models\Department;
use App\Models\User;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class HrOverviewStatsWidget extends Widget
{
    protected static string $view = 'filament.widgets.hr.hr-overview-stats-widget';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 5;

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
                    'icon' => 'users',
                    'color' => '#00c73f',
                ],
                [
                    'label' => 'Active Staff',
                    'value' => $activeStaff,
                    'icon' => 'user-check',
                    'color' => '#3B82F6',
                ],
                [
                    'label' => 'NSS Personnel',
                    'value' => $nssPersonnel,
                    'icon' => 'academic-cap',
                    'color' => '#8B5CF6',
                ],
                [
                    'label' => 'Departments',
                    'value' => $departments,
                    'icon' => 'building-office',
                    'color' => '#F59E0B',
                ],
            ],
        ];
    }
}
