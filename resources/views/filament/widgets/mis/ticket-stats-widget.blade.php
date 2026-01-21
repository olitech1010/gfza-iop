<x-filament-widgets::widget>
    <div class="grid grid-cols-3 gap-3">
        @foreach($stats as $stat)
            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-100 dark:border-gray-700 transition-all duration-200 hover:shadow-md">
                <p class="text-gray-500 dark:text-gray-400 text-xs font-normal">{{ $stat['label'] }}</p>
                <h3 class="text-xl font-semibold mt-1" style="color: {{ $stat['color'] }};">
                    {{ $stat['value'] }}
                </h3>
            </div>
        @endforeach
    </div>
</x-filament-widgets::widget>
