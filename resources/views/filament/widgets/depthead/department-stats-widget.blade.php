<x-filament-widgets::widget>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        {{-- Department Name & Team Size --}}
        <div class="relative overflow-hidden rounded-xl p-5" style="background: linear-gradient(135deg, #00c73f 0%, #00a835 100%);">
            <div class="absolute top-0 right-0 -mt-2 -mr-2 w-20 h-20 opacity-10">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="white">
                    <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                </svg>
            </div>
            <div class="relative z-10">
                <p class="text-white/80 text-sm font-medium">{{ $departmentName }}</p>
                <h3 class="text-white text-3xl font-bold mt-1">{{ $teamCount }}</h3>
                <p class="text-white/70 text-sm mt-1">Team Members</p>
            </div>
        </div>
        
        {{-- Pending Leave Approvals --}}
        <div class="relative overflow-hidden rounded-xl p-5" style="background: linear-gradient(135deg, #EC4899 0%, #db2777 100%);">
            <div class="absolute top-0 right-0 -mt-2 -mr-2 w-20 h-20 opacity-10">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="white">
                    <path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM9 10H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2zm-8 4H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2z"/>
                </svg>
            </div>
            <div class="relative z-10">
                <p class="text-white/80 text-sm font-medium">Pending Approvals</p>
                <h3 class="text-white text-3xl font-bold mt-1">{{ $pendingLeaves }}</h3>
                <p class="text-white/70 text-sm mt-1">Leave Requests</p>
                @if($pendingLeaves > 0)
                    <a href="/admin/leave-requests" class="mt-2 inline-block text-white text-xs font-medium hover:underline">
                        Review Now →
                    </a>
                @endif
            </div>
        </div>
        
        {{-- Open Tickets --}}
        <div class="relative overflow-hidden rounded-xl p-5" style="background: linear-gradient(135deg, #8B5CF6 0%, #7c3aed 100%);">
            <div class="absolute top-0 right-0 -mt-2 -mr-2 w-20 h-20 opacity-10">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="white">
                    <path d="M22 10V6c0-1.1-.9-2-2-2H4c-1.1 0-1.99.9-1.99 2v4c1.1 0 1.99.9 1.99 2s-.89 2-2 2v4c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2v-4c-1.1 0-2-.9-2-2s.9-2 2-2z"/>
                </svg>
            </div>
            <div class="relative z-10">
                <p class="text-white/80 text-sm font-medium">Department</p>
                <h3 class="text-white text-3xl font-bold mt-1">{{ $openTickets }}</h3>
                <p class="text-white/70 text-sm mt-1">Active IT Tickets</p>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
