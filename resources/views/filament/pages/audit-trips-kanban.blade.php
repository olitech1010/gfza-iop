@php
    $trips = $this->getTrips();
    $teams = $this->getTeams();
    $counts = $this->getCounts();
@endphp

<x-filament-panels::page>

    {{-- Filters Bar --}}
    <div class="flex flex-col md:flex-row gap-3 mb-5 items-start md:items-center">
        {{-- Status Tabs --}}
        <div class="flex gap-1 bg-gray-100 dark:bg-gray-800 rounded-lg p-1">
            @foreach([
                'all' => 'All',
                'scheduled' => 'Scheduled',
                'in_progress' => 'In Progress',
                'completed' => 'Completed',
            ] as $tab => $label)
                <button
                    wire:click="setTab('{{ $tab }}')"
                    class="px-3 py-1.5 text-xs font-medium rounded-md transition-all
                        {{ $activeTab === $tab
                            ? 'bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm'
                            : 'text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-white' }}"
                >
                    {{ $label }}
                    <span class="ml-1 inline-flex items-center justify-center min-w-[20px] px-1.5 py-0.5 text-[10px] font-bold rounded-full
                        {{ $activeTab === $tab ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-200 dark:bg-gray-600 text-gray-500 dark:text-gray-400' }}">
                        {{ $counts[$tab] }}
                    </span>
                </button>
            @endforeach
        </div>

        {{-- Team Filter --}}
        <select wire:model.live="teamFilter" class="text-xs border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 focus:ring-green-500 focus:border-green-500">
            <option value="">All Teams</option>
            @foreach($teams as $team)
                <option value="{{ $team }}">{{ $team }}</option>
            @endforeach
        </select>

        {{-- Type Filter --}}
        <select wire:model.live="typeFilter" class="text-xs border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 focus:ring-green-500 focus:border-green-500">
            <option value="">All Types</option>
            <option value="compliance">Compliance</option>
            <option value="monitoring">Monitoring</option>
        </select>

        {{-- Search --}}
        <div class="relative md:ml-auto">
            <x-heroicon-o-magnifying-glass class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" />
            <input wire:model.live.debounce.300ms="searchQuery"
                   type="text"
                   placeholder="Search company, driver, region..."
                   class="text-xs pl-9 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 focus:ring-green-500 focus:border-green-500 w-full md:w-64" />
        </div>
    </div>

    {{-- Cards Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
        @forelse($trips as $trip)
            @php
                $statusConfig = match($trip->status) {
                    'scheduled' => ['border' => 'border-l-slate-400', 'badge' => 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300', 'dot' => 'bg-slate-400'],
                    'in_progress' => ['border' => 'border-l-amber-500', 'badge' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400', 'dot' => 'bg-amber-500'],
                    'completed' => ['border' => 'border-l-emerald-500', 'badge' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400', 'dot' => 'bg-emerald-500'],
                    'cancelled' => ['border' => 'border-l-red-500', 'badge' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400', 'dot' => 'bg-red-500'],
                    'postponed' => ['border' => 'border-l-blue-500', 'badge' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400', 'dot' => 'bg-blue-500'],
                    default => ['border' => 'border-l-slate-400', 'badge' => 'bg-slate-100 text-slate-700', 'dot' => 'bg-slate-400'],
                };
                $driverName = $trip->driver?->user?->name;
            @endphp

            <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 border-l-4 {{ $statusConfig['border'] }} shadow-sm hover:shadow-md transition-shadow duration-200">
                {{-- Header --}}
                <div class="px-5 pt-5 pb-3">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                                {{ $trip->team_name }}
                            </span>
                            <span class="text-[11px] text-gray-400 dark:text-gray-500">#{{ $trip->sequence_number }}</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full {{ $statusConfig['dot'] }}"></span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold {{ $statusConfig['badge'] }}">
                                {{ str_replace('_', ' ', ucfirst($trip->status)) }}
                            </span>
                        </div>
                    </div>

                    {{-- Company Name --}}
                    <h3 class="text-[15px] font-semibold text-gray-900 dark:text-white leading-snug mb-3">
                        {{ $trip->company_name }}
                    </h3>

                    {{-- Tags --}}
                    <div class="flex flex-wrap gap-1.5 mb-4">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-medium
                            {{ $trip->audit_type === 'compliance' ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/20 dark:text-indigo-400' : 'bg-cyan-50 text-cyan-700 dark:bg-cyan-900/20 dark:text-cyan-400' }}">
                            {{ ucfirst($trip->audit_type) }}
                        </span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-medium
                            {{ $trip->schedule_type === 'internal' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400' : 'bg-orange-50 text-orange-700 dark:bg-orange-900/20 dark:text-orange-400' }}">
                            {{ ucfirst($trip->schedule_type) }}
                        </span>
                        @if($trip->region)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-medium bg-purple-50 text-purple-700 dark:bg-purple-900/20 dark:text-purple-400">
                                {{ $trip->region }}
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Details --}}
                <div class="px-5 pb-4 space-y-2.5">
                    {{-- Date --}}
                    <div class="flex items-center gap-2.5 text-xs text-gray-600 dark:text-gray-400">
                        <x-heroicon-o-calendar-days class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" />
                        <span>{{ $trip->scheduled_date }}</span>
                    </div>

                    {{-- Members --}}
                    <div class="flex items-start gap-2.5 text-xs text-gray-600 dark:text-gray-400">
                        <x-heroicon-o-user-group class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0 mt-0.5" />
                        <span class="line-clamp-2 leading-relaxed">{{ $trip->team_members }}</span>
                    </div>

                    {{-- Driver --}}
                    <div class="flex items-center gap-2.5 text-xs">
                        <x-heroicon-o-identification class="w-4 h-4 shrink-0
                            {{ $driverName ? 'text-green-500 dark:text-green-400' : 'text-gray-300 dark:text-gray-600' }}" />
                        @if($driverName)
                            <span class="text-gray-700 dark:text-gray-300 font-medium">{{ $driverName }}</span>
                        @else
                            <span class="text-gray-400 dark:text-gray-500 italic">No driver assigned</span>
                        @endif
                    </div>

                    {{-- Vehicle --}}
                    @if($trip->vehicle)
                        <div class="flex items-center gap-2.5 text-xs text-gray-600 dark:text-gray-400">
                            <x-heroicon-o-truck class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" />
                            <span>{{ $trip->vehicle->registration_number }}</span>
                        </div>
                    @endif
                </div>

                {{-- Footer --}}
                <div class="px-5 py-3 border-t border-gray-100 dark:border-gray-700/50 flex items-center justify-between">
                    <a href="{{ url('/admin/audit-trips/' . $trip->id . '/edit') }}"
                       class="text-xs text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 flex items-center gap-1.5 transition-colors">
                        <x-heroicon-o-pencil-square class="w-3.5 h-3.5" />
                        Edit
                    </a>

                    @if($trip->status === 'scheduled')
                        <button wire:click="markInProgress({{ $trip->id }})"
                                class="text-xs font-medium px-3 py-1.5 rounded-lg bg-amber-50 text-amber-700 hover:bg-amber-100 dark:bg-amber-900/20 dark:text-amber-400 dark:hover:bg-amber-900/40 flex items-center gap-1.5 transition-colors">
                            <x-heroicon-o-play class="w-3.5 h-3.5" />
                            Start
                        </button>
                    @elseif($trip->status === 'in_progress')
                        <button wire:click="markCompleted({{ $trip->id }})"
                                class="text-xs font-medium px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 dark:bg-emerald-900/20 dark:text-emerald-400 dark:hover:bg-emerald-900/40 flex items-center gap-1.5 transition-colors">
                            <x-heroicon-o-check-circle class="w-3.5 h-3.5" />
                            Complete
                        </button>
                    @elseif($trip->status === 'completed')
                        <span class="text-xs text-emerald-600 dark:text-emerald-400 flex items-center gap-1.5 font-medium">
                            <x-heroicon-s-check-circle class="w-3.5 h-3.5" />
                            Done
                        </span>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full py-20 text-center">
                <x-heroicon-o-clipboard-document-check class="mx-auto h-14 w-14 text-gray-200 dark:text-gray-700 mb-4" />
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">No audit trips found</h3>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Try adjusting your filters or search query.</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($trips->hasPages())
        <div class="mt-6">
            {{ $trips->links() }}
        </div>
    @endif
</x-filament-panels::page>
