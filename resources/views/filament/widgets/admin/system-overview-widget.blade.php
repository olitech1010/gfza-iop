<x-filament-widgets::widget>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
        @foreach($stats as $stat)
            <div class="relative overflow-hidden rounded-xl p-4" style="background: linear-gradient(135deg, {{ $stat['color'] }} 0%, {{ $stat['color'] }}dd 100%);">
                <div class="absolute top-0 right-0 -mt-3 -mr-3 w-16 h-16 opacity-10">
                    @if($stat['icon'] === 'users')
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="white">
                            <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                        </svg>
                    @elseif($stat['icon'] === 'building')
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="white">
                            <path d="M12 7V3H2v18h20V7H12zM6 19H4v-2h2v2zm0-4H4v-2h2v2zm0-4H4V9h2v2zm0-4H4V5h2v2zm4 12H8v-2h2v2zm0-4H8v-2h2v2zm0-4H8V9h2v2zm0-4H8V5h2v2zm10 12h-8v-2h2v-2h-2v-2h2v-2h-2V9h8v10zm-2-8h-2v2h2v-2zm0 4h-2v2h2v-2z"/>
                        </svg>
                    @elseif($stat['icon'] === 'ticket')
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="white">
                            <path d="M22 10V6c0-1.1-.9-2-2-2H4c-1.1 0-1.99.9-1.99 2v4c1.1 0 1.99.9 1.99 2s-.89 2-2 2v4c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2v-4c-1.1 0-2-.9-2-2s.9-2 2-2z"/>
                        </svg>
                    @elseif($stat['icon'] === 'computer')
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="white">
                            <path d="M20 18c1.1 0 1.99-.9 1.99-2L22 6c0-1.1-.9-2-2-2H4c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2H0v2h24v-2h-4zM4 6h16v10H4V6z"/>
                        </svg>
                    @elseif($stat['icon'] === 'calendar')
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="white">
                            <path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V9h14v11zM9 11H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2z"/>
                        </svg>
                    @endif
                </div>
                <div class="relative z-10">
                    <p class="text-white/80 text-xs font-medium">{{ $stat['label'] }}</p>
                    <h3 class="text-white text-2xl font-bold mt-1">{{ number_format($stat['value']) }}</h3>
                </div>
            </div>
        @endforeach
    </div>
</x-filament-widgets::widget>
