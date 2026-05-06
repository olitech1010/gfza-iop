<?php

namespace App\Filament\Widgets\Common;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class QuickActionsWidget extends Widget
{
    protected static string $view = 'filament.widgets.common.quick-actions-widget';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 100;

    public function getViewData(): array
    {
        $user = Auth::user();
        $roleName = $user->roles->first()?->name ?? 'staff';

        $actions = $this->getActionsForRole($roleName);

        return [
            'actions' => $actions,
        ];
    }

    protected function getActionsForRole(string $role): array
    {
        $commonActions = [
            [
                'label' => 'Select Meal',
                'icon' => 'heroicon-o-calendar',
                'url' => '/admin/meals/staff-meal-selection',
                'color' => '#FF6B6B',
            ],
        ];

        $staffActions = [
            [
                'label' => 'Submit IT Ticket',
                'icon' => 'heroicon-o-ticket',
                'url' => '/admin/mis-tickets/create',
                'color' => '#06B6D4',
            ],
            [
                'label' => 'Request Leave',
                'icon' => 'heroicon-o-calendar-days',
                'url' => '/admin/leave-requests/create',
                'color' => '#667EEA',
            ],
        ];

        $hrActions = [
            [
                'label' => 'Create Memo',
                'icon' => 'heroicon-o-document-text',
                'url' => '/admin/memos/create',
                'color' => '#8B5CF6',
            ],
            [
                'label' => 'Upload Menu',
                'icon' => 'heroicon-o-clipboard-document-list',
                'url' => '/admin/weekly-menus/create',
                'color' => '#F59E0B',
            ],
            [
                'label' => 'View All Staff',
                'icon' => 'heroicon-o-users',
                'url' => '/admin/users',
                'color' => '#10B981',
            ],
        ];

        $misActions = [
            [
                'label' => 'Register Asset',
                'icon' => 'heroicon-o-computer-desktop',
                'url' => '/admin/mis-assets/create',
                'color' => '#3B82F6',
            ],
            [
                'label' => 'View All Tickets',
                'icon' => 'heroicon-o-queue-list',
                'url' => '/admin/mis-tickets',
                'color' => '#EF4444',
            ],
            [
                'label' => 'User Directory',
                'icon' => 'heroicon-o-user-group',
                'url' => '/admin/users',
                'color' => '#10B981',
            ],
        ];

        $deptHeadActions = [
            [
                'label' => 'Approve Leaves',
                'icon' => 'heroicon-o-check-circle',
                'url' => '/admin/leave-requests',
                'color' => '#EC4899',
            ],
            [
                'label' => 'View Team',
                'icon' => 'heroicon-o-user-group',
                'url' => '/admin/users',
                'color' => '#10B981',
            ],
        ];

        $transportActions = [
            [
                'label' => 'Transport Dashboard',
                'icon' => 'heroicon-o-map',
                'url' => '/admin/transport-dashboard',
                'color' => '#1DB954',
            ],
            [
                'label' => 'Vehicle Requisitions',
                'icon' => 'heroicon-o-clipboard-document-list',
                'url' => '/admin/vehicle-requisitions',
                'color' => '#3B82F6',
            ],
            [
                'label' => 'Log Service',
                'icon' => 'heroicon-o-wrench-screwdriver',
                'url' => '/admin/vehicle-services/create',
                'color' => '#F59E0B',
            ],
            [
                'label' => 'Log Fuel',
                'icon' => 'heroicon-o-fire',
                'url' => '/admin/fuel-logs/create',
                'color' => '#10B981',
            ],
            [
                'label' => 'Audit Schedules',
                'icon' => 'heroicon-o-clipboard-document-check',
                'url' => '/admin/audit-trips',
                'color' => '#8B5CF6',
            ],
            [
                'label' => 'Fleet Registry',
                'icon' => 'heroicon-o-truck',
                'url' => '/admin/vehicles',
                'color' => '#06B6D4',
            ],
            [
                'label' => 'Manage Drivers',
                'icon' => 'heroicon-o-identification',
                'url' => '/admin/drivers',
                'color' => '#EC4899',
            ],
        ];

        // Check if user is transport dept_head
        $user = Auth::user();
        $isTransportHead = $user && $user->isTransportHead();

        return match (true) {
            $role === 'super_admin' => array_merge($commonActions, $hrActions, $misActions),
            $role === 'hr_manager' => array_merge($commonActions, $hrActions),
            $role === 'mis_support' => array_merge($commonActions, $misActions),
            $role === 'dept_head' && $isTransportHead => array_merge($commonActions, $transportActions),
            $role === 'dept_head' => array_merge($commonActions, $staffActions, $deptHeadActions),
            default => array_merge($commonActions, $staffActions),
        };
    }
}
