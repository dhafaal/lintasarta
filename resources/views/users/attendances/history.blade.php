@extends('layouts.user')

@section('title', 'Attendance History')

@section('content')
<div class="min-h-screen bg-white">
    <div class="px-4 sm:px-6 lg:px-8 py-6 space-y-6">
        {{-- Header Section --}}
        <div class="mb-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-sky-500 rounded-xl flex items-center justify-center">
                    <i data-lucide="history" class="w-6 h-6 text-white"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Attendance History</h1>
                    <p class="text-sm text-gray-600">View your attendance records</p>
                </div>
            </div>
        </div>

        {{-- Filter Forms --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <!-- Date Range Filter -->
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 bg-sky-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="calendar-range" class="w-4 h-4 text-sky-600"></i>
                    </div>
                    <h3 class="text-base font-bold text-gray-900">Date Range Filter</h3>
                </div>
                <form method="GET" action="{{ route('user.attendances.history') }}" class="space-y-3">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label for="start_date" class="block text-xs font-medium text-gray-700 mb-1">Start Date</label>
                            <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        </div>
                        <div>
                            <label for="end_date" class="block text-xs font-medium text-gray-700 mb-1">End Date</label>
                            <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        </div>
                    </div>
                    <button type="submit" class="w-full bg-sky-500 hover:bg-sky-600 text-white font-medium py-2.5 px-4 rounded-lg transition-colors text-sm">
                        Apply Filter
                    </button>
                </form>
            </div>

            <!-- Month/Year Filter -->
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 bg-sky-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="clock" class="w-4 h-4 text-sky-600"></i>
                    </div>
                    <h3 class="text-base font-bold text-gray-900">Monthly Filter</h3>
                </div>
                <form method="GET" action="{{ route('user.attendances.history') }}" class="space-y-3">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Month</label>
                            <select name="month" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                                @for ($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ $m == $selectedMonth ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Year</label>
                            <select name="year" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                                @for ($y = now()->year - 3; $y <= now()->year + 3; $y++)
                                    <option value="{{ $y }}" {{ $y == $selectedYear ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="w-full bg-sky-500 hover:bg-sky-600 text-white font-medium py-2.5 px-4 rounded-lg transition-colors text-sm">
                        Apply Filter
                    </button>
                </form>
            </div>
        </div>

        {{-- Attendance Records Table --}}
        @if ($schedules->count() > 0)
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-5 py-4 bg-sky-50 border-b border-gray-200">
                    <h3 class="text-base font-bold text-gray-900">Attendance Records</h3>
                    <p class="text-sm text-gray-600 mt-1">{{ $schedules->count() }} records found</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <i data-lucide="calendar" class="w-4 h-4 inline mr-1"></i>
                                    Date
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <i data-lucide="clock" class="w-4 h-4 inline mr-1"></i>
                                    Shift
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <i data-lucide="map-pin" class="w-4 h-4 inline mr-1"></i>
                                    Location
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <i data-lucide="activity" class="w-4 h-4 inline mr-1"></i>
                                    Status
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <i data-lucide="log-in" class="w-4 h-4 inline mr-1"></i>
                                    Check-In
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <i data-lucide="log-out" class="w-4 h-4 inline mr-1"></i>
                                    Check-Out
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <i data-lucide="clock" class="w-4 h-4 inline mr-1"></i>
                                    Work Hours
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <i data-lucide="message-circle" class="w-4 h-4 inline mr-1"></i>
                                    Notes
                                </th>
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
                                            {{ \Carbon\Carbon::parse($schedule->schedule_date)->translatedFormat('l') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ $schedule->shift->shift_name ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($attendance && $attendance->location)
                                            <div class="flex items-center">
                                                <div class="w-6 h-6 bg-gradient-to-br from-sky-100 to-sky-200 rounded-lg flex items-center justify-center mr-2">
                                                    <i data-lucide="map-pin" class="w-3 h-3 text-sky-600"></i>
                                                </div>
                                                <div>
                                                    <div class="text-xs font-medium text-gray-900">{{ $attendance->location->name }}</div>
                                                    <div class="text-xs text-gray-500">{{ $attendance->location->radius }}m</div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-gray-400 text-xs">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusBase = $attendance->status ?? ($permission ? 'izin' : 'alpha');
                                            $hasForgot = ($statusBase === 'forgot_checkout');
                                            $hasEarly  = ($statusBase === 'early_checkout');
                                            // Consider early checkout permission to display secondary badge even if status is not early_checkout
                                            $hasEarlyPerm = false;
                                            if ($permission && $permission->type === 'izin') {
                                                $reasonStr = (string) ($permission->reason ?? '');
                                                if (strpos($reasonStr, '[EARLY_CHECKOUT]') === 0) {
                                                    // show secondary badge for any early checkout request (pending/approved/rejected)
                                                    $hasEarlyPerm = true;
                                                }
                                            }
                                            $wasLate   = ($statusBase === 'telat') || ($attendance && $attendance->is_late);
                                            $wasPresent= ($statusBase === 'hadir') || ($attendance && $attendance->check_in_time);
                                            // Priority like admin index
                                            $statusText = $statusBase;
                                            if ($statusBase !== 'izin') {
                                                if ($hasEarly) { $statusText = 'early_checkout'; }
                                                elseif ($wasLate) { $statusText = 'telat'; }
                                                elseif ($wasPresent) { $statusText = 'hadir'; }
                                                elseif ($hasForgot) { $statusText = 'forgot_checkout'; }
                                                else { $statusText = 'alpha'; }
                                            }
                                            $statusColor = 'bg-gray-100 text-gray-700';
                                            if($statusText === 'hadir') { $statusColor = 'bg-green-100 text-green-800'; }
                                            if($statusText === 'telat') { $statusColor = 'bg-orange-100 text-orange-800'; }
                                            if($statusText === 'izin') { $statusColor = 'bg-yellow-100 text-yellow-800'; }
                                            if($statusText === 'early_checkout') { $statusColor = 'bg-amber-100 text-amber-800'; }
                                            if($statusText === 'forgot_checkout') { $statusColor = 'bg-rose-100 text-rose-800'; }
                                            if($statusText === 'alpha') { $statusColor = 'bg-red-100 text-red-800'; }
                                            $showStacked = ($hasForgot || $hasEarly || $hasEarlyPerm) && ($wasLate || $wasPresent);
                                            $primaryText = $wasLate ? 'telat' : 'hadir';
                                            $primaryColor = $wasLate ? 'bg-orange-100 text-orange-800' : 'bg-green-100 text-green-800';
                                        @endphp
                                        @if($showStacked)
                                            <div class="flex flex-col space-y-1">
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $primaryColor }}">
                                                    {{ ucwords($primaryText) }}
                                                </span>
                                                @if($hasForgot)
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-rose-100 text-rose-800">
                                                        Forgot Checkout
                                                    </span>
                                                @endif
                                                @if($hasEarly || $hasEarlyPerm)
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                                        Early Checkout
                                                    </span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $statusColor }}">
                                                {{ ucwords(str_replace('_',' ', $statusText)) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($attendance && $attendance->check_in_time)
                                            <div class="flex items-start">
                                                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                                                    <i data-lucide="log-in" class="w-4 h-4 text-green-600"></i>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-semibold text-gray-900">{{ \Carbon\Carbon::parse($attendance->check_in_time)->format('H:i') }}</div>
                                                    <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($attendance->check_in_time)->format('d M Y') }}</div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-gray-400 text-sm">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($attendance && $attendance->check_out_time)
                                            <div class="flex items-start">
                                                <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                                                    <i data-lucide="log-out" class="w-4 h-4 text-red-600"></i>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-semibold text-gray-900">{{ \Carbon\Carbon::parse($attendance->check_out_time)->format('H:i') }}</div>
                                                    <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($attendance->check_out_time)->format('d M Y') }}</div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-gray-400 text-sm">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @php
                                            // Daily work based on total shift durations for this date/user, minus 1 hour if any
                                            $daySchedules = $schedules->where('schedule_date', $schedule->schedule_date)->where('user_id', $schedule->user_id);
                                            $dayMinutesAcc = 0;
                                            foreach ($daySchedules as $ds) {
                                                if (!$ds->shift) { continue; }
                                                $att = $attendances->firstWhere('schedule_id', $ds->id);
                                                $perm = $permissions->firstWhere('schedule_id', $ds->id);
                                                $start = \Carbon\Carbon::parse($ds->shift->start_time);
                                                $end = \Carbon\Carbon::parse($ds->shift->end_time);
                                                if ($end->lt($start)) { $end->addDay(); }
                                                $shiftMinutes = $start->diffInMinutes($end);
                                                if ($att && $att->status === 'alpha') {
                                                    $m = 0;
                                                } elseif (!$att && !$perm) {
                                                    $m = 0; // auto-alpha
                                                } else {
                                                    $m = $shiftMinutes;
                                                }
                                                $dayMinutesAcc += $m;
                                            }
                                            $dayMinutesAfterBreak = $dayMinutesAcc > 0 ? max(0, $dayMinutesAcc - 60) : 0;
                                            $hours = $dayMinutesAfterBreak / 60;
                                        @endphp
                                        {{ $hours == floor($hours) ? floor($hours).' h' : number_format($hours, 1).' h' }}
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
            <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
                <div class="w-16 h-16 bg-sky-100 rounded-xl flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="file-x" class="w-8 h-8 text-sky-600"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">No Records Found</h3>
                <p class="text-sm text-gray-600 mb-6">No attendance records found for the selected period.</p>
                
                <a href="{{ route('user.attendances.index') }}"
                   class="inline-flex items-center gap-2 bg-sky-500 hover:bg-sky-600 text-white font-medium py-2.5 px-5 rounded-lg transition-colors">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    <span>Back to Dashboard</span>
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
