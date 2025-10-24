@extends('layouts.admin')

@section('title', 'Riwayat Jadwal')

@section('content')
    <div class="min-h-screen bg-white sm:p-6 lg:p-8">
        <div class="mx-auto space-y-8">
            <!-- Enhanced Header Section -->
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="flex items-center space-x-4">
                    <div
                        class="w-14 h-14 bg-gradient-to-br from-sky-100 to-sky-200 rounded-2xl flex items-center justify-center shadow-lg">
                        <i data-lucide="calendar-clock" class="w-7 h-7 text-sky-600"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Riwayat Jadwal</h1>
                        <p class="text-sm text-gray-600 mt-1">{{ $user->name }} • Kelola dan lihat riwayat penjadwalan
                            karyawan</p>
                    </div>
                </div>
                <div class="bg-gray-100 hover:bg-gray-200 transition-colors border-2 border-gray-200 rounded-md flex items-center justify-center">
                <a href="{{ route('admin.schedules.index') }}"
                    class="inline-flex items-center px-4 py-2 text-sm text-black">
                    <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                    Kembali
                </a>
                </div>
            </div>

            <!-- Compact User Info -->
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-sky-100 rounded-lg flex items-center justify-center">
                            <span class="text-sm font-bold text-sky-600">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900">{{ $user->name }}</h3>
                            <p class="text-xs text-gray-500">{{ $user->email }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-500">Total</p>
                        <p class="text-xl font-bold text-gray-900">
                            {{ method_exists($schedules, 'total') ? $schedules->total() : $schedules->count() }}</p>
                    </div>
                </div>
            </div>

            <!-- Minimalist Filter Section -->
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-sky-gray-200 bg-gradient-to-br from-sky-50 to-blue-50">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-gray-700">Filter Periode</h3>
                        @if (request('start_date') || request('end_date'))
                            <a href="{{ route('admin.schedules.history', $user->id) }}"
                                class="text-xs text-sky-600 hover:text-sky-700 font-medium">
                                Reset
                            </a>
                        @endif
                    </div>
                </div>

                <div class="p-6">
                    <form method="GET" action="{{ route('admin.schedules.history', $user->id) }}" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="start_date" class="block text-xs font-medium text-gray-600 mb-2">
                                    Dari Tanggal
                                </label>
                                <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}"
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                            </div>

                            <div>
                                <label for="end_date" class="block text-xs font-medium text-gray-600 mb-2">
                                    Sampai Tanggal
                                </label>
                                <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}"
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                            </div>

                            <div class="flex items-end">
                                <button type="submit"
                                    class="w-full inline-flex items-center justify-center px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white text-sm font-medium rounded-lg transition-colors">
                                    <i data-lucide="filter" class="w-4 h-4 mr-2"></i>
                                    Filter
                                </button>
                            </div>
                        </div>

                        <!-- Realtime Search -->
                        <div>
                            <label for="realtime_search" class="block text-xs font-medium text-gray-600 mb-2">
                                Cari di Tabel (Realtime)
                            </label>
                            <div class="relative">
                                <input type="text" id="realtime_search"
                                    class="block w-full pl-9 pr-9 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500"
                                    placeholder="Ketik untuk mencari..." autocomplete="off">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i data-lucide="search" class="w-4 h-4 text-gray-400"></i>
                                </div>
                                <button type="button" id="clear_search"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 hidden">
                                    <i data-lucide="x" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Enhanced Table Section -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-sky-50 to-blue-50 px-8 py-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-sky-100 to-sky-200 rounded-md flex items-center justify-center">
                            <i data-lucide="history" class="w-6 h-6 text-sky-600"></i>
                        </div>
                        Data Riwayat Jadwal
                    </h3>
                </div>

                <div class="overflow-x-auto">   
                    <table class="w-full">
                        <thead class="bg-white border-b-2 border-gray-200">
                            <tr>
                                <th class="px-8 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="lucide lucide-clock text-sky-600 mr-2">
                                            <circle cx="12" cy="12" r="10" />
                                            <polyline points="12 6 12 12 16 14" />
                                        </svg>
                                        Shift
                                    </div>
                                </th>
                                <th class="px-8 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <i data-lucide="map-pin" class="w-4 h-4 text-sky-600 mr-2"></i>
                                        Lokasi
                                    </div>
                                </th>
                                <th class="px-8 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="lucide lucide-calendar text-sky-600 mr-2">
                                            <path d="M8 2v4" />
                                            <path d="M16 2v4" />
                                            <rect width="18" height="18" x="3" y="4" rx="2" />
                                            <path d="M3 10h18" />
                                        </svg>
                                        Tanggal
                                    </div>
                                </th>
                                <th class="px-8 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="lucide lucide-log-in text-sky-600 mr-2">
                                            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" />
                                            <polyline points="10 17 15 12 10 7" />
                                            <line x1="15" x2="3" y1="12" y2="12" />
                                        </svg>
                                        Check In
                                    </div>
                                </th>
                                <th class="px-8 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="lucide lucide-log-out text-sky-600 mr-2">
                                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                                            <polyline points="16 17 21 12 16 7" />
                                            <line x1="21" x2="9" y1="12" y2="12" />
                                        </svg>
                                        Check Out
                                    </div>
                                </th>
                                <th class="px-8 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="lucide lucide-activity text-sky-600 mr-2">
                                            <path d="M22 12h-4l-3 9L9 3l-3 9H2" />
                                        </svg>
                                        Status
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @php
                                // Group schedules by user_id and schedule_date for double shift support
                                $groupedSchedules = $schedules->groupBy(function ($schedule) {
                                    return $schedule->user_id . '_' . $schedule->schedule_date;
                                });
                            @endphp
                            @forelse ($groupedSchedules as $groupKey => $userSchedules)
                                @php
                                    // Get first schedule for user info
                                    $firstSchedule = $userSchedules->first();

                                    // Sort schedules by shift category (Pagi -> Siang -> Malam)
                                    $order = ['Pagi' => 1, 'Siang' => 2, 'Malam' => 3];
                                    $sortedSchedules = $userSchedules->sortBy(function ($s) use ($order) {
                                        return $order[$s->shift->category ?? ''] ?? 99;
                                    });

                                    // Get schedule IDs for this group
                                    $scheduleIds = $sortedSchedules->pluck('id');

                                    // Get attendances and permissions for all schedules in this group
                                    $attGroup = $attendances->whereIn('schedule_id', $scheduleIds);
                                    $permGroup = $permissions->whereIn('schedule_id', $scheduleIds);

                                    // Get first schedule (Pagi if exists) for status calculation
                                    $firstShift = $sortedSchedules->first();
                                    $firstAttendance = $attendances->firstWhere('schedule_id', $firstShift->id);
                                    $firstPermission = $permissions->firstWhere('schedule_id', $firstShift->id);

                                    // Location: pick any available
                                    $firstWithLocation = $attGroup->first(function ($a) {
                                        return $a && $a->location;
                                    });
                                    $location = $firstWithLocation ? $firstWithLocation->location : null;

                                    // Times: earliest check-in, latest check-out
                                    $checkInTime = optional(
                                        $attGroup->whereNotNull('check_in_time')->sortBy('check_in_time')->first(),
                                    )->check_in_time;
                                    $checkOutTime = optional(
                                        $attGroup
                                            ->whereNotNull('check_out_time')
                                            ->sortByDesc('check_out_time')
                                            ->first(),
                                    )->check_out_time;
                                @endphp
                                <tr class="hover:bg-sky-50 transition-colors duration-200">
                                    <td class="px-8 py-6 whitespace-nowrap">
                                        <div class="flex flex-col space-y-1">
                                            @foreach ($sortedSchedules as $us)
                                                <div class="flex items-center">
                                                    @if ($us->shift && $us->shift->category == 'Pagi')
                                                        <div
                                                            class="w-8 h-8 bg-gradient-to-br from-yellow-100 to-orange-100 rounded-lg flex items-center justify-center mr-3 shadow-sm flex-shrink-0">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="14"
                                                                height="14" viewBox="0 0 24 24" fill="none"
                                                                stroke="currentColor" stroke-width="2"
                                                                stroke-linecap="round" stroke-linejoin="round"
                                                                class="lucide lucide-sun text-yellow-600">
                                                                <circle cx="12" cy="12" r="4" />
                                                                <path d="M12 2v2" />
                                                                <path d="M12 20v2" />
                                                                <path d="m4.93 4.93 1.41 1.41" />
                                                                <path d="m17.66 17.66 1.41 1.41" />
                                                                <path d="M2 12h2" />
                                                                <path d="M20 12h2" />
                                                                <path d="m6.34 17.66-1.41 1.41" />
                                                                <path d="m19.07 4.93-1.41 1.41" />
                                                            </svg>
                                                        </div>
                                                    @elseif($us->shift && $us->shift->category == 'Siang')
                                                        <div
                                                            class="w-8 h-8 bg-gradient-to-br from-orange-100 to-red-100 rounded-lg flex items-center justify-center mr-3 shadow-sm flex-shrink-0">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="14"
                                                                height="14" viewBox="0 0 24 24" fill="none"
                                                                stroke="currentColor" stroke-width="2"
                                                                stroke-linecap="round" stroke-linejoin="round"
                                                                class="lucide lucide-sun text-orange-600">
                                                                <circle cx="12" cy="12" r="4" />
                                                                <path d="M12 2v2" />
                                                                <path d="M12 20v2" />
                                                                <path d="m4.93 4.93 1.41 1.41" />
                                                                <path d="m17.66 17.66 1.41 1.41" />
                                                                <path d="M2 12h2" />
                                                                <path d="M20 12h2" />
                                                                <path d="m6.34 17.66-1.41 1.41" />
                                                                <path d="m19.07 4.93-1.41 1.41" />
                                                            </svg>
                                                        </div>
                                                    @elseif($us->shift && $us->shift->category == 'Malam')
                                                        <div
                                                            class="w-8 h-8 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-lg flex items-center justify-center mr-3 shadow-sm flex-shrink-0">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="14"
                                                                height="14" viewBox="0 0 24 24" fill="none"
                                                                stroke="currentColor" stroke-width="2"
                                                                stroke-linecap="round" stroke-linejoin="round"
                                                                class="lucide lucide-moon text-indigo-600">
                                                                <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9" />
                                                            </svg>
                                                        </div>
                                                    @else
                                                        <div
                                                            class="w-8 h-8 bg-gradient-to-br from-gray-100 to-gray-200 rounded-lg flex items-center justify-center mr-3 shadow-sm flex-shrink-0">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="14"
                                                                height="14" viewBox="0 0 24 24" fill="none"
                                                                stroke="currentColor" stroke-width="2"
                                                                stroke-linecap="round" stroke-linejoin="round"
                                                                class="lucide lucide-help-circle text-gray-500">
                                                                <circle cx="12" cy="12" r="10" />
                                                                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3" />
                                                                <path d="M12 17h.01" />
                                                            </svg>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <div class="text-sm font-bold text-gray-900">
                                                            {{ $us->shift->shift_name ?? '-' }}</div>
                                                        <div class="text-xs text-gray-500">
                                                            @if ($us->shift)
                                                                <span
                                                                    class="inline-flex items-center px-2 py-1 rounded-full text-[10px] font-medium 
                                                                @if ($us->shift->category == 'Pagi') bg-yellow-100 text-yellow-800
                                                                @elseif($us->shift->category == 'Siang') bg-orange-100 text-orange-800
                                                                @elseif($us->shift->category == 'Malam') bg-indigo-100 text-indigo-800
                                                                @else bg-gray-100 text-gray-800 @endif">
                                                                    {{ $us->shift->category }}
                                                                </span>
                                                                <span
                                                                    class="ml-1 text-[10px]">{{ \Carbon\Carbon::parse($us->shift->start_time)->format('H:i') }}
                                                                    -
                                                                    {{ \Carbon\Carbon::parse($us->shift->end_time)->format('H:i') }}</span>
                                                            @else
                                                                -
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 whitespace-nowrap">
                                        @if ($location)
                                            <div class="flex items-center">
                                                <div
                                                    class="w-8 h-8 bg-gradient-to-br from-sky-100 to-sky-200 rounded-lg flex items-center justify-center mr-3">
                                                    <i data-lucide="map-pin" class="w-4 h-4 text-sky-600"></i>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-semibold text-gray-900">{{ $location->name }}
                                                    </div>
                                                    <div class="text-xs text-gray-500">Radius: {{ $location->radius }}m
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-gray-400 text-sm">-</span>
                                        @endif
                                    </td>
                                    <td class="px-8 py-6 whitespace-nowrap">
                                        <div class="text-base font-semibold text-gray-900">
                                            {{ \Carbon\Carbon::parse($firstSchedule->schedule_date)->format('d M Y') }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ \Carbon\Carbon::parse($firstSchedule->schedule_date)->translatedFormat('l') }}
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 whitespace-nowrap">
                                        @if ($checkInTime)
                                            <div class="flex items-start">
                                                <div
                                                    class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                                                    <i data-lucide="log-in" class="w-4 h-4 text-green-600"></i>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-semibold text-gray-900">
                                                        {{ \Carbon\Carbon::parse($checkInTime)->format('H:i') }}</div>
                                                    <div class="text-xs text-gray-500">
                                                        {{ \Carbon\Carbon::parse($checkInTime)->format('d M Y') }}</div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-gray-400 text-sm">-</span>
                                        @endif
                                    </td>
                                    <td class="px-8 py-6 whitespace-nowrap">
                                        @if ($checkOutTime)
                                            <div class="flex items-start">
                                                <div
                                                    class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                                                    <i data-lucide="log-out" class="w-4 h-4 text-red-600"></i>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-semibold text-gray-900">
                                                        {{ \Carbon\Carbon::parse($checkOutTime)->format('H:i') }}</div>
                                                    <div class="text-xs text-gray-500">
                                                        {{ \Carbon\Carbon::parse($checkOutTime)->format('d M Y') }}</div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-gray-400 text-sm">-</span>
                                        @endif
                                    </td>
                                    <td class="px-8 py-6 whitespace-nowrap">
                                        @php
                                            // Status priority: izin > early_checkout > telat > hadir > forgot_checkout > alpha
                                            // Status based on FIRST shift (Pagi if exists) for consistency with index.blade.php
                                            $statusText = '-';
                                            if ($attGroup->where('status', 'izin')->isNotEmpty()) {
                                                $statusText = 'izin';
                                            } elseif ($attGroup->where('status', 'early_checkout')->isNotEmpty()) {
                                                $statusText = 'early_checkout';
                                            } elseif ($attGroup->where('status', 'telat')->isNotEmpty()) {
                                                $statusText = 'telat';
                                            } elseif ($attGroup->where('status', 'hadir')->isNotEmpty()) {
                                                $statusText = 'hadir';
                                            } elseif ($attGroup->where('status', 'forgot_checkout')->isNotEmpty()) {
                                                $statusText = 'forgot_checkout';
                                            } elseif ($attGroup->isNotEmpty()) {
                                                $statusText = optional($attGroup->first())->status ?: '-';
                                            }

                                            // Fallback: if no attendance and no permission, mark as alpha
                                            if (
                                                $statusText === '-' &&
                                                $sortedSchedules->isNotEmpty() &&
                                                $attGroup->isEmpty() &&
                                                $permGroup->isEmpty()
                                            ) {
                                                $statusText = 'alpha';
                                            }

                                            // Determine if we need stacked badges
                                            $hasForgot = $attGroup->where('status', 'forgot_checkout')->isNotEmpty();
                                            $hasEarly = $attGroup->where('status', 'early_checkout')->isNotEmpty();
                                            $wasLate =
                                                $attGroup
                                                    ->filter(function ($a) {
                                                        return $a && $a->is_late;
                                                    })
                                                    ->isNotEmpty() || $attGroup->where('status', 'telat')->isNotEmpty();
                                            $wasPresent =
                                                $attGroup
                                                    ->filter(function ($a) {
                                                        return $a && !is_null($a->check_in_time);
                                                    })
                                                    ->isNotEmpty() || $attGroup->where('status', 'hadir')->isNotEmpty();
                                            $showStacked = ($hasForgot || $hasEarly) && ($wasLate || $wasPresent);

                                            $statusColor = 'bg-gray-100 text-gray-700';
                                            if ($statusText === 'hadir') {
                                                $statusColor = 'bg-green-100 text-green-800';
                                            }
                                            if ($statusText === 'telat') {
                                                $statusColor = 'bg-orange-100 text-orange-800';
                                            }
                                            if ($statusText === 'izin') {
                                                $statusColor = 'bg-yellow-100 text-yellow-800';
                                            }
                                            if ($statusText === 'early_checkout') {
                                                $statusColor = 'bg-amber-100 text-amber-800';
                                            }
                                            if ($statusText === 'forgot_checkout') {
                                                $statusColor = 'bg-rose-100 text-rose-800';
                                            }
                                            if ($statusText === 'alpha') {
                                                $statusColor = 'bg-red-100 text-red-800';
                                            }

                                            $primaryText = $wasLate ? 'telat' : 'hadir';
                                            $primaryColor = $wasLate
                                                ? 'bg-orange-100 text-orange-800'
                                                : 'bg-green-100 text-green-800';
                                            $forgotColor = 'bg-rose-100 text-rose-800';
                                            $earlyColor = 'bg-amber-100 text-amber-800';
                                        @endphp
                                        <div class="flex flex-col space-y-2">
                                            @if ($showStacked)
                                                <div class="flex flex-col space-y-1">
                                                    <span
                                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $primaryColor }}">
                                                        {{ ucwords($primaryText) }}
                                                    </span>
                                                    @if ($hasForgot)
                                                        <span
                                                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-rose-100 text-rose-800">
                                                            Forgot Checkout
                                                        </span>
                                                    @endif
                                                    @if ($hasEarly)
                                                        <span
                                                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-amber-100 text-amber-800">
                                                            Early Checkout
                                                        </span>
                                                    @endif
                                                </div>
                                            @else
                                                <span
                                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusColor }}">
                                                    {{ ucwords(str_replace('_', ' ', $statusText)) }}
                                                </span>
                                            @endif
                                            @php
                                                $latestPerm = $permGroup->sortByDesc('created_at')->first();
                                            @endphp
                                            @if ($latestPerm && $latestPerm->reason)
                                                <div class="text-xs text-gray-600 bg-gray-50 px-2 py-1 rounded max-w-xs truncate"
                                                    title="{{ $latestPerm->reason }}">
                                                    <svg class="w-3 h-3 inline mr-1" fill="currentColor"
                                                        viewBox="0 0 8 8">
                                                        <circle cx="4" cy="4" r="3" />
                                                    </svg>
                                                    {{ $latestPerm->reason }}
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-8 py-16 text-center">
                                        <div class="flex flex-col items-center">
                                            <div
                                                class="w-20 h-20 bg-gradient-to-br from-sky-100 to-sky-200 rounded-full flex items-center justify-center mb-6">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="lucide lucide-calendar text-sky-400">
                                                    <path d="M8 2v4" />
                                                    <path d="M16 2v4" />
                                                    <rect width="18" height="18" x="3" y="4" rx="2" />
                                                    <path d="M3 10h18" />
                                                </svg>
                                            </div>
                                            <h3 class="text-xl font-bold text-gray-900 mb-2">Belum ada riwayat jadwal</h3>
                                            <p class="text-gray-600 mb-6 max-w-sm">Tidak ada riwayat jadwal untuk periode
                                                yang dipilih</p>
                                            <a href="{{ route('admin.schedules.create') }}"
                                                class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-sky-500 to-sky-600 hover:from-sky-600 hover:to-sky-700 text-white font-bold rounded-xl transition-all duration-200 transform   shadow-lg">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="lucide lucide-plus mr-2">
                                                    <path d="M12 5v14" />
                                                    <path d="M5 12h14" />
                                                </svg>
                                                Tambah Jadwal
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Enhanced Pagination -->
            @if (method_exists($schedules, 'links'))
                <div class="flex justify-center">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                        {{ $schedules->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Realtime Table Search
            const realtimeSearch = document.getElementById('realtime_search');
            const clearSearchBtn = document.getElementById('clear_search');
            const tableRows = document.querySelectorAll('tbody tr');

            if (realtimeSearch) {
                realtimeSearch.addEventListener('input', function(e) {
                    const searchTerm = e.target.value.toLowerCase().trim();

                    // Show/hide clear button
                    if (clearSearchBtn) {
                        if (searchTerm) {
                            clearSearchBtn.classList.remove('hidden');
                        } else {
                            clearSearchBtn.classList.add('hidden');
                        }
                    }

                    // Filter table rows - search in shift names and dates
                    let visibleCount = 0;
                    tableRows.forEach(row => {
                        const cells = row.querySelectorAll('td');
                        let rowText = '';
                        cells.forEach(cell => {
                            rowText += cell.textContent.toLowerCase() + ' ';
                        });

                        if (rowText.includes(searchTerm)) {
                            row.style.display = '';
                            visibleCount++;
                        } else {
                            row.style.display = 'none';
                        }
                    });

                    // Show no results message if needed
                    const tbody = document.querySelector('tbody');
                    let noResultsRow = tbody.querySelector('.no-results-row');

                    if (visibleCount === 0 && searchTerm) {
                        if (!noResultsRow) {
                            noResultsRow = document.createElement('tr');
                            noResultsRow.className = 'no-results-row';
                            noResultsRow.innerHTML = `
                        <td colspan="6" class="px-8 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <i data-lucide="search-x" class="w-12 h-12 text-gray-300 mb-3"></i>
                                <p class="text-gray-500 text-sm">Tidak ada hasil untuk "<span class="font-semibold">${searchTerm}</span>"</p>
                            </div>
                        </td>
                    `;
                            tbody.appendChild(noResultsRow);
                            lucide.createIcons();
                        }
                    } else if (noResultsRow) {
                        noResultsRow.remove();
                    }
                });

                // Clear search
                if (clearSearchBtn) {
                    clearSearchBtn.addEventListener('click', function() {
                        realtimeSearch.value = '';
                        realtimeSearch.dispatchEvent(new Event('input'));
                        realtimeSearch.focus();
                    });
                }
            }
        });
    </script>
@endsection
