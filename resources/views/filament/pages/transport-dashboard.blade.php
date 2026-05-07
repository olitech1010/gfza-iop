@php
    $stats = $this->getStats();
    $recentRequisitions = $this->getRecentRequisitions();
    $upcomingServices = $this->getUpcomingServices();
@endphp

<x-filament-panels::page>
    {{-- Fleet Overview Stats --}}
    <div class="flex flex-col xl:flex-row gap-4 mb-8 overflow-x-auto pb-2">
        <div class="flex-1 min-w-[170px] rounded-xl px-6 py-5 text-white shadow-md" style="background-color: #1a73e8;">
            <div class="flex items-center gap-3">
                <x-heroicon-o-truck class="w-6 h-6 opacity-90" />
                <div>
                    <div class="text-xs font-medium opacity-80">Total Vehicles</div>
                    <div class="text-2xl font-bold">{{ $stats['totalVehicles'] }}</div>
                </div>
            </div>
        </div>
        <div class="flex-1 min-w-[170px] rounded-xl px-6 py-5 text-white shadow-md" style="background-color: #10b981;">
            <div class="flex items-center gap-3">
                <x-heroicon-o-check-circle class="w-6 h-6 opacity-90" />
                <div>
                    <div class="text-xs font-medium opacity-80">Available</div>
                    <div class="text-2xl font-bold">{{ $stats['availableVehicles'] }}</div>
                </div>
            </div>
        </div>
        <div class="flex-1 min-w-[170px] rounded-xl px-6 py-5 text-white shadow-md" style="background-color: #f59e0b;">
            <div class="flex items-center gap-3">
                <x-heroicon-o-arrow-path class="w-6 h-6 opacity-90" />
                <div>
                    <div class="text-xs font-medium opacity-80">In Use</div>
                    <div class="text-2xl font-bold">{{ $stats['inUseVehicles'] }}</div>
                </div>
            </div>
        </div>
        <div class="flex-1 min-w-[170px] rounded-xl px-6 py-5 text-white shadow-md" style="background-color: {{ $stats['pendingRequisitions'] > 0 ? '#ef4444' : '#6b7280' }};">
            <div class="flex items-center gap-3">
                <x-heroicon-o-bell-alert class="w-6 h-6 opacity-90" />
                <div>
                    <div class="text-xs font-medium opacity-80">Pending Requests</div>
                    <div class="text-2xl font-bold">{{ $stats['pendingRequisitions'] }}</div>
                </div>
            </div>
        </div>
        <div class="flex-1 min-w-[170px] rounded-xl px-6 py-5 text-white shadow-md" style="background-color: #6366f1;">
            <div class="flex items-center gap-3">
                <x-heroicon-o-identification class="w-6 h-6 opacity-90" />
                <div>
                    <div class="text-xs font-medium opacity-80">Active Drivers</div>
                    <div class="text-2xl font-bold">{{ $stats['totalDrivers'] }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <h3 class="text-lg font-semibold mb-4 dark:text-white">Quick Actions</h3>
    <div class="flex flex-row gap-4 mb-10">
        <a href="{{ url('/admin/vehicle-requisitions/create') }}"
           class="flex-1 group relative overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-5 py-5 shadow-sm transition-all hover:shadow-lg hover:border-blue-400 hover:-translate-y-0.5">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/30">
                    <x-heroicon-o-clipboard-document-list class="h-5 w-5 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white">New Requisition</h4>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Request a vehicle</p>
                </div>
            </div>
        </a>
        <a href="{{ url('/admin/vehicle-services/create') }}"
           class="flex-1 group relative overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-5 py-5 shadow-sm transition-all hover:shadow-lg hover:border-amber-400 hover:-translate-y-0.5">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-amber-100 dark:bg-amber-900/30">
                    <x-heroicon-o-wrench-screwdriver class="h-5 w-5 text-amber-600 dark:text-amber-400" />
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white">Log Service</h4>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Record maintenance</p>
                </div>
            </div>
        </a>
        <a href="{{ url('/admin/fuel-logs/create') }}"
           class="flex-1 group relative overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-5 py-5 shadow-sm transition-all hover:shadow-lg hover:border-green-400 hover:-translate-y-0.5">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-green-100 dark:bg-green-900/30">
                    <x-heroicon-o-fire class="h-5 w-5 text-green-600 dark:text-green-400" />
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white">Log Fuel</h4>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Record fuel purchase</p>
                </div>
            </div>
        </a>
        <a href="{{ url('/admin/audit-trips') }}"
           class="flex-1 group relative overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-5 py-5 shadow-sm transition-all hover:shadow-lg hover:border-purple-400 hover:-translate-y-0.5">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-purple-100 dark:bg-purple-900/30">
                    <x-heroicon-o-clipboard-document-check class="h-5 w-5 text-purple-600 dark:text-purple-400" />
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white">Audit Schedules</h4>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $stats['auditScheduled'] }} scheduled</p>
                </div>
            </div>
        </a>
    </div>

    {{-- Monthly Summary Cards --}}
    <div class="flex flex-row gap-4 mb-10">
        <div class="flex-1 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-6 py-5 shadow-sm">
            <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Trips This Month</div>
            <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['completedTripsMonth'] }}</div>
        </div>
        <div class="flex-1 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-6 py-5 shadow-sm">
            <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Active Trips</div>
            <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['activeTrips'] }}</div>
        </div>
        <div class="flex-1 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-6 py-5 shadow-sm">
            <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Fuel Cost (Month)</div>
            <div class="text-xl font-bold text-gray-900 dark:text-white">GHS {{ number_format($stats['totalFuelCostMonth'], 2) }}</div>
        </div>
        <div class="flex-1 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-6 py-5 shadow-sm">
            <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Maintenance Due</div>
            <div class="text-2xl font-bold {{ $stats['maintenanceVehicles'] > 0 ? 'text-red-600' : 'text-gray-900 dark:text-white' }}">{{ $stats['maintenanceVehicles'] }}</div>
        </div>
    </div>

    {{-- Recent Requisitions --}}
    <h3 class="text-lg font-semibold mb-4 dark:text-white">Recent Vehicle Requisitions</h3>
    <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm mb-10">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700/50">
                <tr>
                    <th class="px-5 py-3.5 text-left font-medium text-gray-600 dark:text-gray-300">Ref #</th>
                    <th class="px-5 py-3.5 text-left font-medium text-gray-600 dark:text-gray-300">Requester</th>
                    <th class="px-5 py-3.5 text-left font-medium text-gray-600 dark:text-gray-300">Destination</th>
                    <th class="px-5 py-3.5 text-left font-medium text-gray-600 dark:text-gray-300">Date</th>
                    <th class="px-5 py-3.5 text-left font-medium text-gray-600 dark:text-gray-300">Vehicle</th>
                    <th class="px-5 py-3.5 text-left font-medium text-gray-600 dark:text-gray-300">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse ($recentRequisitions as $req)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                        <td class="px-5 py-3.5 font-medium text-gray-900 dark:text-white">{{ $req->reference_number }}</td>
                        <td class="px-5 py-3.5 text-gray-700 dark:text-gray-300">{{ $req->requester->name ?? '—' }}</td>
                        <td class="px-5 py-3.5 text-gray-700 dark:text-gray-300">{{ \Illuminate\Support\Str::limit($req->destination, 30) }}</td>
                        <td class="px-5 py-3.5 text-gray-700 dark:text-gray-300">{{ $req->requested_date->format('d M Y') }}</td>
                        <td class="px-5 py-3.5 text-gray-700 dark:text-gray-300">{{ $req->vehicle->registration_number ?? '—' }}</td>
                        <td class="px-5 py-3.5">
                            @php
                                $color = match($req->status) {
                                    'pending' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                    'vehicle_assigned' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
                                    'transport_approved' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-400',
                                    'admin_approved' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                                    'in_progress' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400',
                                    'completed' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                                    'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                                    default => 'bg-gray-100 text-gray-800',
                                };
                                $label = str_replace('_', ' ', ucfirst($req->status));
                            @endphp
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $color }}">{{ $label }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-10 text-center text-gray-400 dark:text-gray-500">
                            <x-heroicon-o-inbox class="mx-auto h-8 w-8 mb-2 opacity-50" />
                            No requisitions yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Upcoming Services --}}
    @if($upcomingServices->count() > 0)
    <h3 class="text-lg font-semibold mb-4 dark:text-white">Upcoming / Overdue Maintenance</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($upcomingServices as $svc)
            <div class="rounded-xl border {{ $svc->service_date->isPast() ? 'border-red-300 dark:border-red-700' : 'border-gray-200 dark:border-gray-700' }} bg-white dark:bg-gray-800 p-5 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <span class="font-semibold text-gray-900 dark:text-white">{{ $svc->vehicle->registration_number ?? '—' }}</span>
                    @if($svc->service_date->isPast())
                        <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800 dark:bg-red-900/30 dark:text-red-400">Overdue</span>
                    @else
                        <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-medium text-amber-800 dark:bg-amber-900/30 dark:text-amber-400">Upcoming</span>
                    @endif
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400">{{ str_replace('_', ' ', ucwords($svc->service_type, '_')) }}</div>
                <div class="text-sm text-gray-700 dark:text-gray-300 mt-1">{{ $svc->service_date->format('d M Y') }} · {{ $svc->service_provider }}</div>
            </div>
        @endforeach
    </div>
    @endif
</x-filament-panels::page>
