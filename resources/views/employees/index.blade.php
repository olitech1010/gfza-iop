<x-app-layout>
    <x-slot name="header">
        Employee Directory
    </x-slot>

    <!-- Stats Row (KPI Cards Placeholder or Summary) -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg border border-neutral-divider p-5 shadow-sm">
            <div class="flex justify-between items-start">
                <div class="text-primary text-2xl font-bold">{{ $employees->total() }}</div>
                <div class="text-text-light text-xs font-semibold uppercase tracking-wider">Total Staff</div>
            </div>
        </div>
        <!-- Add more KPIs if needed -->
    </div>

    <div class="bg-white border border-neutral-divider rounded-lg shadow-sm overflow-hidden">
        <div class="p-6">
             <!-- Search and Filter Bar -->
            <form method="GET" action="{{ route('employees.index') }}" class="flex flex-col md:flex-row gap-4 mb-6 items-end">
                <div class="flex-1 w-full">
                    <label class="block text-xs font-bold text-text-dark mb-2">SEARCH</label>
                    <input type="text" name="search" placeholder="Search by name or email..." value="{{ request('search') }}" 
                           class="w-full h-11 px-4 border border-neutral-divider rounded-lg focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all text-sm text-text-dark placeholder-text-light bg-white">
                </div>
                
                <div class="w-full md:w-64">
                    <label class="block text-xs font-bold text-text-dark mb-2">DEPARTMENT</label>
                    <div class="relative">
                        <select name="department_id" class="w-full h-11 px-4 border border-neutral-divider rounded-lg focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all text-sm text-text-dark bg-white appearance-none cursor-pointer">
                            <option value="">All Departments</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-text-light">
                            <span class="material-icons-round text-base">expand_more</span>
                        </div>
                    </div>
                </div>

                <div class="w-full md:w-auto">
                    <button type="submit" class="h-11 px-6 bg-primary text-white text-sm font-semibold rounded-lg hover:bg-primary-hover active:bg-primary-active transition-all w-full md:w-auto flex items-center justify-center shadow-sm gap-2">
                        <span class="material-icons-round text-lg">filter_list</span>
                        Apply Filters
                    </button>
                </div>
            </form>

            <div class="border-t border-neutral-divider my-6"></div>

            <!-- Employee Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach($employees as $employee)
                    <a href="{{ route('employees.show', $employee) }}" class="group block">
                        <div class="bg-white border border-neutral-divider rounded-lg p-5 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 h-full relative group-hover:border-primary">
                            
                            <!-- Header -->
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-shrink-0">
                                    @if($employee->profile_picture_url)
                                        <img class="h-12 w-12 rounded-full object-cover border border-neutral-divider" src="{{ $employee->profile_picture_url }}" alt="{{ $employee->name }}">
                                    @else
                                        <div class="h-12 w-12 rounded-full bg-neutral-bg flex items-center justify-center text-lg font-bold text-primary border border-neutral-divider">
                                            {{ substr($employee->name, 0, 1) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="bg-neutral-bg text-text-secondary text-[10px] font-bold px-2 py-1 rounded-full uppercase tracking-wide">
                                    {{ $employee->roles->first()->name ?? 'Staff' }}
                                </div>
                            </div>

                            <!-- Body -->
                            <div>
                                <h3 class="text-base font-bold text-text-dark mb-1 group-hover:text-primary transition-colors line-clamp-1" title="{{ $employee->name }}">{{ $employee->name }}</h3>
                                <p class="text-xs text-text-secondary mb-3 line-clamp-1">{{ $employee->department->name ?? 'Unassigned' }}</p>
                                
                                <div class="flex items-center gap-2 text-xs text-text-light">
                                    <span class="material-icons-round text-sm">email</span>
                                    <span class="truncate">{{ $employee->email }}</span>
                                </div>
                            </div>

                        </div>
                    </a>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $employees->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
