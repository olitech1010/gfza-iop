@php
    $topDrivers = $this->getTopDrivers()->take(5);
    $flaggedDrivers = $this->getFlaggedDrivers();
@endphp

<x-filament-widgets::widget>
    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm overflow-hidden">
        <div style="padding: 20px 24px 0;">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                    <x-heroicon-o-trophy class="w-4 h-4 text-amber-500" />
                    Driver Leaderboard
                </h3>
                <a href="{{ url('/admin/driver-trip-reviews') }}" class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition-colors">
                    View all reviews →
                </a>
            </div>
        </div>

        @if($topDrivers->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-5 py-2.5 text-left font-medium text-gray-500 dark:text-gray-400 text-xs">#</th>
                            <th class="px-5 py-2.5 text-left font-medium text-gray-500 dark:text-gray-400 text-xs">Driver</th>
                            <th class="px-5 py-2.5 text-left font-medium text-gray-500 dark:text-gray-400 text-xs">Rating</th>
                            <th class="px-5 py-2.5 text-left font-medium text-gray-500 dark:text-gray-400 text-xs">Reviews</th>
                            <th class="px-5 py-2.5 text-left font-medium text-gray-500 dark:text-gray-400 text-xs">Manual</th>
                            <th class="px-5 py-2.5 text-left font-medium text-gray-500 dark:text-gray-400 text-xs">Auto</th>
                            <th class="px-5 py-2.5 text-left font-medium text-gray-500 dark:text-gray-400 text-xs">Status</th>
                            <th class="px-5 py-2.5 text-left font-medium text-gray-500 dark:text-gray-400 text-xs"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($topDrivers as $index => $driver)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                <td class="px-5 py-3">
                                    @if($index === 0)
                                        <span class="text-amber-500 font-bold">🥇</span>
                                    @elseif($index === 1)
                                        <span class="text-gray-400 font-bold">🥈</span>
                                    @elseif($index === 2)
                                        <span class="text-amber-700 font-bold">🥉</span>
                                    @else
                                        <span class="text-xs text-gray-400">{{ $index + 1 }}</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 font-medium text-gray-900 dark:text-white text-xs">{{ $driver['name'] }}</td>
                                <td class="px-5 py-3">
                                    <span class="text-amber-500 text-xs">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= round($driver['avg_rating']))★@else☆@endif
                                        @endfor
                                    </span>
                                    <span class="text-xs font-medium text-gray-700 dark:text-gray-300 ml-1">{{ number_format($driver['avg_rating'], 1) }}</span>
                                </td>
                                <td class="px-5 py-3 text-xs text-gray-500 dark:text-gray-400">{{ $driver['total_reviews'] }}</td>
                                <td class="px-5 py-3 text-xs text-gray-500 dark:text-gray-400">{{ $driver['manual_rating'] ? number_format($driver['manual_rating'], 1) : '—' }}</td>
                                <td class="px-5 py-3 text-xs text-gray-500 dark:text-gray-400">{{ $driver['automatic_rating'] ? number_format($driver['automatic_rating'], 1) : '—' }}</td>
                                <td class="px-5 py-3">
                                    @php
                                        $statusConfig = match($driver['performance_status']) {
                                            'excellent' => ['label' => 'Excellent', 'class' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400'],
                                            'good' => ['label' => 'Good', 'class' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400'],
                                            'average' => ['label' => 'Average', 'class' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/20 dark:text-amber-400'],
                                            'needs_attention' => ['label' => 'Attention', 'class' => 'bg-red-100 text-red-700 dark:bg-red-900/20 dark:text-red-400'],
                                            default => ['label' => '—', 'class' => 'bg-gray-100 text-gray-600'],
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{ $statusConfig['class'] }}">
                                        {{ $statusConfig['label'] }}
                                    </span>
                                </td>
                                <td class="px-5 py-3">
                                    <a href="{{ url('/admin/driver-performance/' . $driver['id']) }}"
                                       class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition-colors">
                                        Profile →
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="py-10 text-center" style="padding-left: 24px; padding-right: 24px;">
                <x-heroicon-o-trophy class="mx-auto h-8 w-8 text-gray-200 dark:text-gray-700 mb-2" />
                <p class="text-sm text-gray-400 dark:text-gray-500">No driver reviews yet. Submit reviews after completed trips to see rankings.</p>
            </div>
        @endif

        {{-- Flagged Drivers Section --}}
        @if($flaggedDrivers->isNotEmpty())
            <div class="border-t border-gray-200 dark:border-gray-700" style="padding: 16px 24px;">
                <h4 class="text-xs font-semibold text-red-600 dark:text-red-400 mb-3 flex items-center gap-1.5">
                    <x-heroicon-o-exclamation-triangle class="w-3.5 h-3.5" />
                    Drivers Needing Attention
                </h4>
                <div class="flex flex-wrap gap-2">
                    @foreach($flaggedDrivers as $driver)
                        <a href="{{ url('/admin/driver-performance/' . $driver['id']) }}"
                           class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/10 text-xs hover:bg-red-100 dark:hover:bg-red-900/20 transition-colors">
                            <span class="font-medium text-red-800 dark:text-red-400">{{ $driver['name'] }}</span>
                            <span class="text-red-500">★ {{ number_format($driver['avg_rating'], 1) }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-filament-widgets::widget>
