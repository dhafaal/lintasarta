@extends('layouts.app')

@section('content')
    <div class="mx-auto bg-white rounded-xl shadow p-6 mt-8">
        <h2 class="text-xl font-bold mb-6">Edit Shift</h2>

        <form action="{{ route('admin.shifts.update', $shift->id) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Nama Shift -->
            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-700">Nama Shift</label>
                <select name="name" class="w-full border rounded p-2 mt-2" required>
                    <option value="Pagi" {{ $shift->name == 'Pagi' ? 'selected' : '' }}>Pagi</option>
                    <option value="Siang" {{ $shift->name == 'Siang' ? 'selected' : '' }}>Siang</option>
                    <option value="Malam" {{ $shift->name == 'Malam' ? 'selected' : '' }}>Malam</option>
                </select>
            </div>

            <!-- Jam Mulai -->
            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-700">Jam Mulai</label>
                <input type="time" name="start_time"
                    value="{{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }}"
                    class="w-full border rounded p-2 mt-2" required>
            </div>

            <!-- Jam Selesai -->
            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-700">Jam Selesai</label>
                <input type="time" name="end_time" value="{{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }}"
                    class="w-full border rounded p-2 mt-2" required>
            </div>

            <div class="flex justify-end gap-4">
                <a href="{{ route('admin.shifts.index') }}" class="px-4 py-2 bg-gray-200 rounded">Batal</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Update</button>
            </div>
        </form>
    </div>
@endsection
