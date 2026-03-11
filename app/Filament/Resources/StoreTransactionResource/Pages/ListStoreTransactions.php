<?php

namespace App\Filament\Resources\StoreTransactionResource\Pages;

use App\Filament\Resources\StoreTransactionResource;
use App\Filament\Widgets\Stores\IssuesByDepartmentChart;
use App\Filament\Widgets\Stores\LowStockAlertsWidget;
use Filament\Resources\Pages\ListRecords;

class ListStoreTransactions extends ListRecords
{
    protected static string $resource = StoreTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getHeaderWidgets(): array
    {
        $user = auth()->user();

        if ($user && ($user->hasRole('super_admin') || $user->hasRole('stores_manager'))) {
            return [
                LowStockAlertsWidget::class,
                IssuesByDepartmentChart::class,
            ];
        }

        return [];
    }

    public function getHeaderWidgetsColumns(): int|array
    {
        return 1;
    }
}
