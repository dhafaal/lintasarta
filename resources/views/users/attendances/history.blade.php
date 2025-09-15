@extends('layouts.user')

@section('content')
<div class="max-w-6xl mx-auto p-6 bg-white shadow rounded-lg">
    <h2 class="text-2xl font-bold mb-6">Riwayat Absensi</h2>

    <!-- Filter & Search -->
    <form method="GET" class="flex flex-col sm:flex-row gap-3 mb-6">
        <input type="date" name="date" value="{{ $schedule_date }}" 
            class="border border-gray-300 px-3 py-2 rounded w-full sm:w-auto">
        <input type="text" name="search" value="{{ $search }}" placeholder="Cari shift..."
            class="border border-gray-300 px-3 py-2 rounded w-full sm:w-64">
        <button type="submit"
            class="px-4 py-2 bg-blue-600 text-white rounded shadow hover:bg-blue-700">
            Filter
        </button>
    </form>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full border border-gray-200 rounded-lg">
            <thead>
                <tr class="bg-gray-100 text-left text-sm font-semibold text-gray-700">
                    <th class="px-4 py-2 border">Tanggal</th>
                    <th class="px-4 py-2 border">Shift</th>
                    <th class="px-4 py-2 border">Check In</th>
                    <th class="px-4 py-2 border">Check Out</th>
                    <th class="px-4 py-2 border">Status</th>
                </tr>
            </thead>
            <tbody class="text-sm">
                @forelse($schedules as $schedule)
                    @php
                        $attendance = $schedule->attendances->first();
                        $permission = $schedule->permissions->first();
                        if ($attendance) {
                            $status = $attendance->status;
                        } elseif ($permission) {
                            $status = 'izin';
                        } else {
                            $status = 'alpha';
                        }
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 border">
                            {{ \Carbon\Carbon::parse($schedule->schedule_date)->format('d M Y') }}
                        </td>
                        <td class="px-4 py-2 border">{{ $schedule->shift->name ?? '-' }}</td>
                        <td class="px-4 py-2 border">
                            {{ $attendance && $attendance->check_in_time 
                                ? \Carbon\Carbon::parse($attendance->check_in_time)->format('H:i') 
                                : '-' }}
                        </td>
                        <td class="px-4 py-2 border">
                            {{ $attendance && $attendance->check_out_time 
                                ? \Carbon\Carbon::parse($attendance->check_out_time)->format('H:i') 
                                : '-' }}
                        </td>
                        <td class="px-4 py-2 border">
                            <span class="px-2 py-1 text-xs rounded
                                @if($status === 'hadir') bg-green-100 text-green-700
                                @elseif($status === 'izin') bg-yellow-100 text-yellow-700
                                @else bg-red-100 text-red-700 @endif">
                                {{ ucfirst($status) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-gray-500">
                            Tidak ada riwayat absensi
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        @if(method_exists($schedules, 'links'))
            {{ $schedules->appends(request()->query())->links() }}
        @endif
    </div>
</div>
@endsection
