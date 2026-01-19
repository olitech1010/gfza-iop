<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Week Selector --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Select Week:</label>
                    <select wire:model.live="selectedWeek" 
                            class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                        @foreach($this->getWeekOptions() as $id => $label)
                            <option value="{{ $id }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                @if($menu = $this->getSelectedMenu())
                    <div class="text-sm text-gray-500">
                        Caterer: <strong>{{ $menu->caterer->name }}</strong>
                    </div>
                @endif
                <button onclick="window.print()" 
                        class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg">
                    <x-heroicon-o-printer class="w-5 h-5 mr-2" />
                    Print Report
                </button>
            </div>
        </div>

        {{-- Overview Stats --}}
        @php $stats = $this->getOverviewStats(); @endphp
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4">
            <div class="bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl shadow p-5 text-center text-white">
                <p class="text-4xl font-bold">{{ $stats['total_meals'] }}</p>
                <p class="text-sm opacity-90">Total Meals</p>
            </div>
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow p-5 text-center text-white">
                <p class="text-4xl font-bold">{{ $stats['total_staff'] }}</p>
                <p class="text-sm opacity-90">Staff</p>
            </div>
            <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl shadow p-5 text-center text-white">
                <p class="text-4xl font-bold">{{ $stats['total_nss'] }}</p>
                <p class="text-sm opacity-90">NSS</p>
            </div>
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow p-5 text-center text-white">
                <p class="text-4xl font-bold">GHS {{ number_format($stats['total_paid'], 0) }}</p>
                <p class="text-sm opacity-90">Paid</p>
            </div>
            <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl shadow p-5 text-center text-white">
                <p class="text-4xl font-bold">GHS {{ number_format($stats['total_pending'], 0) }}</p>
                <p class="text-sm opacity-90">Pending</p>
            </div>
            <div class="bg-gradient-to-br from-teal-500 to-teal-600 rounded-xl shadow p-5 text-center text-white">
                <p class="text-4xl font-bold">{{ $stats['total_served'] }}</p>
                <p class="text-sm opacity-90">Served</p>
            </div>
            <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow p-5 text-center text-white">
                <p class="text-4xl font-bold">{{ $stats['total_unserved'] }}</p>
                <p class="text-sm opacity-90">Pending Serve</p>
            </div>
        </div>

        {{-- Department Breakdown --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Department Breakdown</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-300">Department</th>
                            <th class="px-6 py-3 text-center font-medium text-gray-500 dark:text-gray-300">Total</th>
                            <th class="px-6 py-3 text-center font-medium text-gray-500 dark:text-gray-300">Staff</th>
                            <th class="px-6 py-3 text-center font-medium text-gray-500 dark:text-gray-300">NSS</th>
                            <th class="px-6 py-3 text-right font-medium text-gray-500 dark:text-gray-300">Paid (GHS)</th>
                            <th class="px-6 py-3 text-right font-medium text-gray-500 dark:text-gray-300">Pending (GHS)</th>
                            <th class="px-6 py-3 text-center font-medium text-gray-500 dark:text-gray-300">Served</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($this->getDepartmentBreakdown() as $dept)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $dept['department'] }}</td>
                                <td class="px-6 py-4 text-center">{{ $dept['total'] }}</td>
                                <td class="px-6 py-4 text-center text-blue-600">{{ $dept['staff'] }}</td>
                                <td class="px-6 py-4 text-center text-yellow-600">{{ $dept['nss'] }}</td>
                                <td class="px-6 py-4 text-right text-green-600">{{ number_format($dept['paid'], 2) }}</td>
                                <td class="px-6 py-4 text-right text-red-600">{{ number_format($dept['pending'], 2) }}</td>
                                <td class="px-6 py-4 text-center text-teal-600">{{ $dept['served'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-gray-500">No data available</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>
