<?php

namespace App\Filament\Widgets;

use App\Models\NssAttendance;
use Carbon\Carbon;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AttendanceStatsWidget extends BaseWidget implements HasForms
{
    use InteractsWithForms;

    public ?string $weekStart = null;

    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    /**
     * Prevent this widget from appearing on the dashboard.
     * It's only used as a header widget on the attendance page.
     */
    public static function canView(): bool
    {
        return false;
    }

    public function mount(?string $weekStart = null): void
    {
        $this->weekStart = $weekStart ?? now()->startOfWeek()->format('Y-m-d');
    }

    protected function getColumns(): int
    {
        return 5;
    }

    protected function getStats(): array
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

        return [
            Stat::make('Week', $weekRange)
                ->description('Selected period')
                ->icon('heroicon-o-calendar')
                ->color('gray'),
            Stat::make('Total Records', $total)
                ->description('Attendance entries')
                ->icon('heroicon-o-clipboard-document-list')
                ->color('primary'),
            Stat::make('On Time', $present)
                ->description(($total > 0 ? round($present / $total * 100) : 0).'% of total')
                ->icon('heroicon-o-check-circle')
                ->color('success'),
            Stat::make('Late', $late)
                ->description(($total > 0 ? round($late / $total * 100) : 0).'% of total')
                ->icon('heroicon-o-clock')
                ->color('warning'),
            Stat::make('Absent', $absent)
                ->description(($total > 0 ? round($absent / $total * 100) : 0).'% of total')
                ->icon('heroicon-o-x-circle')
                ->color('danger'),
        ];
    }
}
