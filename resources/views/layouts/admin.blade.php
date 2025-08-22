<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - @yield('title')</title>
    <script src="//unpkg.com/alpinejs" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-sky-50 min-h-screen font-sans antialiased">

    <!-- Updated Alpine.js data to persist sidebar state in localStorage -->
    <div class="flex min-h-screen" x-data="{ 
        sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true',
        usersExpanded: false,
        schedulesExpanded: false,
        shiftsExpanded: false,
        
        toggleSidebar() {
            this.sidebarCollapsed = !this.sidebarCollapsed;
            localStorage.setItem('sidebarCollapsed', this.sidebarCollapsed);
        }
    }">
        <!-- Updated sidebar with sky theme and collapse functionality -->
        <aside :class="sidebarCollapsed ? 'w-16' : 'w-72'" class="bg-sky-600 border-r border-sky-500 transition-all duration-300 ease-in-out relative">
            <!-- Updated padding and layout for better collapsed state -->
            <div :class="sidebarCollapsed ? 'p-2' : 'p-6'">
                <!-- Added collapse toggle button -->
                <div class="flex items-center justify-between mb-8" :class="sidebarCollapsed ? 'mb-4' : 'mb-8'">
                    <div class="flex items-center space-x-3" x-show="!sidebarCollapsed" x-transition>
                        <div class="w-8 h-8 bg-white rounded-md flex items-center justify-center">
                            <svg class="w-4 h-4 text-sky-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2L2 19.777h20L12 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-lg font-semibold text-white">Admin Panel</h1>
                            <p class="text-xs text-sky-200 font-mono">v1.0.0</p>
                        </div>
                    </div>
                    
                    <!-- Repositioned toggle button for collapsed state -->
                    <button @click="toggleSidebar()" 
                            :class="sidebarCollapsed ? 'mx-auto' : ''"
                            class="p-2 rounded-md hover:bg-sky-500 text-white transition-colors duration-150">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  :d="sidebarCollapsed ? 'M9 5l7 7-7 7' : 'M15 19l-7-7 7-7'"></path>
                        </svg>
                    </button>
                </div>

                <!-- Completely restructured navigation with hierarchical collapsible menu system -->
                <nav class="space-y-1">
                    <!-- Dashboard -->
                    <a href="{{ route('admin.dashboard') }}"
                        :class="sidebarCollapsed ? 'justify-center px-2 py-3' : 'px-3 py-2'"
                        class="group flex items-center text-sm font-medium rounded-md transition-all duration-150 relative
                        {{ request()->routeIs('admin.dashboard') ? 'bg-sky-500 text-white border border-sky-400' : 'text-sky-100 hover:bg-sky-500 hover:text-white border border-transparent hover:border-sky-400' }}"
                        :title="sidebarCollapsed ? 'Dashboard' : ''">
                        <svg class="w-5 h-5 {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-sky-200 group-hover:text-white' }}" 
                             :class="sidebarCollapsed ? 'mr-0' : 'mr-3'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"></path>
                        </svg>
                        <span x-show="!sidebarCollapsed" x-transition>DASHBOARD</span>
                    </a>

                    <!-- Users Section -->
                    <div class="space-y-1">
                        <!-- Modified click behavior to navigate when collapsed, expand when not collapsed -->
                        <button @click="sidebarCollapsed ? window.location.href = '{{ route('admin.users.index') }}' : usersExpanded = !usersExpanded"
                                :class="sidebarCollapsed ? 'justify-center px-2 py-3' : 'px-3 py-2'"
                                class="group flex items-center w-full text-sm font-medium rounded-md transition-all duration-150 relative
                                {{ request()->routeIs('admin.users.*') ? 'bg-sky-500 text-white border border-sky-400' : 'text-sky-100 hover:bg-sky-500 hover:text-white border border-transparent hover:border-sky-400' }}"
                                :title="sidebarCollapsed ? 'Users' : ''">
                            <svg class="w-5 h-5 {{ request()->routeIs('admin.users.*') ? 'text-white' : 'text-sky-200 group-hover:text-white' }}" 
                                 :class="sidebarCollapsed ? 'mr-0' : 'mr-3'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                            <span x-show="!sidebarCollapsed" x-transition class="flex-1 text-left">USERS</span>
                            <svg x-show="!sidebarCollapsed" :class="usersExpanded ? 'rotate-90' : ''" 
                                 class="w-4 h-4 text-sky-200 group-hover:text-white transition-transform duration-150" 
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                        
                        <!-- Users Submenu -->
                        <div x-show="usersExpanded && !sidebarCollapsed" x-transition class="ml-6 space-y-1">
                            <a href="{{ route('admin.users.index') }}"
                               class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-150
                               {{ request()->routeIs('admin.users.index') ? 'bg-sky-500 text-white' : 'text-sky-200 hover:bg-sky-500 hover:text-white' }}">
                                <span class="text-xs">• Manage Users</span>
                            </a>
                            <a href="{{ route('admin.users.create') }}"
                               class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-150
                               {{ request()->routeIs('admin.users.create') ? 'bg-sky-500 text-white' : 'text-sky-200 hover:bg-sky-500 hover:text-white' }}">
                                <span class="text-xs">• Create Users</span>
                            </a>
                        </div>
                    </div>

                    <!-- Schedules Section -->
                    <div class="space-y-1">
                        <!-- Modified click behavior to navigate when collapsed, expand when not collapsed -->
                        <button @click="sidebarCollapsed ? window.location.href = '{{ route('admin.schedules.index') }}' : schedulesExpanded = !schedulesExpanded"
                                :class="sidebarCollapsed ? 'justify-center px-2 py-3' : 'px-3 py-2'"
                                class="group flex items-center w-full text-sm font-medium rounded-md transition-all duration-150 relative
                                {{ request()->routeIs('admin.schedules.*') ? 'bg-sky-500 text-white border border-sky-400' : 'text-sky-100 hover:bg-sky-500 hover:text-white border border-transparent hover:border-sky-400' }}"
                                :title="sidebarCollapsed ? 'Schedules' : ''">
                            <svg class="w-5 h-5 {{ request()->routeIs('admin.schedules.*') ? 'text-white' : 'text-sky-200 group-hover:text-white' }}" 
                                 :class="sidebarCollapsed ? 'mr-0' : 'mr-3'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <span x-show="!sidebarCollapsed" x-transition class="flex-1 text-left">SCHEDULES</span>
                            <svg x-show="!sidebarCollapsed" :class="schedulesExpanded ? 'rotate-90' : ''" 
                                 class="w-4 h-4 text-sky-200 group-hover:text-white transition-transform duration-150" 
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                        
                        <!-- Schedules Submenu -->
                        <div x-show="schedulesExpanded && !sidebarCollapsed" x-transition class="ml-6 space-y-1">
                            <a href="{{ route('admin.schedules.index') }}"
                               class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-150
                               {{ request()->routeIs('admin.schedules.index') ? 'bg-sky-500 text-white' : 'text-sky-200 hover:bg-sky-500 hover:text-white' }}">
                                <span class="text-xs">• Manage Schedules</span>
                            </a>
                            <a href="{{ route('admin.schedules.create') }}"
                               class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-150
                               {{ request()->routeIs('admin.schedules.create') ? 'bg-sky-500 text-white' : 'text-sky-200 hover:bg-sky-500 hover:text-white' }}">
                                <span class="text-xs">• Add Schedule</span>
                            </a>
                        </div>
                    </div>

                    <!-- Shifts Section -->
                    <div class="space-y-1">
                        <!-- Modified click behavior to navigate when collapsed, expand when not collapsed -->
                        <button @click="sidebarCollapsed ? window.location.href = '{{ route('admin.shifts.index') }}' : shiftsExpanded = !shiftsExpanded"
                                :class="sidebarCollapsed ? 'justify-center px-2 py-3' : 'px-3 py-2'"
                                class="group flex items-center w-full text-sm font-medium rounded-md transition-all duration-150 relative
                                {{ request()->routeIs('admin.shifts.*') ? 'bg-sky-500 text-white border border-sky-400' : 'text-sky-100 hover:bg-sky-500 hover:text-white border border-transparent hover:border-sky-400' }}"
                                :title="sidebarCollapsed ? 'Shifts' : ''">
                            <svg class="w-5 h-5 {{ request()->routeIs('admin.shifts.*') ? 'text-white' : 'text-sky-200 group-hover:text-white' }}" 
                                 :class="sidebarCollapsed ? 'mr-0' : 'mr-3'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span x-show="!sidebarCollapsed" x-transition class="flex-1 text-left">SHIFTS</span>
                            <svg x-show="!sidebarCollapsed" :class="shiftsExpanded ? 'rotate-90' : ''" 
                                 class="w-4 h-4 text-sky-200 group-hover:text-white transition-transform duration-150" 
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                        
                        <!-- Shifts Submenu -->
                        <div x-show="shiftsExpanded && !sidebarCollapsed" x-transition class="ml-6 space-y-1">
                            <a href="{{ route('admin.shifts.index') }}"
                               class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-150
                               {{ request()->routeIs('admin.shifts.index') ? 'bg-sky-500 text-white' : 'text-sky-200 hover:bg-sky-500 hover:text-white' }}">
                                <span class="text-xs">• Manage Shifts</span>
                            </a>
                            <a href="{{ route('admin.shifts.create') }}"
                               class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-150
                               {{ request()->routeIs('admin.shifts.create') ? 'bg-sky-500 text-white' : 'text-sky-200 hover:bg-sky-500 hover:text-white' }}">
                                <span class="text-xs">• Create Shifts</span>
                            </a>
                        </div>
                    </div>

                    <!-- Calendar -->
                    <a href="/calender"
                        :class="sidebarCollapsed ? 'justify-center px-2 py-3' : 'px-3 py-2'"
                        class="group flex items-center text-sm font-medium rounded-md transition-all duration-150 relative
                        {{ request()->is('calender') ? 'bg-sky-500 text-white border border-sky-400' : 'text-sky-100 hover:bg-sky-500 hover:text-white border border-transparent hover:border-sky-400' }}"
                        :title="sidebarCollapsed ? 'Calendar' : ''">
                        <svg class="w-5 h-5 {{ request()->is('calender') ? 'text-white' : 'text-sky-200 group-hover:text-white' }}" 
                             :class="sidebarCollapsed ? 'mr-0' : 'mr-3'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span x-show="!sidebarCollapsed" x-transition>CALENDAR</span>
                    </a>
                </nav>

                <!-- Updated footer with sky theme and collapse support -->
                <div class="absolute bottom-6 left-6 right-6" x-show="!sidebarCollapsed" x-transition>
                    <div class="border-t border-sky-500 pt-4">
                        <div class="flex items-center space-x-2 text-xs text-sky-200">
                            <span class="font-mono">Built with</span>
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2L2 19.777h20L12 2z"/>
                            </svg>
                            <span class="font-mono">Laravel</span>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Right Content Area -->
        <div class="flex-1 flex flex-col min-h-screen">
            <!-- Updated header with sky theme -->
            <header class="bg-white border-b border-sky-200">
                <div class="px-6 py-4 flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-semibold text-sky-900">@yield('title')</h1>
                        <p class="text-sm text-sky-600 mt-1">Manage your application</p>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-sky-600 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-sky-700">Admin</span>
                        </div>
                        
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="inline-flex items-center px-3 py-2 bg-sky-600 hover:bg-sky-700 text-white font-medium text-sm rounded-md transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <!-- Updated main content with sky theme -->
            <main class="flex-1 p-6 bg-sky-50">
                <div class="bg-white rounded-lg border border-sky-200 p-8 min-h-full shadow-sm">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

</body>

</html>
