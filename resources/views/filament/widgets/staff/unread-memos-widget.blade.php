<x-filament-widgets::widget>
    <div class="rounded-2xl p-6 transition-all duration-300 hover:shadow-md"
         style="background-color: #FFF9C4;">
        <h3 class="text-gray-900 text-sm font-normal mb-3">Memos</h3>
        @if($latestMemos->count() > 0)
            <div class="space-y-2">
                @foreach($latestMemos->take(2) as $memo)
                    <p class="text-gray-700 text-xs font-normal truncate">{{ $memo['subject'] }}</p>
                @endforeach
            </div>
        @else
            <p class="text-gray-700 text-sm font-normal flex items-center gap-1">
                <svg class="w-3.5 h-3.5 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                All caught up!
            </p>
        @endif
    </div>
</x-filament-widgets::widget>
