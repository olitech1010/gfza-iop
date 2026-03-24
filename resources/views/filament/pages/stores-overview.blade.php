@php
    $stats = $this->getStats();
    $recentTransactions = $this->getRecentTransactions();
@endphp

<x-filament-panels::page>
    {{-- Quick Stats Row --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
        <div class="rounded-xl px-5 py-4 text-white shadow-md" style="background-color: #1a73e8;">
            <div class="flex items-center gap-3">
                <x-heroicon-o-cube class="w-6 h-6 opacity-90" />
                <div>
                    <div class="text-xs font-medium opacity-80">Total Items</div>
                    <div class="text-2xl font-bold">{{ $stats['totalItems'] }}</div>
                </div>
            </div>
        </div>
        <div class="rounded-xl px-5 py-4 text-white shadow-md" style="background-color: #6b7280;">
            <div class="flex items-center gap-3">
                <x-heroicon-o-banknotes class="w-6 h-6 opacity-90" />
                <div>
                    <div class="text-xs font-medium opacity-80">Stock Value</div>
                    <div class="text-lg font-bold">GHS {{ $stats['totalStockValue'] }}</div>
                </div>
            </div>
        </div>
        <div class="rounded-xl px-5 py-4 text-white shadow-md" style="background-color: {{ $stats['lowStockCount'] > 0 ? '#ef4444' : '#10b981' }};">
            <div class="flex items-center gap-3">
                <x-heroicon-o-exclamation-triangle class="w-6 h-6 opacity-90" />
                <div>
                    <div class="text-xs font-medium opacity-80">Low Stock</div>
                    <div class="text-2xl font-bold">{{ $stats['lowStockCount'] }}</div>
                </div>
            </div>
        </div>
        <div class="rounded-xl px-5 py-4 text-white shadow-md" style="background-color: #10b981;">
            <div class="flex items-center gap-3">
                <x-heroicon-o-arrow-down-tray class="w-6 h-6 opacity-90" />
                <div>
                    <div class="text-xs font-medium opacity-80">Received Today</div>
                    <div class="text-2xl font-bold">{{ $stats['receiptsToday'] }}</div>
                </div>
            </div>
        </div>
        <div class="rounded-xl px-5 py-4 text-white shadow-md" style="background-color: #f59e0b;">
            <div class="flex items-center gap-3">
                <x-heroicon-o-arrow-up-tray class="w-6 h-6 opacity-90" />
                <div>
                    <div class="text-xs font-medium opacity-80">Issued Today</div>
                    <div class="text-2xl font-bold">{{ $stats['issuesToday'] }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Action Cards --}}
    <h3 class="text-lg font-semibold mb-3 dark:text-white">Quick Actions</h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        {{-- Receive Stock Card --}}
        <a href="{{ url('/admin/receive-stock') }}"
           class="group relative overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6 shadow-sm transition-all hover:shadow-lg hover:border-green-400 hover:-translate-y-1">
            <div class="flex items-center gap-4">
                <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-green-100 dark:bg-green-900/30">
                    <x-heroicon-o-arrow-down-tray class="h-7 w-7 text-green-600 dark:text-green-400" />
                </div>
                <div>
                    <h4 class="text-base font-semibold text-gray-900 dark:text-white">Receive Items</h4>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Record incoming stock from suppliers</p>
                </div>
            </div>
            <div class="absolute right-4 top-1/2 -translate-y-1/2 opacity-0 transition-opacity group-hover:opacity-100">
                <x-heroicon-o-arrow-right class="h-5 w-5 text-green-500" />
            </div>
        </a>

        {{-- Issue Stock Card --}}
        <a href="{{ url('/admin/issue-stock') }}"
           class="group relative overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6 shadow-sm transition-all hover:shadow-lg hover:border-amber-400 hover:-translate-y-1">
            <div class="flex items-center gap-4">
                <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-amber-100 dark:bg-amber-900/30">
                    <x-heroicon-o-arrow-up-tray class="h-7 w-7 text-amber-600 dark:text-amber-400" />
                </div>
                <div>
                    <h4 class="text-base font-semibold text-gray-900 dark:text-white">Issue Items</h4>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Issue stock to departments & staff</p>
                </div>
            </div>
            <div class="absolute right-4 top-1/2 -translate-y-1/2 opacity-0 transition-opacity group-hover:opacity-100">
                <x-heroicon-o-arrow-right class="h-5 w-5 text-amber-500" />
            </div>
        </a>

        {{-- View Inventory Ledger Card --}}
        <a href="{{ url('/admin/store-transactions') }}"
           class="group relative overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6 shadow-sm transition-all hover:shadow-lg hover:border-blue-400 hover:-translate-y-1">
            <div class="flex items-center gap-4">
                <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-blue-100 dark:bg-blue-900/30">
                    <x-heroicon-o-document-text class="h-7 w-7 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <h4 class="text-base font-semibold text-gray-900 dark:text-white">Inventory Ledger</h4>
                    <p class="text-sm text-gray-500 dark:text-gray-400">View full transaction history & reports</p>
                </div>
            </div>
            <div class="absolute right-4 top-1/2 -translate-y-1/2 opacity-0 transition-opacity group-hover:opacity-100">
                <x-heroicon-o-arrow-right class="h-5 w-5 text-blue-500" />
            </div>
        </a>
    </div>

    {{-- Recent Transactions --}}
    <h3 class="text-lg font-semibold mb-3 dark:text-white">Recent Transactions</h3>
    <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700/50">
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-300">Date</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-300">Item</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-300">Type</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-300">Details</th>
                    <th class="px-4 py-3 text-right font-medium text-gray-600 dark:text-gray-300">Qty</th>
                    <th class="px-4 py-3 text-right font-medium text-gray-600 dark:text-gray-300">Balance</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse ($recentTransactions as $txn)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                        <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $txn->transaction_date->format('d M Y') }}</td>
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $txn->item->name ?? 'N/A' }}</td>
                        <td class="px-4 py-3">
                            @if ($txn->type === 'receipt')
                                <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900/30 dark:text-green-400">Receipt</span>
                            @elseif ($txn->type === 'issue')
                                <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-medium text-amber-800 dark:bg-amber-900/30 dark:text-amber-400">Issue</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800 dark:bg-gray-700 dark:text-gray-300">Adjustment</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                            @if ($txn->type === 'receipt')
                                From: {{ $txn->supplier->name ?? 'Unknown' }}
                            @elseif ($txn->type === 'issue')
                                To: {{ $txn->department->name ?? '' }}{{ $txn->user ? ' (' . $txn->user->name . ')' : '' }}
                            @else
                                {{ \Illuminate\Support\Str::limit($txn->notes, 40) }}
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right font-semibold {{ $txn->quantity > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                            {{ $txn->quantity > 0 ? '+' : '' }}{{ $txn->quantity }}
                        </td>
                        <td class="px-4 py-3 text-right font-medium text-gray-900 dark:text-white">{{ $txn->balance_after }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-400 dark:text-gray-500">
                            <x-heroicon-o-inbox class="mx-auto h-8 w-8 mb-2 opacity-50" />
                            No transactions yet. Use the quick actions above to get started.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-filament-panels::page>
