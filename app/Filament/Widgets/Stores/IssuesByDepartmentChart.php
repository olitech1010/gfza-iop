<?php

namespace App\Filament\Widgets\Stores;

use App\Models\StoreTransaction;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class IssuesByDepartmentChart extends ChartWidget
{
    protected static ?string $heading = 'Item Issues by Department (This Month)';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $maxHeight = '300px';

    /**
     * Only show on the dashboard for stores_manager.
     * Super admin sees this on the inventory ledger page instead.
     */
    public static function canView(): bool
    {
        $user = auth()->user();

        return $user && $user->hasRole('stores_manager');
    }

    protected function getData(): array
    {
        $data = StoreTransaction::query()
            ->where('type', 'issue')
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->select('department_id', DB::raw('SUM(ABS(quantity)) as total_issued'))
            ->groupBy('department_id')
            ->with('department')
            ->get();

        $labels = $data->map(fn ($item) => $item->department->name ?? 'Unknown')->toArray();
        $values = $data->pluck('total_issued')->toArray();

        $colors = ['#1a73e8', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#14b8a6', '#f97316'];

        return [
            'datasets' => [
                [
                    'label' => 'Items Issued',
                    'data' => $values,
                    'backgroundColor' => array_slice($colors, 0, count($values)),
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
            ],
        ];
    }
}
