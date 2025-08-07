@props([
    'options' => [],       // array of options (value => label)
    'name' => '',          // input name
    'selected' => null,    // selected value
    'placeholder' => 'Select',
])

<div x-data="{
    open: false,
    selected: '{{ $selected }}',
    display: '{{ $selected ? ($options[$selected] ?? $selected) : $placeholder }}',
    select(value, label) {
        this.selected = value;
        this.display = label;
        this.open = false;
        $refs.input.value = value;
    }
}" class="relative inline-block w-48 text-left">
    <input type="hidden" name="{{ $name }}" x-ref="input" :value="selected" />

    <button type="button" @click="open = !open" 
        class="w-full bg-white border border-gray-300 rounded-md px-4 py-2 text-sm text-left flex justify-between items-center">
        <div class="flex items-center">
            <span class="font-semibold text-gray-900">Filter : <span class="pr-1"></span> </span><span class="text-gray-500" x-text="display"></span>
        </div>
        <svg :class="{'rotate-180 transition-transform': open === true }" class="w-4 h-4 ml-2 rotate-0 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    <ul x-show="open" @click.outside="open = false" 
        x-transition 
        class="absolute z-10 mt-1 w-full space-y-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-auto p-2">
        @foreach ($options as $value => $label)
            <li @click="select('{{ $value }}', '{{ $label }}')" 
                class="cursor-pointer px-4 py-2 rounded-md hover:bg-gray-100 hover:text-gray-500"
                :class="{'text-blue-500 bg-blue-100': selected === '{{ $value }}'}">
                {{ $label }}
            </li>
        @endforeach
    </ul>
</div>
