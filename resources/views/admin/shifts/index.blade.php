@extends('layouts.app')

@section('title', 'Daftar Shift')

@section('content')
    <x-section-content title="Shifts" subtitle="Manage your data shifts here">
        <x-slot:actions>
            <a href="{{ route('admin.shifts.create') }}" class="btn btn-outline">
                See Users
            </a>
            <a href="{{ route('admin.shifts.create') }}" class="btn btn-primary">
                Add Shift
            </a>
        </x-slot:actions>

        @if(session('success'))
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <x-table :headers="['Session', 'Started', 'Ended', 'Action']"> 
            @forelse ($shifts as $shift)
                <x-table.row>
                    <x-table.cell>{{ $shift->name }}</x-table.cell>
                    <x-table.cell>{{ $shift->start_time }}</x-table.cell>
                    <x-table.cell>{{ $shift->end_time }}</x-table.cell>
                    <x-table.cell class="flex items-center gap-2">
                        <a href="{{ route('admin.shifts.edit', $shift->id) }}"
                           class="text-blue-600 hover:underline">Edit</a>
                        <form action="{{ route('admin.shifts.destroy', $shift->id) }}" method="POST"
                              onsubmit="return confirm('Hapus shift ini?')">
                            @csrf 
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                        </form>
                    </x-table.cell>
                </x-table.row>
            @empty
                <x-table.row>
                    <x-table.cell colspan="4" class="text-center text-gray-500 py-4">
                        Belum ada data shift.
                    </x-table.cell>
                </x-table.row>
            @endforelse
        </x-table>
    </x-section-content>
@endsection
