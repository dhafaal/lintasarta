@extends('layouts.user')

@section('title', 'Attendance History')

@section('content')
<div class="min-h-screen bg-white">
    <div class="container mx-auto px-4 py-8 space-y-8">
        {{-- Header Section --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-sky-500 to-sky-600 rounded-2xl shadow-lg mb-4">
                <i data-lucide="history" class="w-7 h-7 text-white"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Attendance History</h1>
            <p class="text-gray-600">View your attendance records and filter by date range</p>
        </div>

        {{-- Filter Forms --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Date Range Filter -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-10 h-10 bg-gradient-to-br from-sky-100 to-sky-200 rounded-xl flex items-center justify-center">
                        <i data-lucide="calendar-range" class="w-4 h-4 text-sky-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Date Range Filter</h3>
                </div>
                <form method="GET" action="{{ route('user.attendances.history') }}" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                            <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}"
                                class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-colors">
                        </div>
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                            <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}"
                                class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-colors">
                        </div>
                    </div>
                    <button type="submit" class="w-full bg-gradient-to-r from-sky-500 to-sky-600 hover:from-sky-600 hover:to-sky-700 text-white font-medium py-3 px-6 rounded-xl transition-all duration-200 transform hover:scale-105 shadow-sm hover:shadow-md">
                        Apply Date Filter
                    </button>
                </form>
            </div>

            <!-- Month/Year Filter -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-10 h-10 bg-gradient-to-br from-sky-100 to-sky-200 rounded-xl flex items-center justify-center">
                        <i data-lucide="clock" class="w-4 h-4 text-sky-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Monthly Filter</h3>
                </div>
                <form method="GET" action="{{ route('user.attendances.history') }}" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Month</label>
                            <select name="month" class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-colors">
                                @for ($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ $m == $selectedMonth ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Year</label>
                            <select name="year" class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-colors">
                                @for ($y = now()->year - 3; $y <= now()->year + 3; $y++)
                                    <option value="{{ $y }}" {{ $y == $selectedYear ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="w-full bg-gradient-to-r from-sky-500 to-sky-600 hover:from-sky-600 hover:to-sky-700 text-white font-medium py-3 px-6 rounded-xl transition-all duration-200 transform hover:scale-105 shadow-sm hover:shadow-md">
                        Apply Monthly Filter
                    </button>
                </form>
            </div>
        </div>

        {{-- Attendance Records Table --}}
        @if ($schedules->count() > 0)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-sky-50 to-sky-100 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Attendance Records</h3>
                    <p class="text-sm text-sky-600 mt-1">{{ $schedules->count() }} records found</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Shift</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check-In</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check-Out</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($schedules as $schedule)
                                @php
                                    $attendance = $attendances->firstWhere('schedule_id', $schedule->id);
                                    $permission = $permissions->firstWhere('schedule_id', $schedule->id);
                                    $status = $attendance->status ?? ($permission ? 'izin' : 'alpha');
                                @endphp
                                <tr class="hover:bg-sky-50/50 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ \Carbon\Carbon::parse($schedule->schedule_date)->format('d M Y') }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ \Carbon\Carbon::parse($schedule->schedule_date)->format('l') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ $schedule->shift->shift_name ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-col space-y-1">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                                @if ($status === 'hadir') bg-green-100 text-green-800
                                                @elseif ($status === 'izin') bg-blue-100 text-blue-800
                                                @elseif ($status === 'telat') bg-yellow-100 text-yellow-800
                                                @elseif ($status === 'alpha') bg-red-100 text-red-800 @endif">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 8 8">
                                                    <circle cx="4" cy="4" r="3"/>
                                                </svg>
                                                {{ ucfirst($status) }}
                                            </span>
                                            @if($permission && $permission->status)
                                                <span class="text-xs text-gray-500">
                                                    Izin: 
                                                    @if($permission->status === 'pending')
                                                        <span class="text-amber-600">Menunggu</span>
                                                    @elseif($permission->status === 'approved')
                                                        <span class="text-green-600">Disetujui</span>
                                                    @elseif($permission->status === 'rejected')
                                                        <span class="text-red-600">Ditolak</span>
                                                    @endif
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $attendance && $attendance->check_in_time ? \Carbon\Carbon::parse($attendance->check_in_time)->format('H:i') : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $attendance && $attendance->check_out_time ? \Carbon\Carbon::parse($attendance->check_out_time)->format('H:i') : '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        @if($permission)
                                            <div class="max-w-xs">
                                                <div class="font-medium text-gray-900 truncate" title="{{ $permission->reason }}">
                                                    {{ $permission->reason }}
                                                </div>
                                                @if($permission->approved_at)
                                                    <div class="text-xs text-gray-500 mt-1">
                                                        {{ $permission->status === 'approved' ? 'Disetujui' : 'Ditolak' }} pada 
                                                        {{ \Carbon\Carbon::parse($permission->approved_at)->format('d/m/Y H:i') }}
                                                    </div>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            {{-- No Records State --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-12 text-center">
                <div class="w-20 h-20 bg-gradient-to-br from-sky-100 to-sky-200 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i data-lucide="file-x" class="w-8 h-8 text-sky-600"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No Records Found</h3>
                <p class="text-gray-600 mb-6">No attendance records found for the selected period. Try adjusting your filter criteria to view other data.</p>
                
                <a href="{{ route('user.attendances.index') }}"
                   class="inline-flex items-center space-x-2 bg-gradient-to-r from-sky-500 to-sky-600 hover:from-sky-600 hover:to-sky-700 text-white font-medium py-3 px-6 rounded-xl transition-all duration-200 transform hover:scale-105 shadow-sm hover:shadow-md">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    <span>Back to Dashboard</span>
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
