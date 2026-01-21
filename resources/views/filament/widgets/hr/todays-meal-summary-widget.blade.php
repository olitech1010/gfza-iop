<x-filament-widgets::widget>
    <div class="relative overflow-hidden rounded-xl p-5" style="background: linear-gradient(135deg, #FF6B6B 0%, #ee5a5a 100%);">
        <div class="absolute top-0 right-0 -mt-2 -mr-2 w-24 h-24 opacity-10">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="white">
                <path d="M11 9H9V2H7v7H5V2H3v7c0 2.12 1.66 3.84 3.75 3.97V22h2.5v-9.03C11.34 12.84 13 11.12 13 9V2h-2v7zm5-3v8h2.5v8H21V2c-2.76 0-5 2.24-5 4z"/>
            </svg>
        </div>
        
        <div class="relative z-10">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <span class="text-white/80 text-sm font-medium">Today's Meals</span>
                </div>
                <a href="{{ $summaryUrl }}" class="text-white/80 text-xs hover:text-white">View All →</a>
            </div>
            
            <div class="grid grid-cols-3 gap-3 mb-4">
                <div class="bg-white/10 rounded-lg p-2 text-center">
                    <p class="text-white text-xl font-bold">{{ $totalOrders }}</p>
                    <p class="text-white/70 text-xs">Total</p>
                </div>
                <div class="bg-white/10 rounded-lg p-2 text-center">
                    <p class="text-white text-xl font-bold">{{ $servedCount }}</p>
                    <p class="text-white/70 text-xs">Served</p>
                </div>
                <div class="bg-white/10 rounded-lg p-2 text-center">
                    <p class="text-white text-xl font-bold">{{ $pendingCount }}</p>
                    <p class="text-white/70 text-xs">Pending</p>
                </div>
            </div>
            
            @if($mealBreakdown->count() > 0)
                <div class="space-y-1">
                    @foreach($mealBreakdown as $meal => $count)
                        <div class="flex items-center justify-between bg-white/10 rounded px-2 py-1">
                            <span class="text-white text-xs truncate">{{ $meal }}</span>
                            <span class="text-white font-semibold text-xs">{{ $count }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-filament-widgets::widget>
