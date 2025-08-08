<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - @yield('title')</title>
    <script src="//unpkg.com/alpinejs" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 min-h-screen font-sans">

    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-64 p-5">
            <div class="text-xl font-bold mb-8 pl-3">Admin Panel</div>
            <ul class="font-semibold text-gray-600 space-y-2">
                <ul class="font-semibold text-gray-600 space-y-2">
                    <li>
                        <a href="{{ route('admin.dashboard') }}"
                            class="block px-3 py-2 rounded-lg transition
               {{ request()->routeIs('admin.dashboard') ? 'bg-white border border-gray-300 shadow-sm text-blue-800 font-bold' : 'hover:bg-blue-100 hover:text-blue-800 border border-transparent' }}">
                            Dashboard
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.users.index') }}"
                            class="block px-3 py-2 rounded-lg transition
               {{ request()->routeIs('admin.users.*') ? 'bg-white border border-gray-300 shadow-sm text-blue-800 font-bold' : 'hover:bg-blue-100 hover:text-blue-800 border border-transparent' }}">
                            Manage Users
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.schedules.index') }}"
                            class="block px-3 py-2 rounded-lg transition
               {{ request()->routeIs('admin.schedules.*') ? 'bg-white border border-gray-300 shadow-sm text-blue-800 font-bold' : 'hover:bg-blue-100 hover:text-blue-800 border border-transparent' }}">
                            Manage Schedules
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.shifts.index') }}"
                            class="block px-3 py-2 rounded-lg transition
               {{ request()->routeIs('admin.shifts.*') ? 'bg-white border border-gray-300 shadow-sm text-blue-800 font-bold' : 'hover:bg-blue-100 hover:text-blue-800 border border-transparent' }}">
                            Manage Shifts
                        </a>
                    </li>

                    <li>
                        <a href="/calender"
                            class="block px-3 py-2 rounded-lg transition
               {{ request()->is('calender') ? 'bg-white border border-gray-300 shadow-sm text-blue-800 font-bold' : 'hover:bg-blue-100 hover:text-blue-800 border border-transparent' }}">
                            Calendar
                        </a>
                    </li>
                </ul>

            </ul>
        </aside>

        <!-- Right Content Area -->
        <div class="flex-1 flex flex-col min-h-screen bg-gradient-to-r from-gray-100 via-gray-50 to-gray-50">
            <!-- Header -->
            <nav
                class="bg-gradient-to-r from-gray-100 to-gray-50 text-gray-900 px-6 py-4 flex justify-between items-center">
                <h1 class="text-lg font-semibold">@yield('title')</h1>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="bg-red-500 text-white font-semibold text-md shadow-sm hover:bg-red-600 px-4 py-2 rounded-lg">
                        Logout
                    </button>
                </form>
            </nav>

            <!-- Main Content -->
            <main class="flex-1 p-4">
                <div class="bg-white rounded-2xl shadow-md border border-gray-300 p-6 min-h-full">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

</body>

</html>