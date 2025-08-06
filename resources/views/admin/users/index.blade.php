@extends('layouts.app')

@section('content')
<div class="min-h-screen flex">
    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow-md border-r border-gray-200 hidden md:block">
        <div class="p-6 text-xl font-semibold text-blue-600">
            <span class="text-gray-800">Admin</span> Panel
        </div>
        <nav class="mt-4">
            <ul class="space-y-2">
                <li>
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center px-6 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                        <i class="fas fa-home mr-3"></i> Dashboard
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.users.index') }}" class="flex items-center px-6 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                        <i class="fas fa-users mr-3"></i> Users
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.schedules.index') }}" class="flex items-center px-6 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                        <i class="fas fa-calendar-alt mr-3"></i> Schedules
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.permissions.index') }}" class="flex items-center px-6 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                        <i class="fas fa-shield-alt mr-3"></i> Permissions
                    </a>
                </li>
            </ul>
        </nav>
    </aside>

    <!-- Main content -->
    <div class="flex-1 flex flex-col">
        <!-- Header -->
        <header class="bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center shadow-sm">
            <h1 class="text-lg font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h1>
            <div class="flex items-center space-x-4">
                <span class="text-gray-600">{{ Auth::user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                        Logout
                    </button>
                </form>
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 p-6 bg-gray-50">
            @yield('admin-content')
        </main>
    </div>
</div>
@endsection