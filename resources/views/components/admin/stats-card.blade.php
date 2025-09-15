<a class="bg-white border-2 border-sky-100 hover:border-sky-300 rounded-2xl p-6 shadow-md hover:shadow-lg transition-shadow">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sky-600 text-sm font-bold uppercase tracking-wide">{{ $title }}</p>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $count }}</p>
            <p class="text-gray-500 text-xs mt-1">{{ $subtitle }}</p>
        </div>
        <div class="w-14 h-14 {{ $bgColor }} rounded-xl flex items-center justify-center">
            {!! $icon !!}
        </div>
    </div>
</a>