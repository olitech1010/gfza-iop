@php
    use SimpleSoftwareIO\QrCode\Facades\QrCode;
    
    $qrCode = '';
    if ($user->qr_token) {
        $qrCode = base64_encode(QrCode::format('svg')->size(120)->generate($user->qr_token));
    }
    
    $photoUrl = $user->photo ? asset('storage/' . $user->photo) : null;
    $logoUrl = asset('images/logo.png');
@endphp

<div class="flex justify-center p-4">
    <div class="relative w-full max-w-sm bg-gradient-to-br from-emerald-600 to-teal-700 rounded-2xl shadow-2xl overflow-hidden">
        {{-- Card Front --}}
        <div class="p-5">
            {{-- Header --}}
            <div class="flex items-center gap-3 mb-4 border-b border-white/20 pb-3">
                <img src="{{ $logoUrl }}" alt="GFZA Logo" class="h-10 w-10 rounded-lg bg-white p-1" onerror="this.style.display='none'">
                <div>
                    <h3 class="text-white font-bold text-sm leading-tight">GHANA FREE ZONES</h3>
                    <p class="text-emerald-200 text-xs">AUTHORITY</p>
                </div>
            </div>
            
            {{-- Badge --}}
            <div class="bg-white/15 rounded-lg px-3 py-1.5 inline-block mb-4">
                <span class="text-white font-semibold text-xs tracking-wider">NSS PERSONNEL</span>
            </div>
            
            {{-- Main Content --}}
            <div class="flex gap-4">
                {{-- Photo --}}
                <div class="flex-shrink-0">
                    @if($photoUrl)
                        <img src="{{ $photoUrl }}" alt="Photo" class="w-20 h-24 rounded-lg object-cover border-2 border-white/30">
                    @else
                        <div class="w-20 h-24 rounded-lg bg-white/20 border-2 border-white/30 flex items-center justify-center">
                            <svg class="w-10 h-10 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                    @endif
                </div>
                
                {{-- User Info --}}
                <div class="flex-1 min-w-0">
                    <h2 class="text-white font-bold text-lg leading-tight truncate">
                        {{ strtoupper($user->first_name . ' ' . $user->last_name) }}
                    </h2>
                    <div class="mt-2 space-y-1">
                        <p class="text-emerald-100 text-xs flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            {{ $user->department?->name ?? 'N/A' }}
                        </p>
                        <p class="text-emerald-100 text-xs flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            {{ $user->job_title ?? 'NSS Personnel' }}
                        </p>
                    </div>
                </div>
            </div>
            
            {{-- Bottom Section --}}
            <div class="flex items-end justify-between mt-4 pt-3 border-t border-white/20">
                {{-- Staff ID Badge --}}
                <div class="bg-amber-400 text-emerald-900 px-3 py-1.5 rounded-lg">
                    <span class="font-bold text-sm">{{ $user->staff_id }}</span>
                </div>
                
                {{-- QR Code --}}
                @if($qrCode)
                    <div class="bg-white p-2 rounded-lg">
                        <img src="data:image/svg+xml;base64,{{ $qrCode }}" alt="QR Code" class="w-14 h-14">
                    </div>
                @endif
            </div>
        </div>
        
        {{-- Footer Bar --}}
        <div class="bg-amber-400 h-2"></div>
    </div>
</div>

<p class="text-center text-sm text-gray-500 mt-3">
    Valid: {{ now()->format('Y') }}
</p>
