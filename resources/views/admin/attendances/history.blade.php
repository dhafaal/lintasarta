@extends('layouts.admin')

@section('title', 'Riwayat Absensi')

@section('content')
    <div class="min-h-screen bg-white sm:p-6 lg:p-8">
        <div class="mx-auto space-y-8">
            <!-- Minimalist Header -->
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div
                        class="w-14 h-14 bg-gradient-to-br from-sky-100 to-sky-200 rounded-2xl flex items-center justify-center shadow-lg">
                        <i data-lucide="calendar-clock" class="w-7 h-7 text-sky-600"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Riwayat Absensi</h1>
                        <p class="text-sm text-gray-500 mt-1">{{ \Carbon\Carbon::parse($date)->format('d M Y') }}</p>
                    </div>
                </div>
                <div class="bg-gray-100 hover:bg-gray-200 transition-colors border-2 border-gray-200 rounded-md flex items-center justify-center">
                <a href="{{ route('admin.attendances.index') }}"
                    class="inline-flex items-center px-4 py-2 text-sm text-black">
                    <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i>
                    Kembali
                </a>
                </div>
            </div>

            <!-- Minimalist Filter Section -->
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-br from-sky-50 to-blue-50">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-gray-700">Filter & Pencarian</h3>
                        @if ($search || $date != now()->toDateString())
                            <a href="{{ route('admin.attendances.history') }}"
                                class="text-xs text-sky-600 hover:text-sky-700 font-medium">
                                Reset
                            </a>
                        @endif
                    </div>
                </div>

                <div class="p-6">
                    <form method="GET" action="{{ url()->current() }}" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="history_date"
                                    class="block text-xs font-medium text-gray-700 mb-1">Tanggal</label>
                                <input type="date" name="date" id="history_date" value="{{ $date }}"
                                    class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-1 focus:ring-sky-500 focus:border-sky-500">
                            </div>

                            <div>
                                <label for="realtime_search" class="block text-xs font-medium text-gray-700 mb-1">Cari
                                    Karyawan</label>

                                <div class="relative">
                                    <input type="text" id="realtime_search"
                                        class="block w-full pl-9 pr-9 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500"
                                        placeholder="Ketik nama karyawan..." autocomplete="off">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i data-lucide="search" class="w-4 h-4 text-gray-400"></i>
                                    </div>
                                    <button type="button" id="clear_search"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 hidden">
                                        <i data-lucide="x" class="w-4 h-4"></i>
                                    </button>
                                </div>

                                <!-- Search Results Dropdown -->
                                <div id="history_user_search_results"
                                    class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg max-h-60 overflow-y-auto hidden">
                                    <div id="history_user_search_loading"
                                        class="px-4 py-3 text-sm text-gray-500 text-center hidden">
                                        <svg class="animate-spin h-4 w-4 mx-auto mb-1" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                        Mencari karyawan...
                                    </div>
                                    <div id="history_user_search_no_results"
                                        class="px-4 py-3 text-sm text-gray-500 text-center hidden">
                                        Tidak ada karyawan ditemukan
                                    </div>
                                    <div id="history_user_search_results_list" class="divide-y divide-gray-100">
                                        <!-- Results will be populated here -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end pt-2">
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white text-sm font-medium rounded-lg transition-colors">
                                <i data-lucide="filter" class="w-4 h-4 mr-2"></i>
                                Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Enhanced Table Section -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-br from-sky-50 to-blue-50 px-8 py-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-sky-100 to-sky-200 rounded-md flex items-center justify-center">
                            <i data-lucide="users" class="w-6 h-6 text-sky-600"></i>
                        </div>
                        Data Absensi Karyawan
                    </h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-white border-b-2 border-gray-200">
                            <tr>
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider w-48">
                                    <div class="flex items-center">
                                        <i data-lucide="user" class="w-4 h-4 text-sky-600 mr-2"></i>
                                        Nama
                                    </div>
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider w-56">
                                    <div class="flex items-center">
                                        <i data-lucide="clock" class="w-4 h-4 text-sky-600 mr-2"></i>
                                        Shift
                                    </div>
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider w-48">
                                    <div class="flex items-center">
                                        <i data-lucide="map-pin" class="w-4 h-4 text-sky-600 mr-2"></i>
                                        Lokasi
                                    </div>
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider w-40">
                                    <div class="flex items-center">
                                        <i data-lucide="calendar" class="w-4 h-4 text-sky-600 mr-2"></i>
                                        Tanggal
                                    </div>
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider w-48">
                                    <div class="flex items-center">
                                        <i data-lucide="log-in" class="w-4 h-4 text-sky-600 mr-2"></i>
                                        Check In
                                    </div>
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider w-48">
                                    <div class="flex items-center">
                                        <i data-lucide="log-out" class="w-4 h-4 text-sky-600 mr-2"></i>
                                        Check Out
                                    </div>
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider w-32">
                                    <div class="flex items-center">
                                        <i data-lucide="clock" class="w-4 h-4 text-sky-600 mr-2"></i>
                                        Jam Kerja
                                    </div>
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider w-32">
                                    <div class="flex items-center">
                                        <i data-lucide="activity" class="w-4 h-4 text-sky-600 mr-2"></i>
                                        Status
                                    </div>
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider w-64">
                                    <div class="flex items-center">
                                        <i data-lucide="message-circle" class="w-4 h-4 text-sky-600 mr-2"></i>
                                        Keterangan
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
                            @forelse($groupedSchedules as $groupKey => $userSchedules)
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
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div
                                                class="w-10 h-10 bg-gradient-to-br from-sky-100 to-sky-200 rounded-full flex items-center justify-center mr-3">
                                                <i data-lucide="user" class="w-5 h-5 text-sky-600"></i>
                                            </div>
                                            <div class="text-sm font-medium text-gray-900 max-w-[200px] truncate"
                                                title="{{ $firstSchedule->user->name }}">{{ $firstSchedule->user->name }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col space-y-1">
                                            @foreach ($sortedSchedules as $us)
                                                <div>
                                                    <div class="text-sm font-semibold text-gray-900 max-w-[160px] truncate"
                                                        title="{{ $us->shift->shift_name ?? '-' }}">
                                                        {{ $us->shift->shift_name ?? '-' }}</div>
                                                    <div class="text-xs text-gray-500 mt-0.5">
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
                                                                class="ml-2">{{ \Carbon\Carbon::parse($us->shift->start_time)->format('H:i') }}
                                                                -
                                                                {{ \Carbon\Carbon::parse($us->shift->end_time)->format('H:i') }}</span>
                                                        @else
                                                            -
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if ($location)
                                            <div class="flex items-center">
                                                <div
                                                    class="w-8 h-8 bg-gradient-to-br from-sky-100 to-sky-200 rounded-lg flex items-center justify-center mr-3">
                                                    <i data-lucide="map-pin" class="w-4 h-4 text-sky-600"></i>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-semibold text-gray-900 max-w-[140px] truncate"
                                                        title="{{ $location->name }}">{{ $location->name }}</div>
                                                    <div class="text-xs text-gray-500">Radius: {{ $location->radius }}m
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-gray-400 text-sm">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-gray-900">
                                            {{ \Carbon\Carbon::parse($firstSchedule->schedule_date)->format('d M Y') }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ \Carbon\Carbon::parse($firstSchedule->schedule_date)->translatedFormat('l') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
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
                                    <td class="px-6 py-4 whitespace-nowrap">
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
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @php
                                            // Calculate work hours for all shifts in this group
                                            $dayMinutesAcc = 0;
                                            foreach ($sortedSchedules as $ds) {
                                                if (!$ds->shift) {
                                                    continue;
                                                }
                                                $att = $attendances->firstWhere('schedule_id', $ds->id);
                                                $perm = $permissions->firstWhere('schedule_id', $ds->id);
                                                $start = \Carbon\Carbon::parse($ds->shift->start_time);
                                                $end = \Carbon\Carbon::parse($ds->shift->end_time);
                                                if ($end->lt($start)) {
                                                    $end->addDay();
                                                }
                                                $shiftMinutes = $start->diffInMinutes($end);
                                                if ($att && $att->status === 'alpha') {
                                                    $m = 0;
                                                } elseif (!$att && !$perm) {
                                                    $m = 0; // auto-alpha when absent without permission
                                                } else {
                                                    $m = $shiftMinutes;
                                                }
                                                $dayMinutesAcc += $m;
                                            }
                                            $dayMinutesAfterBreak =
                                                $dayMinutesAcc > 0 ? max(0, $dayMinutesAcc - 60) : 0;
                                            $hours = $dayMinutesAfterBreak / 60;
                                        @endphp
                                        {{ $hours == floor($hours) ? floor($hours) . ' jam' : number_format($hours, 1) . ' jam' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
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
                                        @endphp
                                        @if ($showStacked)
                                            <div class="flex flex-col space-y-1">
                                                <span
                                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $primaryColor }}">
                                                    {{ ucwords($primaryText) }}
                                                </span>
                                                @if ($hasForgot)
                                                    <span
                                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-rose-100 text-rose-800">
                                                        Forgot Checkout
                                                    </span>
                                                @endif
                                                @if ($hasEarly || $hasEarlyPerm)
                                                    <span
                                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                                        Early Checkout
                                                    </span>
                                                @endif
                                            </div>
                                        @else
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $statusColor }}">
                                                {{ ucwords(str_replace('_', ' ', $statusText)) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="max-w-[200px] line-clamp-2 text-sm text-gray-600 break-words"
                                            title="{{ $permission?->reason ?? '-' }}">
                                            {{ $permission?->reason ?? '-' }}
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <div
                                                class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                                <i data-lucide="search-x" class="w-8 h-8 text-gray-400"></i>
                                            </div>
                                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Tidak ada data</h3>
                                            <p class="text-gray-500">Tidak ada riwayat absensi untuk tanggal yang dipilih.
                                            </p>
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
                <div class="flex justify-center mt-6">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                        {{ $schedules->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        // Initialize Lucide icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

        // History User Search Functionality
        document.addEventListener("DOMContentLoaded", function() {
            // DOM Elements
            const historyUserSearchInput = document.getElementById('history_user_search');
            const historyUserSearchResults = document.getElementById('history_user_search_results');
            const historyUserSearchResultsList = document.getElementById('history_user_search_results_list');
            const historyUserSearchLoading = document.getElementById('history_user_search_loading');
            const historyUserSearchNoResults = document.getElementById('history_user_search_no_results');
            const historySearchValue = document.getElementById('history_search_value');
            const historySelectedUserDisplay = document.getElementById('history_selected_user_display');
            const historySelectedUserName = document.getElementById('history_selected_user_name');
            const historySelectedUserEmail = document.getElementById('history_selected_user_email');
            const historyDateInput = document.getElementById('history_date');
            const historyClearSearch = document.getElementById('history_clear_search');
            const historySearchContainer = document.getElementById('history_search_container');

            // Users data
            const historyUsersData = [
                @foreach ($users ?? [] as $user)
                    {
                        id: {{ $user->id }},
                        name: "{{ $user->name }}",
                        email: "{{ $user->email ?? '' }}"
                    },
                @endforeach
            ];

            let historyForm = null;
            let historySearchTimeout;

            // Initialize form and search
            function initializeHistorySearch() {
                if (historyUserSearchInput && historyDateInput) {
                    historyForm = historyUserSearchInput.closest('form');

                    // Initialize search if there's existing search value
                    if (historySearchValue && historySearchValue.value) {
                        const existingSearch = historySearchValue.value;
                        const matchedUser = historyUsersData.find(user => user.name.toLowerCase() === existingSearch
                            .toLowerCase());
                        if (matchedUser) {
                            historyUserSearchInput.value = matchedUser.name;
                            showSelectedUser(matchedUser);
                            toggleClearButton(true);
                        }
                    }

                    console.log('History Search Initialized:', {
                        hasSearchInput: !!historyUserSearchInput,
                        hasDateInput: !!historyDateInput,
                        hasForm: !!historyForm,
                        usersCount: historyUsersData.length,
                        currentSearch: historySearchValue?.value
                    });
                }
            }

            // Initialize immediately
            initializeHistorySearch();

            // Initialize history user search
            if (historyUserSearchInput) {
                historyUserSearchInput.addEventListener('input', function() {
                    const query = this.value.trim();

                    // Show/hide clear button
                    toggleClearButton(query.length > 0);

                    // Clear previous timeout
                    clearTimeout(historySearchTimeout);

                    // Hide results if query is empty
                    if (!query) {
                        hideHistorySearchResults();
                        return;
                    }

                    // Show loading state
                    showHistoryLoadingState();

                    // Debounce search to avoid too many requests
                    historySearchTimeout = setTimeout(() => {
                        performHistoryUserSearch(query);
                    }, 300);
                });

                // Hide results when clicking outside
                document.addEventListener('click', function(e) {
                    if (!historySearchContainer || !historySearchContainer.contains(e.target)) {
                        hideHistorySearchResults();
                    }
                });
            }

            // Clear search button
            if (historyClearSearch) {
                historyClearSearch.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    clearHistoryUserSelection();
                    hideHistorySearchResults();
                    toggleClearButton(false);
                });
            }

            // Auto-submit form when date changes
            if (historyDateInput) {
                historyDateInput.addEventListener('change', function() {
                    if (historyForm) {
                        console.log('Date changed, submitting form');

                        // Ensure all form data is preserved
                        const formData = new FormData(historyForm);
                        const params = new URLSearchParams(formData);

                        // Submit with updated parameters
                        window.location.href = window.location.pathname + '?' + params.toString();
                    }
                });
            }

            function performHistoryUserSearch(query) {
                // Filter users based on query
                const filteredUsers = historyUsersData.filter(user =>
                    user.name.toLowerCase().includes(query.toLowerCase()) ||
                    user.email.toLowerCase().includes(query.toLowerCase())
                );

                // Show results
                showHistorySearchResults(filteredUsers, query);
            }

            function showHistorySearchResults(users, query) {
                if (!historyUserSearchResultsList) return;

                historyUserSearchResultsList.innerHTML = '';

                if (users.length === 0) {
                    hideHistoryLoadingState();
                    showHistoryNoResultsState();
                    return;
                }

                hideHistoryLoadingState();
                hideHistoryNoResultsState();

                users.forEach(user => {
                    const userItem = document.createElement('div');
                    userItem.className = 'px-4 py-3 hover:bg-gray-50 cursor-pointer transition-colors';
                    userItem.innerHTML = `
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-sky-100 rounded-full flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-900">${highlightHistoryText(user.name, query)}</div>
                            <div class="text-xs text-gray-500">${highlightHistoryText(user.email, query)}</div>
                        </div>
                    </div>
                `;

                    userItem.addEventListener('click', () => selectHistoryUser(user));
                    historyUserSearchResultsList.appendChild(userItem);
                });

                if (historyUserSearchResults) {
                    historyUserSearchResults.classList.remove('hidden');
                }
            }

            function selectHistoryUser(user) {
                if (!historySearchValue || !historyUserSearchInput) return;

                historySearchValue.value = user.name;
                historyUserSearchInput.value = user.name;

                // Show selected user display
                showSelectedUser(user);

                // Hide search results
                hideHistorySearchResults();

                // Show clear button
                toggleClearButton(true);

                // Submit form for immediate filtering
                if (historyForm) {
                    console.log('Submitting form for user:', user.name);

                    // Ensure all form data is preserved
                    const formData = new FormData(historyForm);
                    const params = new URLSearchParams(formData);

                    // Update the search parameter
                    params.set('search', user.name);

                    // Submit with updated parameters
                    window.location.href = window.location.pathname + '?' + params.toString();
                }
            }

            function showSelectedUser(user) {
                if (!historySelectedUserName || !historySelectedUserEmail || !historySelectedUserDisplay) return;

                historySelectedUserName.textContent = user.name;
                historySelectedUserEmail.textContent = user.email;
                historySelectedUserDisplay.classList.remove('hidden');
            }

            window.clearHistoryUserSelection = function() {
                if (!historySearchValue || !historyUserSearchInput) return;

                historySearchValue.value = '';
                historyUserSearchInput.value = '';
                historySelectedUserName.textContent = '';
                historySelectedUserEmail.textContent = '';
                historySelectedUserDisplay.classList.add('hidden');
                toggleClearButton(false);

                // Submit form to clear search results
                if (historyForm) {
                    console.log('Clearing search, submitting form');

                    // Ensure all form data is preserved but remove search parameter
                    const formData = new FormData(historyForm);
                    const params = new URLSearchParams(formData);

                    // Remove search parameter
                    params.delete('search');

                    // Submit with updated parameters
                    window.location.href = window.location.pathname + '?' + params.toString();
                }
            }

            function toggleClearButton(show) {
                if (historyClearSearch) {
                    if (show) {
                        historyClearSearch.classList.remove('hidden');
                    } else {
                        historyClearSearch.classList.add('hidden');
                    }
                }
            }

            function highlightHistoryText(text, query) {
                if (!query || !text) return text;

                const regex = new RegExp(`(${query})`, 'gi');
                return text.replace(regex, '<mark class="bg-yellow-200 text-yellow-800">$1</mark>');
            }

            function showHistoryLoadingState() {
                if (!historyUserSearchResults || !historyUserSearchLoading || !historyUserSearchNoResults || !
                    historyUserSearchResultsList) return;

                historyUserSearchResults.classList.remove('hidden');
                historyUserSearchLoading.classList.remove('hidden');
                historyUserSearchNoResults.classList.add('hidden');
                historyUserSearchResultsList.innerHTML = '';
            }

            function hideHistoryLoadingState() {
                if (historyUserSearchLoading) {
                    historyUserSearchLoading.classList.add('hidden');
                }
            }

            function showHistoryNoResultsState() {
                if (!historyUserSearchResults || !historyUserSearchNoResults) return;

                historyUserSearchResults.classList.remove('hidden');
                historyUserSearchNoResults.classList.remove('hidden');
            }

            function hideHistoryNoResultsState() {
                if (historyUserSearchNoResults) {
                    historyUserSearchNoResults.classList.add('hidden');
                }
            }

            function hideHistorySearchResults() {
                if (historyUserSearchResults) {
                    historyUserSearchResults.classList.add('hidden');
                    hideHistoryLoadingState();
                    hideHistoryNoResultsState();
                }
            }

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

                    // Filter table rows
                    let visibleCount = 0;
                    tableRows.forEach(row => {
                        const nameCell = row.querySelector('td:first-child');
                        if (nameCell) {
                            const name = nameCell.textContent.toLowerCase();
                            if (name.includes(searchTerm)) {
                                row.style.display = '';
                                visibleCount++;
                            } else {
                                row.style.display = 'none';
                            }
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
                            <td colspan="9" class="px-6 py-12 text-center">
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
