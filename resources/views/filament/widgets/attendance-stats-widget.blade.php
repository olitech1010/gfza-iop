@php
    $stats = $this->getStats();
@endphp

<x-filament-widgets::widget>
    <div class="flex flex-wrap gap-3 items-stretch">
        {{-- Week Range --}}
        <div class="flex-1 min-w-[160px] rounded-lg px-4 py-3 text-white" style="background-color: #6b7280;">
            <div class="flex items-center gap-2">
                <x-heroicon-o-calendar class="w-5 h-5" />
                <div>
                    <div class="text-xs font-medium opacity-90">Week</div>
                    <div class="text-sm font-bold">{{ $stats['weekRange'] }}</div>
                </div>
            </div>
        </div>

        {{-- Total Records --}}
        <div class="flex-1 min-w-[120px] rounded-lg px-4 py-3 text-white" style="background-color: #1a73e8;">
            <div class="flex items-center gap-2">
                <x-heroicon-o-clipboard-document-list class="w-5 h-5" />
                <div>
                    <div class="text-xs font-medium opacity-90">Total</div>
                    <div class="text-xl font-bold">{{ $stats['total'] }}</div>
                </div>
            </div>
        </div>

        {{-- On Time --}}
        <div class="flex-1 min-w-[120px] rounded-lg px-4 py-3 text-white" style="background-color: #10b981;">
            <div class="flex items-center gap-2">
                <x-heroicon-o-check-circle class="w-5 h-5" />
                <div>
                    <div class="text-xs font-medium opacity-90">On Time</div>
                    <div class="text-xl font-bold">{{ $stats['present'] }}</div>
                    <div class="text-xs opacity-80">{{ $stats['presentPct'] }}%</div>
                </div>
            </div>
        </div>

        {{-- Late --}}
        <div class="flex-1 min-w-[120px] rounded-lg px-4 py-3 text-white" style="background-color: #f59e0b;">
            <div class="flex items-center gap-2">
                <x-heroicon-o-clock class="w-5 h-5" />
                <div>
                    <div class="text-xs font-medium opacity-90">Late</div>
                    <div class="text-xl font-bold">{{ $stats['late'] }}</div>
                    <div class="text-xs opacity-80">{{ $stats['latePct'] }}%</div>
                </div>
            </div>
        </div>

        {{-- Absent --}}
        <div class="flex-1 min-w-[120px] rounded-lg px-4 py-3 text-white" style="background-color: #ef4444;">
            <div class="flex items-center gap-2">
                <x-heroicon-o-x-circle class="w-5 h-5" />
                <div>
                    <div class="text-xs font-medium opacity-90">Absent</div>
                    <div class="text-xl font-bold">{{ $stats['absent'] }}</div>
                    <div class="text-xs opacity-80">{{ $stats['absentPct'] }}%</div>
                </div>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
