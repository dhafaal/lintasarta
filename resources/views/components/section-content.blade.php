<div class="mb-6 p-2">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-700">{{ $title }}</h1>
            <p class="text-sm font-medium text-gray-500">{{ $subtitle }}</p>
        </div>
        <div class="space-x-2">
            {{ $actions ?? '' }}
        </div>
    </div>

    {{ $slot }}
</div>