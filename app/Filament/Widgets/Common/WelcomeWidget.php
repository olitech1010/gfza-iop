<?php

namespace App\Filament\Widgets\Common;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class WelcomeWidget extends Widget
{
    protected static string $view = 'filament.widgets.common.welcome-widget';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = -10;

    public function getViewData(): array
    {
        $user = Auth::user();
        $hour = now()->hour;

        if ($hour < 12) {
            $greeting = 'Good Morning';
        } elseif ($hour < 17) {
            $greeting = 'Good Afternoon';
        } else {
            $greeting = 'Good Evening';
        }

        $roleName = $user->roles->first()?->name ?? 'User';
        $roleLabel = match ($roleName) {
            'super_admin' => 'System Administrator',
            'hr_manager' => 'HR Manager',
            'mis_support' => 'MIS Support',
            'dept_head' => 'Department Head',
            'staff' => 'Staff Member',
            default => ucfirst(str_replace('_', ' ', $roleName)),
        };

        return [
            'greeting' => $greeting,
            'userName' => $user->first_name ?? $user->name,
            'roleLabel' => $roleLabel,
            'currentDate' => now()->format('l, F j, Y'),
        ];
    }
}
