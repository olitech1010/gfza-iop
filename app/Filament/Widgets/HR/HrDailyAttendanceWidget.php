<?php

namespace App\Filament\Widgets\HR;

use App\Models\NssAttendance;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class HrDailyAttendanceWidget extends Widget
{
    protected static string $view = 'filament.widgets.hr.hr-daily-attendance-widget';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return Auth::user()?->hasRole('hr_manager');
    }

    public function getStats(): array
    {
        $today = now();

        $attendances = NssAttendance::whereDate('date', $today)->get();
        $total = $attendances->count();
        $onTime = $attendances->where('status', 'present')->count();
        $late = $attendances->where('status', 'late')->count();
        $absent = $attendances->where('status', 'absent')->count();

        $onTimePct = $total > 0 ? round(($onTime / $total) * 100) : 0;
        $latePct = $total > 0 ? round(($late / $total) * 100) : 0;
        $absentPct = $total > 0 ? round(($absent / $total) * 100) : 0;

        return [
            'date' => $today->format('D, M d, Y'),
            'time' => $today->format('g:i A'),
            'total' => $total,
            'onTime' => $onTime,
            'onTimePct' => $onTimePct,
            'late' => $late,
            'latePct' => $latePct,
            'absent' => $absent,
            'absentPct' => $absentPct,
        ];
    }
}
