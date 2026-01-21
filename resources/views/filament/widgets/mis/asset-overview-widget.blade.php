<x-filament-widgets::widget>
    <div class="relative overflow-hidden rounded-xl p-5" style="background: linear-gradient(135deg, #3B82F6 0%, #2563eb 100%);">
        <div class="absolute top-0 right-0 -mt-2 -mr-2 w-24 h-24 opacity-10">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="white">
                <path d="M20 18c1.1 0 1.99-.9 1.99-2L22 6c0-1.1-.9-2-2-2H4c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2H0v2h24v-2h-4zM4 6h16v10H4V6z"/>
            </svg>
        </div>
        
        <div class="relative z-10">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <span class="text-white/80 text-sm font-medium">Asset Inventory</span>
                </div>
                <a href="{{ $viewAllUrl }}" class="text-white/80 text-xs hover:text-white">View All →</a>
            </div>
            
            <div class="grid grid-cols-2 gap-3">
                <div class="bg-white/10 rounded-lg p-3 text-center">
                    <p class="text-white text-2xl font-bold">{{ $totalAssets }}</p>
                    <p class="text-white/70 text-xs">Total Assets</p>
                </div>
                <div class="bg-white/10 rounded-lg p-3 text-center">
                    <p class="text-white text-2xl font-bold">{{ $assignedAssets }}</p>
                    <p class="text-white/70 text-xs">Assigned</p>
                </div>
                <div class="bg-white/10 rounded-lg p-3 text-center">
                    <p class="text-white text-2xl font-bold">{{ $availableAssets }}</p>
                    <p class="text-white/70 text-xs">Available</p>
                </div>
                <div class="bg-white/10 rounded-lg p-3 text-center">
                    <p class="text-white text-2xl font-bold">{{ $underMaintenance }}</p>
                    <p class="text-white/70 text-xs">Maintenance</p>
                </div>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
