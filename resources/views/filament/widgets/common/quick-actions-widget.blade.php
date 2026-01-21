<x-filament-widgets::widget>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
        <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4">
            Quick Actions
        </h3>
        
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
            @foreach($actions as $action)
                <a href="{{ $action['url'] }}" 
                   class="group flex flex-col items-center p-4 rounded-lg transition-all duration-200 hover:scale-105"
                   style="background-color: {{ $action['color'] }}15;">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center mb-2 transition-colors duration-200"
                         style="background-color: {{ $action['color'] }}25;">
                        <x-filament::icon 
                            :icon="$action['icon']" 
                            class="w-5 h-5"
                            style="color: {{ $action['color'] }};"
                        />
                    </div>
                    <span class="text-xs font-medium text-gray-700 dark:text-gray-300 text-center leading-tight">
                        {{ $action['label'] }}
                    </span>
                </a>
            @endforeach
        </div>
    </div>
</x-filament-widgets::widget>
