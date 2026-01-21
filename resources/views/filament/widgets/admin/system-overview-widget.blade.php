<x-filament-widgets::widget>
    <div class="grid grid-cols-5 gap-4">
        @foreach($stats as $stat)
            <div class="rounded-xl p-5 text-center transition-all duration-200 hover:shadow-md" 
                 style="background-color: {{ $stat['bgColor'] }};">
                <p class="text-gray-600 text-xs font-normal mb-2">{{ $stat['label'] }}</p>
                <h3 class="text-2xl font-semibold" style="color: {{ $stat['color'] }};">
                    {{ number_format($stat['value']) }}
                </h3>
            </div>
        @endforeach
    </div>
</x-filament-widgets::widget>
