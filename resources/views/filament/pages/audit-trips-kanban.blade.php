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
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse($trips as $trip)
            @php
                $driverName = $trip->driver?->user?->name;

                $statusIcon = match($trip->status) {
                    'scheduled' => 'heroicon-o-clock',
                    'in_progress' => 'heroicon-o-arrow-path',
                    'completed' => 'heroicon-o-check-circle',
                    'cancelled' => 'heroicon-o-x-circle',
                    'postponed' => 'heroicon-o-pause-circle',
                    default => 'heroicon-o-clock',
                };

                $borderColor = match($trip->status) {
                    'scheduled' => 'border-l-gray-400',
                    'in_progress' => 'border-l-amber-500',
                    'completed' => 'border-l-emerald-500',
                    'cancelled' => 'border-l-red-500',
                    'postponed' => 'border-l-blue-500',
                    default => 'border-l-gray-400',
                };
            @endphp

            <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 border-l-4 {{ $borderColor }} shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 ease-out"
                 style="padding: 20px 24px;">

                {{-- 2. Header row: Team + Sequence + Status --}}
                <div class="flex items-center gap-2">
                    {{-- Team pill --}}
                    <span style="font-size: 11px; font-weight: 500; padding: 2px 8px; border-radius: 4px;"
                          class="border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                        {{ $trip->team_name }}
                    </span>
                    {{-- Sequence --}}
                    <span style="font-size: 11px;" class="text-gray-400 dark:text-gray-500">#{{ $trip->sequence_number }}</span>
                    {{-- Status pill (pushed right) --}}
                    @php
                        $statusClasses = match($trip->status) {
                            'in_progress' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/20 dark:text-amber-400',
                            'completed' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-400',
                            'cancelled' => 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
                            'postponed' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400',
                            default => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
                        };
                    @endphp
                    <span class="ml-auto inline-flex items-center gap-1.5 {{ $statusClasses }}"
                          style="font-size: 12px; font-weight: 500; padding: 3px 10px; border-radius: 20px;">
                        <x-dynamic-component :component="$statusIcon" style="width: 13px; height: 13px;" />
                        {{ str_replace('_', ' ', ucfirst($trip->status)) }}
                    </span>
                </div>

                {{-- 3. Card title --}}
                <h3 style="font-size: 17px; font-weight: 500; margin: 14px 0 8px;" class="text-gray-900 dark:text-white leading-snug">
                    {{ $trip->company_name }}
                </h3>

                {{-- 4. Type badges --}}
                <div class="flex flex-wrap items-center" style="gap: 6px; margin-bottom: 16px;">
                    {{-- Audit type --}}
                    @if($trip->audit_type === 'compliance')
                        <span style="font-size: 11px; font-weight: 500; padding: 3px 9px; border-radius: 4px; background-color: #E6F1FB; color: #0C447C;">
                            Compliance
                        </span>
                    @else
                        <span style="font-size: 11px; font-weight: 500; padding: 3px 9px; border-radius: 4px; background-color: #E6F1FB; color: #0C447C;">
                            Monitoring
                        </span>
                    @endif

                    {{-- Schedule type --}}
                    @if($trip->schedule_type === 'internal')
                        <span style="font-size: 11px; font-weight: 500; padding: 3px 9px; border-radius: 4px; background-color: #F1EFE8; color: #444441;">
                            Internal
                        </span>
                    @else
                        <span style="font-size: 11px; font-weight: 500; padding: 3px 9px; border-radius: 4px; background-color: #E1F5EE; color: #085041;">
                            External
                        </span>
                    @endif

                    {{-- Region --}}
                    @if($trip->region)
                        <span style="font-size: 11px; font-weight: 500; padding: 3px 9px; border-radius: 4px; background-color: #F1EFE8; color: #444441;">
                            {{ $trip->region }}
                        </span>
                    @endif
                </div>

                {{-- 5. Divider --}}
                <div style="height: 0.5px; margin-bottom: 16px;" class="bg-gray-200 dark:bg-gray-700"></div>

                {{-- 6. Metadata rows --}}
                <div class="flex flex-col" style="gap: 10px; margin-bottom: 20px;">
                    {{-- Date --}}
                    <div class="flex items-start" style="gap: 10px; font-size: 13px;" >
                        <x-heroicon-o-calendar-days style="width: 15px; height: 15px; margin-top: 1px;" class="text-gray-400 dark:text-gray-500 shrink-0" />
                        <span class="text-gray-600 dark:text-gray-400" style="line-height: 1.5;">{{ $trip->scheduled_date }}</span>
                    </div>

                    {{-- Team members --}}
                    <div class="flex items-start" style="gap: 10px; font-size: 13px;">
                        <x-heroicon-o-user-group style="width: 15px; height: 15px; margin-top: 1px;" class="text-gray-400 dark:text-gray-500 shrink-0" />
                        <span class="text-gray-600 dark:text-gray-400 line-clamp-2" style="line-height: 1.5;">{{ $trip->team_members }}</span>
                    </div>

                    {{-- Driver --}}
                    <div class="flex items-start" style="gap: 10px; font-size: 13px;">
                        <x-heroicon-o-identification style="width: 15px; height: 15px; margin-top: 1px;"
                            class="shrink-0 {{ $driverName ? 'text-green-500 dark:text-green-400' : 'text-gray-300 dark:text-gray-600' }}" />
                        @if($driverName)
                            <span class="text-gray-700 dark:text-gray-300 font-medium" style="line-height: 1.5;">{{ $driverName }}</span>
                        @else
                            <span class="text-gray-400 dark:text-gray-500 italic" style="font-size: 12px; line-height: 1.5;">No driver assigned</span>
                        @endif
                    </div>

                    {{-- Vehicle --}}
                    @if($trip->vehicle)
                        <div class="flex items-start" style="gap: 10px; font-size: 13px;">
                            <x-heroicon-o-truck style="width: 15px; height: 15px; margin-top: 1px;" class="text-gray-400 dark:text-gray-500 shrink-0" />
                            <span class="text-gray-600 dark:text-gray-400" style="line-height: 1.5;">{{ $trip->vehicle->registration_number }}</span>
                        </div>
                    @endif
                </div>

                {{-- 7. Actions footer --}}
                <div class="flex items-center justify-between border-t border-gray-200 dark:border-gray-700" style="padding-top: 16px;">
                    <div>
                        {{-- Edit --}}
                        <a href="{{ url('/admin/audit-trips/' . $trip->id . '/edit') }}"
                           class="inline-flex items-center gap-1.5 border text-gray-500 dark:text-gray-400 border-gray-300 dark:border-gray-600 bg-transparent hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                           style="padding: 7px 16px; border-radius: 6px; font-size: 13px; font-weight: 500;">
                            <x-heroicon-o-pencil-square style="width: 14px; height: 14px;" />
                            Edit
                        </a>
                    </div>

                    <div>
                        @if($trip->status === 'scheduled')
                            <button wire:click="markInProgress({{ $trip->id }})"
                                    class="inline-flex items-center gap-1.5 border border-blue-500 text-blue-600 dark:text-blue-400 dark:border-blue-400 bg-transparent hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors"
                                    style="padding: 7px 16px; border-radius: 6px; font-size: 13px; font-weight: 500;">
                                <x-heroicon-o-play style="width: 14px; height: 14px;" />
                                Start
                            </button>
                        @elseif($trip->status === 'in_progress')
                            <button wire:click="markCompleted({{ $trip->id }})"
                                    class="inline-flex items-center gap-1.5 border border-emerald-500 text-emerald-600 dark:text-emerald-400 dark:border-emerald-400 bg-transparent hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition-colors"
                                    style="padding: 7px 16px; border-radius: 6px; font-size: 13px; font-weight: 500;">
                                <x-heroicon-o-check-circle style="width: 14px; height: 14px;" />
                                Complete
                            </button>
                        @elseif($trip->status === 'completed')
                            <span class="inline-flex items-center gap-1.5 border border-emerald-500 text-emerald-600 dark:text-emerald-400 dark:border-emerald-400"
                                  style="padding: 7px 16px; border-radius: 6px; font-size: 13px; font-weight: 500;">
                                <x-heroicon-s-check-circle style="width: 14px; height: 14px;" />
                                Completed
                            </span>
                        @endif
                    </div>
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
        <div class="mt-8">
            {{ $trips->links() }}
        </div>
    @endif
</x-filament-panels::page>
