@extends('layouts.app')
@section('title', 'Schedules')

@section('content')
<x-section-content title="Schedules" subtitle="Manage all your user schedules here">
    <x-slot:actions class="flex justify-end mb-4">
        <a href="{{ route('admin.schedules.create') }}" class="btn btn-outline">
            Calendar
        </a>
        <a href="{{ route('admin.schedules.create') }}" class="btn btn-primary">
            Add Schedule
        </a>
    </x-slot:actions>

    <x-table :headers="['User', 'Shift', 'Date', 'Action']">
        @forelse ($schedules as $schedule)
            <x-table.row>
                <x-table.cell>{{ $schedule->user->name }}</x-table.cell>
                <x-table.cell>{{ $schedule->shift->name }}</x-table.cell>
                <x-table.cell>{{ \Carbon\Carbon::parse($schedule->schedule_date)->format('d M Y - H:i:s') }}</x-table.cell>
                <x-table.cell>
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
                </x-table.cell>
            </x-table.row>
        @empty
            <x-table.row>
                <x-table.cell colspan="4" class="text-center text-gray-500 py-6">
                    No schedules available.
                </x-table.cell>
            </x-table.row>
        @endforelse
    </x-table>
</x-section-content>
      </tbody>
    </table>
</div>
@endsection

