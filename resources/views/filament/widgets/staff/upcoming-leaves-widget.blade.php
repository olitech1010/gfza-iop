<x-filament-widgets::widget>
    <div class="rounded-2xl p-6 transition-all duration-300 hover:shadow-md"
         style="background-color: #E8F0FE;">
        <h3 class="text-gray-900 text-sm font-normal mb-3">Upcoming ED</h3>
        @if($upcomingLeaves->count() > 0)
            <div class="space-y-2">
                @foreach($upcomingLeaves->take(2) as $leave)
                    <p class="text-gray-700 text-xs font-normal">
                        {{ $leave['start_date'] }} - {{ $leave['end_date'] }} ({{ $leave['days'] }}d)
                    </p>
                @endforeach
            </div>
        @else
            <p class="text-gray-700 text-sm font-normal mb-3">No upcoming ED scheduled</p>
            <a href="{{ $requestUrl }}" 
               class="inline-flex items-center gap-1 px-3 py-1.5 rounded-md text-xs font-normal transition-all duration-200"
               style="background-color: #1a73e8; color: white;">
                + Request ED
            </a>
        @endif
    </div>
</x-filament-widgets::widget>
