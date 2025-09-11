<div class="{{ $bgColor }} rounded-2xl p-6 shadow-xl">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-{{ str_replace('bg-gradient-to-br from-', '', str_replace('-100 to-' . str_replace('green', 'emerald', $bgColor), '', $bgColor)) }}-600 text-sm font-medium uppercase tracking-wide">{{ $title }}</p>
            <p class="text-3xl font-bold mt-2 text-gray-700">{{ $count }}</p>
            <p class="text-{{ str_replace('bg-gradient-to-br from-', '', str_replace('-100 to-' . str_replace('green', 'emerald', $bgColor), '', $bgColor)) }}-500 text-xs mt-1">{{ $subtitle }}</p>
        </div>
        <div class="w-14 h-14 bg-{{ str_replace('bg-gradient-to-br from-', '', str_replace('-100 to-' . str_replace('green', 'emerald', $bgColor), '', $bgColor)) }}-400 bg-opacity-30 rounded-xl flex items-center justify-center">
            {!! $icon !!}
        </div>
    </div>
</div>