<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User - @yield('title')</title>
    <script src="//unpkg.com/alpinejs" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-sky-50 min-h-screen antialiased">

    <div class="flex min-h-screen" x-data="{
        sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true',
        calendarExpanded: false,
    
        toggleSidebar() {
            this.sidebarCollapsed = !this.sidebarCollapsed;
            localStorage.setItem('sidebarCollapsed', this.sidebarCollapsed);
        }
    }">

        <!-- Sidebar -->
        <aside :class="sidebarCollapsed ? 'w-16' : 'w-72'"
            class="bg-sky-600 border-r border-sky-500 transition-all duration-300 ease-in-out relative">
            <div :class="sidebarCollapsed ? 'p-2' : 'p-6'">
                <div class="flex items-center justify-between mb-8" :class="sidebarCollapsed ? 'mb-4' : 'mb-8'">
                    <div class="flex items-center space-x-3" x-show="!sidebarCollapsed" x-transition>
                        <div class="w-8 h-8 bg-white rounded-md flex items-center justify-center">
                            <svg class="w-4 h-4 text-sky-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2L2 19.777h20L12 2z" />
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-lg font-semibold text-white">User Panel</h1>
                            <p class="text-xs text-sky-200 font-mono">v1.0.0</p>
                        </div>
                    </div>
                    <button @click="toggleSidebar()" :class="sidebarCollapsed ? 'mx-auto' : ''"
                        class="p-2 rounded-md hover:bg-sky-500 text-white transition-colors duration-150">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                :d="sidebarCollapsed ? 'M9 5l7 7-7 7' : 'M15 19l-7-7 7-7'"></path>
                        </svg>
                    </button>
                </div>

                <!-- Navigation -->
                <nav class="space-y-1">
                    <!-- Dashboard -->
                    <a href="{{ route('user.dashboard') }}"
                        :class="sidebarCollapsed ? 'justify-center px-2 py-3' : 'px-3 py-2'"
                        class="group flex items-center text-sm font-medium rounded-md transition-all duration-150 relative
                        {{ request()->routeIs('user.dashboard') ? 'bg-sky-500 text-white border border-sky-400' : 'text-sky-100 hover:bg-sky-500 hover:text-white border border-transparent hover:border-sky-400' }}"
                        :title="sidebarCollapsed ? 'Dashboard' : ''">
                        <svg class="w-5 h-5 {{ request()->routeIs('user.dashboard') ? 'text-white' : 'text-sky-200 group-hover:text-white' }}"
                            :class="sidebarCollapsed ? 'mr-0' : 'mr-3'" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"></path>
                        </svg>
                        <span x-show="!sidebarCollapsed" x-transition>DASHBOARD</span>
                    </a>

                    <!-- Calendar -->
                    <a href="{{ route('user.calendar.view') }}"
                        :class="sidebarCollapsed ? 'justify-center px-2 py-3' : 'px-3 py-2'"
                        class="group flex items-center text-sm font-medium rounded-md transition-all duration-150 relative
                        {{ request()->routeIs('user.calendar.view') ? 'bg-sky-500 text-white border border-sky-400' : 'text-sky-100 hover:bg-sky-500 hover:text-white border border-transparent hover:border-sky-400' }}"
                        :title="sidebarCollapsed ? 'Calendar' : ''">
                        <svg class="w-5 h-5 {{ request()->routeIs('user.calendar.view') ? 'text-white' : 'text-sky-200 group-hover:text-white' }}"
                            :class="sidebarCollapsed ? 'mr-0' : 'mr-3'" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                        <span x-show="!sidebarCollapsed" x-transition>CALENDAR</span>
                    </a>

                    <!-- Attendance -->
                    <a href="{{ route('user.attendance.index') }}"
                        :class="sidebarCollapsed ? 'justify-center px-2 py-3' : 'px-3 py-2'"
                        class="group flex items-center text-sm font-medium rounded-md transition-all duration-150 relative
    {{ request()->routeIs('user.attendance.*') ? 'bg-sky-500 text-white border border-sky-400' : 'text-sky-100 hover:bg-sky-500 hover:text-white border border-transparent hover:border-sky-400' }}"
                        :title="sidebarCollapsed ? 'Attendance' : ''">
                        <svg class="w-5 h-5 {{ request()->routeIs('user.attendance.*') ? 'text-white' : 'text-sky-200 group-hover:text-white' }}"
                            :class="sidebarCollapsed ? 'mr-0' : 'mr-3'" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6M5 6h14M5 6a2 2 0 00-2 2v10a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2H5z" />
                        </svg>
                        <span x-show="!sidebarCollapsed" x-transition>ATTENDANCE</span>
                    </a>


                </nav>

                <!-- Footer -->
                <div class="absolute bottom-6 left-6 right-6" x-show="!sidebarCollapsed" x-transition>
                    <div class="border-t border-sky-500 pt-4">
                        <div class="flex items-center space-x-2 text-xs text-sky-200">
                            <span class="font-mono">Built with</span>
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2L2 19.777h20L12 2z" />
                            </svg>
                            <span class="font-mono">Laravel</span>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-h-screen">
            <!-- Header -->
            <header class="bg-white border-b border-sky-200">
                <div class="px-6 py-4 flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-semibold text-sky-900">@yield('title')</h1>
                        <p class="text-sm text-sky-600 mt-1">Welcome to your dashboard</p>
                    </div>

                    <div class="flex items-center space-x-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-sky-600 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-sky-700">{{ auth()->user()->name }}</span>
                        </div>

                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="inline-flex items-center px-3 py-2 bg-sky-600 hover:bg-sky-700 text-white font-medium text-sm rounded-md transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                    </path>
                                </svg>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <!-- Main -->
            <main class="flex-1 p-6 bg-sky-50">
                <div class="bg-white rounded-lg border border-sky-200 p-8 min-h-full shadow-sm">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    @stack('scripts')
</body>

</html>
