<!DOCTYPE html>
<html lang="en" class="h-full">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <title>@yield('title') - Lintasarta</title>
        <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
        <script src="https://unpkg.com/lucide@latest"></script>
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

            /* Enhanced Animations */
            @keyframes fadeIn {
                from {
                    opacity: 0;
                    transform: translateY(8px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            @keyframes slideIn {
                from {
                    opacity: 0;
                    transform: translateX(-10px);
                }
                to {
                    opacity: 1;
                    transform: translateX(0);
                }
            }

            /* Smooth Transitions */
            .smooth-transition {
                transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            }

            /* Subtle Hover Effects */
            .hover-lift:hover {
                transform: translateY(-1px);
                box-shadow: 0 4px 12px -2px rgba(0, 0, 0, 0.1);
            }

            .hover-scale:hover {
                transform: scale(1.02);
            }

            /* Mobile Menu Slide */
            @keyframes slideInLeft {
                from {
                    transform: translateX(-100%);
                }
                to {
                    transform: translateX(0);
                }
            }

            .mobile-menu-enter {
                animation: slideInLeft 0.3s ease-out;
            }

            /* Backdrop Blur Support */
            @supports (backdrop-filter: blur(10px)) {
                .backdrop-blur-custom {
                    backdrop-filter: blur(10px);
                    -webkit-backdrop-filter: blur(10px);
                }
            }

            /* Custom Scrollbar */
            .custom-scrollbar::-webkit-scrollbar {
                width: 6px;
            }

            .custom-scrollbar::-webkit-scrollbar-track {
                background: #f8fafc;
                border-radius: 3px;
            }

            .custom-scrollbar::-webkit-scrollbar-thumb {
                background: #cbd5e1;
                border-radius: 3px;
            }

            .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                background: #94a3b8;
            }

            /* Enhanced Active States */
            .nav-active {
                background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
                color: #0369a1;
                border-left: 3px solid #0284c7;
            }

            /* Responsive Text Sizes */
            .text-responsive-xs {
                font-size: 0.75rem;
                line-height: 1rem;
            }

            .text-responsive-sm {
                font-size: 0.875rem;
                line-height: 1.25rem;
            }

            .text-responsive-base {
                font-size: 1rem;
                line-height: 1.5rem;
            }

            .text-responsive-lg {
                font-size: 1.125rem;
                line-height: 1.75rem;
            }

            .text-responsive-xl {
                font-size: 1.25rem;
                line-height: 1.75rem;
            }

            .text-responsive-2xl {
                font-size: 1.5rem;
                line-height: 2rem;
            }

            /* Glass Effect */
            .glass-effect {
                background: rgba(255, 255, 255, 0.8);
                backdrop-filter: blur(8px);
                -webkit-backdrop-filter: blur(8px);
            }
        </style>
    </head>

    <body class="min-h-screen bg-white antialiased">
        <div
            class="min-h-screen"
            x-data="{
                mobileMenuOpen: false,
                userMenuOpen: false,
                attendancesOpen:
                    {{ request()->routeIs('user.attendances.*') || request()->routeIs('user.permissions.*') || request()->routeIs('user.swaps.*') ? 'true' : 'false' }},
            }"
        >
            <!-- Mobile Menu Overlay -->
            <div
                x-show="mobileMenuOpen"
                @click="mobileMenuOpen = false"
                x-transition:enter="transition-opacity duration-300 ease-linear"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity duration-300 ease-linear"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 z-40 bg-gray-900/60 backdrop-blur-sm lg:hidden"
            ></div>

            <!-- Enhanced Sidebar -->
            <aside
                class="fixed inset-y-0 left-0 z-50 w-64 transform border-r border-slate-200 bg-white shadow-lg transition-transform duration-300 ease-in-out lg:translate-x-0"
                :class="mobileMenuOpen ? 'translate-x-0' : '-translate-x-full'"
            >
                <!-- Logo -->
                <div
                    class="flex h-16 items-center border-b border-slate-200 bg-gradient-to-r from-sky-500 to-blue-600 px-6"
                >
                    <div class="flex items-center space-x-3">
                        <div
                            class="hover-lift smooth-transition flex h-8 w-8 items-center justify-center rounded-lg bg-white"
                        >
                            <i data-lucide="building-2" class="h-5 w-5 text-sky-500"></i>
                        </div>
                        <div>
                            <h1 class="text-lg font-bold text-white">Scheduler</h1>
                            <p class="text-xs text-blue-100">Employee</p>
                        </div>
                    </div>
                </div>

                <!-- Navigation -->
                <nav class="custom-scrollbar flex-1 space-y-1 overflow-y-auto px-3 py-4">
                    <!-- Dashboard -->
                    <a
                        href="{{ route('user.dashboard') }}"
                        class="smooth-transition {{ request()->routeIs('user.dashboard') ? 'nav-active' : 'text-slate-700 hover:bg-slate-50' }} flex items-center rounded-lg px-3 py-2.5 text-sm font-medium"
                    >
                        <i
                            data-lucide="layout-dashboard"
                            class="{{ request()->routeIs('user.dashboard') ? 'text-sky-600' : 'text-slate-400' }} mr-3 h-5 w-5"
                        ></i>
                        <span>Dashboard</span>
                    </a>

                    <!-- Attendances Dropdown -->
                    <div class="space-y-1">
                        <button
                            @click="attendancesOpen = !attendancesOpen"
                            class="smooth-transition {{ request()->routeIs('user.attendances.*') || request()->routeIs('user.permissions.*') || request()->routeIs('user.swaps.*') ? 'nav-active' : 'text-slate-700 hover:bg-slate-50' }} flex w-full items-center justify-between rounded-lg px-3 py-2.5 text-sm font-medium"
                        >
                            <div class="flex items-center">
                                <i
                                    data-lucide="clock"
                                    class="{{ request()->routeIs('user.attendances.*') || request()->routeIs('user.permissions.*') || request()->routeIs('user.swaps.*') ? 'text-sky-600' : 'text-slate-400' }} mr-3 h-5 w-5"
                                ></i>
                                <span>Attendances</span>
                            </div>
                            <i
                                data-lucide="chevron-down"
                                class="h-4 w-4 transition-transform duration-200"
                                :class="attendancesOpen ? 'rotate-180' : ''"
                            ></i>
                        </button>

                        <!-- Submenu -->
                        <div
                            x-show="attendancesOpen"
                            x-transition:enter="transition duration-200 ease-out"
                            x-transition:enter-start="-translate-y-1 opacity-0"
                            x-transition:enter-end="translate-y-0 opacity-100"
                            x-transition:leave="transition duration-150 ease-in"
                            x-transition:leave-start="translate-y-0 opacity-100"
                            x-transition:leave-end="-translate-y-1 opacity-0"
                            class="ml-8 space-y-1"
                        >
                            <a
                                href="{{ route('user.attendances.index') }}"
                                class="smooth-transition {{ request()->routeIs('user.attendances.index') ? 'bg-sky-50 text-sky-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }} flex items-center rounded-lg px-3 py-2 text-sm font-medium"
                            >
                                <i
                                    data-lucide="log-in"
                                    class="{{ request()->routeIs('user.attendances.index') ? 'text-sky-600' : 'text-slate-400' }} mr-2 h-4 w-4"
                                ></i>
                                <span>Check In/Out</span>
                            </a>

                            <a
                                href="{{ route('user.permissions.index') }}"
                                class="smooth-transition {{ request()->routeIs('user.permissions.index') ? 'bg-sky-50 text-sky-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }} flex items-center rounded-lg px-3 py-2 text-sm font-medium"
                            >
                                <i
                                    data-lucide="file-check"
                                    class="{{ request()->routeIs('user.permissions.index') ? 'text-sky-600' : 'text-slate-400' }} mr-2 h-4 w-4"
                                ></i>
                                <span>Permissions</span>
                            </a>
                            <a
                                href="{{ route('user.swaps.index') }}"
                                class="smooth-transition {{ request()->routeIs('user.swaps.*') ? 'bg-sky-50 text-sky-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }} flex items-center rounded-lg px-3 py-2 text-sm font-medium"
                            >
                                <i
                                    data-lucide="arrow-right-left"
                                    class="{{ request()->routeIs('user.swaps.*') ? 'text-sky-600' : 'text-slate-400' }} mr-2 h-4 w-4"
                                ></i>
                                <span>Swap Requests</span>
                            </a>

                            <a
                                href="{{ route('user.attendances.history') }}"
                                class="smooth-transition {{ request()->routeIs('user.attendances.history') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }} flex items-center rounded-lg px-3 py-2 text-sm font-medium"
                            >
                                <i
                                    data-lucide="history"
                                    class="{{ request()->routeIs('user.attendances.history') ? 'text-emerald-600' : 'text-slate-400' }} mr-2 h-4 w-4"
                                ></i>
                                <span>History</span>
                            </a>
                        </div>
                    </div>
                </nav>

                <!-- User Profile -->
                <div class="border-t border-slate-200 p-3">
                    <div class="hover-lift smooth-transition flex items-center rounded-lg bg-slate-50 px-3 py-2">
                        @if(auth()->user()->profile_photo)
                            <img src="{{ Storage::url(auth()->user()->profile_photo) }}" alt="Profile Photo" class="h-9 w-9 rounded-lg object-cover shadow-sm">
                        @else
                            <div
                                class="flex h-9 w-9 items-center justify-center rounded-lg bg-sky-500 text-sm font-semibold text-white shadow-sm"
                            >
                                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                            </div>
                        @endif
                        <div class="ml-3 min-w-0 flex-1">
                            <p class="truncate text-sm font-medium text-slate-900">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-slate-500">Employee</p>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Main Content -->
            <div class="lg:ml-64">
                <!-- Top Navigation -->
                <header class="backdrop-blur-custom sticky top-0 z-30 border-b border-slate-200 bg-white/80 shadow-sm">
                    <div class="flex h-16 items-center justify-between px-4 sm:px-6 lg:px-8">
                        <!-- Mobile Menu Button -->
                        <button
                            @click="mobileMenuOpen = !mobileMenuOpen"
                            class="smooth-transition hover-lift rounded-lg p-2 text-slate-500 hover:bg-slate-100 lg:hidden"
                        >
                            <i data-lucide="menu" class="h-6 w-6"></i>
                        </button>

                        <!-- Page Title -->
                        <div class="hidden lg:block">
                            <h2 class="text-lg font-semibold text-slate-900">@yield('title')</h2>
                        </div>

                        <!-- Right Side -->
                        <div class="ml-auto flex items-center space-x-3">
                            <!-- Live Clock -->
                            <div
                                class="hover-lift smooth-transition hidden items-center space-x-2 rounded-lg border border-slate-200 bg-slate-50 px-3 py-1.5 sm:flex"
                            >
                                <i data-lucide="clock" class="h-4 w-4 text-slate-500"></i>
                                <div class="text-xs">
                                    <div class="font-semibold text-slate-900" id="live-time">--:--:--</div>
                                    <div class="text-slate-500" id="live-date">-- --- ----</div>
                                </div>
                            </div>

                            <!-- User Menu -->
                            <div class="relative" x-data="{ open: false }">
                                <button
                                    @click="open = !open"
                                    class="smooth-transition hover-lift flex items-center space-x-2 rounded-lg px-3 py-2 hover:bg-slate-50 focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 focus:outline-none"
                                    :aria-expanded="open"
                                >
                                    @if(auth()->user()->profile_photo)
                                        <img src="{{ Storage::url(auth()->user()->profile_photo) }}" alt="Profile Photo" class="h-8 w-8 rounded-lg object-cover shadow-sm">
                                    @else
                                        <div
                                            class="flex h-8 w-8 items-center justify-center rounded-lg bg-sky-500 text-sm font-semibold text-white shadow-sm"
                                        >
                                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                                        </div>
                                    @endif
                                    <div class="hidden text-left sm:block">
                                        <p class="text-sm font-medium text-slate-900">{{ auth()->user()->name }}</p>
                                        <p class="text-xs text-slate-500">Employee</p>
                                    </div>
                                    <i
                                        data-lucide="chevron-down"
                                        class="smooth-transition h-4 w-4 text-slate-400"
                                        :class="{ 'rotate-180': open }"
                                    ></i>
                                </button>

                                <!-- Dropdown Menu -->
                                <div
                                    x-show="open"
                                    @click.away="open = false"
                                    x-transition:enter="transition duration-200 ease-out"
                                    x-transition:enter-start="scale-95 opacity-0"
                                    x-transition:enter-end="scale-100 opacity-100"
                                    x-transition:leave="transition duration-150 ease-in"
                                    x-transition:leave-start="scale-100 opacity-100"
                                    x-transition:leave-end="scale-95 opacity-0"
                                    class="absolute right-0 z-50 mt-2 w-64 rounded-lg border border-slate-200 bg-white py-1 shadow-lg"
                                >
                                    <!-- User Info -->
                                    <div class="border-b border-slate-100 px-4 py-3">
                                        <p class="text-sm font-medium text-slate-900">{{ auth()->user()->name }}</p>
                                        <p class="truncate text-xs text-slate-500">{{ auth()->user()->email }}</p>
                                    </div>

                                    <!-- Menu Items -->
                                    <div class="py-1">
                                        <a
                                            href="{{ route('user.dashboard') }}"
                                            class="smooth-transition flex items-center px-4 py-2 text-sm text-slate-700 hover:bg-slate-50"
                                        >
                                            <i data-lucide="layout-dashboard" class="mr-3 h-4 w-4 text-slate-400"></i>
                                            Dashboard
                                        </a>

                                        <a
                                            href="{{ route('user.attendances.index') }}"
                                            class="smooth-transition flex items-center px-4 py-2 text-sm text-slate-700 hover:bg-slate-50"
                                        >
                                            <i data-lucide="clock" class="mr-3 h-4 w-4 text-slate-400"></i>
                                            Attendance
                                        </a>

                                        <a
                                            href="{{ route('user.profile.index') }}"
                                            class="smooth-transition flex items-center px-4 py-2 text-sm text-slate-700 hover:bg-slate-50"
                                        >
                                            <i data-lucide="user-circle" class="mr-3 h-4 w-4 text-slate-400"></i>
                                            Profile
                                        </a>
                                    </div>

                                    <!-- Logout -->
                                    <div class="border-t border-slate-100 py-1">
                                        <form action="{{ route('logout') }}" method="POST">
                                            @csrf
                                            <button
                                                type="submit"
                                                class="smooth-transition flex w-full items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50"
                                            >
                                                <i data-lucide="log-out" class="mr-3 h-4 w-4"></i>
                                                Sign out
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Page Content -->
                <main class="min-h-screen bg-white">
                    <div class="px-4 py-6 sm:px-6 lg:px-8">
                        <!-- Stats Row -->
                        @hasSection('stats')
                            <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                                @yield('stats')
                            </div>
                        @endif

                        <!-- Main Content -->
                        @yield('content')

                        <!-- Secondary Content -->
                        @hasSection('secondary-content')
                            <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
                                @yield('secondary-content')
                            </div>
                        @endif
                    </div>
                </main>
            </div>
        </div>

        @stack('scripts')
        <script>
            // Initialize Lucide icons
            document.addEventListener('DOMContentLoaded', function () {
                lucide.createIcons();
                initializeLiveClock();
            });

            // Live Clock with Indonesian Time Format
            function initializeLiveClock() {
                function updateClock() {
                    const now = new Date();

                    const timeOptions = {
                        timeZone: 'Asia/Jakarta',
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit',
                        hour12: false,
                    };

                    const dateOptions = {
                        timeZone: 'Asia/Jakarta',
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric',
                    };

                    const timeString = now.toLocaleTimeString('id-ID', timeOptions);
                    const dateString = now.toLocaleDateString('id-ID', dateOptions);

                    const timeElement = document.getElementById('live-time');
                    const dateElement = document.getElementById('live-date');

                    if (timeElement) timeElement.textContent = timeString;
                    if (dateElement) dateElement.textContent = dateString;
                }

                updateClock();
                setInterval(updateClock, 1000);
            }
        </script>
    </body>
</html>
