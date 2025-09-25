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
        /* Enhanced Sidebar Transitions */
        .sidebar-transition {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .menu-item-transition {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .icon-transition {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Mobile Responsive Improvements */
        @media (max-width: 768px) {
            .sidebar-transition {
                transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
        }

        /* Smooth scroll for mobile */
        @media (max-width: 768px) {
            .overflow-y-auto {
                -webkit-overflow-scrolling: touch;
            }
        }

        /* Enhanced Menu Item Hover Effects */
        .menu-item {
            position: relative;
            overflow: hidden;
        }

        .menu-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(14, 165, 233, 0.1), transparent);
            transition: left 0.5s ease-in-out;
        }

        .menu-item:hover::before {
            left: 100%;
        }

        .menu-item:hover {
            transform: translateX(4px) scale(1.02);
            box-shadow: 0 4px 12px rgba(14, 165, 233, 0.15);
        }

        .menu-item:active {
            transform: translateX(2px) scale(0.98);
        }

        /* Enhanced Focus States */
        .menu-item:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.3), 0 4px 12px rgba(14, 165, 233, 0.15);
            transform: translateX(2px);
        }

        .menu-item:focus-visible {
            outline: 2px solid #0ea5e9;
            outline-offset: 2px;
        }

        /* Enhanced Icon Animations */
        .menu-item:hover .icon-hover {
            transform: scale(1.15) rotate(5deg);
            filter: drop-shadow(0 2px 4px rgba(14, 165, 233, 0.3));
        }

        .menu-item:active .icon-hover {
            transform: scale(1.05) rotate(-2deg);
        }

        /* Enhanced Tooltip */
        .tooltip {
            pointer-events: none;
            z-index: 9999;
            backdrop-filter: blur(8px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
        }

        /* Live Clock Styles */
        .live-clock {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border: 1px solid rgba(14, 165, 233, 0.2);
            animation: clockPulse 2s ease-in-out infinite;
        }

        @keyframes clockPulse {
            0%, 100% {
                box-shadow: 0 0 0 0 rgba(14, 165, 233, 0.4);
            }
            50% {
                box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.1);
            }
        }

        /* Button Enhancements */
        .btn-primary {
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease-in-out;
        }

        .btn-primary:hover::before {
            left: 100%;
        }

        .btn-primary:hover {
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 8px 25px rgba(14, 165, 233, 0.4);
        }

        .btn-primary:active {
            transform: translateY(0) scale(0.98);
        }

        /* Sidebar Toggle Animation */
        .sidebar-toggle:hover {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            transform: scale(1.1) rotate(180deg);
        }

        /* Responsive Enhancements */
        @media (max-width: 640px) {
            .menu-item:hover {
                transform: translateX(2px) scale(1.01);
            }
        }

        .sidebar-footer {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
        }

        /* Prevent tooltip/popover from creating horizontal scrollbars
           and make collapsed sidebar tooltips wrap safely on small screens */
        html, body {
            overflow-x: hidden;
        }

        .tooltip {
            max-width: calc(100vw - 6rem);
            overflow-wrap: anywhere;
            word-break: break-word;
        }

        /* Tooltip helper to position to the right and prevent off-screen placement */
        .tooltip-right {
            left: calc(100% + 0.5rem) !important;
            right: auto !important;
            z-index: 9999;
        }

        /* Ensure sidebar doesn't create unexpected layout shifts when collapsed */
        aside.sidebar-transition {
            -webkit-backface-visibility: hidden;
            backface-visibility: hidden;
            will-change: transform;
        }
    </style>
</head>

<body class="min-h-screen bg-white antialiased">
    <div class="flex min-h-screen" x-data="{
        sidebarCollapsed: false,
        usersExpanded: false,
        schedulesExpanded: false,
        shiftsExpanded: false,
        attendancesExpanded: false,
        mobileMenuOpen: false,
        isMobile: false,
        init() {
            this.checkMobile();
            // Don't auto-collapse on desktop, only on mobile
            this.sidebarCollapsed = this.isMobile ? true : (localStorage.getItem('sidebarCollapsed') === 'true');
            this.mobileMenuOpen = false; // Always start with mobile menu closed
            this.usersExpanded = localStorage.getItem('usersExpanded') === 'true';
            this.schedulesExpanded = localStorage.getItem('schedulesExpanded') === 'true';
            this.shiftsExpanded = localStorage.getItem('shiftsExpanded') === 'true';
            this.attendancesExpanded = localStorage.getItem('attendancesExpanded') === 'true';
            
            // Auto-collapse on mobile with debounce
            let resizeTimeout;
            window.addEventListener('resize', () => {
                clearTimeout(resizeTimeout);
                resizeTimeout = setTimeout(() => {
                    const wasMobile = this.isMobile;
                    this.checkMobile();
                    
                    // If switching from desktop to mobile
                    if (!wasMobile && this.isMobile) {
                        this.sidebarCollapsed = true;
                        this.mobileMenuOpen = false;
                    }
                    // If switching from mobile to desktop
                    else if (wasMobile && !this.isMobile) {
                        this.sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
                        this.mobileMenuOpen = false;
                    }
                }, 150);
            });
        },
        checkMobile() {
            this.isMobile = window.innerWidth < 768;
            // Force close mobile menu when checking mobile state
            if (this.isMobile) {
                this.mobileMenuOpen = false;
            }
        },
        toggleSidebar() {
            if (this.isMobile) {
                this.mobileMenuOpen = !this.mobileMenuOpen;
            } else {
                this.sidebarCollapsed = !this.sidebarCollapsed;
                localStorage.setItem('sidebarCollapsed', this.sidebarCollapsed);
            }
        },
        closeMobileMenu() {
            if (this.isMobile) {
                this.mobileMenuOpen = false;
            }
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
        },
        toggleAttendances() {
            this.attendancesExpanded = !this.attendancesExpanded;
            localStorage.setItem('attendancesExpanded', this.attendancesExpanded);
        }
    }" x-init="init()"
    @click.away="closeMobileMenu()"
    @keydown.escape="closeMobileMenu()"
    >

        <!-- Mobile Overlay -->
        <div x-show="isMobile && mobileMenuOpen" 
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-600 bg-opacity-75 z-20 md:hidden"
             @click="closeMobileMenu()"></div>

        <!-- Sidebar -->
        <aside 
            :class="{
                'w-20': sidebarCollapsed && !isMobile,
                'w-64': (!sidebarCollapsed && !isMobile) || (isMobile && mobileMenuOpen),
                'translate-x-0': (isMobile && mobileMenuOpen) || !isMobile,
                '-translate-x-full': isMobile && !mobileMenuOpen
            }"
            class="bg-white/95 backdrop-blur-lg border-r border-sky-200 sidebar-transition fixed top-0 left-0 h-screen z-30 flex flex-col shadow-xl md:shadow-none">

            <!-- Sidebar Header -->
            <div :class="sidebarCollapsed && !isMobile ? 'p-4' : 'p-4 sm:p-6'" class="border-b border-sky-200 flex-shrink-0">
                <div class="flex items-center" :class="(sidebarCollapsed && !isMobile) ? 'justify-center' : 'justify-between mb-2'">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3"
                        x-show="!sidebarCollapsed || isMobile" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
                        <div>
                            <h1 class="text-2xl font-semibold text-gray-700 tracking-tight">Admin Panel</h1>
                            <p class="text-sm text-gray-500 font-medium">v1.0.0</p>
                        </div>
                    </a>

                    <button @click="toggleSidebar()" 
                        :class="(sidebarCollapsed && !isMobile) ? 'mx-auto' : ''"
                        x-show="!isMobile"
                        class="sidebar-toggle p-2.5 rounded-xl hover:bg-sky-100 text-gray-600 menu-item-transition focus:outline-none focus:ring-2 focus:ring-sky-400 focus:ring-offset-2 group"
                        aria-label="Toggle sidebar">
                        <i :data-lucide="sidebarCollapsed ? 'panel-right-open' : 'panel-left-close'"
                            class="w-5 h-5 icon-transition group-hover:scale-110"></i>
                    </button>
                    
                    <!-- Mobile Close Button -->
                    <button @click="closeMobileMenu()" 
                        x-show="isMobile"
                        class="p-2.5 rounded-xl hover:bg-sky-100 text-gray-600 menu-item-transition focus:outline-none focus:ring-2 focus:ring-sky-400 focus:ring-offset-2 group md:hidden"
                        aria-label="Close menu">
                        <i data-lucide="x" class="w-5 h-5 icon-transition group-hover:scale-110"></i>
                    </button>
                </div>
            </div>

            <!-- Sidebar Navigation -->
            <nav class="flex-1 space-y-2 p-3 overflow-y" role="navigation">

                <!-- Dashboard -->
                <a href="{{ route('admin.dashboard') }}"
                    @click="closeMobileMenu()"
                    :class="sidebarCollapsed && !isMobile ? 'justify-center px-2 py-4 relative group' : 'px-4 py-3'"
                    class="menu-item group flex items-center text-sm font-semibold rounded-xl menu-item-transition
                    {{ request()->routeIs('admin.dashboard') ? 'bg-sky-100 text-sky-700 border border-sky-200' : 'text-gray-600 hover:bg-sky-100 hover:text-sky-700 border border-transparent hover:border-sky-200' }}"
                    :aria-label="sidebarCollapsed && !isMobile ? 'Dashboard' : ''">
                    <i data-lucide="layout-dashboard"
                        class="icon-hover w-5 h-5 icon-transition {{ request()->routeIs('admin.dashboard') ? 'text-sky-700' : 'text-gray-500 group-hover:text-sky-700' }}"
                        :class="(sidebarCollapsed && !isMobile) ? 'mr-0' : 'mr-3'"></i>
                    <span x-show="!sidebarCollapsed || isMobile" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 translate-x-2"
                        x-transition:enter-end="opacity-100 translate-x-0">Dashboard</span>

                    <div x-show="sidebarCollapsed && !isMobile"
                        class="tooltip tooltip-right absolute top-1/2 transform -translate-y-1/2 px-3 py-2 bg-gray-800 text-white text-sm rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-50">
                        Dashboard
                        <div
                            class="absolute top-1/2 left-0 transform -translate-y-1/2 -translate-x-1 w-2 h-2 bg-gray-800 rotate-45">
                        </div>
                    </div>
                </a>

                <!-- Users -->
                <div class="space-y-1 relative">
                    <button
                        @click="(sidebarCollapsed && !isMobile) ? window.location.href = '{{ route('admin.users.index') }}' : toggleUsers()"
                        :class="(sidebarCollapsed && !isMobile) ? 'justify-center px-2 py-4 relative group' : 'px-4 py-3'"
                        class="menu-item group flex items-center w-full text-sm font-semibold rounded-xl menu-item-transition
                        {{ request()->routeIs('admin.users.*') ? 'bg-sky-100 text-sky-700 border border-sky-200' : 'text-gray-600 hover:bg-sky-100 hover:text-sky-700 border border-transparent hover:border-sky-200' }}"
                        :aria-label="(sidebarCollapsed && !isMobile) ? 'Users' : ''">
                        <i data-lucide="users"
                            class="icon-hover w-5 h-5 icon-transition {{ request()->routeIs('admin.users.*') ? 'text-sky-700' : 'text-gray-500 group-hover:text-sky-700' }}"
                            :class="(sidebarCollapsed && !isMobile) ? 'mr-0' : 'mr-3'"></i>
                        <span x-show="!sidebarCollapsed || isMobile" class="flex-1 text-left" x-transition>Users</span>
                        <i x-show="(!sidebarCollapsed || isMobile)" data-lucide="chevron-right"
                            :class="usersExpanded ? 'rotate-90' : 'rotate-0'"
                            class="w-4 h-4 text-gray-500 group-hover:text-sky-700 sidebar-transition"></i>

                        <div x-show="sidebarCollapsed && !isMobile"
                            class="tooltip tooltip-right absolute top-1/2 transform -translate-y-1/2 px-3 py-2 bg-gray-800 text-white text-sm rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap">
                            Users Management
                            <div
                                class="absolute top-1/2 left-0 transform -translate-y-1/2 -translate-x-1 w-2 h-2 bg-gray-800 rotate-45">
                            </div>
                        </div>
                    </button>

                    <div x-show="usersExpanded && ((!sidebarCollapsed && !isMobile) || (isMobile && mobileMenuOpen))"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-2" class="ml-8 space-y-1">
                        <a href="{{ route('admin.users.index') }}"
                            @click="closeMobileMenu()"
                            class="flex items-center px-3 py-2 text-sm text-gray-600 hover:text-sky-700 hover:bg-sky-50 rounded-lg transition-colors {{ request()->routeIs('admin.users.index') ? 'text-sky-700 bg-sky-50' : '' }}">
                            <i data-lucide="list" class="w-4 h-4 mr-2"></i>
                            All Users
                        </a>
                        <a href="{{ route('admin.users.create') }}"
                            @click="closeMobileMenu()"
                            class="flex items-center px-3 py-2 text-sm text-gray-600 hover:text-sky-700 hover:bg-sky-50 rounded-lg transition-colors {{ request()->routeIs('admin.users.create') ? 'text-sky-700 bg-sky-50' : '' }}">
                            <i data-lucide="user-plus" class="w-4 h-4 mr-2"></i>
                            Add User
                        </a>
                    </div>
                </div>

                <!-- Schedules -->
                <div class="space-y-1 relative">
                    <button
                        @click="(sidebarCollapsed && !isMobile) ? window.location.href = '{{ route('admin.schedules.index') }}' : toggleSchedules()"
                        :class="(sidebarCollapsed && !isMobile) ? 'justify-center px-2 py-4 relative group' : 'px-4 py-3'"
                        class="menu-item group flex items-center w-full text-sm font-semibold rounded-xl menu-item-transition
                        {{ request()->routeIs('admin.schedules.*') ? 'bg-sky-100 text-sky-700 border border-sky-200' : 'text-gray-600 hover:bg-sky-100 hover:text-sky-700 border border-transparent hover:border-sky-200' }}"
                        :aria-label="sidebarCollapsed ? 'Schedules' : ''">
                        <i data-lucide="calendar"
                            class="icon-hover w-5 h-5 icon-transition {{ request()->routeIs('admin.schedules.*') ? 'text-sky-700' : 'text-gray-500 group-hover:text-sky-700' }}"
                            :class="(sidebarCollapsed && !isMobile) ? 'mr-0' : 'mr-3'"></i>
                        <span x-show="!sidebarCollapsed || isMobile" class="flex-1 text-left" x-transition>Schedules</span>
                        <i x-show="(!sidebarCollapsed || isMobile)" data-lucide="chevron-right"
                            :class="schedulesExpanded ? 'rotate-90' : 'rotate-0'"
                            class="w-4 h-4 text-gray-500 group-hover:text-sky-700 sidebar-transition"></i>

                        <div x-show="sidebarCollapsed && !isMobile"
                            class="tooltip tooltip-right absolute top-1/2 transform -translate-y-1/2 px-3 py-2 bg-gray-800 text-white text-sm rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap">
                            Schedule Management
                            <div
                                class="absolute top-1/2 left-0 transform -translate-y-1/2 -translate-x-1 w-2 h-2 bg-gray-800 rotate-45">
                            </div>
                        </div>
                    </button>

                    <div x-show="schedulesExpanded && ((!sidebarCollapsed && !isMobile) || (isMobile && mobileMenuOpen))"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-2"
                        class="ml-8 space-y-1 border-l-2 border-sky-200 border-opacity-30 pl-4">
                        <a href="{{ route('admin.schedules.index') }}"
                            @click="closeMobileMenu()"
                            class="group flex items-center px-3 py-2 text-sm font-semibold rounded-xl menu-item-transition {{ request()->routeIs('admin.schedules.index') ? 'bg-sky-100 text-sky-700' : 'text-gray-600 hover:bg-sky-100 hover:text-sky-700' }}">
                            <i data-lucide="calendar-days"
                                class="w-4 h-4 mr-3 text-gray-500 group-hover:text-sky-700"></i>
                            <span>Manage Schedules</span>
                        </a>
                        <a href="{{ route('admin.schedules.create') }}"
                            @click="closeMobileMenu()"
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
                        @click="(sidebarCollapsed && !isMobile) ? window.location.href = '{{ route('admin.shifts.index') }}' : toggleShifts()"
                        :class="(sidebarCollapsed && !isMobile) ? 'justify-center px-2 py-4 relative group' : 'px-4 py-3'"
                        class="menu-item group flex items-center w-full text-sm font-semibold rounded-xl menu-item-transition
                        {{ request()->routeIs('admin.shifts.*') ? 'bg-sky-100 text-sky-700 border border-sky-200' : 'text-gray-600 hover:bg-sky-100 hover:text-sky-700 border border-transparent hover:border-sky-200' }}"
                        :aria-label="sidebarCollapsed ? 'Shifts' : ''">
                        <i data-lucide="clock"
                            class="icon-hover w-5 h-5 icon-transition {{ request()->routeIs('admin.shifts.*') ? 'text-sky-700' : 'text-gray-500 group-hover:text-sky-700' }}"
                            :class="(sidebarCollapsed && !isMobile) ? 'mr-0' : 'mr-3'"></i>
                        <span x-show="!sidebarCollapsed || isMobile" class="flex-1 text-left" x-transition>Shifts</span>
                        <i x-show="(!sidebarCollapsed || isMobile)" data-lucide="chevron-right"
                            :class="shiftsExpanded ? 'rotate-90' : 'rotate-0'"
                            class="w-4 h-4 text-gray-500 group-hover:text-sky-700 sidebar-transition"></i>

                        <div x-show="sidebarCollapsed && !isMobile"
                            class="tooltip tooltip-right absolute top-1/2 transform -translate-y-1/2 px-3 py-2 bg-gray-800 text-white text-sm rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap">
                            Shift Management
                            <div
                                class="absolute top-1/2 left-0 transform -translate-y-1/2 -translate-x-1 w-2 h-2 bg-gray-800 rotate-45">
                            </div>
                        </div>
                    </button>

                    <div x-show="shiftsExpanded && ((!sidebarCollapsed && !isMobile) || (isMobile && mobileMenuOpen))"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-2"
                        class="ml-8 space-y-1 border-l-2 border-sky-200 border-opacity-30 pl-4">
                        <a href="{{ route('admin.shifts.index') }}"
                            @click="closeMobileMenu()"
                            class="group flex items-center px-3 py-2 text-sm font-semibold rounded-xl menu-item-transition {{ request()->routeIs('admin.shifts.index') ? 'bg-sky-100 text-sky-700' : 'text-gray-600 hover:bg-sky-100 hover:text-sky-700' }}">
                            <i data-lucide="clock-4" class="w-4 h-4 mr-3 text-gray-500 group-hover:text-sky-700"></i>
                            <span>Manage Shifts</span>
                        </a>
                        <a href="{{ route('admin.shifts.create') }}"
                            @click="closeMobileMenu()"
                            class="group flex items-center px-3 py-2 text-sm font-semibold rounded-xl menu-item-transition {{ request()->routeIs('admin.shifts.create') ? 'bg-sky-100 text-sky-700' : 'text-gray-600 hover:bg-sky-100 hover:text-sky-700' }}">
                            <i data-lucide="plus-circle"
                                class="w-4 h-4 mr-3 text-gray-500 group-hover:text-sky-700"></i>
                            <span>Create Shifts</span>
                        </a>
                    </div>
                </div>

                <!-- Attendances -->
                <div class="space-y-1 relative">
                    <button
                        @click="(sidebarCollapsed && !isMobile) ? window.location.href = '{{ route('admin.attendances.index') }}' : toggleAttendances()"
                        :class="(sidebarCollapsed && !isMobile) ? 'justify-center px-2 py-4 relative group' : 'px-4 py-3'"
                        class="menu-item group flex items-center w-full text-sm font-semibold rounded-xl menu-item-transition
                        {{ request()->routeIs('admin.attendances.*') || request()->routeIs('admin.locations.*') ? 'bg-sky-100 text-sky-700 border border-sky-200' : 'text-gray-600 hover:bg-sky-100 hover:text-sky-700 border border-transparent hover:border-sky-200' }}"
                        :aria-label="sidebarCollapsed ? 'Attendances' : ''">
                        <i data-lucide="user-check"
                            class="icon-hover w-5 h-5 icon-transition {{ request()->routeIs('admin.attendances.*') || request()->routeIs('admin.locations.*') ? 'text-sky-700' : 'text-gray-500 group-hover:text-sky-700' }}"
                            :class="(sidebarCollapsed && !isMobile) ? 'mr-0' : 'mr-3'"></i>
                        <span x-show="!sidebarCollapsed || isMobile" class="flex-1 text-left" x-transition>Attendances</span>
                        <i x-show="(!sidebarCollapsed || isMobile)" data-lucide="chevron-right"
                            :class="attendancesExpanded ? 'rotate-90' : 'rotate-0'"
                            class="w-4 h-4 text-gray-500 group-hover:text-sky-700 sidebar-transition"></i>

                        <div x-show="sidebarCollapsed && !isMobile"
                            class="tooltip tooltip-right absolute top-1/2 transform -translate-y-1/2 px-3 py-2 bg-gray-800 text-white text-sm rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap">
                            Attendance Management
                            <div
                                class="absolute top-1/2 left-0 transform -translate-y-1/2 -translate-x-1 w-2 h-2 bg-gray-800 rotate-45">
                            </div>
                        </div>
                    </button>

                    <div x-show="attendancesExpanded && ((!sidebarCollapsed && !isMobile) || (isMobile && mobileMenuOpen))"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-2"
                        class="ml-8 space-y-1 border-l-2 border-sky-200 border-opacity-30 pl-4">
                        <a href="{{ route('admin.attendances.index') }}"
                            @click="closeMobileMenu()"
                            class="group flex items-center px-3 py-2 text-sm font-semibold rounded-xl menu-item-transition {{ request()->routeIs('admin.attendances.index') ? 'bg-sky-100 text-sky-700' : 'text-gray-600 hover:bg-sky-100 hover:text-sky-700' }}">
                            <i data-lucide="user-check"
                                class="w-4 h-4 mr-3 text-gray-500 group-hover:text-sky-700"></i>
                            <span>Manage Attendances</span>
                        </a>
                        <a href="{{ route('admin.locations.create') }}"
                            @click="closeMobileMenu()"
                            class="group flex items-center px-3 py-2 text-sm font-semibold rounded-xl menu-item-transition {{ request()->routeIs('admin.locations.*') ? 'bg-sky-100 text-sky-700' : 'text-gray-600 hover:bg-sky-100 hover:text-sky-700' }}">
                            <i data-lucide="map-pin"
                                class="w-4 h-4 mr-3 text-gray-500 group-hover:text-sky-700"></i>
                            <span>Add Locations</span>
                        </a>
                    </div>
                </div>

            </nav>

            <!-- Sidebar Footer -->
            <div class="sidebar-footer p-4 border-t border-sky-200 border-opacity-30 flex-shrink-0">
                <!-- Expanded Footer (Desktop & Mobile) -->
                <div x-show="(!sidebarCollapsed && !isMobile) || (isMobile && mobileMenuOpen)"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                    class="flex items-center justify-center space-x-2 text-sm text-gray-500 bg-sky-50 rounded-xl p-3">
                    <i data-lucide="code" class="w-4 h-4"></i>
                    <span class="font-medium">Built with Laravel</span>
                </div>
                
                <!-- Collapsed Footer (Desktop Only) -->
                <div x-show="sidebarCollapsed && !isMobile"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                    class="flex items-center justify-center p-2">
                    <div class="w-8 h-8 bg-sky-100 rounded-lg flex items-center justify-center group hover:bg-sky-200 transition-colors relative">
                        <i data-lucide="code" class="w-4 h-4 text-sky-600"></i>
                        <div class="tooltip tooltip-right absolute top-1/2 transform -translate-y-1/2 px-3 py-2 bg-gray-800 text-white text-sm rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-50">
                            Built with Laravel
                            <div class="absolute top-1/2 left-0 transform -translate-y-1/2 -translate-x-1 w-2 h-2 bg-gray-800 rotate-45"></div>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-h-screen sidebar-transition"
            :class="{
                'ml-20': sidebarCollapsed && !isMobile,
                'ml-64': !sidebarCollapsed && !isMobile,
                'ml-0': isMobile
            }">

            <!-- Header -->
            <header class="bg-white/90 backdrop-blur-lg border-b border-sky-200 flex-shrink-0 sticky top-0 z-20">
                <div class="px-4 sm:px-6 py-4 flex justify-between items-center gap-4">
                    <div class="flex items-center space-x-4">
                        <!-- Mobile Menu Toggle -->
                        <button x-show="isMobile" @click="toggleSidebar()"
                            class="p-2 rounded-lg hover:bg-sky-100 text-gray-600 focus:outline-none focus:ring-2 focus:ring-sky-400 focus:ring-offset-2 md:hidden">
                            <i data-lucide="menu" class="w-6 h-6"></i>
                        </button>
                        
                        <div>
                            <h1 class="text-2xl font-semibold text-gray-7
                            00 tracking-tight">@yield('title')</h1>
                            <p class="text-base text-gray-500 mt-1">Manage your application</p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-6">
                        <!-- Live Clock -->
                        <div
                            class="hidden md:flex items-center space-x-3 px-4 py-3 live-clock rounded-2xl transition-all duration-300 hover:scale-105">
                            <div class="relative">
                                <i data-lucide="clock" class="w-5 h-5 text-sky-600"></i>
                                <div class="absolute -top-1 -right-1 w-2 h-2 bg-green-400 rounded-full animate-pulse">
                                </div>
                            </div>
                            <div class="text-center">
                                <div class="text-sm font-bold text-sky-700" id="live-time">--:--:--</div>
                                <div class="text-xs text-sky-600" id="live-date">-- --- ----</div>
                            </div>
                        </div>

                        <!-- User Profile Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open"
                                class="flex items-center space-x-3 px-4 py-3 rounded-2xl hover:bg-sky-50 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-sky-400 focus:ring-offset-2 group"
                                :class="{ 'bg-sky-50 ring-2 ring-sky-200': open }" :aria-expanded="open">
                                <div
                                    class="w-10 h-10 rounded-2xl bg-gradient-to-br from-sky-500 to-sky-600 flex items-center justify-center shadow-md group-hover:shadow-lg transition-shadow duration-300">
                                    <i data-lucide="user" class="w-5 h-5 text-white"></i>
                                </div>
                                <div class="hidden sm:block text-left">
                                    <p
                                        class="text-sm font-semibold text-gray-700 group-hover:text-sky-700 transition-colors">
                                        {{ auth()->user()->name }}</p>
                                    <p class="text-xs text-sky-600 font-medium">Administrator</p>
                                </div>
                                <i data-lucide="chevron-down"
                                    class="w-4 h-4 text-gray-500 transition-all duration-300 group-hover:text-sky-600"
                                    :class="{ 'rotate-180': open }"></i>
                            </button>

                            <!-- Modern Admin Dropdown menu -->
                            <div x-show="open" @click.away="open = false"
                                class="absolute right-0 mt-3 w-80 bg-white/95 backdrop-blur-xl rounded-3xl shadow-2xl border border-sky-100 overflow-hidden z-50"
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="transform opacity-0 scale-90 translate-y-2"
                                x-transition:enter-end="transform opacity-100 scale-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-200"
                                x-transition:leave-start="transform opacity-100 scale-100 translate-y-0"
                                x-transition:leave-end="transform opacity-0 scale-90 translate-y-2">

                                <!-- Header with gradient -->
                                <div class="bg-gradient-to-r from-sky-500 to-sky-600 px-6 py-4">
                                    <div class="flex items-center space-x-4">
                                        <div
                                            class="w-16 h-16 rounded-3xl bg-white/20 backdrop-blur-sm flex items-center justify-center border border-white/30">
                                            <i data-lucide="shield-check" class="w-8 h-8 text-white"></i>
                                        </div>
                                        <div class="flex-1">
                                            <h3 class="text-lg font-bold text-white">{{ auth()->user()->name }}</h3>
                                            <p class="text-sky-100 text-sm font-medium">{{ auth()->user()->email }}</p>
                                            <div class="flex items-center mt-1">
                                                <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse mr-2"></div>
                                                <span class="text-xs text-sky-100">Administrator</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Menu Items -->
                                <div class="p-4 space-y-2">
                                    <!-- Dashboard Link -->
                                    <a href="{{ route('admin.dashboard') }}"
                                        class="flex items-center space-x-3 px-4 py-3 rounded-2xl hover:bg-sky-50 transition-all duration-200 group">
                                        <div
                                            class="w-10 h-10 rounded-xl bg-sky-100 flex items-center justify-center group-hover:bg-sky-200 transition-colors">
                                            <i data-lucide="layout-dashboard" class="w-5 h-5 text-sky-600"></i>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-sm font-semibold text-gray-700 group-hover:text-sky-700">
                                                Dashboard</p>
                                            <p class="text-xs text-gray-500">Admin overview</p>
                                        </div>
                                        <i data-lucide="chevron-right"
                                            class="w-4 h-4 text-gray-400 group-hover:text-sky-600"></i>
                                    </a>

                                    <!-- Users Management Link -->
                                    <a href="{{ route('admin.users.index') }}"
                                        class="flex items-center space-x-3 px-4 py-3 rounded-2xl hover:bg-purple-50 transition-all duration-200 group">
                                        <div
                                            class="w-10 h-10 rounded-xl bg-purple-100 flex items-center justify-center group-hover:bg-purple-200 transition-colors">
                                            <i data-lucide="users" class="w-5 h-5 text-purple-600"></i>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-sm font-semibold text-gray-700 group-hover:text-purple-700">
                                                Users</p>
                                            <p class="text-xs text-gray-500">Manage employees</p>
                                        </div>
                                        <i data-lucide="chevron-right"
                                            class="w-4 h-4 text-gray-400 group-hover:text-purple-600"></i>
                                    </a>

                                    <!-- Schedules Link -->
                                    <a href="{{ route('admin.schedules.index') }}"
                                        class="flex items-center space-x-3 px-4 py-3 rounded-2xl hover:bg-emerald-50 transition-all duration-200 group">
                                        <div
                                            class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center group-hover:bg-emerald-200 transition-colors">
                                            <i data-lucide="calendar" class="w-5 h-5 text-emerald-600"></i>
                                        </div>
                                        <div class="flex-1">
                                            <p
                                                class="text-sm font-semibold text-gray-700 group-hover:text-emerald-700">
                                                Schedules</p>
                                            <p class="text-xs text-gray-500">Manage work schedules</p>
                                        </div>
                                        <i data-lucide="chevron-right"
                                            class="w-4 h-4 text-gray-400 group-hover:text-emerald-600"></i>
                                    </a>

                                    <!-- Attendance Link -->
                                    <a href="{{ route('admin.attendances.index') }}"
                                        class="flex items-center space-x-3 px-4 py-3 rounded-2xl hover:bg-amber-50 transition-all duration-200 group">
                                        <div
                                            class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center group-hover:bg-amber-200 transition-colors">
                                            <i data-lucide="user-check" class="w-5 h-5 text-amber-600"></i>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-sm font-semibold text-gray-700 group-hover:text-amber-700">
                                                Attendance</p>
                                            <p class="text-xs text-gray-500">View attendance records</p>
                                        </div>
                                        <i data-lucide="chevron-right"
                                            class="w-4 h-4 text-gray-400 group-hover:text-amber-600"></i>
                                    </a>
                                    <!-- Activity Logs Link -->
                                    <a href="{{ route('admin.activity-logs.index') }}"
                                        class="flex items-center space-x-3 px-4 py-3 rounded-2xl hover:bg-indigo-50 transition-all duration-200 group">
                                        <div
                                            class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center group-hover:bg-indigo-200 transition-colors">
                                            <i data-lucide="activity" class="w-5 h-5 text-indigo-600"></i>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-sm font-semibold text-gray-700 group-hover:text-indigo-700">
                                                Activity Logs</p>
                                            <p class="text-xs text-gray-500">View system activity logs</p>
                                        </div>
                                        <i data-lucide="chevron-right"
                                            class="w-4 h-4 text-gray-400 group-hover:text-indigo-600"></i>
                                    </a>
                                    <!-- Security Management Link -->
                                    <a href="{{ route('admin.security.index') }}"
                                        class="flex items-center space-x-3 px-4 py-3 rounded-2xl hover:bg-red-50 transition-all duration-200 group">
                                        <div
                                            class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center group-hover:bg-red-200 transition-colors">
                                            <i data-lucide="shield-alert" class="w-5 h-5 text-red-600"></i>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-sm font-semibold text-gray-700 group-hover:text-red-700">
                                                Security</p>
                                            <p class="text-xs text-gray-500">Manage system security</p>
                                        </div>
                                        <i data-lucide="chevron-right"
                                            class="w-4 h-4 text-gray-400 group-hover:text-red-600"></i>
                                    </a>
                                </div>

                                <!-- Divider -->
                                <div class="border-t border-sky-100 mx-4"></div>

                                <!-- Logout Section -->
                                <div class="p-4">
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="flex items-center space-x-3 w-full px-4 py-3 text-red-600 hover:bg-red-50 rounded-2xl transition-all duration-200 focus:outline-none focus:bg-red-50 group">
                                            <div
                                                class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center group-hover:bg-red-200 transition-colors">
                                                <i data-lucide="log-out" class="w-5 h-5 text-red-600"></i>
                                            </div>
                                            <div class="flex-1 text-left">
                                                <p class="text-sm font-semibold">Sign out</p>
                                                <p class="text-xs text-red-500">End admin session</p>
                                            </div>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
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

            // Initialize live clock
            initializeLiveClock();
        });

        // Re-initialize icons when Alpine updates the DOM
        document.addEventListener('alpine:init', () => {
            setTimeout(() => {
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            }, 100);
        });

        // Live Clock with Indonesian Time Format
        function initializeLiveClock() {
            function updateClock() {
                const now = new Date();

                // Indonesian time options
                const timeOptions = {
                    timeZone: 'Asia/Jakarta',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    hour12: false
                };

                const dateOptions = {
                    timeZone: 'Asia/Jakarta',
                    weekday: 'short',
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                };

                // Format time and date in Indonesian locale
                const timeString = now.toLocaleTimeString('id-ID', timeOptions);
                const dateString = now.toLocaleDateString('id-ID', dateOptions);

                // Update DOM elements
                const timeElement = document.getElementById('live-time');
                const dateElement = document.getElementById('live-date');

                if (timeElement) {
                    timeElement.textContent = timeString;
                    // Add subtle animation on second change
                    timeElement.style.transform = 'scale(1.05)';
                    setTimeout(() => {
                        timeElement.style.transform = 'scale(1)';
                    }, 150);
                }

                if (dateElement) {
                    dateElement.textContent = dateString;
                }
            }

            // Update immediately and then every second
            updateClock();
            setInterval(updateClock, 1000);
        }

        // Enhanced keyboard navigation
        document.addEventListener('keydown', function(e) {
            // ESC key to close dropdowns and remove focus
            if (e.key === 'Escape') {
                document.activeElement.blur();
                // Close mobile menu if open
                const mobileMenuToggle = document.querySelector('[x-data]');
                if (mobileMenuToggle && window.innerWidth < 768) {
                    mobileMenuToggle.__x.$data.mobileMenuOpen = false;
                }
            }

            // Alt + S to toggle sidebar
            if (e.altKey && e.key === 's') {
                e.preventDefault();
                const sidebarToggle = document.querySelector('.sidebar-toggle');
                if (sidebarToggle) {
                    sidebarToggle.click();
                }
            }

            // Alt + M to toggle mobile menu
            if (e.altKey && e.key === 'm' && window.innerWidth < 768) {
                e.preventDefault();
                const mobileToggle = document.querySelector('[x-show="isMobile"]');
                if (mobileToggle) {
                    mobileToggle.click();
                }
            }
        });

        // Add smooth scrolling for better UX
        document.documentElement.style.scrollBehavior = 'smooth';

        // Add loading state management
        window.addEventListener('beforeunload', function() {
            document.body.style.opacity = '0.7';
            document.body.style.pointerEvents = 'none';
        });
    </script>

    @stack('scripts')
</body>

</html>