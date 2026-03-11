<?php

namespace App\Filament\Widgets\Stores;

use App\Models\StoreItem;
use App\Models\StoreTransaction;
use Filament\Widgets\Widget;

class InventoryOverviewWidget extends Widget
{
    protected static string $view = 'filament.widgets.stores.inventory-overview-widget';

    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    /**
     * Only show on the dashboard for stores_manager and super_admin.
     */
    public static function canView(): bool
    {
        $user = auth()->user();

        return $user && ($user->hasRole('super_admin') || $user->hasRole('stores_manager'));
    }

    public function getStats(): array
    {
        $totalItems = StoreItem::count();
        $totalStockValue = StoreItem::sum(\Illuminate\Support\Facades\DB::raw('current_stock * unit_cost'));
        $lowStockCount = StoreItem::whereColumn('current_stock', '<=', 'reorder_level')->count();
        $issuesToday = StoreTransaction::where('type', 'issue')
            ->whereDate('transaction_date', today())
            ->count();
        $receiptsToday = StoreTransaction::where('type', 'receipt')
            ->whereDate('transaction_date', today())
            ->count();

        return [
            'totalItems' => $totalItems,
            'totalStockValue' => number_format($totalStockValue, 2),
            'lowStockCount' => $lowStockCount,
            'issuesToday' => $issuesToday,
            'receiptsToday' => $receiptsToday,
        ];
    }
}
