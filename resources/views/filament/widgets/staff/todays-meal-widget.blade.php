<x-filament-widgets::widget>
    <div class="rounded-2xl p-6 transition-all duration-300 hover:shadow-md"
         style="background-color: #2196F3;">
        <h3 class="text-white text-sm font-normal mb-3">Today's Meal</h3>
        @if($hasSelectedMeal)
            <p class="text-white text-base font-normal mb-2 truncate">{{ $mealName }}</p>
            @if($isServed)
                <span class="inline-flex items-center px-3 py-1 rounded-md text-xs font-normal"
                      style="background-color: rgba(255, 255, 255, 0.25); color: white;">
                    ✓ Served
                </span>
            @else
                <span class="inline-flex items-center px-3 py-1 rounded-md text-xs font-normal"
                      style="background-color: rgba(255, 255, 255, 0.25); color: white;">
                    Pending Pickup
                </span>
            @endif
        @else
            <p class="text-white/90 text-sm font-normal mb-3">No meal selected</p>
            <a href="{{ $selectUrl }}" 
               class="inline-flex items-center gap-1 px-3 py-1.5 rounded-md text-xs font-normal transition-all duration-200"
               style="background-color: rgba(255, 255, 255, 0.25); color: white;">
                + Select Meal
            </a>
        @endif
    </div>
</x-filament-widgets::widget>
