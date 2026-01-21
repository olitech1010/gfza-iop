<x-filament-widgets::widget>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full flex items-center justify-center" style="background-color: #8B5CF620;">
                    <svg class="w-4 h-4" style="color: #8B5CF6;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Recent Memos</h3>
            </div>
            <a href="{{ $createUrl }}" class="text-xs font-medium text-primary-600 hover:text-primary-500">+ New</a>
        </div>
        
        @if($recentMemos->count() > 0)
            <div class="space-y-3">
                @foreach($recentMemos as $memo)
                    <div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate flex-1 pr-2">{{ Str::limit($memo['subject'], 30) }}</p>
                            <span class="shrink-0 text-xs text-gray-500 dark:text-gray-400">{{ $memo['createdAt'] }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="flex-1 h-1.5 bg-gray-200 dark:bg-gray-600 rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all" 
                                     style="width: {{ $memo['readPercentage'] }}%; background-color: {{ $memo['readPercentage'] >= 75 ? '#10B981' : ($memo['readPercentage'] >= 50 ? '#F59E0B' : '#EF4444') }};"></div>
                            </div>
                            <span class="shrink-0 text-xs text-gray-500 dark:text-gray-400">
                                {{ $memo['readCount'] }}/{{ $memo['totalRecipients'] }} read
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
            <a href="{{ $viewAllUrl }}" class="mt-3 inline-block text-xs font-medium text-primary-600 hover:text-primary-500">
                View All Memos →
            </a>
        @else
            <div class="text-center py-6">
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">No memos sent yet</p>
                <a href="{{ $createUrl }}" 
                   class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-primary-600 text-white hover:bg-primary-500 transition-colors">
                    Create First Memo
                </a>
            </div>
        @endif
    </div>
</x-filament-widgets::widget>
