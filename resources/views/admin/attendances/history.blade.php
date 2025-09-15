@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto p-6 bg-white shadow rounded">
    <h2 class="text-xl font-bold mb-4">Riwayat Absensi - {{ \Carbon\Carbon::parse($date)->format('d M Y') }}</h2>

    {{-- Filter Tanggal + Search --}}
    <form method="GET" class="mb-4 flex gap-2">
        <input type="date" name="date" value="{{ $date }}" class="border px-3 py-2 rounded">
        <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama..."
            class="border px-3 py-2 rounded w-1/3">
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded shadow hover:bg-blue-700">
            Filter
        </button>
    </form>

    <table class="min-w-full border text-sm">
        <thead>
            <tr class="bg-gray-100 text-left">
                <th class="px-4 py-2 border">Nama</th>
                <th class="px-4 py-2 border">Shift</th>
                <th class="px-4 py-2 border">Tanggal</th>
                <th class="px-4 py-2 border">Check In</th>
                <th class="px-4 py-2 border">Check Out</th>
                <th class="px-4 py-2 border">Status</th>
                <th class="px-4 py-2 border">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($schedules as $schedule)
                @php
                    $attendance = $attendances->firstWhere('schedule_id', $schedule->id);
                    $permission = $permissions->firstWhere('schedule_id', $schedule->id);
                @endphp
                <tr>
                    <td class="px-4 py-2 border">{{ $schedule->user->name }}</td>
                    <td class="px-4 py-2 border">{{ $schedule->shift->name ?? '-' }}</td>
                    <td class="px-4 py-2 border">{{ \Carbon\Carbon::parse($schedule->schedule_date)->format('d M Y') }}</td>
                    <td class="px-4 py-2 border">
                        {{ $attendance?->check_in_time ? \Carbon\Carbon::parse($attendance->check_in_time)->format('H:i') : '-' }}
                    </td>
                    <td class="px-4 py-2 border">
                        {{ $attendance?->check_out_time ? \Carbon\Carbon::parse($attendance->check_out_time)->format('H:i') : '-' }}
                    </td>
                    <td class="px-4 py-2 border">
                        @if($attendance?->status === 'hadir')
                            <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-700">Hadir</span>
                        @elseif($attendance?->status === 'izin')
                            <span class="px-2 py-1 text-xs rounded bg-yellow-100 text-yellow-700">Izin</span>
                        @else
                            <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-700">Alpha</span>
                        @endif
                    </td>
                    <td class="px-4 py-2 border">
                        {{ $permission?->reason ?? '-' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
