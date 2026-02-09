<?php
// This file is deprecated - functionality moved to ListNssAttendances
// Keeping just to prevent errors - will be fully deleted later

namespace App\Filament\Pages;

use Filament\Pages\Page;

class WeeklyAttendanceReport extends Page
{
    protected static string $view = 'filament.pages.weekly-attendance-report';

    // Hide from navigation completely
    protected static bool $shouldRegisterNavigation = false;

    public static function canAccess(): bool
    {
        return false;
    }
}
