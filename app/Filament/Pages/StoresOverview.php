<?php

namespace App\Filament\Pages;

use App\Models\StoreItem;
use App\Models\StoreTransaction;
use Filament\Pages\Page;

class StoresOverview extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $navigationGroup = 'Stores Management';

    protected static ?string $navigationLabel = 'Stores Overview';

    protected static ?string $title = 'Stores Overview';

    protected static ?int $navigationSort = 0;

    protected static string $view = 'filament.pages.stores-overview';

    public function getStats(): array
    {
        $totalItems = StoreItem::count();
        $totalStockValue = StoreItem::selectRaw('SUM(current_stock * unit_cost) as total')->value('total') ?? 0;
        $lowStockCount = StoreItem::whereColumn('current_stock', '<=', 'reorder_level')->count();
        $issuesToday = StoreTransaction::where('type', 'issue')->whereDate('transaction_date', today())->count();
        $receiptsToday = StoreTransaction::where('type', 'receipt')->whereDate('transaction_date', today())->count();

        return [
            'totalItems' => $totalItems,
            'totalStockValue' => number_format($totalStockValue, 2),
            'lowStockCount' => $lowStockCount,
            'issuesToday' => $issuesToday,
            'receiptsToday' => $receiptsToday,
        ];
    }

    public function getRecentTransactions(): \Illuminate\Database\Eloquent\Collection
    {
        return StoreTransaction::with(['item', 'supplier', 'user', 'department'])
            ->latest('transaction_date')
            ->latest('id')
            ->limit(10)
            ->get();
    }
}
