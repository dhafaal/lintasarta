@props([
    'route',
    'active',
])

<a href="{{ route($route) }}"
   @click="closeMobileMenu?.()"
   class="block rounded-md px-3 py-2 text-sm font-medium
   {{ request()->routeIs($active)
        ? 'bg-sky-100 text-sky-700'
        : 'text-gray-600 hover:bg-gray-100' }}">
    {{ $slot }}
</a>
