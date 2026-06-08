@php
    $stats = $this->getSummaryStats();
    $transmission = $this->getTransmissionCompetency();
    $adminBreakdown = $this->getAdminBreakdown();
    $passengerBreakdown = $this->getPassengerBreakdown();
    $incidents = $this->getIncidents();
    $recentReviews = $this->getRecentReviews();
    $driver = $this->driver;

    $adminLabels = [
        'vehicle_condition' => 'Vehicle Condition',
        'cleanliness' => 'Cleanliness',
        'fuel_efficiency' => 'Fuel Efficiency',
        'timeliness' => 'Timeliness',
        'rule_compliance' => 'Rule Compliance',
    ];
    $passengerLabels = [
        'punctuality' => 'Punctuality',
        'driving_quality' => 'Driving Quality',
        'professionalism' => 'Professionalism',
        'safety_feeling' => 'Safety',
        'overall_satisfaction' => 'Overall Satisfaction',
    ];
@endphp

<x-filament-panels::page>

    {{-- ROW 1: Driver Header --}}
    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm" style="padding: 24px 28px;">
        <div class="flex flex-col md:flex-row items-start gap-6">
            {{-- Avatar & Big Rating --}}
            <div class="flex flex-col items-center text-center" style="min-width: 120px;">
                <div class="flex h-20 w-20 items-center justify-center rounded-full bg-gradient-to-br from-emerald-400 to-emerald-600 text-3xl font-bold text-white shadow-md mb-3">
                    {{ strtoupper(substr($driver->name, 0, 1)) }}
                </div>
                @if($stats['avgRating'])
                    <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['avgRating'], 1) }}</div>
                    <div class="text-amber-500 text-lg tracking-wider">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= round($stats['avgRating']))★@else☆@endif
                        @endfor
                    </div>
                    <div class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ $stats['totalReviews'] }} reviews</div>
                @else
                    <div class="text-sm text-gray-400 dark:text-gray-500 mt-2">No reviews yet</div>
                @endif
            </div>

            {{-- Driver Info --}}
            <div class="flex-1">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">{{ $driver->name }}</h2>
                <div class="flex flex-wrap gap-3 mb-4">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium
                        {{ $driver->status === 'active' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-400' : ($driver->status === 'on_leave' ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/20 dark:text-amber-400' : 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400') }}">
                        {{ ucfirst(str_replace('_', ' ', $driver->status)) }}
                    </span>
                    @php
                        $perfStatus = $driver->performance_status;
                        $perfConfig = match($perfStatus) {
                            'excellent' => ['label' => 'Excellent', 'class' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-400'],
                            'good' => ['label' => 'Good', 'class' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400'],
                            'average' => ['label' => 'Average', 'class' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/20 dark:text-amber-400'],
                            'needs_attention' => ['label' => 'Needs Attention', 'class' => 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400'],
                            default => ['label' => 'No Data', 'class' => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400'],
                        };
                    @endphp
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium {{ $perfConfig['class'] }}">
                        Performance: {{ $perfConfig['label'] }}
                    </span>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">License</div>
                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $driver->license_number ?? '—' }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Total Trips</div>
                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $stats['totalTrips'] }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Reviews</div>
                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $stats['adminReviews'] }} admin · {{ $stats['passengerReviews'] }} passenger</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Since</div>
                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $stats['since'] ?? '—' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ROW 2: Transmission Competency --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
        @foreach(['manual' => 'Manual Vehicles', 'automatic' => 'Automatic Vehicles'] as $type => $label)
            @php $t = $transmission[$type]; @endphp
            <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm" style="padding: 24px;">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">{{ $label }}</h3>
                    <x-heroicon-o-cog-6-tooth class="w-5 h-5 text-gray-300 dark:text-gray-600" />
                </div>
                @if($t['avg'])
                    <div class="text-3xl font-bold text-gray-900 dark:text-white mb-1">{{ $t['avg'] }}<span class="text-lg text-gray-400 dark:text-gray-500"> / 5.0</span></div>
                    <div class="text-amber-500 text-base mb-3">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= round($t['avg']))★@else☆@endif
                        @endfor
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 mb-3">
                        <div class="h-2 rounded-full transition-all
                            {{ $t['avg'] >= 4.0 ? 'bg-emerald-500' : ($t['avg'] >= 3.0 ? 'bg-amber-500' : 'bg-red-500') }}"
                             style="width: {{ ($t['avg'] / 5) * 100 }}%"></div>
                    </div>
                    <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                        <span>{{ $t['count'] }} trips reviewed</span>
                        @if($t['recommendation'])
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium
                                {{ $t['recommendation'] === 'recommended' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400' :
                                   ($t['recommendation'] === 'needs_training' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/20 dark:text-amber-400' :
                                   'bg-red-100 text-red-700 dark:bg-red-900/20 dark:text-red-400') }}">
                                {{ ucfirst(str_replace('_', ' ', $t['recommendation'])) }}
                            </span>
                        @endif
                    </div>
                @else
                    <div class="py-8 text-center text-gray-400 dark:text-gray-500">
                        <x-heroicon-o-clipboard-document class="mx-auto h-8 w-8 mb-2 opacity-50" />
                        <p class="text-sm">No {{ $type }} reviews yet</p>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    {{-- ROW 3: Rating Category Breakdown --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
        {{-- Admin Ratings --}}
        <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm" style="padding: 24px;">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-5">Admin Ratings</h3>
            <div class="space-y-4">
                @foreach($adminBreakdown as $field => $avg)
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-xs font-medium text-gray-600 dark:text-gray-400">{{ $adminLabels[$field] }}</span>
                            <span class="text-xs font-bold text-gray-900 dark:text-white">{{ $avg ? number_format($avg, 1) : '—' }}</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            @if($avg)
                                <div class="h-2 rounded-full transition-all
                                    {{ $avg >= 4.0 ? 'bg-emerald-500' : ($avg >= 3.0 ? 'bg-amber-500' : 'bg-red-500') }}"
                                     style="width: {{ ($avg / 5) * 100 }}%"></div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Passenger Ratings --}}
        <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm" style="padding: 24px;">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-5">Passenger Ratings</h3>
            <div class="space-y-4">
                @foreach($passengerBreakdown as $field => $avg)
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-xs font-medium text-gray-600 dark:text-gray-400">{{ $passengerLabels[$field] }}</span>
                            <span class="text-xs font-bold text-gray-900 dark:text-white">{{ $avg ? number_format($avg, 1) : '—' }}</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            @if($avg)
                                <div class="h-2 rounded-full transition-all
                                    {{ $avg >= 4.0 ? 'bg-emerald-500' : ($avg >= 3.0 ? 'bg-amber-500' : 'bg-red-500') }}"
                                     style="width: {{ ($avg / 5) * 100 }}%"></div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ROW 4: Incident History --}}
    @if($incidents->isNotEmpty())
        <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm mt-6" style="padding: 24px;">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Incident History</h3>
            <div class="space-y-3">
                @foreach($incidents as $incident)
                    <div class="flex items-start gap-3 px-4 py-3 rounded-lg bg-gray-50 dark:bg-gray-900/30">
                        @php
                            $sevColor = match($incident->damage_severity) {
                                'severe' => 'text-red-500',
                                'moderate' => 'text-orange-500',
                                'minor' => 'text-amber-500',
                                default => 'text-gray-400',
                            };
                        @endphp
                        <span class="{{ $sevColor }} mt-0.5">
                            @if($incident->damage_severity === 'severe') 🔴
                            @elseif($incident->damage_severity === 'moderate') 🟠
                            @elseif($incident->damage_severity === 'minor') 🟡
                            @else ⚪ @endif
                        </span>
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-xs font-medium text-gray-900 dark:text-white">{{ $incident->review_date->format('d M Y') }}</span>
                                <span class="text-[10px] px-2 py-0.5 rounded-full font-medium
                                    {{ match($incident->damage_severity) {
                                        'severe' => 'bg-red-100 text-red-700 dark:bg-red-900/20 dark:text-red-400',
                                        'moderate' => 'bg-orange-100 text-orange-700 dark:bg-orange-900/20 dark:text-orange-400',
                                        'minor' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/20 dark:text-amber-400',
                                        default => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400',
                                    } }}">
                                    {{ ucfirst($incident->damage_severity ?? 'none') }} damage
                                </span>
                                <span class="text-[10px] text-gray-400">{{ $incident->vehicle->registration_number ?? '' }}</span>
                            </div>
                            @if($incident->damage_notes)
                                <p class="text-xs text-gray-600 dark:text-gray-400">{{ $incident->damage_notes }}</p>
                            @endif
                            @if($incident->incidents)
                                <p class="text-xs text-red-600 dark:text-red-400 mt-1">{{ $incident->incidents }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="text-xs text-gray-400 dark:text-gray-500 mt-3">
                Total incidents recorded: {{ $incidents->count() }}
            </div>
        </div>
    @endif

    {{-- ROW 5: Recent Reviews Table --}}
    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm mt-6 overflow-hidden">
        <div style="padding: 20px 24px 0;">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Recent Reviews</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-5 py-3 text-left font-medium text-gray-600 dark:text-gray-300 text-xs">Date</th>
                        <th class="px-5 py-3 text-left font-medium text-gray-600 dark:text-gray-300 text-xs">Type</th>
                        <th class="px-5 py-3 text-left font-medium text-gray-600 dark:text-gray-300 text-xs">Vehicle</th>
                        <th class="px-5 py-3 text-left font-medium text-gray-600 dark:text-gray-300 text-xs">Trans.</th>
                        <th class="px-5 py-3 text-left font-medium text-gray-600 dark:text-gray-300 text-xs">Rating</th>
                        <th class="px-5 py-3 text-left font-medium text-gray-600 dark:text-gray-300 text-xs">Rec.</th>
                        <th class="px-5 py-3 text-left font-medium text-gray-600 dark:text-gray-300 text-xs">Reviewer</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($recentReviews as $review)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                            <td class="px-5 py-3 text-gray-700 dark:text-gray-300 text-xs">{{ $review->review_date->format('d M Y') }}</td>
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium
                                    {{ $review->review_type === 'admin' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400' : 'bg-green-100 text-green-700 dark:bg-green-900/20 dark:text-green-400' }}">
                                    {{ ucfirst($review->review_type) }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-gray-700 dark:text-gray-300 text-xs">{{ $review->vehicle->registration_number ?? '—' }}</td>
                            <td class="px-5 py-3 text-gray-500 dark:text-gray-400 text-xs">{{ $review->transmission_used === 'manual' ? 'M' : 'A' }}</td>
                            <td class="px-5 py-3">
                                <span class="text-amber-500 text-xs">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= round($review->overall_rating))★@else☆@endif
                                    @endfor
                                </span>
                                <span class="text-xs font-medium text-gray-700 dark:text-gray-300 ml-1">{{ number_format($review->overall_rating, 1) }}</span>
                            </td>
                            <td class="px-5 py-3">
                                @if($review->recommendation)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium
                                        {{ match($review->recommendation) {
                                            'recommended' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400',
                                            'needs_training' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/20 dark:text-amber-400',
                                            'restricted' => 'bg-red-100 text-red-700 dark:bg-red-900/20 dark:text-red-400',
                                            'not_recommended' => 'bg-red-100 text-red-700 dark:bg-red-900/20 dark:text-red-400',
                                            default => 'bg-gray-100 text-gray-600',
                                        } }}">
                                        {{ ucfirst(str_replace('_', ' ', $review->recommendation)) }}
                                    </span>
                                @else
                                    <span class="text-gray-400 text-xs">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-gray-500 dark:text-gray-400 text-xs">{{ $review->reviewer->name ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-10 text-center text-gray-400 dark:text-gray-500">
                                <x-heroicon-o-star class="mx-auto h-8 w-8 mb-2 opacity-50" />
                                No reviews yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</x-filament-panels::page>
