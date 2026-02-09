<x-filament-panels::page>
    {{-- Week Selector --}}
    <div class="mb-6">
        {{ $this->form }}
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <x-filament::section>
            <div class="text-center">
                <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] ?? 0 }}</div>
                <div class="text-sm text-gray-500">Total Records</div>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div class="text-center">
                <div class="text-3xl font-bold text-green-600">{{ $stats['present'] ?? 0 }}</div>
                <div class="text-sm text-gray-500">On Time</div>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div class="text-center">
                <div class="text-3xl font-bold text-amber-500">{{ $stats['late'] ?? 0 }}</div>
                <div class="text-sm text-gray-500">Late</div>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div class="text-center">
                <div class="text-3xl font-bold text-red-500">{{ $stats['absent'] ?? 0 }}</div>
                <div class="text-sm text-gray-500">Absent</div>
            </div>
        </x-filament::section>
    </div>

    {{-- Charts Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- Pie Chart --}}
        <x-filament::section>
            <x-slot name="heading">Attendance Distribution</x-slot>
            <div class="h-64">
                <canvas id="pieChart"></canvas>
            </div>
        </x-filament::section>

        {{-- Bar Chart --}}
        <x-filament::section>
            <x-slot name="heading">Daily Attendance Trend</x-slot>
            <div class="h-64">
                <canvas id="barChart"></canvas>
            </div>
        </x-filament::section>
    </div>

    {{-- Late Comers Table --}}
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center justify-between">
                <span>Late Comers This Week</span>
                @if(count($lateComers) > 0)
                    <x-filament::button 
                        wire:click="sendMemoToLateComers"
                        color="warning"
                        icon="heroicon-m-envelope"
                    >
                        Send Memo to All ({{ count($lateComers) }})
                    </x-filament::button>
                @endif
            </div>
        </x-slot>

        {{ $this->table }}
    </x-filament::section>

    {{-- Chart.js Script --}}
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chartData = @json($this->getChartData());

            // Pie Chart
            const pieCtx = document.getElementById('pieChart');
            if (pieCtx) {
                new Chart(pieCtx, {
                    type: 'doughnut',
                    data: {
                        labels: chartData.pie.labels,
                        datasets: [{
                            data: chartData.pie.data,
                            backgroundColor: chartData.pie.colors,
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            }

            // Bar Chart
            const barCtx = document.getElementById('barChart');
            if (barCtx) {
                new Chart(barCtx, {
                    type: 'bar',
                    data: {
                        labels: chartData.bar.labels,
                        datasets: [
                            {
                                label: 'On Time',
                                data: chartData.bar.present,
                                backgroundColor: '#10b981'
                            },
                            {
                                label: 'Late',
                                data: chartData.bar.late,
                                backgroundColor: '#f59e0b'
                            },
                            {
                                label: 'Absent',
                                data: chartData.bar.absent,
                                backgroundColor: '#ef4444'
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: { stacked: true },
                            y: { stacked: true, beginAtZero: true }
                        },
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            }
        });
    </script>
    @endpush
</x-filament-panels::page>
