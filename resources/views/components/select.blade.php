@props([
    'name',
    'label' => '',
    'options' => [],
    'selected' => null,
    'required' => false,
])

@php
    $selected = $selected ?? old($name);
@endphp

<div class="space-y-2">
    @if ($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 mb-1">{{ $label }}</label>
    @endif
    <select
        name="{{ $name }}"
        id="{{ $name }}"
        {{ $required ? 'required' : '' }}
        {{ $attributes->merge(['class' => 'w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500']) }}
    >
        <option value="" disabled {{ !$selected ? 'selected' : '' }}>-- Select {{ Str::of($label ?: $name)->title() }} --</option>
        @foreach($options as $key => $value)
            <option value="{{ $key }}" {{ $selected == $key ? 'selected' : '' }}>
                {{ $value }}
            </option>
        @endforeach
    </select>
</div>
