<x-filament-widgets::widget>
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 transition-all duration-300 hover:shadow-md">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">{{ $currentDate }}</p>
                <h2 class="text-gray-900 dark:text-white text-2xl font-bold mt-1">
                    {{ $greeting }}, {{ $userName }}
                </h2>
                <div class="mt-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold" 
                          style="background-color: #E6F4EA; color: #1e7e34;">
                        {{ $roleLabel }}
                    </span>
                </div>
            </div>
            
            <div class="hidden sm:flex items-center gap-3">
                <a href="/admin/leave-requests/create" 
                   class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 hover:scale-105"
                   style="background-color: #c62828; color: white;">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Request ED
                </a>
                <a href="/admin/mis-tickets/create" 
                   class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 hover:scale-105"
                   style="background-color: #00c73f; color: white;">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                    </svg>
                    Submit Ticket
                </a>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
