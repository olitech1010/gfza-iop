@php
    $stats = $this->getStats();
@endphp

<x-filament-widgets::widget>
    <div class="space-y-4">
        {{-- Fleet Overview Stats --}}
        <div class="flex flex-col xl:flex-row gap-3 overflow-x-auto pb-1">
            <div class="flex-1 min-w-[150px] rounded-xl px-4 py-3 text-white shadow-md" style="background-color: #1a73e8;">
                <div class="flex items-center gap-3">
                    <x-heroicon-o-truck class="w-5 h-5 opacity-90" />
                    <div>
                        <div class="text-xs font-medium opacity-80">Total Vehicles</div>
                        <div class="text-xl font-bold">{{ $stats['totalVehicles'] }}</div>
                    </div>
                </div>
            </div>
            <div class="flex-1 min-w-[150px] rounded-xl px-4 py-3 text-white shadow-md" style="background-color: #10b981;">
                <div class="flex items-center gap-3">
                    <x-heroicon-o-check-circle class="w-5 h-5 opacity-90" />
                    <div>
                        <div class="text-xs font-medium opacity-80">Available</div>
                        <div class="text-xl font-bold">{{ $stats['availableVehicles'] }}</div>
                    </div>
                </div>
            </div>
            <div class="flex-1 min-w-[150px] rounded-xl px-4 py-3 text-white shadow-md" style="background-color: #f59e0b;">
                <div class="flex items-center gap-3">
                    <x-heroicon-o-arrow-path class="w-5 h-5 opacity-90" />
                    <div>
                        <div class="text-xs font-medium opacity-80">In Use</div>
                        <div class="text-xl font-bold">{{ $stats['inUseVehicles'] }}</div>
                    </div>
                </div>
            </div>
            <div class="flex-1 min-w-[150px] rounded-xl px-4 py-3 text-white shadow-md" style="background-color: {{ $stats['pendingRequisitions'] > 0 ? '#ef4444' : '#6b7280' }};">
                <div class="flex items-center gap-3">
                    <x-heroicon-o-bell-alert class="w-5 h-5 opacity-90" />
                    <div>
                        <div class="text-xs font-medium opacity-80">Pending Requests</div>
                        <div class="text-xl font-bold">{{ $stats['pendingRequisitions'] }}</div>
                    </div>
                </div>
            </div>
            <div class="flex-1 min-w-[150px] rounded-xl px-4 py-3 text-white shadow-md" style="background-color: #6366f1;">
                <div class="flex items-center gap-3">
                    <x-heroicon-o-map class="w-5 h-5 opacity-90" />
                    <div>
                        <div class="text-xs font-medium opacity-80">Active Trips</div>
                        <div class="text-xl font-bold">{{ $stats['activeTrips'] }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="flex flex-row gap-3">
            <a href="{{ url('/admin/vehicle-requisitions') }}"
               class="flex-1 group relative overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-3 shadow-sm transition-all hover:shadow-lg hover:border-blue-400 hover:-translate-y-0.5">
                <div class="flex items-center gap-3">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/30">
                        <x-heroicon-o-clipboard-document-list class="h-4 w-4 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white">Requisitions</h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $stats['pendingRequisitions'] }} pending</p>
                    </div>
                </div>
            </a>
            <a href="{{ url('/admin/fuel-logs/create') }}"
               class="flex-1 group relative overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-3 shadow-sm transition-all hover:shadow-lg hover:border-green-400 hover:-translate-y-0.5">
                <div class="flex items-center gap-3">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-green-100 dark:bg-green-900/30">
                        <x-heroicon-o-fire class="h-4 w-4 text-green-600 dark:text-green-400" />
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white">Log Fuel</h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400">GHS {{ number_format($stats['totalFuelCostMonth'], 2) }} this month</p>
                    </div>
                </div>
            </a>
            <a href="{{ url('/admin/audit-trips') }}"
               class="flex-1 group relative overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-3 shadow-sm transition-all hover:shadow-lg hover:border-purple-400 hover:-translate-y-0.5">
                <div class="flex items-center gap-3">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-purple-100 dark:bg-purple-900/30">
                        <x-heroicon-o-clipboard-document-check class="h-4 w-4 text-purple-600 dark:text-purple-400" />
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white">Audit Schedules</h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $stats['auditScheduled'] }} scheduled</p>
                    </div>
                </div>
            </a>
            <a href="{{ url('/admin/transport-dashboard') }}"
               class="flex-1 group relative overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-3 shadow-sm transition-all hover:shadow-lg hover:border-indigo-400 hover:-translate-y-0.5">
                <div class="flex items-center gap-3">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-indigo-100 dark:bg-indigo-900/30">
                        <x-heroicon-o-chart-bar class="h-4 w-4 text-indigo-600 dark:text-indigo-400" />
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white">Full Dashboard</h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400">View all transport data</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
</x-filament-widgets::widget>
