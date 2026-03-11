<?php

namespace App\Filament\Widgets\Stores;

use App\Models\StoreItem;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LowStockAlertsWidget extends BaseWidget
{
    protected static ?string $heading = 'Low Stock Alerts';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    /**
     * Only show on the dashboard for stores_manager and super_admin.
     */
    public static function canView(): bool
    {
        $user = auth()->user();

        return $user && ($user->hasRole('super_admin') || $user->hasRole('stores_manager'));
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                StoreItem::query()
                    ->whereColumn('current_stock', '<=', 'reorder_level')
                    ->orderBy('current_stock', 'asc')
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Item')
                    ->searchable(),
                TextColumn::make('category.name')
                    ->label('Category'),
                TextColumn::make('current_stock')
                    ->label('Stock')
                    ->badge()
                    ->color(fn (StoreItem $record): string => $record->current_stock <= 0 ? 'danger' : 'warning'),
                TextColumn::make('reorder_level')
                    ->label('Reorder Level'),
                TextColumn::make('unit_of_measure')
                    ->label('Unit'),
            ])
            ->emptyStateHeading('All items are well stocked')
            ->emptyStateDescription('No items are below their reorder level.')
            ->emptyStateIcon('heroicon-o-check-circle');
    }
}
