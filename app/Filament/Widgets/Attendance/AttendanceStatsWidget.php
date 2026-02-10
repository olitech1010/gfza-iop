<?php

namespace App\Filament\Widgets\Attendance;

use App\Models\NssAttendance;
use Carbon\Carbon;
use Filament\Widgets\Widget;

class AttendanceStatsWidget extends Widget
{
    protected static string $view = 'filament.widgets.attendance-stats-widget';

    public ?string $weekStart = null;

    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    /**
     * Only show on attendance page, not on dashboard.
     */
    public static function canView(): bool
    {
        return request()->routeIs('filament.admin.resources.nss-attendances.*');
    }

    public function mount(?string $weekStart = null): void
    {
        $this->weekStart = $weekStart ?? now()->startOfWeek()->format('Y-m-d');
    }

    public function getStats(): array
    {
        $startDate = Carbon::parse($this->weekStart)->startOfWeek();
        $endDate = $startDate->copy()->endOfWeek();
        $weekRange = $startDate->format('M d').' - '.$endDate->format('M d, Y');

        $user = auth()->user();

        // Build query based on user role
        $query = NssAttendance::whereBetween('date', [$startDate, $endDate]);

        if ($user && $user->hasRole('dept_head') && ! $user->hasRole(['super_admin', 'hr_manager'])) {
            $query->whereHas('user', function ($q) use ($user) {
                $q->where('department_id', $user->department_id);
            });
        }

        $attendances = $query->get();

        $total = $attendances->count();
        $present = $attendances->where('status', 'present')->count();
        $late = $attendances->where('status', 'late')->count();
        $absent = $attendances->where('status', 'absent')->count();

        $presentPct = $total > 0 ? round($present / $total * 100) : 0;
        $latePct = $total > 0 ? round($late / $total * 100) : 0;
        $absentPct = $total > 0 ? round($absent / $total * 100) : 0;

        return [
            'weekRange' => $weekRange,
            'total' => $total,
            'present' => $present,
            'presentPct' => $presentPct,
            'late' => $late,
            'latePct' => $latePct,
            'absent' => $absent,
            'absentPct' => $absentPct,
        ];
    }
}
