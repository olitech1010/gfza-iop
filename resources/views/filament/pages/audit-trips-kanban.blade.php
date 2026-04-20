@php
    $trips = $this->getTrips();
    $teams = $this->getTeams();
    $counts = $this->getCounts();
@endphp

<x-filament-panels::page>

    {{-- Filters Bar --}}
    <div class="flex flex-col md:flex-row gap-3 mb-6 items-start md:items-center">
        {{-- Status Tabs --}}
        <div class="flex gap-1 bg-gray-100 dark:bg-gray-800 rounded-lg p-1">
            @foreach([
                'all' => ['label' => 'All', 'color' => 'gray'],
                'scheduled' => ['label' => 'Scheduled', 'color' => 'gray'],
                'in_progress' => ['label' => 'In Progress', 'color' => 'amber'],
                'completed' => ['label' => 'Completed', 'color' => 'green'],
            ] as $tab => $info)
                <button
                    wire:click="setTab('{{ $tab }}')"
                    class="px-3 py-1.5 text-xs font-medium rounded-md transition-all
                        {{ $activeTab === $tab
                            ? 'bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm'
                            : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}"
                >
                    {{ $info['label'] }}
                    <span class="ml-1 inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-bold rounded-full
                        {{ $activeTab === $tab ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-200 dark:bg-gray-600 text-gray-600 dark:text-gray-300' }}">
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
        <div class="relative ml-auto">
            <x-heroicon-o-magnifying-glass class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" />
            <input wire:model.live.debounce.300ms="searchQuery"
                   type="text"
                   placeholder="Search company, members..."
                   class="text-xs pl-9 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 focus:ring-green-500 focus:border-green-500 w-64" />
        </div>
    </div>

    {{-- Kanban Cards Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @forelse($trips as $trip)
            @php
                $statusColors = match($trip->status) {
                    'scheduled' => ['border' => 'border-l-gray-400', 'bg' => 'bg-gray-50 dark:bg-gray-800/50', 'badge' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300', 'dot' => 'bg-gray-400'],
                    'in_progress' => ['border' => 'border-l-amber-500', 'bg' => 'bg-amber-50/50 dark:bg-amber-900/10', 'badge' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400', 'dot' => 'bg-amber-500'],
                    'completed' => ['border' => 'border-l-green-500', 'bg' => 'bg-green-50/50 dark:bg-green-900/10', 'badge' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400', 'dot' => 'bg-green-500'],
                    'cancelled' => ['border' => 'border-l-red-500', 'bg' => 'bg-red-50/50 dark:bg-red-900/10', 'badge' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400', 'dot' => 'bg-red-500'],
                    'postponed' => ['border' => 'border-l-blue-500', 'bg' => 'bg-blue-50/50 dark:bg-blue-900/10', 'badge' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400', 'dot' => 'bg-blue-500'],
                    default => ['border' => 'border-l-gray-400', 'bg' => 'bg-gray-50 dark:bg-gray-800/50', 'badge' => 'bg-gray-100 text-gray-700', 'dot' => 'bg-gray-400'],
                };
                $typeColors = $trip->audit_type === 'compliance'
                    ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400'
                    : 'bg-cyan-100 text-cyan-700 dark:bg-cyan-900/30 dark:text-cyan-400';
                $scheduleColors = $trip->schedule_type === 'internal'
                    ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400'
                    : 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400';
            @endphp

            <div class="rounded-xl border border-gray-200 dark:border-gray-700 {{ $statusColors['bg'] }} border-l-4 {{ $statusColors['border'] }} shadow-sm hover:shadow-md transition-all duration-200 overflow-hidden">
                {{-- Card Header --}}
                <div class="px-4 pt-4 pb-2">
                    <div class="flex items-start justify-between mb-2">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                                {{ $trip->team_name }}
                            </span>
                            <span class="text-[10px] text-gray-400 dark:text-gray-500">#{{ $trip->sequence_number }}</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full {{ $statusColors['dot'] }}"></span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{ $statusColors['badge'] }}">
                                {{ str_replace('_', ' ', ucfirst($trip->status)) }}
                            </span>
                        </div>
                    </div>

                    {{-- Company Name --}}
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-2 leading-tight">
                        {{ $trip->company_name }}
                    </h3>

                    {{-- Tags Row --}}
                    <div class="flex flex-wrap gap-1.5 mb-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-medium {{ $typeColors }}">
                            {{ ucfirst($trip->audit_type) }}
                        </span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-medium {{ $scheduleColors }}">
                            {{ ucfirst($trip->schedule_type) }}
                        </span>
                        @if($trip->region)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-medium bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400">
                                {{ $trip->region }}
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Card Body --}}
                <div class="px-4 pb-3 space-y-2">
                    {{-- Date --}}
                    <div class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-400">
                        <x-heroicon-o-calendar-days class="w-3.5 h-3.5 text-gray-400 shrink-0" />
                        <span>{{ $trip->scheduled_date }}</span>
                    </div>

                    {{-- Team Members --}}
                    <div class="flex items-start gap-2 text-xs text-gray-600 dark:text-gray-400">
                        <x-heroicon-o-user-group class="w-3.5 h-3.5 text-gray-400 shrink-0 mt-0.5" />
                        <span class="line-clamp-2">{{ $trip->team_members }}</span>
                    </div>
                </div>

                {{-- Card Footer / Actions --}}
                <div class="px-4 py-2.5 border-t border-gray-100 dark:border-gray-700/50 flex items-center justify-between">
                    <a href="{{ url('/admin/audit-trips/' . $trip->id . '/edit') }}"
                       class="text-xs text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 flex items-center gap-1 transition-colors">
                        <x-heroicon-o-pencil-square class="w-3.5 h-3.5" />
                        Edit
                    </a>

                    @if($trip->status === 'scheduled')
                        <button wire:click="markInProgress({{ $trip->id }})"
                                class="text-xs font-medium text-amber-700 hover:text-amber-900 dark:text-amber-400 dark:hover:text-amber-300 flex items-center gap-1 transition-colors">
                            <x-heroicon-o-play class="w-3.5 h-3.5" />
                            Start
                        </button>
                    @elseif($trip->status === 'in_progress')
                        <button wire:click="markCompleted({{ $trip->id }})"
                                class="text-xs font-medium text-green-700 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300 flex items-center gap-1 transition-colors">
                            <x-heroicon-o-check-circle class="w-3.5 h-3.5" />
                            Complete
                        </button>
                    @elseif($trip->status === 'completed')
                        <span class="text-xs text-green-600 dark:text-green-400 flex items-center gap-1">
                            <x-heroicon-s-check-circle class="w-3.5 h-3.5" />
                            Done
                        </span>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center">
                <x-heroicon-o-clipboard-document-check class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-600 mb-3" />
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">No audit trips found</h3>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Try adjusting your filters.</p>
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
