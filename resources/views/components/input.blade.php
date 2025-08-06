@props([
    'label' => '',
    'name',
    'type' => 'text',
    'value' => '',
    'placeholder' => '',
    'required' => false,
])

<div class="space-y-1">
    @if($label)
    <label for="{{ $name }}" class="block text-sm font-semibold text-gray-800">{{ $label }}</label>
    @endif
    <input type="{{ $type }}" name="{{ $name }}" id="{{ $name }}"
        value="{{ old($name, $value) }}" placeholder="{{ $placeholder }}"
            @if($required) required @endif
        {{ $attributes->merge(['class' => 'w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500']) }}>
</div>