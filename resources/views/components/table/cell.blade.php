@props([
    'class' => '',
])

<td class="px-6 py-4 space-x-2 {{ $class }} ">
    {{ $slot }}
</td>
