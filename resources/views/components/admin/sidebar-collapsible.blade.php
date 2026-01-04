@props([
    'label',
    'icon' => null,
    'mainRoute',
    'activePattern',
])

@php
    $isActive = request()->routeIs($activePattern);
@endphp

<div
    x-data="{ open: {{ $isActive ? 'true' : 'false' }} }"
    class="space-y-1"
>
    <!-- Parent -->
    <button
        @click="(sidebarCollapsed && !isMobile)
            ? window.location.href='{{ route($mainRoute) }}'
            : open = !open"
        class="w-full flex items-center gap-3 rounded-lg px-4 py-2 text-sm font-semibold
        transition-colors
        {{ $isActive
            ? 'bg-sky-100 text-sky-700'
            : 'text-gray-600 hover:bg-gray-100' }}"
    >
        @if($icon)
            <i
                data-lucide="{{ $icon }}"
                class="w-5 h-5 shrink-0 {{ $isActive ? 'text-sky-700' : 'text-gray-500' }}"
            ></i>
        @endif

        <span
            x-show="!sidebarCollapsed || isMobile"
            class="flex-1 text-left"
        >
            {{ $label }}
        </span>

        <span
            x-show="!sidebarCollapsed || isMobile"
            class="text-xs transition-transform"
            :class="open ? 'rotate-90' : 'rotate-0'"
        >
            ▸
        </span>
    </button>

    <!-- Children -->
    <div
        x-show="open && (!sidebarCollapsed || isMobile)"
        x-cloak
        class="ml-4 border-l border-gray-200 pl-3 space-y-1"
    >
        {{ $slot }}
    </div>
</div>
