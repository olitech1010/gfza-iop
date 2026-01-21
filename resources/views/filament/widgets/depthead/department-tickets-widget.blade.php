<x-filament-widgets::widget>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full flex items-center justify-center" style="background-color: #8B5CF620;">
                    <svg class="w-4 h-4" style="color: #8B5CF6;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                    </svg>
                </div>
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Department IT Tickets</h3>
            </div>
            <a href="{{ $viewAllUrl }}" class="text-xs font-medium text-primary-600 hover:text-primary-500">View All</a>
        </div>
        
        @if($recentTickets->count() > 0)
            <div class="space-y-3">
                @foreach($recentTickets as $ticket)
                    <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                        <div class="flex-1 min-w-0 pr-3">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ Str::limit($ticket['title'], 35) }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $ticket['userName'] }}</p>
                        </div>
                        <span class="shrink-0 px-2 py-1 rounded-full text-xs font-medium" 
                              style="background-color: {{ $ticket['statusColor'] }}20; color: {{ $ticket['statusColor'] }};">
                            {{ $ticket['statusLabel'] }}
                        </span>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-6">
                <p class="text-sm text-gray-500 dark:text-gray-400">No tickets from your department</p>
            </div>
        @endif
    </div>
</x-filament-widgets::widget>
