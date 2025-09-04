    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin - @yield('title')</title>
        <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
        <script src="https://cdn.jsdelivr.net/npm/heroicons@2.1.5/24/outline/heroicons.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/heroicons@2.1.5/24/solid/heroicons.min.js"></script>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-sky-50 min-h-screen font-sans antialiased">
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
            <aside :class="sidebarCollapsed ? 'w-16' : 'w-72 sm:w-64'"
                class="bg-sky-600 border-r border-sky-500 transition-all duration-300 ease-in-out fixed top-0 left-0 h-screen z-10">
                <div :class="sidebarCollapsed ? 'p-3' : 'p-6'">
                    <div class="flex items-center justify-between mb-8" :class="sidebarCollapsed ? 'mb-4' : 'mb-8'">
                        <div class="flex items-center space-x-3" x-show="!sidebarCollapsed">
                            <div class="w-10 h-10 bg-white rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-sky-600" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true" data-heroicon-solid="shield-check">
<path fill-rule="evenodd" d="M19.449 8.448 16.388 11a4.52 4.52 0 0 1 0 2.002l3.061 2.55a8.275 8.275 0 0 0 0-7.103ZM15.552 19.45 13 16.388a4.52 4.52 0 0 1-2.002 0l-2.55 3.061a8.275 8.275 0 0 0 7.103 0ZM4.55 15.552 7.612 13a4.52 4.52 0 0 1 0-2.002L4.551 8.45a8.275 8.275 0 0 0 0 7.103ZM8.448 4.55 11 7.612a4.52 4.52 0 0 1 2.002 0l2.55-3.061a8.275 8.275 0 0 0-7.103 0Zm8.657-.86a9.776 9.776 0 0 1 1.79 1.415 9.776 9.776 0 0 1 1.414 1.788 9.764 9.764 0 0 1 0 10.211 9.777 9.777 0 0 1-1.415 1.79 9.777 9.777 0 0 1-1.788 1.414 9.764 9.764 0 0 1-10.212 0 9.776 9.776 0 0 1-1.788-1.415 9.776 9.776 0 0 1-1.415-1.788 9.764 9.764 0 0 1 0-10.212 9.774 9.774 0 0 1 1.415-1.788A9.774 9.774 0 0 1 6.894 3.69a9.764 9.764 0 0 1 10.211 0ZM14.121 9.88a2.985 2.985 0 0 0-1.11-.704 3.015 3.015 0 0 0-2.022 0 2.985 2.985 0 0 0-1.11.704c-.326.325-.56.705-.704 1.11a3.015 3.015 0 0 0 0 2.022c.144.405.378.785.704 1.11.325.326.705.56 1.11.704.652.233 1.37.233 2.022 0a2.985 2.985 0 0 0 1.11-.704c.326-.325.56-.705.704-1.11a3.016 3.016 0 0 0 0-2.022 2.985 2.985 0 0 0-.704-1.11Z" clip-rule="evenodd" /></svg>                                </svg>
                            </div>
                            <div>
                                <h1 class="text-xl font-semibold text-white">Admin Panel</h1>
                                <p class="text-sm text-sky-200 font-mono">v1.0.0</p>
                            </div>
                        </div>
                        <button @click="toggleSidebar()" :class="sidebarCollapsed ? 'mx-auto' : ''"
                            class="p-2 rounded-md hover:bg-sky-500 text-white transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-sky-400 focus:ring-offset-2 focus:ring-offset-sky-600"
                            aria-label="Toggle sidebar">
                            <svg :class="sidebarCollapsed ? 'heroicon-chevron-right' : 'heroicon-chevron-left'"
                                class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true" data-heroicon-outline="chevron-right">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                    <nav class="space-y-1" role="navigation">
                        <!-- Dashboard -->
                        <a href="{{ route('admin.dashboard') }}"
                            :class="sidebarCollapsed ? 'justify-center px-2 py-3' : 'px-4 py-3'"
                            class="group flex items-center text-base font-medium rounded-md transition-all duration-150 relative
                            {{ request()->routeIs('admin.dashboard') ? 'bg-sky-500 text-white border border-sky-400' : 'text-sky-100 hover:bg-sky-500 hover:text-white border border-transparent hover:border-sky-400' }}"
                            :aria-label="sidebarCollapsed ? 'Dashboard' : ''">
                            <svg class="w-6 h-6 {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-sky-200 group-hover:text-white' }}"
                                :class="sidebarCollapsed ? 'mr-0' : 'mr-4'" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true" data-heroicon-outline="home">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            <span x-show="!sidebarCollapsed">DASHBOARD</span>
                        </a>
                        <!-- Users -->
                        <div class="space-y-1">
                            <button
                                @click="sidebarCollapsed ? window.location.href = '{{ route('admin.users.index') }}' : toggleUsers()"
                                :class="sidebarCollapsed ? 'justify-center px-2 py-3' : 'px-4 py-3'"
                                class="group flex items-center w-full text-base font-medium rounded-md transition-all duration-150 relative
                                    {{ request()->routeIs('admin.users.*') ? 'bg-sky-500 text-white border border-sky-400' : 'text-sky-100 hover:bg-sky-500 hover:text-white border border-transparent hover:border-sky-400' }}"
                                :aria-label="sidebarCollapsed ? 'Users' : ''">
                                <svg class="w-6 h-6 {{ request()->routeIs('admin.users.*') ? 'text-white' : 'text-sky-200 group-hover:text-white' }}"
                                    :class="sidebarCollapsed ? 'mr-0' : 'mr-4'" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true" data-heroicon-outline="user-group">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 005.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <span x-show="!sidebarCollapsed" class="flex-1 text-left">USERS</span>
                                <svg x-show="!sidebarCollapsed" :class="usersExpanded ? 'rotate-90' : ''"
                                    class="w-5 h-5 text-sky-200 group-hover:text-white transition-transform duration-150"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true" data-heroicon-outline="chevron-right">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                            <div x-show="usersExpanded && !sidebarCollapsed" class="ml-6 space-y-1">
                                <a href="{{ route('admin.users.index') }}"
                                    class="group flex items-center px-4 py-2 text-sm font-medium rounded-md transition-all duration-150
                                {{ request()->routeIs('admin.users.index') ? 'bg-sky-500 text-white' : 'text-sky-200 hover:bg-sky-500 hover:text-white' }}">
                                    <svg class="w-5 h-5 mr-2 text-sky-200 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true" data-heroicon-outline="minus">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 12H6" />
                                    </svg>
                                    <span class="text-sm">Manage Users</span>
                                </a>
                                <a href="{{ route('admin.users.create') }}"
                                    class="group flex items-center px-4 py-2 text-sm font-medium rounded-md transition-all duration-150
                                {{ request()->routeIs('admin.users.create') ? 'bg-sky-500 text-white' : 'text-sky-200 hover:bg-sky-500 hover:text-white' }}">
                                    <svg class="w-5 h-5 mr-2 text-sky-200 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true" data-heroicon-outline="minus">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 12H6" />
                                    </svg>
                                    <span class="text-sm">Create Users</span>
                                </a>
                            </div>
                        </div>
                        <!-- Schedules -->
                        <div class="space-y-1">
                            <button
                                @click="sidebarCollapsed ? window.location.href = '{{ route('admin.schedules.index') }}' : toggleSchedules()"
                                :class="sidebarCollapsed ? 'justify-center px-2 py-3' : 'px-4 py-3'"
                                class="group flex items-center w-full text-base font-medium rounded-md transition-all duration-150 relative
                                    {{ request()->routeIs('admin.schedules.*') ? 'bg-sky-500 text-white border border-sky-400' : 'text-sky-100 hover:bg-sky-500 hover:text-white border border-transparent hover:border-sky-400' }}"
                                :aria-label="sidebarCollapsed ? 'Schedules' : ''">
                                <svg class="w-6 h-6 {{ request()->routeIs('admin.schedules.*') ? 'text-white' : 'text-sky-200 group-hover:text-white' }}"
                                    :class="sidebarCollapsed ? 'mr-0' : 'mr-4'" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true" data-heroicon-outline="calendar">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span x-show="!sidebarCollapsed" class="flex-1 text-left">SCHEDULES</span>
                                <svg x-show="!sidebarCollapsed" :class="schedulesExpanded ? 'rotate-90' : ''"
                                    class="w-5 h-5 text-sky-200 group-hover:text-white transition-transform duration-150"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true" data-heroicon-outline="chevron-right">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                            <div x-show="schedulesExpanded && !sidebarCollapsed" class="ml-6 space-y-1">
                                <a href="{{ route('admin.schedules.index') }}"
                                    class="group flex items-center px-4 py-2 text-sm font-medium rounded-md transition-all duration-150
                                {{ request()->routeIs('admin.schedules.index') ? 'bg-sky-500 text-white' : 'text-sky-200 hover:bg-sky-500 hover:text-white' }}">
                                    <svg class="w-5 h-5 mr-2 text-sky-200 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true" data-heroicon-outline="minus">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 12H6" />
                                    </svg>
                                    <span class="text-sm">Manage Schedules</span>
                                </a>
                                <a href="{{ route('admin.schedules.create') }}"
                                    class="group flex items-center px-4 py-2 text-sm font-medium rounded-md transition-all duration-150
                                {{ request()->routeIs('admin.schedules.create') ? 'bg-sky-500 text-white' : 'text-sky-200 hover:bg-sky-500 hover:text-white' }}">
                                    <svg class="w-5 h-5 mr-2 text-sky-200 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true" data-heroicon-outline="minus">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 12H6" />
                                    </svg>
                                    <span class="text-sm">Add Schedule</span>
                                </a>
                            </div>
                        </div>
                        <!-- Shifts -->
                        <div class="space-y-1">
                            <button
                                @click="sidebarCollapsed ? window.location.href = '{{ route('admin.shifts.index') }}' : toggleShifts()"
                                :class="sidebarCollapsed ? 'justify-center px-2 py-3' : 'px-4 py-3'"
                                class="group flex items-center w-full text-base font-medium rounded-md transition-all duration-150 relative
                                    {{ request()->routeIs('admin.shifts.*') ? 'bg-sky-500 text-white border border-sky-400' : 'text-sky-100 hover:bg-sky-500 hover:text-white border border-transparent hover:border-sky-400' }}"
                                :aria-label="sidebarCollapsed ? 'Shifts' : ''">
                                <svg class="w-6 h-6 {{ request()->routeIs('admin.shifts.*') ? 'text-white' : 'text-sky-200 group-hover:text-white' }}"
                                    :class="sidebarCollapsed ? 'mr-0' : 'mr-4'" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true" data-heroicon-outline="clock">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span x-show="!sidebarCollapsed" class="flex-1 text-left">SHIFTS</span>
                                <svg x-show="!sidebarCollapsed" :class="shiftsExpanded ? 'rotate-90' : ''"
                                    class="w-5 h-5 text-sky-200 group-hover:text-white transition-transform duration-150"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true" data-heroicon-outline="chevron-right">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                            <div x-show="shiftsExpanded && !sidebarCollapsed" class="ml-6 space-y-1">
                                <a href="{{ route('admin.shifts.index') }}"
                                    class="group flex items-center px-4 py-2 text-sm font-medium rounded-md transition-all duration-150
                                {{ request()->routeIs('admin.shifts.index') ? 'bg-sky-500 text-white' : 'text-sky-200 hover:bg-sky-500 hover:text-white' }}">
                                    <svg class="w-5 h-5 mr-2 text-sky-200 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true" data-heroicon-outline="minus">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 12H6" />
                                    </svg>
                                    <span class="text-sm">Manage Shifts</span>
                                </a>
                                <a href="{{ route('admin.shifts.create') }}"
                                    class="group flex items-center px-4 py-2 text-sm font-medium rounded-md transition-all duration-150
                                {{ request()->routeIs('admin.shifts.create') ? 'bg-sky-500 text-white' : 'text-sky-200 hover:bg-sky-500 hover:text-white' }}">
                                    <svg class="w-5 h-5 mr-2 text-sky-200 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true" data-heroicon-outline="minus">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 12H6" />
                                    </svg>
                                    <span class="text-sm">Create Shifts</span>
                                </a>
                            </div>
                        </div>
                        <!-- Attendance -->
                        <a href="{{ route('admin.attendances.index') }}"
                            :class="sidebarCollapsed ? 'justify-center px-2 py-3' : 'px-4 py-3'"
                            class="group flex items-center text-base font-medium rounded-md transition-all duration-150 relative
                            {{ request()->routeIs('admin.attendances.*') ? 'bg-sky-500 text-white border border-sky-400' : 'text-sky-100 hover:bg-sky-500 hover:text-white border border-transparent hover:border-sky-400' }}"
                            :aria-label="sidebarCollapsed ? 'Attendance' : ''">
                            <svg class="w-6 h-6 {{ request()->routeIs('admin.attendances.*') ? 'text-white' : 'text-sky-200 group-hover:text-white' }}"
                                :class="sidebarCollapsed ? 'mr-0' : 'mr-4'" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true" data-heroicon-outline="check-circle">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span x-show="!sidebarCollapsed">ATTENDANCE</span>
                        </a>
                        <!-- Calendar -->
                        <a href="{{ route('admin.calendar.view') }}"
                            :class="sidebarCollapsed ? 'justify-center px-2 py-3' : 'px-4 py-3'"
                            class="group flex items-center text-base font-medium rounded-md transition-all duration-150 relative
                            {{ request()->routeIs('admin.calendar.view') ? 'bg-sky-500 text-white border border-sky-400' : 'text-sky-100 hover:bg-sky-500 hover:text-white border border-transparent hover:border-sky-400' }}"
                            :aria-label="sidebarCollapsed ? 'Calendar' : ''">
                            <svg class="w-6 h-6 {{ request()->routeIs('admin.calendar.view') ? 'text-white' : 'text-sky-200 group-hover:text-white' }}"
                                :class="sidebarCollapsed ? 'mr-0' : 'mr-4'" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true" data-heroicon-outline="calendar-days">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008h-.008V13.5zm0 2.25h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V15.75z" />
                            </svg>
                            <span x-show="!sidebarCollapsed">CALENDAR</span>
                        </a>
                    </nav>
                    <div class="absolute bottom-6 left-6 right-6" x-show="!sidebarCollapsed">
                        <div class="border-t border-sky-500 pt-4">
                            <div class="flex items-center space-x-2 text-sm text-sky-200">
                                <span class="font-mono">Built with</span>
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true" data-heroicon-solid="shield-check">
                                    <path fill-rule="evenodd" d="M12.516 2.17a.75.75 0 0 0-1.032 0 11.209 11.209 0 0 1-7.877 3.08.75.75 0 0 0-.722.515A12.74 12.74 0 0 0 2.25 9.75c0 5.942 4.064 10.933 9.563 12.348a.749.749 0 0 0 .374 0c5.499-1.415 9.563-6.406 9.563-12.348 0-1.39-.223-2.73-.635-3.985a.75.75 0 0 0-.722-.516l-.143.001c-2.996.05-5.717-1.17-7.734-3.08Zm3.094 8.016a.75.75 0 0 1 1.06-.053l4.125 4.5a.75.75 0 1 1-1.114 1.004L15 10.586l-3.294 3.294a.75.75 0 0 1-1.06 0L8.25 11.484a.75.75 0 0 1 1.06-1.06l1.875 1.876 2.834-2.834a.75.75 0 0 1 .053-.106Z" clip-rule="evenodd" />
                                </svg>
                                <span class="font-mono">Laravel</span>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>
            <div class="flex-1 flex flex-col min-h-screen" :class="sidebarCollapsed ? 'ml-16 sm:ml-16' : 'ml-72 sm:ml-64'">
                <header class="bg-white border-b border-sky-200">
                    <div class="px-6 py-4 flex justify-between items-center">
                        <div>
                            <h1 class="text-3xl font-semibold text-sky-900">@yield('title')</h1>
                            <p class="text-base text-sky-600">Manage your application</p>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-sky-600 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true" data-heroicon-outline="user-circle">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13a7 7 0 1114 0H5zm7-5a4 4 0 100-8 4 4 0 000 8z" />
                                    </svg>
                                </div>
                                <span class="text-base font-medium text-sky-700">{{ auth()->user()->name }}</span>
                            </div>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white font-medium text-base rounded-md transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2"
                                    aria-label="Log out">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true" data-heroicon-outline="arrow-right-end-on-rectangle">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6m9 10V5m3 3V3h-3" />
                                    </svg>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </header>
                <main class="flex-1 p-0 bg-sky-50">
                    <div class="bg-white border-sky-200 p-8 min-h-full shadow-sm">
                        @yield('content')
                    </div>
                </main>
            </div>
        </div>
        @stack('scripts')
    </body>
    </html>WG