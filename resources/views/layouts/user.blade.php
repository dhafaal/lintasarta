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
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(5px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>

<body class="bg-gray-50 min-h-screen antialiased text-gray-800">
    <div class="flex min-h-screen" x-data="{
        sidebarCollapsed: false,
        isDarkMode: false,
        userMenuOpen: false
    }">
        <!-- Sidebar -->
        <div class="w-64 bg-white border-r border-gray-200 flex flex-col h-screen fixed z-30">
            <!-- Logo -->
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-blue-800 rounded-xl flex items-center justify-center">
                        <i data-lucide="building-2" class="w-5 h-5 text-white"></i>
                    </div>
                    <span class="ml-3 text-lg font-semibold text-gray-800">Lintasarta</span>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-4 py-6 space-y-1">
                <a href="{{ route('user.dashboard') }}" class="flex items-center px-4 py-3 text-sm text-gray-600 rounded-lg transition-all duration-200 hover:bg-gray-100 {{ request()->routeIs('user.dashboard') ? 'bg-blue-50 text-blue-700 font-medium' : '' }}">
                    <i data-lucide="layout-dashboard" class="w-5 h-5 mr-3 {{ request()->routeIs('user.dashboard') ? 'text-blue-600' : 'text-gray-500' }}"></i>
                    <span>Dashboard</span>
                </a>
                
                <a href="{{ route('user.attendances.index') }}" class="flex items-center px-4 py-3 text-sm text-gray-600 rounded-lg transition-all duration-200 hover:bg-gray-100 {{ request()->routeIs('user.attendances.*') ? 'bg-blue-50 text-blue-700 font-medium' : '' }}">
                    <i data-lucide="clock" class="w-5 h-5 mr-3 {{ request()->routeIs('user.attendances.*') ? 'text-blue-600' : 'text-gray-500' }}"></i>
                    <span>Attendance</span>
                </a>
            </nav>

            <!-- User Profile -->
            <div class="p-4 border-t border-gray-200">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                        <i data-lucide="user" class="w-5 h-5 text-blue-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-800">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500">{{ auth()->user()->email }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Page Content -->
        <div class="flex-1 ml-64 transition-all duration-300">
            <!-- Top Navigation -->
            <header class="bg-white border-b border-gray-200 px-6 py-4 sticky top-0 z-20">
                <div class="flex justify-end items-center ">

                    <!-- Right Side Icons -->
                    <div class="flex items-center space-x-4 justify-end">
                        
                        <div class="relative" x-data="{ open: false }">
                            <button 
                                @click="open = !open" 
                                class="flex items-center space-x-2 focus:outline-none"
                                :class="{ 'ring-2 ring-blue-200': open }"
                                :aria-expanded="open"
                            >
                                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center">
                                    <i data-lucide="user" class="w-4 h-4 text-blue-600"></i>
                                </div>
                                <i data-lucide="chevron-down" class="w-4 h-4 text-gray-500 transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                            </button>
                            
                            <!-- Dropdown menu -->
                            <div x-show="open" @click.away="open = false" 
                                 class="absolute right-0 mt-2 w-22 bg-white rounded-md shadow-lg py-1 z-50"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95">
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Sign out</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="p-6 bg-gray-50">
                <!-- Page Header -->
                <div class="mb-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-800">@yield('title')</h1>
                            <p class="text-gray-500 text-sm mt-1">@yield('subtitle', 'Overview and insights')</p>
                        </div>
                        @hasSection('actions')
                            <div class="flex items-center space-x-2">
                                @yield('actions')
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Page Content -->
                <div class="space-y-6">
                    <!-- Stats Row -->
                    @hasSection('stats')
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            @yield('stats')
                        </div>
                    @endif

                    <!-- Main Content -->
                    <div class="w-full">
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="p-6">
                                @yield('content')
                            </div>
                        </div>
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
        lucide.createIcons();
        
        // Add smooth scroll behavior
        document.addEventListener('DOMContentLoaded', function() {
            // Add smooth scrolling to all links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    document.querySelector(this.getAttribute('href')).scrollIntoView({
                        behavior: 'smooth'
                    });
                });
            });
        });
    </script>
</body>
</html>