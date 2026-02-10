<?php

namespace App\Filament\Widgets\Attendance;

use App\Models\NssAttendance;
use Filament\Widgets\ChartWidget;

class AttendanceChartsWidget extends ChartWidget
{
    protected static ?string $heading = 'Weekly Attendance Overview';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $maxHeight = '300px';

    /**
     * Only show on attendance page, not on dashboard.
     */
    public static function canView(): bool
    {
        return request()->routeIs('filament.admin.resources.nss-attendances.*');
    }

    public ?string $filter = 'bar';

    protected function getFilters(): ?array
    {
        return [
            'bar' => 'Daily Trend (Bar)',
            'pie' => 'Status Distribution (Pie)',
        ];
    }

    public ?string $weekStart = null;

    protected function getData(): array
    {
        $start = $this->weekStart ? \Carbon\Carbon::parse($this->weekStart) : now();
        $startDate = $start->startOfWeek();
        $endDate = $start->copy()->endOfWeek();

        if ($this->filter === 'pie') {
            $attendances = NssAttendance::whereBetween('date', [$startDate, $endDate])->get();

            return [
                'datasets' => [
                    [
                        'label' => 'Attendance',
                        'data' => [
                            $attendances->where('status', 'present')->count(),
                            $attendances->where('status', 'late')->count(),
                            $attendances->where('status', 'absent')->count(),
                        ],
                        'backgroundColor' => ['#10b981', '#f59e0b', '#ef4444'],
                    ],
                ],
                'labels' => ['On Time', 'Late', 'Absent'],
            ];
        }

        // Daily breakdown for bar chart
        $dailyData = [];
        $labels = [];
        $present = [];
        $late = [];
        $absent = [];

        $current = $startDate->copy();

        while ($current <= $endDate) {
            if ($current->isWeekday()) {
                $dayAttendance = NssAttendance::whereDate('date', $current)->get();
                $labels[] = $current->format('D');
                $present[] = $dayAttendance->where('status', 'present')->count();
                $late[] = $dayAttendance->where('status', 'late')->count();
                $absent[] = $dayAttendance->where('status', 'absent')->count();
            }
            $current->addDay();
        }

        return [
            'datasets' => [
                [
                    'label' => 'On Time',
                    'data' => $present,
                    'backgroundColor' => '#10b981',
                ],
                [
                    'label' => 'Late',
                    'data' => $late,
                    'backgroundColor' => '#f59e0b',
                ],
                [
                    'label' => 'Absent',
                    'data' => $absent,
                    'backgroundColor' => '#ef4444',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return $this->filter === 'pie' ? 'doughnut' : 'bar';
    }

    protected function getOptions(): array
    {
        if ($this->filter === 'pie') {
            return [
                'plugins' => [
                    'legend' => [
                        'display' => true,
                        'position' => 'bottom',
                    ],
                ],
            ];
        }

        return [
            'scales' => [
                'x' => [
                    'stacked' => true,
                ],
                'y' => [
                    'stacked' => true,
                    'beginAtZero' => true,
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
            ],
        ];
    }
}
