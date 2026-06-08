@php
    $pendingTrips = $this->getPendingTrips();
    $pendingCount = $pendingTrips->count();
@endphp

<x-filament-widgets::widget>
    @if($pendingCount > 0)
        <div class="rounded-xl border border-amber-200 dark:border-amber-700/50 bg-amber-50 dark:bg-amber-900/10 shadow-sm" style="padding: 20px 24px;">
            <div class="flex items-start gap-4">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-amber-100 dark:bg-amber-900/30">
                    <x-heroicon-o-star class="h-5 w-5 text-amber-600 dark:text-amber-400" />
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-semibold text-amber-900 dark:text-amber-200 mb-1">
                        You have {{ $pendingCount }} trip{{ $pendingCount > 1 ? 's' : '' }} awaiting your review
                    </h3>
                    <p class="text-xs text-amber-700 dark:text-amber-400 mb-4">
                        Please rate the driver for your recent trips. Your feedback helps management track driver performance.
                    </p>
                    <div class="space-y-2">
                        @foreach($pendingTrips as $trip)
                            <div class="flex items-center justify-between bg-white dark:bg-gray-800 rounded-lg px-4 py-2.5 border border-amber-100 dark:border-gray-700">
                                <div class="flex items-center gap-3">
                                    <span class="text-xs font-medium text-gray-900 dark:text-white">{{ $trip->reference_number }}</span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ $trip->destination }}</span>
                                    <span class="text-xs text-gray-400 dark:text-gray-500">·</span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ $trip->driver?->user?->name ?? 'Unknown driver' }}</span>
                                </div>
                                <a href="{{ url('/admin/driver-trip-reviews/create?' . http_build_query([
                                    'review_type' => 'passenger',
                                    'driver_id' => $trip->driver_id,
                                    'vehicle_id' => $trip->vehicle_id,
                                    'vehicle_requisition_id' => $trip->id,
                                ])) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-medium bg-amber-100 text-amber-800 hover:bg-amber-200 dark:bg-amber-900/30 dark:text-amber-400 dark:hover:bg-amber-900/50 transition-colors">
                                    <x-heroicon-o-star class="w-3.5 h-3.5" />
                                    Review
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif
</x-filament-widgets::widget>
