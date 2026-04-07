<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <title>Admin - @yield('title')</title>
        <link rel="icon" href="" />
        <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
        <script>
            // Initialize Alpine.js store for global state
            document.addEventListener('alpine:init', () => {
                Alpine.store('badges', {
                    izin: 0,
                    cuti: 0,
                    isLoading: false,
                    lastUpdate: null,
                    errorCount: 0,

                    init() {
                        // Start polling when component initializes
                        this.startPolling();
                    },

                    startPolling() {
                        // Initial poll immediately
                        this.fetchCounts();

                        // Set up interval for polling (every 10 seconds)
                        setInterval(() => this.fetchCounts(), 10000);
                    },

                    async fetchCounts() {
                        // Skip if already loading
                        if (this.isLoading) return;

                        this.isLoading = true;

                        try {
                            const response = await fetch('{{ route('admin.attendances.pending-counts') }}', {
                                method: 'GET',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    Accept: 'application/json',
                                },
                                credentials: 'same-origin',
                            });

                            if (!response.ok) {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }

                            const data = await response.json();

                            // Update counts with animation
                            const oldIzin = this.izin;
                            const oldCuti = this.cuti;

                            this.izin = parseInt(data.izin || 0);
                            this.cuti = parseInt(data.cuti || 0);
                            this.lastUpdate = new Date();
                            this.errorCount = 0; // Reset error count on success

                            // Log if counts changed
                            if (oldIzin !== this.izin || oldCuti !== this.cuti) {
                                console.log('Badge counts updated:', { izin: this.izin, cuti: this.cuti });
                            }
                        } catch (error) {
                            this.errorCount++;
                            console.error('Error fetching badge counts:', error);

                            // Stop polling after 3 consecutive errors
                            if (this.errorCount >= 3) {
                                console.warn('Badge polling stopped after 3 consecutive errors');
                            }
                        } finally {
                            this.isLoading = false;
                        }
                    },
                });
            });
        </script>
        <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('styles')
        <style>
            ::-webkit-scrollbar {
                width: 5px;
            }

            ::-webkit-scrollbar-thumb {
                background-color: #e0f2fe;
                border-radius: 10px;
            }

            ::-webkit-scrollbar-thumb:hover {
                background-color: #0ea5e9;
            }

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
                box-shadow:
                    0 0 0 3px rgba(14, 165, 233, 0.3),
                    0 4px 12px rgba(14, 165, 233, 0.15);
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
                0%,
                100% {
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
            html,
            body {
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

            /* Global responsive helpers for all admin pages */
            @media (max-width: 768px) {
                /* Make any table within admin content scroll horizontally instead of breaking layout */
                .admin-content table {
                    display: block;
                    width: 100%;
                    overflow-x: auto;
                    -webkit-overflow-scrolling: touch;
                }
                .admin-content thead,
                .admin-content tbody,
                .admin-content th,
                .admin-content td,
                .admin-content tr {
                    white-space: nowrap;
                }

                /* Prevent large blocks from causing side scroll */
                .admin-content img,
                .admin-content video,
                .admin-content canvas,
                .admin-content iframe {
                    max-width: 100%;
                    height: auto;
                }

                /* Utility to keep cards and sections nicely spaced on mobile */
                .admin-content .card,
                .admin-content .panel,
                .admin-content .section {
                    border-radius: 0.75rem;
                }
            }
        </style>
    </head>

    <body class="min-h-screen bg-white antialiased">
        <div
            class="flex min-h-screen"
            x-data="{
                sidebarCollapsed: false,
                usersExpanded: false,
                schedulesExpanded: false,
                shiftsExpanded: false,
                attendancesExpanded: false,
                locationsExpanded: false,
                mobileMenuOpen: false,
                isMobile: false,
                init() {
                    this.checkMobile()
                    // Don't auto-collapse on desktop, only on mobile
                    this.sidebarCollapsed = this.isMobile
                        ? true
                        : localStorage.getItem('sidebarCollapsed') === 'true'
                    this.mobileMenuOpen = false // Always start with mobile menu closed
                    this.usersExpanded = localStorage.getItem('usersExpanded') === 'true'
                    this.schedulesExpanded =
                        localStorage.getItem('schedulesExpanded') === 'true'
                    this.shiftsExpanded = localStorage.getItem('shiftsExpanded') === 'true'
                    this.attendancesExpanded =
                        localStorage.getItem('attendancesExpanded') === 'true'
                    this.locationsExpanded =
                        localStorage.getItem('locationsExpanded') === 'true'

                    // Auto-collapse on mobile with debounce
                    let resizeTimeout
                    window.addEventListener('resize', () => {
                        clearTimeout(resizeTimeout)
                        resizeTimeout = setTimeout(() => {
                            const wasMobile = this.isMobile
                            this.checkMobile()

                            // If switching from desktop to mobile
                            if (! wasMobile && this.isMobile) {
                                this.sidebarCollapsed = true
                                this.mobileMenuOpen = false
                            }
                            // If switching from mobile to desktop
                            else if (wasMobile && ! this.isMobile) {
                                this.sidebarCollapsed =
                                    localStorage.getItem('sidebarCollapsed') === 'true'
                                this.mobileMenuOpen = false
                            }
                        }, 150)
                    })
                },
                checkMobile() {
                    this.isMobile = window.innerWidth < 768
                    // Force close mobile menu when checking mobile state
                    if (this.isMobile) {
                        this.mobileMenuOpen = false
                    }
                },
                toggleSidebar() {
                    if (this.isMobile) {
                        this.mobileMenuOpen = ! this.mobileMenuOpen
                    } else {
                        this.sidebarCollapsed = ! this.sidebarCollapsed
                        localStorage.setItem('sidebarCollapsed', this.sidebarCollapsed)
                    }
                },
                closeMobileMenu() {
                    if (this.isMobile) {
                        this.mobileMenuOpen = false
                    }
                },
                toggleUsers() {
                    this.usersExpanded = ! this.usersExpanded
                    localStorage.setItem('usersExpanded', this.usersExpanded)
                },
                toggleSchedules() {
                    this.schedulesExpanded = ! this.schedulesExpanded
                    localStorage.setItem('schedulesExpanded', this.schedulesExpanded)
                },
                toggleShifts() {
                    this.shiftsExpanded = ! this.shiftsExpanded
                    localStorage.setItem('shiftsExpanded', this.shiftsExpanded)
                },
                toggleAttendances() {
                    this.attendancesExpanded = ! this.attendancesExpanded
                    localStorage.setItem('attendancesExpanded', this.attendancesExpanded)
                },
                toggleLocations() {
                    this.locationsExpanded = ! this.locationsExpanded
                    localStorage.setItem('locationsExpanded', this.locationsExpanded)
                },
            }"
            x-init="init()"
            @click.away="closeMobileMenu()"
            @keydown.escape="closeMobileMenu()"
        >
            <!-- Mobile Overlay -->
            <div
                x-show="isMobile && mobileMenuOpen"
                x-transition:enter="transition-opacity duration-300 ease-linear"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity duration-300 ease-linear"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 z-20 bg-gray-800/50 md:hidden"
                @click="closeMobileMenu()"
            ></div>

            <!-- Sidebar -->
            <aside
                :class="{
                'w-20': sidebarCollapsed && !isMobile,
                'w-64': (!sidebarCollapsed && !isMobile) || (isMobile && mobileMenuOpen),
                'translate-x-0': (isMobile && mobileMenuOpen) || !isMobile,
                '-translate-x-full': isMobile && !mobileMenuOpen
            }"
                class="sidebar-transition fixed top-0 left-0 z-30 flex h-screen flex-col border-r border-sky-200 bg-white/95 shadow-xl backdrop-blur-lg md:shadow-none"
            >
                <!-- Sidebar Header -->
                <div
                    :class="sidebarCollapsed && !isMobile ? 'p-4' : 'p-4 sm:p-6'"
                    class="flex-shrink-0 border-b border-sky-200"
                >
                    <div
                        class="flex items-center"
                        :class="(sidebarCollapsed && !isMobile) ? 'justify-center' : 'justify-between mb-2'"
                    >
                        <a
                            href="{{ route('admin.dashboard') }}"
                            class="group relative flex w-full items-center overflow-hidden"
                            x-show="!sidebarCollapsed || isMobile"
                            x-transition:enter="transition duration-300 ease-out"
                            x-transition:enter-start="translate-x-[-10px] opacity-0"
                            x-transition:enter-end="translate-x-0 opacity-100"
                        >
                            <div class="flex w-full items-center justify-start rounded-xl p-1.5 transition-all duration-300 group-hover:bg-slate-50">
                                <img src="{{ asset('Logo-Lintasarta-new.webp') }}" alt="Lintasarta" class="h-9 w-auto object-contain drop-shadow-sm transition-transform duration-300 group-hover:scale-105" />
                                <div class="ml-3 flex flex-col border-l-2 border-sky-500 pl-3">
                                    <span class="text-xs font-bold tracking-widest text-slate-800 uppercase">Portal</span>
                                    <span class="text-[10px] font-medium tracking-widest text-slate-500 uppercase">Admin</span>
                                </div>
                            </div>
                        </a>

                        <button
                            @click="toggleSidebar()"
                            :class="(sidebarCollapsed && !isMobile) ? 'mx-auto' : ''"
                            x-show="!isMobile"
                            class="sidebar-toggle menu-item-transition group rounded-xl p-2.5 text-gray-600 hover:bg-sky-100 focus:ring-2 focus:ring-sky-400 focus:ring-offset-2 focus:outline-none"
                            aria-label="Toggle sidebar"
                        >
                            <i
                                :data-lucide="sidebarCollapsed ? 'panel-right-open' : 'panel-left-close'"
                                class="icon-transition h-5 w-5 group-hover:scale-110"
                            ></i>
                        </button>

                        <!-- Mobile Close Button -->
                        <button
                            @click="closeMobileMenu()"
                            x-show="isMobile"
                            class="menu-item-transition group rounded-xl p-2.5 text-gray-600 hover:bg-sky-100 focus:ring-2 focus:ring-sky-400 focus:ring-offset-2 focus:outline-none md:hidden"
                            aria-label="Close menu"
                        >
                            <i data-lucide="x" class="icon-transition h-5 w-5 group-hover:scale-110"></i>
                        </button>
                    </div>
                </div>

                <!-- Sidebar Navigation -->
                <nav class="flex-1 space-y-2 overflow-y-auto p-3" role="navigation">
                    <!-- Dashboard -->
                    <a
                        href="{{ route('admin.dashboard') }}"
                        @click="closeMobileMenu()"
                        :class="sidebarCollapsed && !isMobile ? 'justify-center px-2 py-4 relative group' : 'px-4 py-3'"
                        class="menu-item group menu-item-transition {{ request()->routeIs('admin.dashboard') ? 'border border-sky-200 bg-sky-100 text-sky-700' : 'border border-transparent text-gray-600 hover:border-sky-200 hover:bg-sky-100 hover:text-sky-700' }} flex items-center rounded-xl text-sm font-semibold"
                        :aria-label="sidebarCollapsed && !isMobile ? 'Dashboard' : ''"
                    >
                        <i
                            data-lucide="layout-dashboard"
                            class="icon-hover icon-transition {{ request()->routeIs('admin.dashboard') ? 'text-sky-700' : 'text-gray-500 group-hover:text-sky-700' }} h-5 w-5"
                            :class="(sidebarCollapsed && !isMobile) ? 'mr-0' : 'mr-3'"
                        ></i>
                        <span
                            x-show="!sidebarCollapsed || isMobile"
                            x-transition:enter="transition duration-200 ease-out"
                            x-transition:enter-start="translate-x-2 opacity-0"
                            x-transition:enter-end="translate-x-0 opacity-100"
                        >
                            Dashboard
                        </span>

                        <div
                            x-show="sidebarCollapsed && !isMobile"
                            class="tooltip tooltip-right absolute top-1/2 z-50 -translate-y-1/2 transform rounded-lg bg-gray-800 px-3 py-2 text-sm whitespace-nowrap text-white opacity-0 transition-opacity duration-200 group-hover:opacity-100"
                        >
                            Dashboard
                            <div
                                class="absolute top-1/2 left-0 h-2 w-2 -translate-x-1 -translate-y-1/2 rotate-45 transform bg-gray-800"
                            ></div>
                        </div>
                    </a>

                    <!-- Users -->
                    <div class="relative space-y-1">
                        <button
                            @click="(sidebarCollapsed && !isMobile) ? window.location.href = '{{ route('admin.users.index') }}' : toggleUsers()"
                            :class="(sidebarCollapsed && !isMobile) ? 'justify-center px-2 py-4 relative group' : 'px-4 py-3'"
                            class="menu-item group menu-item-transition {{ request()->routeIs('admin.users.*') ? 'border border-sky-200 bg-sky-100 text-sky-700' : 'border border-transparent text-gray-600 hover:border-sky-200 hover:bg-sky-100 hover:text-sky-700' }} flex w-full items-center rounded-xl text-sm font-semibold"
                            :aria-label="(sidebarCollapsed && !isMobile) ? 'Users' : ''"
                        >
                            <i
                                data-lucide="users"
                                class="icon-hover icon-transition {{ request()->routeIs('admin.users.*') ? 'text-sky-700' : 'text-gray-500 group-hover:text-sky-700' }} h-5 w-5"
                                :class="(sidebarCollapsed && !isMobile) ? 'mr-0' : 'mr-3'"
                            ></i>
                            <span x-show="!sidebarCollapsed || isMobile" class="flex-1 text-left" x-transition>
                                Users
                            </span>
                            <i
                                x-show="! sidebarCollapsed || isMobile"
                                data-lucide="chevron-right"
                                :class="usersExpanded ? 'rotate-90' : 'rotate-0'"
                                class="sidebar-transition h-4 w-4 text-gray-500 group-hover:text-sky-700"
                            ></i>

                            <div
                                x-show="sidebarCollapsed"
                                class="tooltip tooltip-right absolute top-1/2 -translate-y-1/2 transform rounded-lg bg-gray-800 px-3 py-2 text-sm whitespace-nowrap text-white opacity-0 transition-opacity duration-200 group-hover:opacity-100"
                            >
                                Users Management
                                <div
                                    class="absolute top-1/2 left-0 h-2 w-2 -translate-x-1 -translate-y-1/2 rotate-45 transform bg-gray-800"
                                ></div>
                            </div>
                        </button>

                        <div
                            x-show="
                                usersExpanded &&
                                    ((! sidebarCollapsed && ! isMobile) || (isMobile && mobileMenuOpen))
                            "
                            x-transition:enter="transition duration-200 ease-out"
                            x-transition:enter-start="-translate-y-2 opacity-0"
                            x-transition:enter-end="translate-y-0 opacity-100"
                            x-transition:leave="transition duration-150 ease-in"
                            x-transition:leave-start="translate-y-0 opacity-100"
                            x-transition:leave-end="-translate-y-2 opacity-0"
                            class="border-opacity-30 ml-8 space-y-1 border-l-2 border-sky-200 pl-4"
                        >
                            <a
                                href="{{ route('admin.users.index') }}"
                                @click="closeMobileMenu()"
                                class="group menu-item-transition {{ request()->routeIs('admin.users.index') ? 'bg-sky-100 text-sky-700' : 'text-gray-600 hover:bg-sky-100 hover:text-sky-700' }} flex items-center rounded-xl px-3 py-2 text-sm font-semibold"
                            >
                                <i data-lucide="users" class="mr-3 h-4 w-4 text-gray-500 group-hover:text-sky-700"></i>
                                <span>Manage Users</span>
                            </a>
                            <a
                                href="{{ route('admin.users.create') }}"
                                @click="closeMobileMenu()"
                                class="group menu-item-transition {{ request()->routeIs('admin.users.create') ? 'bg-sky-100 text-sky-700' : 'text-gray-600 hover:bg-sky-100 hover:text-sky-700' }} flex items-center rounded-xl px-3 py-2 text-sm font-semibold"
                            >
                                <i
                                    data-lucide="user-plus"
                                    class="mr-3 h-4 w-4 text-gray-500 group-hover:text-sky-700"
                                ></i>
                                <span>Add User</span>
                            </a>
                        </div>
                    </div>

                    <!-- Shifts -->
                    <div class="relative space-y-1">
                        <button
                            @click="(sidebarCollapsed && !isMobile) ? window.location.href = '{{ route('admin.shifts.index') }}' : toggleShifts()"
                            :class="(sidebarCollapsed && !isMobile) ? 'justify-center px-2 py-4 relative group' : 'px-4 py-3'"
                            class="menu-item group menu-item-transition {{ request()->routeIs('admin.shifts.*') ? 'border border-sky-200 bg-sky-100 text-sky-700' : 'border border-transparent text-gray-600 hover:border-sky-200 hover:bg-sky-100 hover:text-sky-700' }} flex w-full items-center rounded-xl text-sm font-semibold"
                            :aria-label="sidebarCollapsed ? 'Shifts' : ''"
                        >
                            <i
                                data-lucide="clock"
                                class="icon-hover icon-transition {{ request()->routeIs('admin.shifts.*') ? 'text-sky-700' : 'text-gray-500 group-hover:text-sky-700' }} h-5 w-5"
                                :class="(sidebarCollapsed && !isMobile) ? 'mr-0' : 'mr-3'"
                            ></i>
                            <span x-show="!sidebarCollapsed || isMobile" class="flex-1 text-left" x-transition>
                                Shifts
                            </span>
                            <i
                                x-show="! sidebarCollapsed || isMobile"
                                data-lucide="chevron-right"
                                :class="shiftsExpanded ? 'rotate-90' : 'rotate-0'"
                                class="sidebar-transition h-4 w-4 text-gray-500 group-hover:text-sky-700"
                            ></i>

                            <div
                                x-show="sidebarCollapsed"
                                class="tooltip tooltip-right absolute top-1/2 -translate-y-1/2 transform rounded-lg bg-gray-800 px-3 py-2 text-sm whitespace-nowrap text-white opacity-0 transition-opacity duration-200 group-hover:opacity-100"
                            >
                                Shift Management
                                <div
                                    class="absolute top-1/2 left-0 h-2 w-2 -translate-x-1 -translate-y-1/2 rotate-45 transform bg-gray-800"
                                ></div>
                            </div>
                        </button>

                        <div
                            x-show="
                                shiftsExpanded &&
                                    ((! sidebarCollapsed && ! isMobile) || (isMobile && mobileMenuOpen))
                            "
                            x-transition:enter="transition duration-200 ease-out"
                            x-transition:enter-start="-translate-y-2 opacity-0"
                            x-transition:enter-end="translate-y-0 opacity-100"
                            x-transition:leave="transition duration-150 ease-in"
                            x-transition:leave-start="translate-y-0 opacity-100"
                            x-transition:leave-end="-translate-y-2 opacity-0"
                            class="border-opacity-30 ml-8 space-y-1 border-l-2 border-sky-200 pl-4"
                        >
                            <a
                                href="{{ route('admin.shifts.index') }}"
                                @click="closeMobileMenu()"
                                class="group menu-item-transition {{ request()->routeIs('admin.shifts.index') ? 'bg-sky-100 text-sky-700' : 'text-gray-600 hover:bg-sky-100 hover:text-sky-700' }} flex items-center rounded-xl px-3 py-2 text-sm font-semibold"
                            >
                                <i
                                    data-lucide="clock-4"
                                    class="mr-3 h-4 w-4 text-gray-500 group-hover:text-sky-700"
                                ></i>
                                <span>Manage Shifts</span>
                            </a>
                            <a
                                href="{{ route('admin.shifts.create') }}"
                                @click="closeMobileMenu()"
                                class="group menu-item-transition {{ request()->routeIs('admin.shifts.create') ? 'bg-sky-100 text-sky-700' : 'text-gray-600 hover:bg-sky-100 hover:text-sky-700' }} flex items-center rounded-xl px-3 py-2 text-sm font-semibold"
                            >
                                <i
                                    data-lucide="plus-circle"
                                    class="mr-3 h-4 w-4 text-gray-500 group-hover:text-sky-700"
                                ></i>
                                <span>Add Shift</span>
                            </a>
                        </div>
                    </div>

                    <!-- Schedules -->
                    <div class="relative space-y-1">
                        <button
                            @click="(sidebarCollapsed && !isMobile) ? window.location.href = '{{ route('admin.schedules.index') }}' : toggleSchedules()"
                            :class="(sidebarCollapsed && !isMobile) ? 'justify-center px-2 py-4 relative group' : 'px-4 py-3'"
                            class="menu-item group menu-item-transition {{ request()->routeIs('admin.schedules.*') ? 'border border-sky-200 bg-sky-100 text-sky-700' : 'border border-transparent text-gray-600 hover:border-sky-200 hover:bg-sky-100 hover:text-sky-700' }} flex w-full items-center rounded-xl text-sm font-semibold"
                            :aria-label="sidebarCollapsed ? 'Schedules' : ''"
                        >
                            <i
                                data-lucide="calendar"
                                class="icon-hover icon-transition {{ request()->routeIs('admin.schedules.*') ? 'text-sky-700' : 'text-gray-500 group-hover:text-sky-700' }} h-5 w-5"
                                :class="(sidebarCollapsed && !isMobile) ? 'mr-0' : 'mr-3'"
                            ></i>
                            <span x-show="!sidebarCollapsed || isMobile" class="flex-1 text-left" x-transition>
                                Schedules
                            </span>
                            <i
                                x-show="! sidebarCollapsed || isMobile"
                                data-lucide="chevron-right"
                                :class="schedulesExpanded ? 'rotate-90' : 'rotate-0'"
                                class="sidebar-transition h-4 w-4 text-gray-500 group-hover:text-sky-700"
                            ></i>

                            <div
                                x-show="sidebarCollapsed"
                                class="tooltip tooltip-right absolute top-1/2 -translate-y-1/2 transform rounded-lg bg-gray-800 px-3 py-2 text-sm whitespace-nowrap text-white opacity-0 transition-opacity duration-200 group-hover:opacity-100"
                            >
                                Schedule Management
                                <div
                                    class="absolute top-1/2 left-0 h-2 w-2 -translate-x-1 -translate-y-1/2 rotate-45 transform bg-gray-800"
                                ></div>
                            </div>
                        </button>

                        <div
                            x-show="
                                schedulesExpanded &&
                                    ((! sidebarCollapsed && ! isMobile) || (isMobile && mobileMenuOpen))
                            "
                            x-transition:enter="transition duration-200 ease-out"
                            x-transition:enter-start="-translate-y-2 opacity-0"
                            x-transition:enter-end="translate-y-0 opacity-100"
                            x-transition:leave="transition duration-150 ease-in"
                            x-transition:leave-start="translate-y-0 opacity-100"
                            x-transition:leave-end="-translate-y-2 opacity-0"
                            class="border-opacity-30 ml-8 space-y-1 border-l-2 border-sky-200 pl-4"
                        >
                            <a
                                href="{{ route('admin.schedules.index') }}"
                                @click="closeMobileMenu()"
                                class="group menu-item-transition {{ request()->routeIs('admin.schedules.index') ? 'bg-sky-100 text-sky-700' : 'text-gray-600 hover:bg-sky-100 hover:text-sky-700' }} flex items-center rounded-xl px-3 py-2 text-sm font-semibold"
                            >
                                <i
                                    data-lucide="calendar-days"
                                    class="mr-3 h-4 w-4 text-gray-500 group-hover:text-sky-700"
                                ></i>
                                <span>Manage Schedules</span>
                            </a>
                            <a
                                href="{{ route('admin.schedules.create') }}"
                                @click="closeMobileMenu()"
                                class="group menu-item-transition {{ request()->routeIs('admin.schedules.create') ? 'bg-sky-100 text-sky-700' : 'text-gray-600 hover:bg-sky-100 hover:text-sky-700' }} flex items-center rounded-xl px-3 py-2 text-sm font-semibold"
                            >
                                <i
                                    data-lucide="calendar-plus"
                                    class="mr-3 h-4 w-4 text-gray-500 group-hover:text-sky-700"
                                ></i>
                                <span>Add Schedules</span>
                            </a>
                            <a
                                href="{{ route('admin.calendar.view') }}"
                                @click="closeMobileMenu()"
                                class="group menu-item-transition {{ request()->routeIs('admin.calendar.view') ? 'bg-sky-100 text-sky-700' : 'text-gray-600 hover:bg-sky-100 hover:text-sky-700' }} flex items-center rounded-xl px-3 py-2 text-sm font-semibold"
                            >
                                <i
                                    data-lucide="calendar-range"
                                    class="mr-3 h-4 w-4 text-gray-500 group-hover:text-sky-700"
                                ></i>
                                <span>Schedules Table</span>
                            </a>
                        </div>
                    </div>

                    <!-- Locations -->
                    <div class="relative space-y-1">
                        <button
                            @click="(sidebarCollapsed && !isMobile) ? window.location.href = '{{ route('admin.locations.index') }}' : toggleLocations()"
                            :class="(sidebarCollapsed && !isMobile) ? 'justify-center px-2 py-4 relative group' : 'px-4 py-3'"
                            class="menu-item group menu-item-transition {{ request()->routeIs('admin.locations.*') ? 'border border-sky-200 bg-sky-100 text-sky-700' : 'border border-transparent text-gray-600 hover:border-sky-200 hover:bg-sky-100 hover:text-sky-700' }} flex w-full items-center rounded-xl text-sm font-semibold"
                            :aria-label="sidebarCollapsed ? 'Locations' : ''"
                        >
                            <i
                                data-lucide="map-pin"
                                class="icon-hover icon-transition {{ request()->routeIs('admin.locations.*') ? 'text-sky-700' : 'text-gray-500 group-hover:text-sky-700' }} h-5 w-5"
                                :class="(sidebarCollapsed && !isMobile) ? 'mr-0' : 'mr-3'"
                            ></i>
                            <span x-show="!sidebarCollapsed || isMobile" class="flex-1 text-left" x-transition>
                                Locations
                            </span>
                            <i
                                x-show="! sidebarCollapsed || isMobile"
                                data-lucide="chevron-right"
                                :class="locationsExpanded ? 'rotate-90' : 'rotate-0'"
                                class="sidebar-transition h-4 w-4 text-gray-500 group-hover:text-sky-700"
                            ></i>

                            <div
                                x-show="sidebarCollapsed"
                                class="tooltip tooltip-right absolute top-1/2 -translate-y-1/2 transform rounded-lg bg-gray-800 px-3 py-2 text-sm whitespace-nowrap text-white opacity-0 transition-opacity duration-200 group-hover:opacity-100"
                            >
                                Location Management
                                <div
                                    class="absolute top-1/2 left-0 h-2 w-2 -translate-x-1 -translate-y-1/2 rotate-45 transform bg-gray-800"
                                ></div>
                            </div>
                        </button>

                        <div
                            x-show="
                                locationsExpanded &&
                                    ((! sidebarCollapsed && ! isMobile) || (isMobile && mobileMenuOpen))
                            "
                            x-transition:enter="transition duration-200 ease-out"
                            x-transition:enter-start="-translate-y-2 opacity-0"
                            x-transition:enter-end="translate-y-0 opacity-100"
                            x-transition:leave="transition duration-150 ease-in"
                            x-transition:leave-start="translate-y-0 opacity-100"
                            x-transition:leave-end="-translate-y-2 opacity-0"
                            class="border-opacity-30 ml-8 space-y-1 border-l-2 border-sky-200 pl-4"
                        >
                            <a
                                href="{{ route('admin.locations.index') }}"
                                @click="closeMobileMenu()"
                                class="group menu-item-transition {{ request()->routeIs('admin.locations.index') ? 'bg-sky-100 text-sky-700' : 'text-gray-600 hover:bg-sky-100 hover:text-sky-700' }} flex items-center rounded-xl px-3 py-2 text-sm font-semibold"
                            >
                                <i data-lucide="map" class="mr-3 h-4 w-4 text-gray-500 group-hover:text-sky-700"></i>
                                <span>Manage Locations</span>
                            </a>
                            <a
                                href="{{ route('admin.locations.create') }}"
                                @click="closeMobileMenu()"
                                class="group menu-item-transition {{ request()->routeIs('admin.locations.create') ? 'bg-sky-100 text-sky-700' : 'text-gray-600 hover:bg-sky-100 hover:text-sky-700' }} flex items-center rounded-xl px-3 py-2 text-sm font-semibold"
                            >
                                <i
                                    data-lucide="plus-circle"
                                    class="mr-3 h-4 w-4 text-gray-500 group-hover:text-sky-700"
                                ></i>
                                <span>Add Location</span>
                            </a>
                        </div>
                    </div>

                    <!-- Attendances -->
                    <div class="relative space-y-1">
                        <button
                            @click="(sidebarCollapsed && !isMobile) ? window.location.href = '{{ route('admin.attendances.index') }}' : toggleAttendances()"
                            :class="(sidebarCollapsed && !isMobile) ? 'justify-center px-2 py-4 relative group' : 'px-4 py-3'"
                            class="menu-item group menu-item-transition {{ request()->routeIs('admin.attendances.*') ? 'border border-sky-200 bg-sky-100 text-sky-700' : 'border border-transparent text-gray-600 hover:border-sky-200 hover:bg-sky-100 hover:text-sky-700' }} flex w-full items-center rounded-xl text-sm font-semibold"
                            :aria-label="sidebarCollapsed ? 'Attendances' : ''"
                        >
                            <i
                                data-lucide="user-check"
                                class="icon-hover icon-transition {{ request()->routeIs('admin.attendances.*') ? 'text-sky-700' : 'text-gray-500 group-hover:text-sky-700' }} h-5 w-5"
                                :class="(sidebarCollapsed && !isMobile) ? 'mr-0' : 'mr-3'"
                            ></i>
                            <span x-show="!sidebarCollapsed || isMobile" class="flex-1 text-left" x-transition>
                                Attendances
                            </span>
                            <i
                                x-show="! sidebarCollapsed || isMobile"
                                data-lucide="chevron-right"
                                :class="attendancesExpanded ? 'rotate-90' : 'rotate-0'"
                                class="sidebar-transition h-4 w-4 text-gray-500 group-hover:text-sky-700"
                            ></i>

                            <div
                                x-show="sidebarCollapsed"
                                class="tooltip tooltip-right absolute top-1/2 -translate-y-1/2 transform rounded-lg bg-gray-800 px-3 py-2 text-sm whitespace-nowrap text-white opacity-0 transition-opacity duration-200 group-hover:opacity-100"
                            >
                                Attendance Management
                                <div
                                    class="absolute top-1/2 left-0 h-2 w-2 -translate-x-1 -translate-y-1/2 rotate-45 transform bg-gray-800"
                                ></div>
                            </div>
                        </button>

                        <div
                            x-show="
                                attendancesExpanded &&
                                    ((! sidebarCollapsed && ! isMobile) || (isMobile && mobileMenuOpen))
                            "
                            x-transition:enter="transition duration-200 ease-out"
                            x-transition:enter-start="-translate-y-2 opacity-0"
                            x-transition:enter-end="translate-y-0 opacity-100"
                            x-transition:leave="transition duration-150 ease-in"
                            x-transition:leave-start="translate-y-0 opacity-100"
                            x-transition:leave-end="-translate-y-2 opacity-0"
                            class="border-opacity-30 ml-8 space-y-1 border-l-2 border-sky-200 pl-4"
                        >
                            <a
                                href="{{ route('admin.attendances.index') }}"
                                @click="closeMobileMenu()"
                                class="group menu-item-transition {{ request()->routeIs('admin.attendances.index') ? 'bg-sky-100 text-sky-700' : 'text-gray-600 hover:bg-sky-100 hover:text-sky-700' }} flex items-center rounded-xl px-3 py-2 text-sm font-semibold"
                            >
                                <i
                                    data-lucide="book-check"
                                    class="mr-3 h-4 w-4 text-gray-500 group-hover:text-sky-700"
                                ></i>
                                <span>View Attendances</span>
                                <span
                                    x-data="{}"
                                    x-show="$store.badges.izin > 0"
                                    x-transition:enter="transition duration-300 ease-out"
                                    x-transition:enter-start="scale-50 opacity-0"
                                    x-transition:enter-end="scale-100 opacity-100"
                                    class="ml-2 inline-flex animate-pulse items-center justify-center rounded-full bg-red-600 px-2 py-0.5 text-xs font-bold text-white"
                                >
                                    <span x-text="$store.badges.izin"></span>
                                </span>
                            </a>
                            <a
                                href="{{ route('admin.attendances.leave-requests') }}"
                                @click="closeMobileMenu()"
                                class="group menu-item-transition {{ request()->routeIs('admin.attendances.leave-requests') ? 'bg-sky-100 text-sky-700' : 'text-gray-600 hover:bg-sky-100 hover:text-sky-700' }} flex items-center rounded-xl px-3 py-2 text-sm font-semibold"
                            >
                                <i
                                    data-lucide="book-open"
                                    class="mr-3 h-4 w-4 text-gray-500 group-hover:text-sky-700"
                                ></i>
                                <span>Leave Requests</span>
                                <span
                                    x-data="{}"
                                    x-show="$store.badges.cuti > 0"
                                    x-transition:enter="transition duration-300 ease-out"
                                    x-transition:enter-start="scale-50 opacity-0"
                                    x-transition:enter-end="scale-100 opacity-100"
                                    class="ml-2 inline-flex animate-pulse items-center justify-center rounded-full bg-red-600 px-2 py-0.5 text-xs font-bold text-white"
                                >
                                    <span x-text="$store.badges.cuti"></span>
                                </span>
                            </a>
                            <a
                                href="{{ route('admin.swaps.index') }}"
                                @click="closeMobileMenu()"
                                class="group menu-item-transition {{ request()->routeIs('admin.swaps.*') ? 'bg-sky-100 text-sky-700' : 'text-gray-600 hover:bg-sky-100 hover:text-sky-700' }} flex items-center rounded-xl px-3 py-2 text-sm font-semibold"
                            >
                                <i
                                    data-lucide="arrow-right-left"
                                    class="mr-3 h-4 w-4 text-gray-500 group-hover:text-sky-700"
                                ></i>
                                <span>Swap Requests</span>
                            </a>
                        </div>
                    </div>

                    <!-- Divider -->
                    <div class="border-opacity-30 my-4 border-t border-sky-200"></div>
                </nav>

                <!-- Sidebar Footer -->
                <div class="sidebar-footer border-opacity-30 flex-shrink-0 border-t border-sky-200 p-4">
                    <!-- Expanded Footer (Desktop & Mobile) -->
                    <div
                        x-show="(! sidebarCollapsed && ! isMobile) || (isMobile && mobileMenuOpen)"
                        x-transition:enter="transition duration-300 ease-out"
                        x-transition:enter-start="scale-95 opacity-0"
                        x-transition:enter-end="scale-100 opacity-100"
                        class="flex items-center justify-center space-x-2 rounded-xl bg-sky-50 p-3 text-sm text-gray-500"
                    >
                        <i data-lucide="code" class="h-4 w-4"></i>
                        <span class="font-medium">Made by DaDiBuy</span>
                    </div>

                    <!-- Collapsed Footer (Desktop Only) -->
                    <div
                        x-show="sidebarCollapsed && !isMobile"
                        x-transition:enter="transition duration-300 ease-out"
                        x-transition:enter-start="scale-95 opacity-0"
                        x-transition:enter-end="scale-100 opacity-100"
                        class="flex items-center justify-center p-2"
                    >
                        <div
                            class="group relative flex h-8 w-8 items-center justify-center rounded-lg bg-sky-100 transition-colors hover:bg-sky-200"
                        >
                            <i data-lucide="code" class="h-4 w-4 text-sky-600"></i>
                            <div
                                class="tooltip tooltip-right absolute top-1/2 z-50 -translate-y-1/2 transform rounded-lg bg-gray-800 px-3 py-2 text-sm whitespace-nowrap text-white opacity-0 transition-opacity duration-200 group-hover:opacity-100"
                            >
                                Made by DaDiBuy
                                <div
                                    class="absolute top-1/2 left-0 h-2 w-2 -translate-x-1 -translate-y-1/2 rotate-45 transform bg-gray-800"
                                ></div>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Main Content Area -->
            <div
                class="sidebar-transition flex min-h-screen flex-1 flex-col"
                :class="{
                'ml-20': sidebarCollapsed && !isMobile,
                'ml-64': !sidebarCollapsed && !isMobile,
                'ml-0': isMobile
            }"
            >
                <!-- Header -->
                <header class="sticky top-0 z-20 flex-shrink-0 border-b border-sky-200 bg-white/90 backdrop-blur-lg">
                    <div class="flex items-center justify-between gap-4 px-4 py-4 sm:px-6">
                        <div class="flex items-center space-x-4">
                            <!-- Mobile Menu Toggle -->
                            <button
                                x-show="isMobile"
                                @click="toggleSidebar()"
                                class="rounded-lg p-2 text-gray-600 hover:bg-sky-100 focus:ring-2 focus:ring-sky-400 focus:ring-offset-2 focus:outline-none md:hidden"
                            >
                                <i data-lucide="menu" class="h-6 w-6"></i>
                            </button>

                            <div>
                                <h1 class="text-2xl font-semibold tracking-tight text-gray-700">
                                    @yield('title')
                                </h1>
                                <p class="mt-1 text-base text-gray-500">Manage your application</p>
                            </div>
                        </div>

                        <div class="flex items-center space-x-6">
                            <!-- Live Clock -->
                            <div
                                class="live-clock hidden items-center space-x-3 rounded-2xl px-4 py-3 transition-all duration-300 md:flex"
                            >
                                <div class="relative">
                                    <i data-lucide="clock" class="h-5 w-5 text-sky-600"></i>
                                    <div
                                        class="absolute -top-1 -right-1 h-2 w-2 animate-pulse rounded-full bg-green-400"
                                    ></div>
                                </div>
                                <div class="text-center">
                                    <div class="text-sm font-bold text-sky-700" id="live-time">--:--:--</div>
                                    <div class="text-xs text-sky-600" id="live-date">-- --- ----</div>
                                </div>
                            </div>

                            <!-- User Profile Dropdown -->
                            <div class="relative" x-data="{ open: false }">
                                <button
                                    @click="open = !open"
                                    class="group flex items-center space-x-3 rounded-2xl px-4 py-3 transition-all duration-300 hover:bg-sky-50 focus:ring-2 focus:ring-sky-400 focus:ring-offset-2 focus:outline-none"
                                    :class="{ 'bg-sky-50 ring-2 ring-sky-200': open }"
                                    :aria-expanded="open"
                                >
                                    @if(auth()->user()->profile_photo)
                                        <img src="{{ Storage::url(auth()->user()->profile_photo) }}" alt="Profile Photo" class="h-10 w-10 rounded-2xl object-cover shadow-md transition-shadow duration-300 group-hover:shadow-lg">
                                    @else
                                        <div
                                            class="flex h-10 w-10 items-center justify-center rounded-2xl bg-gradient-to-br from-sky-500 to-sky-600 shadow-md transition-shadow duration-300 group-hover:shadow-lg"
                                        >
                                            <i data-lucide="user" class="h-5 w-5 text-white"></i>
                                        </div>
                                    @endif
                                    <div class="hidden text-left sm:block">
                                        <p
                                            class="text-sm font-semibold text-gray-700 transition-colors group-hover:text-sky-700"
                                        >
                                            {{ auth()->user()->name }}
                                        </p>
                                        <p class="text-xs font-medium text-sky-600">Administrator</p>
                                    </div>
                                    <i
                                        data-lucide="chevron-down"
                                        class="h-4 w-4 text-gray-500 transition-all duration-300 group-hover:text-sky-600"
                                        :class="{ 'rotate-180': open }"
                                    ></i>
                                </button>

                                <!-- Modern Admin Dropdown menu -->
                                <div
                                    x-show="open"
                                    @click.away="open = false"
                                    class="absolute right-0 z-50 mt-3 w-80 overflow-hidden rounded-3xl border border-sky-100 bg-white/95 shadow-2xl backdrop-blur-xl"
                                    x-transition:enter="transition duration-300 ease-out"
                                    x-transition:enter-start="translate-y-2 scale-90 transform opacity-0"
                                    x-transition:enter-end="translate-y-0 scale-100 transform opacity-100"
                                    x-transition:leave="transition duration-200 ease-in"
                                    x-transition:leave-start="translate-y-0 scale-100 transform opacity-100"
                                    x-transition:leave-end="translate-y-2 scale-90 transform opacity-0"
                                >
                                    <!-- Header with gradient -->
                                    <div class="bg-gradient-to-r from-sky-500 to-sky-600 px-6 py-4">
                                        <div class="flex items-center space-x-4">
                                            @if(auth()->user()->profile_photo)
                                                <img src="{{ Storage::url(auth()->user()->profile_photo) }}" alt="Profile Photo" class="h-16 w-16 rounded-3xl border border-white/30 object-cover shadow-sm bg-white/20 backdrop-blur-sm">
                                            @else
                                                <div
                                                    class="flex h-16 w-16 items-center justify-center rounded-3xl border border-white/30 bg-white/20 backdrop-blur-sm"
                                                >
                                                    <i data-lucide="shield-check" class="h-8 w-8 text-white"></i>
                                                </div>
                                            @endif
                                            <div class="flex-1">
                                                <h3 class="text-lg font-bold text-white">
                                                    {{ auth()->user()->name }}
                                                </h3>
                                                <p class="text-sm font-medium text-sky-100">
                                                    {{ auth()->user()->email }}
                                                </p>
                                                <div class="mt-1 flex items-center">
                                                    <div
                                                        class="mr-2 h-2 w-2 animate-pulse rounded-full bg-green-400"
                                                    ></div>
                                                    <span class="text-xs text-sky-100">Administrator</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Menu Items -->
                                    <div class="space-y-2 p-4">
                                        <!-- Dashboard Link -->
                                        <!--<a href="{{ route('admin.dashboard') }}" --
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
                                    </a> -->

                                        <!-- Users Management Link -->
                                        <!--<a href="{{ route('admin.users.index') }}"
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
                                    </a> -->

                                        <!-- Schedules Link -->
                                        <!--<a href="{{ route('admin.schedules.index') }}"
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
                                    </a> -->

                                        <!-- Attendance Link -->
                                        <!--<a href="{{ route('admin.attendances.index') }}"
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
                                    </a> -->
                                        <!-- Activity Logs Link -->
                                        <a
                                            href="{{ route('admin.activity-logs.index') }}"
                                            class="group flex items-center space-x-3 rounded-2xl px-4 py-3 transition-all duration-200 hover:bg-indigo-50"
                                        >
                                            <div
                                                class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-100 transition-colors group-hover:bg-indigo-200"
                                            >
                                                <i data-lucide="activity" class="h-5 w-5 text-indigo-600"></i>
                                            </div>
                                            <div class="flex-1">
                                                <p
                                                    class="text-sm font-semibold text-gray-700 group-hover:text-indigo-700"
                                                >
                                                    Activity Logs
                                                </p>
                                                <p class="text-xs text-gray-500">View system activity logs</p>
                                            </div>
                                            <i
                                                data-lucide="chevron-right"
                                                class="h-4 w-4 text-gray-400 group-hover:text-indigo-600"
                                            ></i>
                                        </a>
                                        <!-- Security Management Link -->
                                        <a
                                            href="{{ route('admin.security.index') }}"
                                            class="group flex items-center space-x-3 rounded-2xl px-4 py-3 transition-all duration-200 hover:bg-red-50"
                                        >
                                            <div
                                                class="flex h-10 w-10 items-center justify-center rounded-xl bg-red-100 transition-colors group-hover:bg-red-200"
                                            >
                                                <i data-lucide="shield-alert" class="h-5 w-5 text-red-600"></i>
                                            </div>
                                            <div class="flex-1">
                                                <p class="text-sm font-semibold text-gray-700 group-hover:text-red-700">
                                                    Security
                                                </p>
                                                <p class="text-xs text-gray-500">Manage system security</p>
                                            </div>
                                            <i
                                                data-lucide="chevron-right"
                                                class="h-4 w-4 text-gray-400 group-hover:text-red-600"
                                            ></i>
                                        </a>
                                        <!-- Profile Link -->
                                        <a
                                            href="{{ route('admin.profile.index') }}"
                                            class="group flex items-center space-x-3 rounded-2xl px-4 py-3 transition-all duration-200 hover:bg-teal-50"
                                        >
                                            <div
                                                class="flex h-10 w-10 items-center justify-center rounded-xl bg-teal-100 transition-colors group-hover:bg-teal-200"
                                            >
                                                <i data-lucide="user-circle" class="h-5 w-5 text-teal-600"></i>
                                            </div>
                                            <div class="flex-1">
                                                <p
                                                    class="text-sm font-semibold text-gray-700 group-hover:text-teal-700"
                                                >
                                                    Profile
                                                </p>
                                                <p class="text-xs text-gray-500">Manage your profile</p>
                                            </div>
                                            <i
                                                data-lucide="chevron-right"
                                                class="h-4 w-4 text-gray-400 group-hover:text-teal-600"
                                            ></i>
                                        </a>
                                    </div>

                                    <!-- Divider -->
                                    <div class="mx-4 border-t border-sky-100"></div>

                                    <!-- Logout Section -->
                                    <div class="p-4">
                                        <form action="{{ route('logout') }}" method="POST">
                                            @csrf
                                            <button
                                                type="submit"
                                                class="group flex w-full items-center space-x-3 rounded-2xl px-4 py-3 text-red-600 transition-all duration-200 hover:bg-red-50 focus:bg-red-50 focus:outline-none"
                                            >
                                                <div
                                                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-red-100 transition-colors group-hover:bg-red-200"
                                                >
                                                    <i data-lucide="log-out" class="h-5 w-5 text-red-600"></i>
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
                <main class="flex-1 overflow-auto bg-white">
                    <div class="admin-content m-0 min-h-full p-4 sm:p-6 lg:p-8">
                        @yield('content')
                    </div>
                </main>
            </div>
        </div>

        <script>
            // Initialize Lucide icons after DOM is loaded
            document.addEventListener('DOMContentLoaded', function () {
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
                        hour12: false,
                    };

                    const dateOptions = {
                        timeZone: 'Asia/Jakarta',
                        weekday: 'short',
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric',
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
            document.addEventListener('keydown', function (e) {
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
        </script>

        @stack('scripts')
    </body>
</html>
