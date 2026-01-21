<x-filament-widgets::widget>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        @foreach($stats as $stat)
            <div class="relative overflow-hidden rounded-xl p-5" style="background: linear-gradient(135deg, {{ $stat['color'] }} 0%, {{ $stat['color'] }}dd 100%);">
                <div class="absolute top-0 right-0 -mt-2 -mr-2 w-20 h-20 opacity-10">
                    @if($stat['icon'] === 'exclamation')
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="white">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                        </svg>
                    @elseif($stat['icon'] === 'clock')
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="white">
                            <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
                        </svg>
                    @elseif($stat['icon'] === 'check')
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="white">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                    @endif
                </div>
                <div class="relative z-10">
                    <p class="text-white/80 text-sm font-medium">{{ $stat['label'] }}</p>
                    <h3 class="text-white text-3xl font-bold mt-1">{{ $stat['value'] }}</h3>
                </div>
            </div>
        @endforeach
    </div>
</x-filament-widgets::widget>
