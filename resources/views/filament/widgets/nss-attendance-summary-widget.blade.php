<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    @php
        $stats = $this->getStats();
    @endphp

    {{-- Present Card --}}
    <div class="relative overflow-hidden rounded-xl bg-white shadow-sm" style="min-height: 120px;">
        <div class="p-6 flex flex-col justify-center h-full">
            <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">Present</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['present'] }}</p>
        </div>
        <div class="absolute bottom-0 left-0 right-0 h-1" style="background: #14b8a6;"></div>
    </div>

    {{-- Absent Card --}}
    <div class="relative overflow-hidden rounded-xl bg-white shadow-sm" style="min-height: 120px;">
        <div class="p-6 flex flex-col justify-center h-full">
            <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">Absent</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['absent'] }}</p>
        </div>
        <div class="absolute bottom-0 left-0 right-0 h-1" style="background: #ec4899;"></div>
    </div>

    {{-- Late Card --}}
    <div class="relative overflow-hidden rounded-xl bg-white shadow-sm" style="min-height: 120px;">
        <div class="p-6 flex flex-col justify-center h-full">
            <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">Late</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['late'] }}</p>
        </div>
        <div class="absolute bottom-0 left-0 right-0 h-1" style="background: #f59e0b;"></div>
    </div>
</div>
