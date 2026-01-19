<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Filters --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date</label>
                    <input type="date" 
                           wire:model.live="selectedDate" 
                           class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Department</label>
                    <select wire:model.live="selectedDepartment" 
                            class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                        <option value="">All Departments</option>
                        @foreach($this->getDepartmentOptions() as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <button onclick="window.print()" 
                            class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg">
                        <x-heroicon-o-printer class="w-5 h-5 mr-2" />
                        Print
                    </button>
                </div>
            </div>
        </div>

        {{-- Stats Cards --}}
        @php $stats = $this->getStats(); @endphp
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 text-center">
                <p class="text-3xl font-bold text-primary-600">{{ $stats['total'] }}</p>
                <p class="text-sm text-gray-500">Total Meals</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 text-center">
                <p class="text-3xl font-bold text-blue-600">{{ $stats['staff'] }}</p>
                <p class="text-sm text-gray-500">Staff</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 text-center">
                <p class="text-3xl font-bold text-yellow-600">{{ $stats['nss'] }}</p>
                <p class="text-sm text-gray-500">NSS</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 text-center">
                <p class="text-3xl font-bold text-green-600">{{ $stats['paid'] }}</p>
                <p class="text-sm text-gray-500">Paid</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 text-center">
                <p class="text-3xl font-bold text-red-600">{{ $stats['pending'] }}</p>
                <p class="text-sm text-gray-500">Pending</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 text-center">
                <p class="text-3xl font-bold text-teal-600">{{ $stats['served'] }}</p>
                <p class="text-sm text-gray-500">Served</p>
            </div>
        </div>

        {{-- Payment Totals --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-green-50 dark:bg-green-900/20 rounded-lg shadow p-4">
                <p class="text-sm text-green-600 dark:text-green-400">Total Paid</p>
                <p class="text-2xl font-bold text-green-700 dark:text-green-300">GHS {{ number_format($stats['total_paid'], 2) }}</p>
            </div>
            <div class="bg-red-50 dark:bg-red-900/20 rounded-lg shadow p-4">
                <p class="text-sm text-red-600 dark:text-red-400">Total Pending</p>
                <p class="text-2xl font-bold text-red-700 dark:text-red-300">GHS {{ number_format($stats['total_pending'], 2) }}</p>
            </div>
        </div>

        {{-- Table --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow">
            {{ $this->table }}
        </div>
    </div>
</x-filament-panels::page>
