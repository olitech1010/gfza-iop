<x-filament-widgets::widget>
    <div class="rounded-2xl p-6 transition-all duration-300 hover:shadow-md"
         style="background-color: #E6F4EA;">
        <h3 class="text-gray-900 text-sm font-normal mb-3">IT Tickets</h3>
        @if($recentTickets->count() > 0)
            <div class="space-y-2">
                @foreach($recentTickets->take(2) as $ticket)
                    <p class="text-gray-700 text-xs font-normal truncate">{{ Str::limit($ticket['title'], 35) }}</p>
                @endforeach
            </div>
        @else
            <p class="text-gray-700 text-sm font-normal mb-3">No tickets submitted yet</p>
            <a href="{{ $createUrl }}" 
               class="inline-flex items-center gap-1 px-3 py-1.5 rounded-md text-xs font-normal transition-all duration-200"
               style="background-color: #34a853; color: white;">
                + New Ticket
            </a>
        @endif
    </div>
</x-filament-widgets::widget>
