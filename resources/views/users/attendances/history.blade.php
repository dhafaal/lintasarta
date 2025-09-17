@extends('layouts.user')

@section('title', 'Attendance History')

@section('content')
<div class="p-6 space-y-4">
    <!-- Filter Form -->
    <div class="bg-white rounded-lg border border-gray-200 p-4">
        <form method="GET" action="{{ route('user.attendances.history') }}" class="flex flex-wrap gap-3 items-end">
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}"
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}"
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                Filter
            </button>
        </form>
    </div>

    <!-- Additional Filter -->
    <div class="bg-white rounded-lg border border-gray-200 p-4">
        <form method="GET" action="{{ route('user.attendances.history') }}" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Month</label>
                <select name="month" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                    @for ($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $m == $selectedMonth ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                <select name="year" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                    @for ($y = now()->year - 3; $y <= now()->year + 3; $y++)
                        <option value="{{ $y }}" {{ $y == $selectedYear ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                Filter
            </button>
        </form>
    </div>

    @if ($schedules->count() > 0)
        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium text-gray-700 border-b border-gray-200">Date</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-700 border-b border-gray-200">Shift</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-700 border-b border-gray-200">Status</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-700 border-b border-gray-200">Check-In</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-700 border-b border-gray-200">Check-Out</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-700 border-b border-gray-200">Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($schedules as $schedule)
                            @php
                                $attendance = $attendances->firstWhere('schedule_id', $schedule->id);
                                $permission = $permissions->firstWhere('schedule_id', $schedule->id);
                                $status = $attendance->status ?? ($permission ? 'izin' : 'alpha');
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 border-b border-gray-200">
                                    {{ \Carbon\Carbon::parse($schedule->schedule_date)->format('d M Y') }}
                                </td>
                                <td class="px-4 py-3 border-b border-gray-200">
                                    <span class="px-2 py-1 text-xs bg-gray-100 text-gray-700 rounded">
                                        {{ $schedule->shift->name ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 border-b border-gray-200">
                                    <span class="px-2 py-1 text-xs font-medium rounded
                                        @if ($status === 'hadir') bg-green-100 text-green-700
                                        @elseif ($status === 'izin') bg-yellow-100 text-yellow-700
                                        @elseif ($status === 'alpha') bg-red-100 text-red-700 @endif">
                                        {{ ucfirst($status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 border-b border-gray-200">
                                    {{ $attendance && $attendance->check_in_time ? \Carbon\Carbon::parse($attendance->check_in_time)->format('H:i') : '-' }}
                                </td>
                                <td class="px-4 py-3 border-b border-gray-200">
                                    {{ $attendance && $attendance->check_out_time ? \Carbon\Carbon::parse($attendance->check_out_time)->format('H:i') : '-' }}
                                </td>
                                <td class="px-4 py-3 border-b border-gray-200 text-gray-600">
                                    {{ $permission ? $permission->reason : '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="bg-white rounded-lg border border-gray-200 p-4 text-center">
            <p class="text-gray-600 mb-2">No attendance records found</p>
            <p class="text-sm text-gray-500">Try adjusting the filter period to see other data</p>
        </div>
    @endif
</div>
@endsection
