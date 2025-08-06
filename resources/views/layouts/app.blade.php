<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - @yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

    <!-- Navbar -->
    <nav class="bg-blue-600 p-4 text-white flex justify-between items-center">
        <div class="text-lg font-semibold">Admin Panel</div>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded">
                Logout
            </button>
        </form>
    </nav>

    <!-- Sidebar + Content -->
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-white shadow-md p-6">
            <ul class="space-y-4">
                <li><a href="{{ route('admin.dashboard') }}" class="text-blue-600 hover:underline">Dashboard</a></li>
                <li><a href="{{ route('admin.users.index') }}" class="text-blue-600 hover:underline">Manage Users</a></li>
                <li><a href="{{ route('admin.schedules.index') }}" class="text-blue-600 hover:underline">Manage Schedules</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-6">
            <h1 class="text-2xl font-bold mb-4">@yield('title')</h1>
            @yield('content')
        </main>
    </div>

</body>
</html>
