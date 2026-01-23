<x-filament-widgets::widget>
    <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-100 dark:border-gray-700">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-gray-900 dark:text-white text-sm font-normal">Department IT Tickets</h3>
            <a href="{{ $viewAllUrl }}" class="text-xs text-blue-600 hover:text-blue-800">View All</a>
        </div>
        
        @if($recentTickets->count() > 0)
            <div class="space-y-2">
                @foreach($recentTickets as $ticket)
                    <div class="flex items-center justify-between p-3 rounded-lg" style="background-color: #F5F5F5;">
                        <div class="flex-1 min-w-0 pr-3">
                            <p class="text-gray-900 text-xs font-normal truncate">{{ Str::limit($ticket['title'], 35) }}</p>
                            <p class="text-gray-500 text-xs">{{ $ticket['userName'] }}</p>
                        </div>
                        <span class="shrink-0 px-2 py-1 rounded-full text-xs font-medium" 
                              style="background-color: {{ $ticket['statusColor'] }}20; color: {{ $ticket['statusColor'] }};">
                            {{ $ticket['statusLabel'] }}
                        </span>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500 text-sm text-center py-4">No tickets from your department</p>
        @endif
    </div>
</x-filament-widgets::widget>
