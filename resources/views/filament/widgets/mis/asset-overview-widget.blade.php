<x-filament-widgets::widget>
    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-100 dark:border-gray-700">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-gray-900 dark:text-white text-sm font-normal">Asset Inventory</h3>
            <a href="{{ $viewAllUrl }}" class="text-xs text-blue-600 hover:text-blue-800">View All →</a>
        </div>
        
        <div class="flex gap-2">
            <div class="flex-1 text-center py-2 px-1 rounded-lg" style="background-color: #E8F0FE;">
                <p class="text-lg font-semibold" style="color: #1a73e8;">{{ $totalAssets }}</p>
                <p class="text-gray-600 text-[10px]">Total</p>
            </div>
            <div class="flex-1 text-center py-2 px-1 rounded-lg" style="background-color: #E6F4EA;">
                <p class="text-lg font-semibold" style="color: #34a853;">{{ $assignedAssets }}</p>
                <p class="text-gray-600 text-[10px]">Assigned</p>
            </div>
            <div class="flex-1 text-center py-2 px-1 rounded-lg" style="background-color: #FFF4E5;">
                <p class="text-lg font-semibold" style="color: #e65100;">{{ $availableAssets }}</p>
                <p class="text-gray-600 text-[10px]">Available</p>
            </div>
            <div class="flex-1 text-center py-2 px-1 rounded-lg" style="background-color: #FDECEA;">
                <p class="text-lg font-semibold" style="color: #ea4335;">{{ $underMaintenance }}</p>
                <p class="text-gray-600 text-[10px]">Repair</p>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>

