<a
    class="rounded-2xl border-2 border-sky-100 bg-white p-6 shadow-md transition-shadow hover:border-sky-300 hover:shadow-lg"
>
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm font-bold tracking-wide text-sky-600 uppercase">{{ $title }}</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ $count }}</p>
            <p class="mt-1 text-xs text-gray-500">{{ $subtitle }}</p>
        </div>
        <div class="{{ $bgColor }} flex h-14 w-14 items-center justify-center rounded-xl">
            {!! $icon !!}
        </div>
    </div>
</a>
