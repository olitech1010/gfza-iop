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

        return match ($role) {
            'super_admin' => array_merge($commonActions, $hrActions, $misActions),
            'hr_manager' => array_merge($commonActions, $hrActions),
            'mis_support' => array_merge($commonActions, $misActions),
            'dept_head' => array_merge($commonActions, $staffActions, $deptHeadActions),
            default => array_merge($commonActions, $staffActions),
        };
    }
}
