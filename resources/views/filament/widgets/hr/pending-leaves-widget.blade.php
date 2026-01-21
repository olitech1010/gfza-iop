<x-filament-widgets::widget>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full flex items-center justify-center" style="background-color: #EC489920;">
                    <svg class="w-4 h-4" style="color: #EC4899;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Pending HR Approval</h3>
                @if($totalPending > 0)
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-pink-100 text-pink-700 dark:bg-pink-900/30 dark:text-pink-400">
                        {{ $totalPending }}
                    </span>
                @endif
            </div>
            <a href="{{ $viewAllUrl }}" class="text-xs font-medium text-primary-600 hover:text-primary-500">View All</a>
        </div>
        
        @if($pendingLeaves->count() > 0)
            <div class="space-y-3">
                @foreach($pendingLeaves as $leave)
                    <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $leave['userName'] }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $leave['department'] }} • {{ $leave['startDate'] }} - {{ $leave['endDate'] }} ({{ $leave['days'] }}d)
                            </p>
                        </div>
                        <a href="/admin/leave-requests/{{ $leave['id'] }}/edit" 
                           class="shrink-0 px-3 py-1 rounded-lg text-xs font-medium bg-pink-100 text-pink-700 dark:bg-pink-900/30 dark:text-pink-400 hover:bg-pink-200 transition-colors">
                            Approve
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-6">
                <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400">No pending approvals</p>
            </div>
        @endif
    </div>
</x-filament-widgets::widget>
