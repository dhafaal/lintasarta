@extends('layouts.app')

@section('title', 'Schedules')

@section('content')
<x-section-content title="Schedules" subtitle="Manage all your user schedules here">
    <x-slot:actions class="flex justify-end mb-4">
        <a href="{{ route('admin.schedules.create') }}" class="btn btn-outline">
            Views Schedule
        </a>
        <a href="{{ route('admin.schedules.create') }}"
            class="btn btn-primary">
            Add Schedule
        </a>
    </x-slot:actions>

    <div class="overflow-x-auto bg-white rounded-xl shadow border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">User</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Shift</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($schedules as $schedule)
                <tr>
                    <td class="px-6 py-4 text-sm text-gray-800">{{ $schedule->user->name }}</td>
                    <td class="px-6 py-4 text-sm text-gray-800">{{ $schedule->shift->name }}</td>
                    <td class="px-6 py-4 text-sm text-gray-800">{{ \Carbon\Carbon::parse($schedule->schedule_date)->format('d M Y - H:i:s') }}</td>
                    <td class="px-6 py-4 text-sm text-right space-x-2">
                        <a href="{{ route('admin.schedules.edit', $schedule->id) }}"
                            class="inline-block px-3 py-1 text-yellow-600 border border-yellow-400 rounded hover:bg-yellow-50 hover:text-yellow-700">
                            Edit
                        </a>

                        <form action="{{ route('admin.schedules.destroy', $schedule->id) }}" method="POST" class="inline-block"
                            onsubmit="return confirm('Are you sure to delete this schedule?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="inline-block px-3 py-1 text-red-600 border border-red-400 rounded hover:bg-red-50 hover:text-red-700">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center py-6 text-gray-500">No schedules available.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-section-content>
@endsection