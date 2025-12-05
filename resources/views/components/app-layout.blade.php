<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'GFZA IOP') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
        <!-- Material Icons -->
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">

        <!-- Scripts -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            primary: {
                                DEFAULT: '#00c73f',
                                hover: '#00b033',
                                active: '#009929',
                                disabled: '#cccccc'
                            },
                            neutral: {
                                bg: '#f5f5f5',
                                surface: '#ffffff',
                                divider: '#e0e0e0'
                            },
                            text: {
                                dark: '#1a1a1a',
                                secondary: '#666666',
                                light: '#999999'
                            },
                            semantic: {
                                success: '#4caf50',
                                warning: '#ff9800',
                                error: '#f44336',
                                info: '#2196f3'
                            }
                        },
                        fontFamily: {
                            sans: ['Poppins', 'sans-serif'],
                        }
                    }
                }
            }
        </script>
        
        <style>
            .sidebar-scroll::-webkit-scrollbar {
                width: 4px;
            }
            .sidebar-scroll::-webkit-scrollbar-thumb {
                background-color: #e0e0e0;
                border-radius: 4px;
            }
            .material-icons-round {
                font-size: 24px;
                /* Vertical align middle to centering with text */
                vertical-align: middle; 
            }
        </style>
    </head>
    <body class="font-sans antialiased text-text-dark bg-neutral-bg">
        <div class="min-h-screen flex">
            <!-- Sidebar -->
            <aside class="w-[280px] bg-white border-r border-neutral-divider fixed h-full z-50 flex flex-col hidden lg:flex">
                <!-- Logo -->
                <div class="h-16 flex items-center justify-center border-b border-neutral-divider px-4">
                    <span class="text-2xl font-bold text-primary tracking-tight">GFZA IOP</span>
                </div>

                <!-- Nav -->
                <nav class="flex-1 overflow-y-auto sidebar-scroll py-4 px-3 space-y-1">
                    
                    <!-- Group: Home -->
                    <div class="px-3 mb-2 mt-2">
                        <span class="text-xs font-semibold text-text-light uppercase tracking-wider">Home</span>
                    </div>
                    <a href="#" class="flex items-center px-4 py-3 text-sm font-medium text-text-secondary rounded-lg hover:bg-neutral-bg hover:text-text-dark transition-colors group">
                        <span class="material-icons-round mr-3 text-text-light group-hover:text-text-dark">dashboard</span>
                        Dashboard
                    </a>
                    <a href="#" class="flex items-center px-4 py-3 text-sm font-medium text-text-secondary rounded-lg hover:bg-neutral-bg hover:text-text-dark transition-colors group">
                        <span class="material-icons-round mr-3 text-text-light group-hover:text-text-dark">check_circle</span>
                        My Tasks
                    </a>
                    <a href="#" class="flex items-center px-4 py-3 text-sm font-medium text-text-secondary rounded-lg hover:bg-neutral-bg hover:text-text-dark transition-colors group">
                        <span class="material-icons-round mr-3 text-text-light group-hover:text-text-dark">notifications</span>
                        Notifications
                    </a>

                    <!-- Group: HR Operations -->
                    <div class="px-3 mb-2 mt-6">
                        <span class="text-xs font-semibold text-text-light uppercase tracking-wider">HR Operations</span>
                    </div>
                    <a href="#" class="flex items-center px-4 py-3 text-sm font-medium text-text-secondary rounded-lg hover:bg-neutral-bg hover:text-text-dark transition-colors group">
                        <span class="material-icons-round mr-3 text-text-light group-hover:text-text-dark">mail</span>
                        Memos
                    </a>
                     <a href="#" class="flex items-center px-4 py-3 text-sm font-medium text-text-secondary rounded-lg hover:bg-neutral-bg hover:text-text-dark transition-colors group">
                        <span class="material-icons-round mr-3 text-text-light group-hover:text-text-dark">restaurant</span>
                        Meal Planning
                    </a>
                    <a href="{{ route('employees.index') }}" class="flex items-center px-4 py-3 text-sm font-medium {{ request()->routeIs('employees.*') ? 'bg-primary text-white shadow-sm' : 'text-text-secondary hover:bg-neutral-bg hover:text-text-dark' }} rounded-lg transition-colors group">
                        <span class="material-icons-round mr-3 {{ request()->routeIs('employees.*') ? 'text-white' : 'text-text-light group-hover:text-text-dark' }}">people</span>
                        Employee Directory
                    </a>

                    <!-- Group: MIS Support -->
                    <div class="px-3 mb-2 mt-6">
                        <span class="text-xs font-semibold text-text-light uppercase tracking-wider">MIS Support</span>
                    </div>
                    <a href="#" class="flex items-center px-4 py-3 text-sm font-medium text-text-secondary rounded-lg hover:bg-neutral-bg hover:text-text-dark transition-colors group">
                        <span class="material-icons-round mr-3 text-text-light group-hover:text-text-dark">build_circle</span>
                        IT Tickets
                    </a>
                     <a href="#" class="flex items-center px-4 py-3 text-sm font-medium text-text-secondary rounded-lg hover:bg-neutral-bg hover:text-text-dark transition-colors group">
                        <span class="material-icons-round mr-3 text-text-light group-hover:text-text-dark">inventory_2</span>
                        Asset Management
                    </a>

                </nav>

                <!-- User Profile -->
                <div class="h-16 border-t border-neutral-divider bg-neutral-bg px-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="h-9 w-9 rounded-full bg-gray-300 flex items-center justify-center text-sm font-bold text-gray-600">
                             {{ substr(auth()->user()->name ?? 'Guest', 0, 1) }}
                        </div>
                        <div class="flex flex-col">
                            <span class="text-xs font-bold text-text-dark">{{ auth()->user()->name ?? 'Guest User' }}</span>
                            <span class="text-[10px] text-text-light">{{ auth()->user()->email ?? 'guest@gfza.com' }}</span>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Main Content Area -->
            <div class="flex-1 lg:ml-[280px]">
                <!-- Header (Top bar for mobile or Breadcrumbs for Desktop) -->
                @if (isset($header))
                    <header class="bg-white border-b border-neutral-divider h-16 flex items-center px-8 justify-between sticky top-0 z-40">
                         <!-- Mobile Menu Trigger (Visible only on mobile) -->
                         <button class="lg:hidden text-text-secondary">
                             <span class="material-icons-round">menu</span>
                         </button>

                        <div class="text-text-dark font-semibold text-lg">
                            {{ $header }}
                        </div>
                        
                        <!-- Quick Actions -->
                        <div class="flex items-center gap-4">
                            <!-- Helper buttons could go here -->
                        </div>
                    </header>
                @endif

                <!-- Content -->
                <main class="p-6 max-w-[1400px] mx-auto">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
