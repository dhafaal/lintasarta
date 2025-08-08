@props(['headers' => []])

<div class="overflow-x-auto bg-white rounded-xl shadow border border-gray-200">
    <table class="min-w-full divide-y divide-gray-200 text-sm text-left text-gray-700">
        <thead class="bg-gray-50 text-gray-800">
            <tr>
                @foreach ($headers as $header)
                    <th class="px-6 py-3 text-sm font-semibold uppercase tracking-wider">{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-100">
            {{ $slot }}
        </tbody>
    </table>
</div>
