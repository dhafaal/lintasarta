<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - @yield('title')</title>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-sky-50 min-h-screen antialiased">
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
                            <i data-lucide="shield" class="w-5 h-5 text-sky-600"></i>
                        </div>
                        <div>
                            <h1 class="text-xl font-semibold text-white">Admin Panel</h1>
                            <p class="text-sm text-sky-200 font-mono">v1.0.0</p>
                        </div>
                    </div>
                    <button @click="toggleSidebar()" :class="sidebarCollapsed ? 'mx-auto' : ''"
                        class="p-2 rounded-md hover:bg-sky-500 text-white transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-sky-400 focus:ring-offset-2 focus:ring-offset-sky-600"
                        aria-label="Toggle sidebar">
                        <i :data-lucide="sidebarCollapsed ? 'chevron-right' : 'chevron-left'" class="w-5 h-5"></i>
                    </button>
                </div>

                <!-- Sidebar Menu -->
                <nav class="space-y-1" role="navigation">
                    <!-- Dashboard -->
                    <a href="{{ route('admin.dashboard') }}"
                        :class="sidebarCollapsed ? 'justify-center px-2 py-3' : 'px-4 py-3'"
                        class="group flex items-center text-base font-medium rounded-md transition-all duration-150 relative
                        {{ request()->routeIs('admin.dashboard') ? 'bg-sky-500 text-white border border-sky-400' : 'text-sky-100 hover:bg-sky-500 hover:text-white border border-transparent hover:border-sky-400' }}"
                        :aria-label="sidebarCollapsed ? 'Dashboard' : ''">
                        <i data-lucide="home" class="w-6 h-6 {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-sky-200 group-hover:text-white' }}" :class="sidebarCollapsed ? 'mr-0' : 'mr-4'"></i>
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
                            <i data-lucide="users" class="w-6 h-6 {{ request()->routeIs('admin.users.*') ? 'text-white' : 'text-sky-200 group-hover:text-white' }}" :class="sidebarCollapsed ? 'mr-0' : 'mr-4'"></i>
                            <span x-show="!sidebarCollapsed" class="flex-1 text-left">USERS</span>
                            <i x-show="!sidebarCollapsed" data-lucide="chevron-right" :class="usersExpanded ? 'rotate-90' : ''" class="w-5 h-5 text-sky-200 group-hover:text-white transition-transform duration-150"></i>
                        </button>
                        <div x-show="usersExpanded && !sidebarCollapsed" class="ml-6 space-y-1">
                            <a href="{{ route('admin.users.index') }}" class="group flex items-center px-4 py-2 text-sm font-medium rounded-md transition-all duration-150 {{ request()->routeIs('admin.users.index') ? 'bg-sky-500 text-white' : 'text-sky-200 hover:bg-sky-500 hover:text-white' }}">
                                <i data-lucide="minus" class="w-5 h-5 mr-2 text-sky-200 group-hover:text-white"></i>
                                <span>Manage Users</span>
                            </a>
                            <a href="{{ route('admin.users.create') }}" class="group flex items-center px-4 py-2 text-sm font-medium rounded-md transition-all duration-150 {{ request()->routeIs('admin.users.create') ? 'bg-sky-500 text-white' : 'text-sky-200 hover:bg-sky-500 hover:text-white' }}">
                                <i data-lucide="plus" class="w-5 h-5 mr-2 text-sky-200 group-hover:text-white"></i>
                                <span>Create Users</span>
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
                            <i data-lucide="calendar" class="w-6 h-6 {{ request()->routeIs('admin.schedules.*') ? 'text-white' : 'text-sky-200 group-hover:text-white' }}" :class="sidebarCollapsed ? 'mr-0' : 'mr-4'"></i>
                            <span x-show="!sidebarCollapsed" class="flex-1 text-left">SCHEDULES</span>
                            <i x-show="!sidebarCollapsed" data-lucide="chevron-right" :class="schedulesExpanded ? 'rotate-90' : ''" class="w-5 h-5 text-sky-200 group-hover:text-white transition-transform duration-150"></i>
                        </button>
                        <div x-show="schedulesExpanded && !sidebarCollapsed" class="ml-6 space-y-1">
                            <a href="{{ route('admin.schedules.index') }}" class="group flex items-center px-4 py-2 text-sm font-medium rounded-md transition-all duration-150 {{ request()->routeIs('admin.schedules.index') ? 'bg-sky-500 text-white' : 'text-sky-200 hover:bg-sky-500 hover:text-white' }}">
                                <i data-lucide="minus" class="w-5 h-5 mr-2 text-sky-200 group-hover:text-white"></i>
                                <span>Manage Schedules</span>
                            </a>
                            <a href="{{ route('admin.schedules.create') }}" class="group flex items-center px-4 py-2 text-sm font-medium rounded-md transition-all duration-150 {{ request()->routeIs('admin.schedules.create') ? 'bg-sky-500 text-white' : 'text-sky-200 hover:bg-sky-500 hover:text-white' }}">
                                <i data-lucide="plus" class="w-5 h-5 mr-2 text-sky-200 group-hover:text-white"></i>
                                <span>Add Schedule</span>
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
                            <i data-lucide="clock" class="w-6 h-6 {{ request()->routeIs('admin.shifts.*') ? 'text-white' : 'text-sky-200 group-hover:text-white' }}" :class="sidebarCollapsed ? 'mr-0' : 'mr-4'"></i>
                            <span x-show="!sidebarCollapsed" class="flex-1 text-left">SHIFTS</span>
                            <i x-show="!sidebarCollapsed" data-lucide="chevron-right" :class="shiftsExpanded ? 'rotate-90' : ''" class="w-5 h-5 text-sky-200 group-hover:text-white transition-transform duration-150"></i>
                        </button>
                        <div x-show="shiftsExpanded && !sidebarCollapsed" class="ml-6 space-y-1">
                            <a href="{{ route('admin.shifts.index') }}" class="group flex items-center px-4 py-2 text-sm font-medium rounded-md transition-all duration-150 {{ request()->routeIs('admin.shifts.index') ? 'bg-sky-500 text-white' : 'text-sky-200 hover:bg-sky-500 hover:text-white' }}">
                                <i data-lucide="minus" class="w-5 h-5 mr-2 text-sky-200 group-hover:text-white"></i>
                                <span>Manage Shifts</span>
                            </a>
                            <a href="{{ route('admin.shifts.create') }}" class="group flex items-center px-4 py-2 text-sm font-medium rounded-md transition-all duration-150 {{ request()->routeIs('admin.shifts.create') ? 'bg-sky-500 text-white' : 'text-sky-200 hover:bg-sky-500 hover:text-white' }}">
                                <i data-lucide="plus" class="w-5 h-5 mr-2 text-sky-200 group-hover:text-white"></i>
                                <span>Create Shifts</span>
                            </a>
                        </div>
                    </div>

                    <!-- Attendance -->
                    <a href="{{ route('admin.attendances.index') }}" :class="sidebarCollapsed ? 'justify-center px-2 py-3' : 'px-4 py-3'"
                        class="group flex items-center text-base font-medium rounded-md transition-all duration-150 relative
                        {{ request()->routeIs('admin.attendances.*') ? 'bg-sky-500 text-white border border-sky-400' : 'text-sky-100 hover:bg-sky-500 hover:text-white border border-transparent hover:border-sky-400' }}"
                        :aria-label="sidebarCollapsed ? 'Attendance' : ''">
                        <i data-lucide="check-circle" class="w-6 h-6 {{ request()->routeIs('admin.attendances.*') ? 'text-white' : 'text-sky-200 group-hover:text-white' }}" :class="sidebarCollapsed ? 'mr-0' : 'mr-4'"></i>
                        <span x-show="!sidebarCollapsed">ATTENDANCE</span>
                    </a>

                    <!-- Calendar -->
                    <a href="{{ route('admin.calendar.view') }}" :class="sidebarCollapsed ? 'justify-center px-2 py-3' : 'px-4 py-3'"
                        class="group flex items-center text-base font-medium rounded-md transition-all duration-150 relative
                        {{ request()->routeIs('admin.calendar.view') ? 'bg-sky-500 text-white border border-sky-400' : 'text-sky-100 hover:bg-sky-500 hover:text-white border border-transparent hover:border-sky-400' }}"
                        :aria-label="sidebarCollapsed ? 'Calendar' : ''">
                        <i data-lucide="calendar-days" class="w-6 h-6 {{ request()->routeIs('admin.calendar.view') ? 'text-white' : 'text-sky-200 group-hover:text-white' }}" :class="sidebarCollapsed ? 'mr-0' : 'mr-4'"></i>
                        <span x-show="!sidebarCollapsed">CALENDAR</span>
                    </a>
                </nav>

                <!-- Footer -->
                <div class="absolute bottom-6 left-6 right-6" x-show="!sidebarCollapsed">
                    <div class="border-t border-sky-500 pt-4">
                        <div class="flex items-center space-x-2 text-sm text-sky-200">
                            <span class="font-mono">Built with</span>
                            <i data-lucide="shield-check" class="w-4 h-4"></i>
                            <span class="font-mono">Laravel</span>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Content -->
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
                                <i data-lucide="user" class="w-6 h-6 text-white"></i>
                            </div>
                            <span class="text-base font-medium text-sky-700">{{ auth()->user()->name }}</span>
                        </div>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="inline-flex gap-x-1 items-center px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white font-medium text-base rounded-md transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2"
                                aria-label="Log out">
                                <i data-lucide="log-out" class="w-5 h-5"></i>
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
</html>
