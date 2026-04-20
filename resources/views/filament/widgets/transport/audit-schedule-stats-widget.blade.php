@php
    $stats = $this->getStats();
@endphp

<x-filament-widgets::widget>
    <div class="flex flex-col xl:flex-row gap-4 overflow-x-auto pb-2">
        {{-- Total Audits --}}
        <div class="flex-1 min-w-[160px] rounded-xl px-5 py-4 text-white shadow-md" style="background-color: #1a73e8;">
            <div class="flex items-center gap-3">
                <x-heroicon-o-clipboard-document-check class="w-6 h-6 opacity-90" />
                <div>
                    <div class="text-xs font-medium opacity-80">Total Audits</div>
                    <div class="text-2xl font-bold">{{ $stats['total'] }}</div>
                </div>
            </div>
        </div>
        {{-- Scheduled --}}
        <div class="flex-1 min-w-[160px] rounded-xl px-5 py-4 text-white shadow-md" style="background-color: #6b7280;">
            <div class="flex items-center gap-3">
                <x-heroicon-o-clock class="w-6 h-6 opacity-90" />
                <div>
                    <div class="text-xs font-medium opacity-80">Scheduled</div>
                    <div class="text-2xl font-bold">{{ $stats['scheduled'] }}</div>
                </div>
            </div>
        </div>
        {{-- In Progress --}}
        <div class="flex-1 min-w-[160px] rounded-xl px-5 py-4 text-white shadow-md" style="background-color: #f59e0b;">
            <div class="flex items-center gap-3">
                <x-heroicon-o-arrow-path class="w-6 h-6 opacity-90" />
                <div>
                    <div class="text-xs font-medium opacity-80">In Progress</div>
                    <div class="text-2xl font-bold">{{ $stats['inProgress'] }}</div>
                </div>
            </div>
        </div>
        {{-- Completed --}}
        <div class="flex-1 min-w-[160px] rounded-xl px-5 py-4 text-white shadow-md" style="background-color: #10b981;">
            <div class="flex items-center gap-3">
                <x-heroicon-o-check-circle class="w-6 h-6 opacity-90" />
                <div>
                    <div class="text-xs font-medium opacity-80">Completed</div>
                    <div class="text-2xl font-bold">{{ $stats['completed'] }}</div>
                </div>
            </div>
        </div>
        {{-- Completion Rate --}}
        <div class="flex-1 min-w-[160px] rounded-xl px-5 py-4 text-white shadow-md" style="background-color: {{ $stats['completionRate'] >= 75 ? '#10b981' : ($stats['completionRate'] >= 40 ? '#f59e0b' : '#ef4444') }};">
            <div class="flex items-center gap-3">
                <x-heroicon-o-chart-pie class="w-6 h-6 opacity-90" />
                <div>
                    <div class="text-xs font-medium opacity-80">Progress</div>
                    <div class="text-2xl font-bold">{{ $stats['completionRate'] }}%</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Type Breakdown --}}
    <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-3 text-center">
            <div class="text-xs text-gray-500 dark:text-gray-400">Internal</div>
            <div class="text-lg font-bold text-gray-900 dark:text-white">{{ $stats['internalCount'] }}</div>
        </div>
        <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-3 text-center">
            <div class="text-xs text-gray-500 dark:text-gray-400">External</div>
            <div class="text-lg font-bold text-gray-900 dark:text-white">{{ $stats['externalCount'] }}</div>
        </div>
        <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-3 text-center">
            <div class="text-xs text-gray-500 dark:text-gray-400">Compliance</div>
            <div class="text-lg font-bold text-gray-900 dark:text-white">{{ $stats['complianceCount'] }}</div>
        </div>
        <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-3 text-center">
            <div class="text-xs text-gray-500 dark:text-gray-400">Monitoring</div>
            <div class="text-lg font-bold text-gray-900 dark:text-white">{{ $stats['monitoringCount'] }}</div>
        </div>
    </div>
</x-filament-widgets::widget>
