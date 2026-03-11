@php
    $stats = $this->getStats();
@endphp

<x-filament-widgets::widget>
    <div class="flex flex-wrap gap-3 items-stretch">
        {{-- Total Items --}}
        <div class="flex-1 min-w-[140px] rounded-lg px-4 py-3 text-white" style="background-color: #1a73e8;">
            <div class="flex items-center gap-2">
                <x-heroicon-o-cube class="w-5 h-5" />
                <div>
                    <div class="text-xs font-medium opacity-90">Total Items</div>
                    <div class="text-xl font-bold">{{ $stats['totalItems'] }}</div>
                </div>
            </div>
        </div>

        {{-- Stock Value --}}
        <div class="flex-1 min-w-[140px] rounded-lg px-4 py-3 text-white" style="background-color: #6b7280;">
            <div class="flex items-center gap-2">
                <x-heroicon-o-banknotes class="w-5 h-5" />
                <div>
                    <div class="text-xs font-medium opacity-90">Stock Value</div>
                    <div class="text-lg font-bold">GHS {{ $stats['totalStockValue'] }}</div>
                </div>
            </div>
        </div>

        {{-- Low Stock Alerts --}}
        <div class="flex-1 min-w-[140px] rounded-lg px-4 py-3 text-white" style="background-color: {{ $stats['lowStockCount'] > 0 ? '#ef4444' : '#10b981' }};">
            <div class="flex items-center gap-2">
                <x-heroicon-o-exclamation-triangle class="w-5 h-5" />
                <div>
                    <div class="text-xs font-medium opacity-90">Low Stock</div>
                    <div class="text-xl font-bold">{{ $stats['lowStockCount'] }}</div>
                </div>
            </div>
        </div>

        {{-- Receipts Today --}}
        <div class="flex-1 min-w-[140px] rounded-lg px-4 py-3 text-white" style="background-color: #10b981;">
            <div class="flex items-center gap-2">
                <x-heroicon-o-arrow-down-tray class="w-5 h-5" />
                <div>
                    <div class="text-xs font-medium opacity-90">Received Today</div>
                    <div class="text-xl font-bold">{{ $stats['receiptsToday'] }}</div>
                </div>
            </div>
        </div>

        {{-- Issues Today --}}
        <div class="flex-1 min-w-[140px] rounded-lg px-4 py-3 text-white" style="background-color: #f59e0b;">
            <div class="flex items-center gap-2">
                <x-heroicon-o-arrow-up-tray class="w-5 h-5" />
                <div>
                    <div class="text-xs font-medium opacity-90">Issued Today</div>
                    <div class="text-xl font-bold">{{ $stats['issuesToday'] }}</div>
                </div>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
