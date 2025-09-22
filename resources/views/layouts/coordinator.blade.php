<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - @yield('title')</title>
    <link rel="icon" href="">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .sidebar-transition {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .menu-item-transition {
            transition: all 0.15s ease-in-out;
        }

        .icon-transition {
            transition: transform 0.2s ease-in-out;
        }

        .tooltip {
            pointer-events: none;
            z-index: 9999;
        }

        .menu-item:hover .icon-hover {
            transform: scale(1.05);
        }

        .sidebar-footer {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
        }
    </style>
</head>

<body class="min-h-screen bg-white antialiased">
    <div class="flex min-h-screen" x-data="{
        sidebarCollapsed: false,
        usersExpanded: false,
        schedulesExpanded: false,
        shiftsExpanded: false,
        init() {
            this.sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true' || window.innerWidth < 640;
            this.usersExpanded = localStorage.getItem('usersExpanded') === 'true';
            this.schedulesExpanded = localStorage.getItem('schedulesExpanded') === 'true';
            this.shiftsExpanded = localStorage.getItem('shiftsExpanded') === 'true';
    
            setTimeout(() => {
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            }, 100);
        },
        toggleSidebar() {
            this.sidebarCollapsed = !this.sidebarCollapsed;
            localStorage.setItem('sidebarCollapsed', this.sidebarCollapsed);
        },
        toggleUsers() {
            this.usersExpanded = !this.usersExpanded;
            localStorage.setItem('usersExpanded', this.usersExpanded);
        },
        toggleSchedules() {
            this.schedulesExpanded = !this.schedulesExpanded;
            localStorage.setItem('schedulesExpanded', this.schedulesExpanded);
        },
        toggleShifts() {
            this.shiftsExpanded = !this.shiftsExpanded;
            localStorage.setItem('shiftsExpanded', this.shiftsExpanded);
        }
    }" x-init="init()">

        <!-- Sidebar -->
        <aside :class="sidebarCollapsed ? 'w-16' : 'w-72 sm:w-64'"
            class="bg-white/90 backdrop-blur-lg border-r border-sky-200 sidebar-transition fixed top-0 left-0 h-screen z-10 flex flex-col">

            <!-- Sidebar Header -->
            <div :class="sidebarCollapsed ? 'p-3' : 'p-4 sm:p-6'" class="border-b border-sky-200 flex-shrink-0">
                <div class="flex items-center justify-between" :class="sidebarCollapsed ? 'mb-0' : 'mb-2'">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3"
                        x-show="!sidebarCollapsed" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
                        <div>
                            <h1 class="text-2xl font-semibold text-gray-700 tracking-tight">Admin Panel</h1>
                            <p class="text-sm text-gray-500 font-medium">v1.0.0</p>
                        </div>
                    </a>

                    <button @click="toggleSidebar()" :class="sidebarCollapsed ? 'mx-auto' : ''"
                        class="p-2.5 rounded-xl hover:bg-sky-100 text-gray-600 menu-item-transition focus:outline-none focus:ring-2 focus:ring-sky-400 focus:ring-offset-2 group"
                        aria-label="Toggle sidebar">
                        <i :data-lucide="sidebarCollapsed ? 'panel-right-open' : 'panel-left-close'"
                            class="w-5 h-5 icon-transition group-hover:scale-110"></i>
                    </button>
                </div>
            </div>

            <!-- Sidebar Navigation -->
            <nav class="flex-1 space-y-2 p-3 overflow-y-auto" role="navigation">

                <!-- Dashboard -->
                <a href="{{ route('admin.dashboard') }}"
                    :class="sidebarCollapsed ? 'justify-center px-3 py-4 relative group' : 'px-4 py-3'"
                    class="menu-item group flex items-center text-sm font-semibold rounded-xl menu-item-transition
        {{ request()->routeIs('admin.dashboard') ? 'bg-sky-100 text-sky-700 border border-sky-200' : 'text-gray-600 hover:bg-sky-100 hover:text-sky-700 border border-transparent hover:border-sky-200' }}"
                    :aria-label="sidebarCollapsed ? 'Dashboard' : ''">
                    <i data-lucide="layout-dashboard"
                        class="icon-hover w-5 h-5 icon-transition {{ request()->routeIs('admin.dashboard') ? 'text-sky-700' : 'text-gray-500 group-hover:text-sky-700' }}"
                        :class="sidebarCollapsed ? 'mr-0' : 'mr-3'"></i>
                    <span x-show="!sidebarCollapsed" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 translate-x-2"
                        x-transition:enter-end="opacity-100 translate-x-0">Dashboard</span>

                    <div x-show="sidebarCollapsed"
                        class="tooltip absolute left-full top-1/2 transform -translate-y-1/2 ml-3 px-3 py-2 bg-gray-800 text-white text-sm rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap">
                        Dashboard
                        <div
                            class="absolute top-1/2 left-0 transform -translate-y-1/2 -translate-x-1 w-2 h-2 bg-gray-800 rotate-45">
                        </div>
                    </div>
                </a>

                <!-- Users -->
                <div class="space-y-1 relative">
                    <button
                        @click="sidebarCollapsed ? window.location.href = '{{ route('admin.users.index') }}' : toggleUsers()"
                        :class="sidebarCollapsed ? 'justify-center px-3 py-4 relative group' : 'px-4 py-3'"
                        class="menu-item group flex items-center w-full text-sm font-semibold rounded-xl menu-item-transition
                {{ request()->routeIs('admin.users.*') ? 'bg-sky-100 text-sky-700 border border-sky-200' : 'text-gray-600 hover:bg-sky-100 hover:text-sky-700 border border-transparent hover:border-sky-200' }}"
                        :aria-label="sidebarCollapsed ? 'Users' : ''">
                        <i data-lucide="users"
                            class="icon-hover w-5 h-5 icon-transition {{ request()->routeIs('admin.users.*') ? 'text-sky-700' : 'text-gray-500 group-hover:text-sky-700' }}"
                            :class="sidebarCollapsed ? 'mr-0' : 'mr-3'"></i>
                        <span x-show="!sidebarCollapsed" class="flex-1 text-left" x-transition>Users</span>
                        <i x-show="!sidebarCollapsed" data-lucide="chevron-right"
                            :class="usersExpanded ? 'rotate-90' : 'rotate-0'"
                            class="w-4 h-4 text-gray-500 group-hover:text-sky-700 sidebar-transition"></i>

                        <div x-show="sidebarCollapsed"
                            class="tooltip absolute left-full top-1/2 transform -translate-y-1/2 ml-3 px-3 py-2 bg-gray-800 text-white text-sm rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap">
                            Users Management
                            <div
                                class="absolute top-1/2 left-0 transform -translate-y-1/2 -translate-x-1 w-2 h-2 bg-gray-800 rotate-45">
                            </div>
                        </div>
                    </button>

                    <div x-show="usersExpanded && !sidebarCollapsed"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        class="ml-8 space-y-1 border-l-2 border-sky-200 border-opacity-30 pl-4">
                        <a href="{{ route('admin.users.index') }}"
                            class="group flex items-center px-3 py-2 text-sm font-semibold rounded-xl menu-item-transition {{ request()->routeIs('admin.users.index') ? 'bg-sky-100 text-sky-700' : 'text-gray-600 hover:bg-sky-100 hover:text-sky-700' }}">
                            <i data-lucide="list" class="w-4 h-4 mr-3 text-gray-500 group-hover:text-sky-700"></i>
                            <span>Manage Users</span>
                        </a>
                        <a href="{{ route('admin.users.create') }}"
                            class="group flex items-center px-3 py-2 text-sm font-semibold rounded-xl menu-item-transition {{ request()->routeIs('admin.users.create') ? 'bg-sky-100 text-sky-700' : 'text-gray-600 hover:bg-sky-100 hover:text-sky-700' }}">
                            <i data-lucide="user-plus" class="w-4 h-4 mr-3 text-gray-500 group-hover:text-sky-700"></i>
                            <span>Create Users</span>
                        </a>
                    </div>
                </div>

                <!-- Schedules -->
                <div class="space-y-1 relative">
                    <button
                        @click="sidebarCollapsed ? window.location.href = '{{ route('admin.schedules.index') }}' : toggleSchedules()"
                        :class="sidebarCollapsed ? 'justify-center px-3 py-4 relative group' : 'px-4 py-3'"
                        class="menu-item group flex items-center w-full text-sm font-semibold rounded-xl menu-item-transition
                {{ request()->routeIs('admin.schedules.*') ? 'bg-sky-100 text-sky-700 border border-sky-200' : 'text-gray-600 hover:bg-sky-100 hover:text-sky-700 border border-transparent hover:border-sky-200' }}"
                        :aria-label="sidebarCollapsed ? 'Schedules' : ''">
                        <i data-lucide="calendar"
                            class="icon-hover w-5 h-5 icon-transition {{ request()->routeIs('admin.schedules.*') ? 'text-sky-700' : 'text-gray-500 group-hover:text-sky-700' }}"
                            :class="sidebarCollapsed ? 'mr-0' : 'mr-3'"></i>
                        <span x-show="!sidebarCollapsed" class="flex-1 text-left" x-transition>Schedules</span>
                        <i x-show="!sidebarCollapsed" data-lucide="chevron-right"
                            :class="schedulesExpanded ? 'rotate-90' : 'rotate-0'"
                            class="w-4 h-4 text-gray-500 group-hover:text-sky-700 sidebar-transition"></i>

                        <div x-show="sidebarCollapsed"
                            class="tooltip absolute left-full top-1/2 transform -translate-y-1/2 ml-3 px-3 py-2 bg-gray-800 text-white text-sm rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap">
                            Schedule Management
                            <div
                                class="absolute top-1/2 left-0 transform -translate-y-1/2 -translate-x-1 w-2 h-2 bg-gray-800 rotate-45">
                            </div>
                        </div>
                    </button>

                    <div x-show="schedulesExpanded && !sidebarCollapsed"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        class="ml-8 space-y-1 border-l-2 border-sky-200 border-opacity-30 pl-4">
                        <a href="{{ route('admin.schedules.index') }}"
                            class="group flex items-center px-3 py-2 text-sm font-semibold rounded-xl menu-item-transition {{ request()->routeIs('admin.schedules.index') ? 'bg-sky-100 text-sky-700' : 'text-gray-600 hover:bg-sky-100 hover:text-sky-700' }}">
                            <i data-lucide="calendar-days"
                                class="w-4 h-4 mr-3 text-gray-500 group-hover:text-sky-700"></i>
                            <span>Manage Schedules</span>
                        </a>
                        <a href="{{ route('admin.schedules.create') }}"
                            class="group flex items-center px-3 py-2 text-sm font-semibold rounded-xl menu-item-transition {{ request()->routeIs('admin.schedules.create') ? 'bg-sky-100 text-sky-700' : 'text-gray-600 hover:bg-sky-100 hover:text-sky-700' }}">
                            <i data-lucide="calendar-plus"
                                class="w-4 h-4 mr-3 text-gray-500 group-hover:text-sky-700"></i>
                            <span>Add Schedule</span>
                        </a>
                    </div>
                </div>

                <!-- Shifts -->
                <div class="space-y-1 relative">
                    <button
                        @click="sidebarCollapsed ? window.location.href = '{{ route('admin.shifts.index') }}' : toggleShifts()"
                        :class="sidebarCollapsed ? 'justify-center px-3 py-4 relative group' : 'px-4 py-3'"
                        class="menu-item group flex items-center w-full text-sm font-semibold rounded-xl menu-item-transition
                {{ request()->routeIs('admin.shifts.*') ? 'bg-sky-100 text-sky-700 border border-sky-200' : 'text-gray-600 hover:bg-sky-100 hover:text-sky-700 border border-transparent hover:border-sky-200' }}"
                        :aria-label="sidebarCollapsed ? 'Shifts' : ''">
                        <i data-lucide="clock"
                            class="icon-hover w-5 h-5 icon-transition {{ request()->routeIs('admin.shifts.*') ? 'text-sky-700' : 'text-gray-500 group-hover:text-sky-700' }}"
                            :class="sidebarCollapsed ? 'mr-0' : 'mr-3'"></i>
                        <span x-show="!sidebarCollapsed" class="flex-1 text-left" x-transition>Shifts</span>
                        <i x-show="!sidebarCollapsed" data-lucide="chevron-right"
                            :class="shiftsExpanded ? 'rotate-90' : 'rotate-0'"
                            class="w-4 h-4 text-gray-500 group-hover:text-sky-700 sidebar-transition"></i>

                        <div x-show="sidebarCollapsed"
                            class="tooltip absolute left-full top-1/2 transform -translate-y-1/2 ml-3 px-3 py-2 bg-gray-800 text-white text-sm rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap">
                            Shift Management
                            <div
                                class="absolute top-1/2 left-0 transform -translate-y-1/2 -translate-x-1 w-2 h-2 bg-gray-800 rotate-45">
                            </div>
                        </div>
                    </button>

                    <div x-show="shiftsExpanded && !sidebarCollapsed"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        class="ml-8 space-y-1 border-l-2 border-sky-200 border-opacity-30 pl-4">
                        <a href="{{ route('admin.shifts.index') }}"
                            class="group flex items-center px-3 py-2 text-sm font-semibold rounded-xl menu-item-transition {{ request()->routeIs('admin.shifts.index') ? 'bg-sky-100 text-sky-700' : 'text-gray-600 hover:bg-sky-100 hover:text-sky-700' }}">
                            <i data-lucide="clock-4" class="w-4 h-4 mr-3 text-gray-500 group-hover:text-sky-700"></i>
                            <span>Manage Shifts</span>
                        </a>
                        <a href="{{ route('admin.shifts.create') }}"
                            class="group flex items-center px-3 py-2 text-sm font-semibold rounded-xl menu-item-transition {{ request()->routeIs('admin.shifts.create') ? 'bg-sky-100 text-sky-700' : 'text-gray-600 hover:bg-sky-100 hover:text-sky-700' }}">
                            <i data-lucide="plus-circle"
                                class="w-4 h-4 mr-3 text-gray-500 group-hover:text-sky-700"></i>
                            <span>Create Shifts</span>
                        </a>
                    </div>
                </div>

                <!-- Divider -->
                <div class="my-4 border-t border-sky-200 border-opacity-30"></div>

                <!-- Attendance -->
                <div class="space-y-1 relative">
                    <a href="{{ route('admin.attendances.index') }}"
                        :class="sidebarCollapsed ? 'justify-center px-3 py-4 relative group' : 'px-4 py-3'"
                        class="menu-item group flex items-center text-sm font-semibold rounded-xl menu-item-transition
            {{ request()->routeIs('admin.attendances.*')
                ? 'bg-sky-100 text-sky-700 border border-sky-200'
                : 'text-gray-600 hover:bg-sky-100 hover:text-sky-700 border border-transparent hover:border-sky-200' }}"
                        :aria-label="sidebarCollapsed ? 'Attendance' : ''">
                        <i data-lucide="user-check"
                            class="icon-hover w-5 h-5 icon-transition {{ request()->routeIs('admin.attendances.*') ? 'text-sky-700' : 'text-gray-500 group-hover:text-sky-700' }}"
                            :class="sidebarCollapsed ? 'mr-0' : 'mr-3'"></i>
                        <span x-show="!sidebarCollapsed" x-transition>Attendances</span>

                        <div x-show="sidebarCollapsed"
                            class="tooltip absolute left-full top-1/2 transform -translate-y-1/2 ml-3 px-3 py-2 bg-gray-800 text-white text-sm rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap">
                            Attendance Records
                            <div
                                class="absolute top-1/2 left-0 transform -translate-y-1/2 -translate-x-1 w-2 h-2 bg-gray-800 rotate-45">
                            </div>
                        </div>
                    </a>

                    <!-- Calendar -->
                    <a href="{{ route('admin.calendar.view') }}"
                        :class="sidebarCollapsed ? 'justify-center px-3 py-4 relative group' : 'px-4 py-3'"
                        class="menu-item group flex items-center text-sm font-semibold rounded-xl menu-item-transition
       {{ request()->routeIs('admin.calendar.view') ? 'bg-sky-100 text-sky-700 border border-sky-200' : 'text-gray-600 hover:bg-sky-100 hover:text-sky-700 border border-transparent hover:border-sky-200' }}"
                        :aria-label="sidebarCollapsed ? 'Calendar' : ''">
                        <i data-lucide="calendar-range"
                            class="icon-hover w-5 h-5 icon-transition {{ request()->routeIs('admin.calendar.view') ? 'text-sky-700' : 'text-gray-500 group-hover:text-sky-700' }}"
                            :class="sidebarCollapsed ? 'mr-0' : 'mr-3'"></i>
                        <span x-show="!sidebarCollapsed" x-transition>Calendar</span>

                        <div x-show="sidebarCollapsed"
                            class="tooltip absolute left-full top-1/2 transform -translate-y-1/2 ml-3 px-3 py-2 bg-gray-800 text-white text-sm rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap">
                            Calendar View
                            <div
                                class="absolute top-1/2 left-0 transform -translate-y-1/2 -translate-x-1 w-2 h-2 bg-gray-800 rotate-45">
                            </div>
                        </div>
                    </a>
            </nav>

            <!-- Sidebar Footer -->
            <div class="sidebar-footer p-4 border-t border-sky-200 border-opacity-30 flex-shrink-0">
                <div x-show="!sidebarCollapsed" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                    class="flex items-center justify-center space-x-2 text-sm text-gray-500 bg-sky-50 rounded-xl p-3">
                    <i data-lucide="code" class="w-4 h-4"></i>
                    <span class="font-medium">Built with Laravel</span>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-h-screen sidebar-transition"
            :class="sidebarCollapsed ? 'ml-16 sm:ml-16' : 'ml-72 sm:ml-64'">

            <!-- Header -->
            <header class="bg-white/90 backdrop-blur-lg border-b border-sky-200 flex-shrink-0">
                <div class="px-4 sm:px-6 py-4 flex justify-between items-center gap-4">
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-700 tracking-tight">@yield('title')</h1>
                        <p class="text-base text-gray-500 mt-1">Manage your application</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <!-- User Info -->
                        <div class="flex items-center space-x-3">
                            <div
                                class="w-10 h-10 bg-gradient-to-br from-sky-100 to-sky-200 rounded-xl flex items-center justify-center">
                                <i data-lucide="user" class="w-5 h-5 text-sky-700"></i>
                            </div>
                            <span
                                class="text-base font-semibold text-gray-700 hidden sm:inline">{{ auth()->user()->name }}</span>
                        </div>

                        <!-- Logout Button -->
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-sky-500 to-sky-600 hover:from-sky-600 hover:to-sky-700 text-white font-semibold rounded-xl shadow-md hover:shadow-lg hover:bg-[#1E90FF]/90 transition duration-300 flex justify-center items-center gap-2"
                                aria-label="Log out">
                                <i data-lucide="log-out" class="w-5 h-5 mr-2"></i>
                                <span class="hidden sm:inline">Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 bg-white overflow-auto">
                <div class="p-8 sm:p-6 lg:p-8 min-h-full m-0">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <script>
        // Initialize Lucide icons after DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });

        // Re-initialize icons when Alpine updates the DOM
        document.addEventListener('alpine:init', () => {
            setTimeout(() => {
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            }, 100);
        });
    </script>

    @stack('scripts')
</body>

</html>
