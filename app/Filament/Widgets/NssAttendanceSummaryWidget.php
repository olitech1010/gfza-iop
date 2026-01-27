<?php

namespace App\Filament\Widgets;

use App\Models\NssAttendance;
use App\Models\User;
use Filament\Widgets\Widget;

class NssAttendanceSummaryWidget extends Widget
{
    protected static string $view = 'filament.widgets.nss-attendance-summary-widget';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function getStats(): array
    {
        $today = today();
        $totalNss = User::where('is_nss', true)->where('is_active', true)->count();

        $todayAttendance = NssAttendance::whereDate('date', $today);

        $present = (clone $todayAttendance)->where('status', 'present')->count();
        $late = (clone $todayAttendance)->where('status', 'late')->count();
        $checkedIn = $present + $late;
        $absent = $totalNss - $checkedIn;

        return [
            'present' => $present,
            'late' => $late,
            'absent' => max(0, $absent),
            'total' => $totalNss,
        ];
    }
}
