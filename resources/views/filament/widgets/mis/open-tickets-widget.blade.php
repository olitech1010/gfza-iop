<x-filament-widgets::widget>
    <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-100 dark:border-gray-700">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-gray-900 dark:text-white text-sm font-normal">Open Tickets</h3>
            <a href="{{ $viewAllUrl }}" class="text-xs text-blue-600 hover:text-blue-800">View All</a>
        </div>
        
        @if($openTickets->count() > 0)
            <div class="space-y-2">
                @foreach($openTickets as $ticket)
                    <a href="/admin/mis-tickets/{{ $ticket['id'] }}/edit" 
                       class="block p-3 rounded-lg transition-colors hover:bg-gray-50 dark:hover:bg-gray-700"
                       style="background-color: #FDECEA;">
                        <p class="text-gray-900 text-xs font-normal truncate">{{ Str::limit($ticket['title'], 35) }}</p>
                        <p class="text-gray-500 text-xs mt-1">{{ $ticket['userName'] }} • {{ $ticket['createdAt'] }}</p>
                    </a>
                @endforeach
            </div>
        @else
            <p class="text-gray-500 text-sm text-center py-4">No open tickets 🎉</p>
        @endif
    </div>
</x-filament-widgets::widget>
