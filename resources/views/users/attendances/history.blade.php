@extends('layouts.user')

@section('title', 'Riwayat Kehadiran')

@section('content')
<div class="max-w-5xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">📜 Riwayat Kehadiran</h1>

    {{-- Filter Tanggal --}}
    <form method="GET" action="{{ route('user.attendances.history') }}" class="mb-6 flex items-center gap-3">
        <input type="date" name="date" value="{{ $date ?? '' }}"
               class="border border-gray-300 rounded-lg px-3 py-2">
        <button type="submit"
                class="px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white rounded-lg shadow">
            Filter
        </button>
        @if(!empty($date))
            <a href="{{ route('user.attendances.history') }}"
               class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-lg">
               Reset
            </a>
        @endif
    </form>

    @if ($schedules->count() > 0)
        <div class="overflow-x-auto bg-white rounded-lg shadow border border-gray-200">
            <table class="min-w-full text-sm text-left border-collapse">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 border-b">Tanggal</th>
                        <th class="px-4 py-2 border-b">Shift</th>
                        <th class="px-4 py-2 border-b">Status</th>
                        <th class="px-4 py-2 border-b">Check-In</th>
                        <th class="px-4 py-2 border-b">Check-Out</th>
                        <th class="px-4 py-2 border-b">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($schedules as $schedule)
                        @php
                            $attendance = $attendances->firstWhere('schedule_id', $schedule->id);
                            $permission = $permissions->firstWhere('schedule_id', $schedule->id);

                            // Tentukan status otomatis jika tidak ada data
                            $status = $attendance->status ?? ($permission ? 'izin' : 'alpha');
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 border-b">{{ $schedule->schedule_date }}</td>
                            <td class="px-4 py-2 border-b">{{ $schedule->shift->name ?? '-' }}</td>
                            <td class="px-4 py-2 border-b">
                                <span class="px-2 py-1 text-xs rounded
                                    @if ($status === 'hadir') bg-green-100 text-green-700
                                    @elseif ($status === 'izin') bg-yellow-100 text-yellow-700
                                    @elseif ($status === 'alpha') bg-red-100 text-red-700 @endif">
                                    {{ ucfirst($status) }}
                                </span>
                            </td>
                            <td class="px-4 py-2 border-b">
                                {{ $attendance->check_in_time ?? '-' }}
                            </td>
                            <td class="px-4 py-2 border-b">
                                {{ $attendance->check_out_time ?? '-' }}
                            </td>
                            <td class="px-4 py-2 border-b">
                                {{ $permission->reason ?? '-' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="p-6 bg-yellow-50 border border-yellow-200 text-yellow-700 rounded-lg text-center">
            Tidak ada riwayat jadwal.
        </div>
    @endif
</div>
@endsection
