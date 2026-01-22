<x-filament-widgets::widget>
    <div class="flex gap-4">
        @foreach($stats as $stat)
            <div class="flex-1 rounded-xl p-4 text-center transition-all duration-200 hover:shadow-md" 
                 style="background-color: {{ $stat['bgColor'] }};">
                <p class="text-gray-600 text-xs font-normal mb-1">{{ $stat['label'] }}</p>
                <h3 class="text-xl font-semibold" style="color: {{ $stat['color'] }};">
                    {{ number_format($stat['value']) }}
                </h3>
            </div>
        @endforeach
    </div>
</x-filament-widgets::widget>
