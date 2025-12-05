<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <a href="{{ route('employees.index') }}" class="text-text-light hover:text-primary transition-colors text-sm">Employee Directory</a>
            <span class="text-text-light text-sm">/</span>
            <span class="text-text-dark text-sm">Profile</span>
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto">
        <!-- Main Card -->
        <div class="bg-white rounded-lg border border-neutral-divider shadow-sm overflow-hidden flex flex-col md:flex-row">
            
            <!-- Left Panel: Profile Summary -->
            <div class="md:w-1/3 bg-neutral-bg border-r border-neutral-divider p-8 flex flex-col items-center text-center">
                <div class="mb-6 relative">
                     <div class="h-32 w-32 rounded-full bg-white flex items-center justify-center text-4xl font-bold text-primary border-4 border-white shadow-sm">
                        {{ substr($employee->name, 0, 1) }}
                    </div>
                </div>
                
                <h1 class="text-xl font-bold text-text-dark mb-1">{{ $employee->name }}</h1>
                <p class="text-sm text-text-secondary mb-4">{{ $employee->roles->pluck('name')->join(', ') }}</p>
                
                <div class="w-full">
                     <span class="inline-flex items-center justify-center px-3 py-1 bg-semantic-success/10 text-semantic-success text-xs font-bold rounded-full border border-semantic-success/20">
                        Active Employee
                     </span>
                </div>
            </div>

            <!-- Right Panel: Details -->
            <div class="md:w-2/3 p-8">
                <div class="mb-8">
                    <h2 class="text-lg font-bold text-text-dark mb-4 pb-2 border-b border-neutral-divider">Contact Information</h2>
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-text-light uppercase tracking-wider mb-1">Email Address</label>
                            <p class="text-sm font-medium text-text-dark flex items-center gap-2">
                                <span class="material-icons-round text-base text-text-secondary">email</span>
                                <a href="mailto:{{ $employee->email }}" class="hover:text-primary transition-colors">{{ $employee->email }}</a>
                            </p>
                        </div>
                        <div>
                             <label class="block text-xs font-bold text-text-light uppercase tracking-wider mb-1">Phone Number</label>
                            <p class="text-sm font-medium text-text-dark">{{ $employee->phone ?? 'Not Available' }}</p>
                        </div>
                    </div>
                </div>

                <div>
                    <h2 class="text-lg font-bold text-text-dark mb-4 pb-2 border-b border-neutral-divider">Organization</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-text-light uppercase tracking-wider mb-1">Department</label>
                            <p class="text-sm font-medium text-text-dark">{{ $employee->department->name ?? 'Unassigned' }}</p>
                        </div>
                        <div>
                             <label class="block text-xs font-bold text-text-light uppercase tracking-wider mb-1">Location / Floor</label>
                            <p class="text-sm font-medium text-text-dark">{{ $employee->floor ?? 'Not Assigned' }}</p>
                        </div>
                    </div>
                </div>
                
                <!-- Back Button -->
                <div class="mt-10 pt-6 border-t border-neutral-divider flex justify-end">
                     <a href="{{ route('employees.index') }}" class="px-5 py-2.5 border border-primary text-primary text-sm font-semibold rounded-lg hover:bg-primary/5 transition-colors">
                        &larr; Back to Directory
                    </a>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
