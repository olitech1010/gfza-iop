<x-filament-widgets::widget>
    <div class="flex gap-4">
        {{-- Department & Team Size --}}
        <div class="flex-1 rounded-xl p-4 text-center transition-all duration-200 hover:shadow-md" 
             style="background-color: #E6F4EA;">
            <p class="text-gray-600 text-xs font-normal mb-1">{{ $departmentName }}</p>
            <h3 class="text-xl font-semibold" style="color: #00c73f;">{{ $teamCount }}</h3>
            <p class="text-gray-500 text-xs">Team Members</p>
        </div>
        
        {{-- Pending Leave Approvals --}}
        <div class="flex-1 rounded-xl p-4 text-center transition-all duration-200 hover:shadow-md" 
             style="background-color: #FCE8EC;">
            <p class="text-gray-600 text-xs font-normal mb-1">Pending Approvals</p>
            <h3 class="text-xl font-semibold" style="color: #ea4335;">{{ $pendingLeaves }}</h3>
            <p class="text-gray-500 text-xs">ED Requests</p>
        </div>
        
        {{-- Open Tickets --}}
        <div class="flex-1 rounded-xl p-4 text-center transition-all duration-200 hover:shadow-md" 
             style="background-color: #EDE7F6;">
            <p class="text-gray-600 text-xs font-normal mb-1">Department</p>
            <h3 class="text-xl font-semibold" style="color: #7c3aed;">{{ $openTickets }}</h3>
            <p class="text-gray-500 text-xs">Active Tickets</p>
        </div>
    </div>
</x-filament-widgets::widget>
