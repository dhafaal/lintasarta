@php
$menus = [
    ['label' => 'Dashboard', 'icon' => 'layout-dashboard', 'route' => 'admin.dashboard'],
    ['label' => 'Users', 'icon' => 'users', 'route' => 'admin.users.index'],
    ['label' => 'Shifts', 'icon' => 'clock', 'route' => 'admin.shifts.index'],
    ['label' => 'Schedules', 'icon' => 'calendar-days', 'route' => 'admin.schedules.index'],
];
@endphp

<aside id="sidebar"
    class="h-screen bg-white border-r border-gray-200 flex flex-col transition-[width] duration-200 ease-out w-64"
>
    <!-- Header -->
    <div class="flex items-center justify-between px-4 h-16 border-b">
        <span class="font-semibold text-gray-700 sidebar-label">Admin</span>
        <button id="sidebarToggle" class="text-gray-500 hover:text-gray-700">
            <i data-lucide="chevron-left"></i>
        </button>
    </div>

    <!-- Menu -->
    <nav class="flex-1 p-2 space-y-1">
        @foreach ($menus as $menu)
            <a href="{{ route($menu['route']) }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-100
               {{ request()->routeIs($menu['route']) ? 'bg-gray-100 font-medium text-gray-900' : '' }}">
                <i data-lucide="{{ $menu['icon'] }}" class="w-5 h-5 shrink-0"></i>
                <span class="sidebar-label text-sm">{{ $menu['label'] }}</span>
            </a>
        @endforeach
    </nav>
</aside>
