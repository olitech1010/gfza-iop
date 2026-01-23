<x-filament-widgets::widget>
    <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-100 dark:border-gray-700">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-gray-900 dark:text-white text-sm font-normal">Pending Leave Approvals</h3>
            @if($pendingLeaves->count() > 0)
                <a href="{{ $viewAllUrl }}" class="text-xs text-blue-600 hover:text-blue-800">View All</a>
            @endif
        </div>
        
        @if($pendingLeaves->count() > 0)
            <div class="space-y-2">
                @foreach($pendingLeaves as $leave)
                    <div class="flex items-center justify-between p-3 rounded-lg" style="background-color: #FCE8EC;">
                        <div class="flex-1 min-w-0">
                            <p class="text-gray-900 text-xs font-normal truncate">{{ $leave['userName'] }}</p>
                            <p class="text-gray-500 text-xs">
                                {{ $leave['startDate'] }} - {{ $leave['endDate'] }} ({{ $leave['days'] }} days)
                            </p>
                        </div>
                        <a href="/admin/leave-requests/{{ $leave['id'] }}/edit" 
                           class="shrink-0 px-3 py-1 rounded-lg text-xs font-medium transition-colors"
                           style="background-color: #ea4335; color: white;">
                            Review
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500 text-sm text-center py-4">No pending approvals ✓</p>
        @endif
    </div>
</x-filament-widgets::widget>
