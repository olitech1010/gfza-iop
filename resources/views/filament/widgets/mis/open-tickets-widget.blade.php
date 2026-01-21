<x-filament-widgets::widget>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full flex items-center justify-center" style="background-color: #EF444420;">
                    <svg class="w-4 h-4" style="color: #EF4444;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Open Tickets</h3>
            </div>
            <a href="{{ $viewAllUrl }}" class="text-xs font-medium text-primary-600 hover:text-primary-500">View All</a>
        </div>
        
        @if($openTickets->count() > 0)
            <div class="space-y-3">
                @foreach($openTickets as $ticket)
                    <a href="/admin/mis-tickets/{{ $ticket['id'] }}/edit" 
                       class="block p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <div class="flex items-center justify-between mb-1">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate flex-1 pr-2">
                                {{ Str::limit($ticket['title'], 30) }}
                            </p>
                            <span class="shrink-0 text-xs text-gray-500 dark:text-gray-400">{{ $ticket['createdAt'] }}</span>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $ticket['userName'] }} • {{ $ticket['department'] }}
                        </p>
                    </a>
                @endforeach
            </div>
        @else
            <div class="text-center py-6">
                <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400">No open tickets! 🎉</p>
            </div>
        @endif
    </div>
</x-filament-widgets::widget>
