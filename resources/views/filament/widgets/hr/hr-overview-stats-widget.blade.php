<x-filament-widgets::widget>
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach($stats as $stat)
            <div class="relative overflow-hidden rounded-xl p-5" style="background: linear-gradient(135deg, {{ $stat['color'] }} 0%, {{ $stat['color'] }}dd 100%);">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 opacity-10">
                    @if($stat['icon'] === 'users')
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="white">
                            <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                        </svg>
                    @elseif($stat['icon'] === 'user-check')
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="white">
                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm-1.5 9l1.5-5.5L10 14c-2-1-5-2-5-4V8c0-1.1.9-2 2-2h6c1.1 0 2 .9 2 2v2c0 2-3 3-5 4l-2 1.5L9.5 21h1z"/>
                        </svg>
                    @elseif($stat['icon'] === 'academic-cap')
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="white">
                            <path d="M5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82zM12 3L1 9l11 6 9-4.91V17h2V9L12 3z"/>
                        </svg>
                    @elseif($stat['icon'] === 'building-office')
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="white">
                            <path d="M12 7V3H2v18h20V7H12zM6 19H4v-2h2v2zm0-4H4v-2h2v2zm0-4H4V9h2v2zm0-4H4V5h2v2zm4 12H8v-2h2v2zm0-4H8v-2h2v2zm0-4H8V9h2v2zm0-4H8V5h2v2zm10 12h-8v-2h2v-2h-2v-2h2v-2h-2V9h8v10zm-2-8h-2v2h2v-2zm0 4h-2v2h2v-2z"/>
                        </svg>
                    @endif
                </div>
                <div class="relative z-10">
                    <p class="text-white/80 text-sm font-medium">{{ $stat['label'] }}</p>
                    <h3 class="text-white text-3xl font-bold mt-1">{{ number_format($stat['value']) }}</h3>
                </div>
            </div>
        @endforeach
    </div>
</x-filament-widgets::widget>
