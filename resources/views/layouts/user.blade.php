<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Lintasarta</title>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    <style>
        /* Enhanced Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-20px); }
            to { opacity: 1; transform: translateX(0); }
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        /* Enhanced Transitions */
        .menu-item-transition {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .hover-lift {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .hover-lift:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(14, 165, 233, 0.15);
        }
        
        /* Live Clock Styles */
        .live-clock {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border: 1px solid rgba(14, 165, 233, 0.2);
            animation: clockPulse 3s ease-in-out infinite;
        }
        
        @keyframes clockPulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(14, 165, 233, 0.4); }
            50% { box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.1); }
        }
        
        /* Sidebar Enhancements */
        .sidebar-item {
            position: relative;
            overflow: hidden;
        }
        
        .sidebar-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(14, 165, 233, 0.1), transparent);
            transition: left 0.5s ease-in-out;
        }
        
        .sidebar-item:hover::before {
            left: 100%;
        }
    </style>
</head>

<body class="bg-gradient-to-br from-sky-50 via-white to-sky-50 min-h-screen antialiased text-gray-800">
    <div class="flex min-h-screen" x-data="{
        sidebarCollapsed: false,
        isDarkMode: false,
        userMenuOpen: false
    }">
        <!-- Sidebar -->
        <div class="w-64 bg-white/90 backdrop-blur-lg border-r border-sky-200 flex flex-col h-screen fixed z-30 shadow-xl">
            <!-- Logo -->
            <div class="p-6 border-b border-sky-200 bg-gradient-to-r from-sky-50 to-sky-100">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-gradient-to-br from-sky-500 to-sky-600 rounded-2xl flex items-center justify-center shadow-lg hover:shadow-xl transition-shadow duration-300">
                        <i data-lucide="building-2" class="w-6 h-6 text-white"></i>
                    </div>
                    <div class="ml-3">
                        <span class="text-xl font-bold text-gray-800">Lintasarta</span>
                        <p class="text-xs text-sky-600 font-medium">Employee Portal</p>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-4 py-6 space-y-3">
                <a href="{{ route('user.dashboard') }}" class="sidebar-item flex items-center px-4 py-4 text-sm font-semibold rounded-2xl menu-item-transition hover-lift {{ request()->routeIs('user.dashboard') ? 'bg-gradient-to-r from-sky-100 to-sky-200 text-sky-700 border border-sky-300 shadow-sm' : 'text-gray-600 hover:bg-sky-50 hover:text-sky-700 border border-transparent hover:border-sky-200' }}">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center mr-3 {{ request()->routeIs('user.dashboard') ? 'bg-sky-500 text-white shadow-md' : 'bg-gray-100 text-gray-500 group-hover:bg-sky-100 group-hover:text-sky-600' }}">
                        <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                    </div>
                    <span>Dashboard</span>
                </a>
                
                <a href="{{ route('user.attendances.index') }}" class="sidebar-item flex items-center px-4 py-4 text-sm font-semibold rounded-2xl menu-item-transition hover-lift {{ request()->routeIs('user.attendances.*') ? 'bg-gradient-to-r from-sky-100 to-sky-200 text-sky-700 border border-sky-300 shadow-sm' : 'text-gray-600 hover:bg-sky-50 hover:text-sky-700 border border-transparent hover:border-sky-200' }}">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center mr-3 {{ request()->routeIs('user.attendances.*') ? 'bg-sky-500 text-white shadow-md' : 'bg-gray-100 text-gray-500 group-hover:bg-sky-100 group-hover:text-sky-600' }}">
                        <i data-lucide="clock" class="w-5 h-5"></i>
                    </div>
                    <span>Attendance</span>
                </a>
            </nav>

            <!-- User Profile -->
            <div class="p-4 border-t border-sky-200 bg-gradient-to-r from-sky-50 to-sky-100">
                <div class="flex items-center p-3 bg-white rounded-2xl shadow-sm border border-sky-200 hover:shadow-md transition-shadow duration-300">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-sky-500 to-sky-600 flex items-center justify-center shadow-md">
                        <i data-lucide="user" class="w-6 h-6 text-white"></i>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-semibold text-gray-800">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-sky-600 font-medium">Employee</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Page Content -->
        <div class="flex-1 ml-64 transition-all duration-300">
            <!-- Top Navigation -->
            <header class="bg-white/90 backdrop-blur-lg border-b border-sky-200 px-6 py-4 sticky top-0 z-20 shadow-sm">
                <div class="flex justify-end items-center">
                    <!-- Right Side Icons -->
                    <div class="flex items-center space-x-6">
                        <!-- Live Clock -->
                        <div class="flex items-center space-x-3 px-4 py-3 live-clock rounded-2xl">
                            <div class="relative">
                                <i data-lucide="clock" class="w-5 h-5 text-sky-600"></i>
                                <div class="absolute -top-1 -right-1 w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                            </div>
                            <div class="text-center">
                                <div class="text-sm font-bold text-sky-700" id="live-time">--:--:--</div>
                                <div class="text-xs text-sky-600" id="live-date">-- --- ----</div>
                            </div>
                        </div>
                        
                        <div class="relative" x-data="{ open: false }">
                            <button 
                                @click="open = !open" 
                                class="flex items-center space-x-3 px-4 py-3 rounded-2xl hover:bg-sky-50 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-sky-400 focus:ring-offset-2 group"
                                :class="{ 'bg-sky-50 ring-2 ring-sky-200': open }"
                                :aria-expanded="open"
                            >
                                <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-sky-500 to-sky-600 flex items-center justify-center shadow-md group-hover:shadow-lg transition-shadow duration-300">
                                    <i data-lucide="user" class="w-5 h-5 text-white"></i>
                                </div>
                                <div class="hidden sm:block text-left">
                                    <p class="text-sm font-semibold text-gray-700 group-hover:text-sky-700 transition-colors">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-sky-600 font-medium">Employee</p>
                                </div>
                                <i data-lucide="chevron-down" class="w-4 h-4 text-gray-500 transition-all duration-300 group-hover:text-sky-600" :class="{ 'rotate-180': open }"></i>
                            </button>
                            
                            <!-- Modern Dropdown menu -->
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
                                        <div class="w-16 h-16 rounded-3xl bg-white/20 backdrop-blur-sm flex items-center justify-center border border-white/30">
                                            <i data-lucide="user" class="w-8 h-8 text-white"></i>
                                        </div>
                                        <div class="flex-1">
                                            <h3 class="text-lg font-bold text-white">{{ auth()->user()->name }}</h3>
                                            <p class="text-sky-100 text-sm font-medium">{{ auth()->user()->email }}</p>
                                            <div class="flex items-center mt-1">
                                                <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse mr-2"></div>
                                                <span class="text-xs text-sky-100">Online</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Menu Items -->
                                <div class="p-4 space-y-2">
                                    <!-- Dashboard Link -->
                                    <a href="{{ route('user.dashboard') }}" class="flex items-center space-x-3 px-4 py-3 rounded-2xl hover:bg-sky-50 transition-all duration-200 group">
                                        <div class="w-10 h-10 rounded-xl bg-sky-100 flex items-center justify-center group-hover:bg-sky-200 transition-colors">
                                            <i data-lucide="layout-dashboard" class="w-5 h-5 text-sky-600"></i>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-sm font-semibold text-gray-700 group-hover:text-sky-700">Dashboard</p>
                                            <p class="text-xs text-gray-500">View your overview</p>
                                        </div>
                                        <i data-lucide="chevron-right" class="w-4 h-4 text-gray-400 group-hover:text-sky-600"></i>
                                    </a>
                                    
                                    <!-- Attendance Link -->
                                    <a href="{{ route('user.attendances.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-2xl hover:bg-sky-50 transition-all duration-200 group">
                                        <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center group-hover:bg-emerald-200 transition-colors">
                                            <i data-lucide="clock" class="w-5 h-5 text-emerald-600"></i>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-sm font-semibold text-gray-700 group-hover:text-emerald-700">Attendance</p>
                                            <p class="text-xs text-gray-500">Manage your time</p>
                                        </div>
                                        <i data-lucide="chevron-right" class="w-4 h-4 text-gray-400 group-hover:text-emerald-600"></i>
                                    </a>
                                </div>
                                
                                <!-- Divider -->
                                <div class="border-t border-sky-100 mx-4"></div>
                                
                                <!-- Logout Section -->
                                <div class="p-4">
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="flex items-center space-x-3 w-full px-4 py-3 text-red-600 hover:bg-red-50 rounded-2xl transition-all duration-200 focus:outline-none focus:bg-red-50 group">
                                            <div class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center group-hover:bg-red-200 transition-colors">
                                                <i data-lucide="log-out" class="w-5 h-5 text-red-600"></i>
                                            </div>
                                            <div class="flex-1 text-left">
                                                <p class="text-sm font-semibold">Sign out</p>
                                                <p class="text-xs text-red-500">End your session</p>
                                            </div>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="p-6 bg-white min-h-screen">

                <!-- Page Content -->
                <div class="space-y-8">
                    <!-- Stats Row -->
                    @hasSection('stats')
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            @yield('stats')
                        </div>
                    @endif

                    <!-- Main Content -->
                    <div class="w-full">
                        @yield('content')
                    </div>

                    <!-- Secondary Content -->
                    @hasSection('secondary-content')
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
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
            
            // Initialize live clock
            initializeLiveClock();
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
        
        // Add smooth scroll behavior
        document.addEventListener('DOMContentLoaded', function() {
            // Add smooth scrolling to all links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth'
                        });
                    }
                });
            });
            
            // Enhanced keyboard navigation
            document.addEventListener('keydown', function(e) {
                // ESC key to close dropdowns
                if (e.key === 'Escape') {
                    document.activeElement.blur();
                }
            });
        });
        
        // Add loading state management
        window.addEventListener('beforeunload', function() {
            document.body.style.opacity = '0.8';
            document.body.style.pointerEvents = 'none';
        });
    </script>
</body>
</html>