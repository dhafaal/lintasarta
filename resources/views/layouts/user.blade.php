<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - Lintasarta</title>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    <style>
        /* Smooth Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-10px); }
            to { opacity: 1; transform: translateX(0); }
        }
        
        /* Minimalist Transitions */
        .smooth-transition {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        /* Subtle Hover Effects */
        .hover-scale:hover {
            transform: scale(1.02);
        }
        
        /* Mobile Menu Slide */
        @keyframes slideInLeft {
            from { transform: translateX(-100%); }
            to { transform: translateX(0); }
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
    </style>
</head>

<body class="bg-white min-h-screen antialiased">
    <div class="min-h-screen" x-data="{ 
        mobileMenuOpen: false,
        userMenuOpen: false,
        attendancesOpen: {{ request()->routeIs('user.attendances.*') || request()->routeIs('user.permissions.*') ? 'true' : 'false' }}
    }">
        <!-- Mobile Menu Overlay -->
        <div x-show="mobileMenuOpen" 
             @click="mobileMenuOpen = false"
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-40 lg:hidden">
        </div>

        <!-- Sidebar -->
        <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-200 transform transition-transform duration-300 ease-in-out lg:translate-x-0"
               :class="mobileMenuOpen ? 'translate-x-0' : '-translate-x-full'">
            
            <!-- Logo -->
            <div class="h-16 flex items-center px-6 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-sky-500 rounded-lg flex items-center justify-center">
                        <i data-lucide="building-2" class="w-5 h-5 text-white"></i>
                    </div>
                    <div>
                        <h1 class="text-lg font-bold text-gray-900">Lintasarta</h1>
                        <p class="text-xs text-gray-500">Employee Portal</p>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
                <!-- Dashboard -->
                <a href="{{ route('user.dashboard') }}" 
                   class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg smooth-transition {{ request()->routeIs('user.dashboard') ? 'bg-sky-50 text-sky-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i data-lucide="layout-dashboard" class="w-5 h-5 mr-3 {{ request()->routeIs('user.dashboard') ? 'text-sky-600' : 'text-gray-400' }}"></i>
                    <span>Dashboard</span>
                </a>
                
                <!-- Attendances Dropdown -->
                <div class="space-y-1">
                    <button @click="attendancesOpen = !attendancesOpen" 
                            class="w-full flex items-center justify-between px-3 py-2.5 text-sm font-medium rounded-lg smooth-transition {{ request()->routeIs('user.attendances.*') || request()->routeIs('user.permissions.*') ? 'bg-sky-50 text-sky-700' : 'text-gray-700 hover:bg-gray-50' }}">
                        <div class="flex items-center">
                            <i data-lucide="clock" class="w-5 h-5 mr-3 {{ request()->routeIs('user.attendances.*') || request()->routeIs('user.permissions.*') ? 'text-sky-600' : 'text-gray-400' }}"></i>
                            <span>Attendances</span>
                        </div>
                        <i data-lucide="chevron-down" class="w-4 h-4 transition-transform duration-200" :class="attendancesOpen ? 'rotate-180' : ''"></i>
                    </button>
                    
                    <!-- Submenu -->
                    <div x-show="attendancesOpen" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-1"
                         class="ml-8 space-y-1">
                        <a href="{{ route('user.attendances.index') }}" 
                           class="flex items-center px-3 py-2 text-sm font-medium rounded-lg smooth-transition {{ request()->routeIs('user.attendances.index') ? 'bg-sky-50 text-sky-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                            <i data-lucide="log-in" class="w-4 h-4 mr-2 {{ request()->routeIs('user.attendances.index') ? 'text-sky-600' : 'text-gray-400' }}"></i>
                            <span>Check In/Out</span>
                        </a>
                        
                        <a href="{{ route('user.permissions.index') }}" 
                           class="flex items-center px-3 py-2 text-sm font-medium rounded-lg smooth-transition {{ request()->routeIs('user.permissions.*') ? 'bg-purple-50 text-purple-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                            <i data-lucide="file-text" class="w-4 h-4 mr-2 {{ request()->routeIs('user.permissions.*') ? 'text-purple-600' : 'text-gray-400' }}"></i>
                            <span>Permissions</span>
                        </a>
                        
                        <a href="{{ route('user.attendances.history') }}" 
                           class="flex items-center px-3 py-2 text-sm font-medium rounded-lg smooth-transition {{ request()->routeIs('user.attendances.history') ? 'bg-emerald-50 text-emerald-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                            <i data-lucide="history" class="w-4 h-4 mr-2 {{ request()->routeIs('user.attendances.history') ? 'text-emerald-600' : 'text-gray-400' }}"></i>
                            <span>History</span>
                        </a>
                    </div>
                </div>
            </nav>

            <!-- User Profile -->
            <div class="p-3 border-t border-gray-200">
                <div class="flex items-center px-3 py-2 bg-gray-50 rounded-lg">
                    <div class="w-9 h-9 rounded-lg bg-sky-500 flex items-center justify-center text-white font-semibold text-sm">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                    <div class="ml-3 flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500">Employee</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="lg:ml-64">
            <!-- Top Navigation -->
            <header class="sticky top-0 z-30 bg-white border-b border-gray-200">
                <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
                    <!-- Mobile Menu Button -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" 
                            class="lg:hidden p-2 rounded-lg text-gray-500 hover:bg-gray-100 smooth-transition">
                        <i data-lucide="menu" class="w-6 h-6"></i>
                    </button>

                    <!-- Page Title (Optional - can be populated by child views) -->
                    <div class="hidden lg:block">
                        <h2 class="text-lg font-semibold text-gray-900">@yield('page-title', 'Dashboard')</h2>
                    </div>

                    <!-- Right Side -->
                    <div class="flex items-center space-x-3 ml-auto">
                        <!-- Live Clock -->
                        <div class="hidden sm:flex items-center space-x-2 px-3 py-1.5 bg-gray-50 rounded-lg border border-gray-200">
                            <i data-lucide="clock" class="w-4 h-4 text-gray-500"></i>
                            <div class="text-xs">
                                <div class="font-semibold text-gray-900" id="live-time">--:--:--</div>
                                <div class="text-gray-500" id="live-date">-- --- ----</div>
                            </div>
                        </div>
                        
                        <!-- User Menu -->
                        <div class="relative" x-data="{ open: false }">
                            <button 
                                @click="open = !open" 
                                class="flex items-center space-x-2 px-3 py-2 rounded-lg hover:bg-gray-50 smooth-transition focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2"
                                :aria-expanded="open"
                            >
                                <div class="w-8 h-8 rounded-lg bg-sky-500 flex items-center justify-center text-white font-semibold text-sm">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                                </div>
                                <div class="hidden sm:block text-left">
                                    <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-gray-500">Employee</p>
                                </div>
                                <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400 smooth-transition" :class="{ 'rotate-180': open }"></i>
                            </button>
                            
                            <!-- Dropdown Menu -->
                            <div x-show="open" 
                                 @click.away="open = false" 
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
                                
                                <!-- User Info -->
                                <div class="px-4 py-3 border-b border-gray-100">
                                    <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                                </div>
                                
                                <!-- Menu Items -->
                                <div class="py-1">
                                    <a href="{{ route('user.dashboard') }}" 
                                       class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 smooth-transition">
                                        <i data-lucide="layout-dashboard" class="w-4 h-4 mr-3 text-gray-400"></i>
                                        Dashboard
                                    </a>
                                    
                                    <a href="{{ route('user.attendances.index') }}" 
                                       class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 smooth-transition">
                                        <i data-lucide="clock" class="w-4 h-4 mr-3 text-gray-400"></i>
                                        Attendance
                                    </a>
                                    
                                    <a href="{{ route('user.profile.index') }}" 
                                       class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 smooth-transition">
                                        <i data-lucide="user-circle" class="w-4 h-4 mr-3 text-gray-400"></i>
                                        Profile
                                    </a>
                                </div>
                                
                                <!-- Logout -->
                                <div class="border-t border-gray-100 py-1">
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" 
                                                class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 smooth-transition">
                                            <i data-lucide="log-out" class="w-4 h-4 mr-3"></i>
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
                <div class="px-4 sm:px-6 lg:px-8 py-6">
                    <!-- Stats Row -->
                    @hasSection('stats')
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                            @yield('stats')
                        </div>
                    @endif

                    <!-- Main Content -->
                    @yield('content')

                    <!-- Secondary Content -->
                    @hasSection('secondary-content')
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
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
        document.addEventListener('DOMContentLoaded', function() {
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
                    hour12: false
                };
                
                const dateOptions = {
                    timeZone: 'Asia/Jakarta',
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
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