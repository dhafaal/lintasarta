@props([
    'title',
    'value',
    'color' => 'gray',
    'icon',
    'route' => null,
])

@php
$colors = [
    'gray' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-600'],
    'green' => ['bg' => 'bg-green-100', 'text' => 'text-green-600'],
    'orange' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-600'],
    'yellow' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-600'],
    'red' => ['bg' => 'bg-red-100', 'text' => 'text-red-600'],
    'sky' => ['bg' => 'bg-sky-100', 'text' => 'text-sky-600'],
];

$c = $colors[$color] ?? $colors['gray'];
@endphp

<div {{ $attributes->merge([
    'class' => "bg-white rounded-xl border border-gray-200 p-4 sm:p-6 hover:shadow-sm transition cursor-pointer"
]) }}>
    <div class="flex items-center justify-between">
        <div>
            <p class="text-xs sm:text-sm font-medium {{ $c['text'] }}">
                {{ $title }}
            </p>
            <p class="text-xl sm:text-2xl font-bold text-gray-900">
                {{ $value }}
            </p>
        </div>

        <div class="p-3 {{ $c['bg'] }} rounded-lg">
            <i data-lucide="{{ $icon }}" class="w-6 h-6 {{ $c['text'] }}"></i>
        </div>
    </div>

    @if ($route)
        <a href="{{ $route }}"
           class="mt-3 inline-flex items-center text-sm font-medium {{ $c['text'] }}">
            Manage
            <i data-lucide="chevron-right" class="w-4 h-4 ml-1"></i>
        </a>
    @endif
</div>
