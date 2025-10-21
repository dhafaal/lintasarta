@extends('layouts.admin')

@section('title', 'Riwayat Absensi')

@section('content')
<div class="min-h-screen bg-white sm:p-6 lg:p-8">
    <div class="mx-auto space-y-8">
        <!-- Enhanced Header Section -->
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center shadow-sm">
                    <i data-lucide="clock" class="w-6 h-6 text-sky-700"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-700 tracking-tight">Riwayat Absensi</h1>
                    <p class="text-gray-500 mt-1">{{ \Carbon\Carbon::parse($date)->format('d F Y') }} - Kelola dan lihat riwayat absensi karyawan</p>
                </div>
            </div>
        </div>

        <!-- Enhanced Filter Section -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-sky-500 to-sky-600 px-8 py-6">
                <h2 class="text-xl font-semibold text-white flex items-center">
                    <i data-lucide="filter" class="w-5 h-5 mr-2"></i>
                    Filter & Pencarian
                </h2>
                <p class="text-sky-100 mt-1">Pilih tanggal dan cari karyawan</p>
            </div>

            <div class="p-8">
                <form method="GET" action="{{ url()->current() }}" class="space-y-6">
                    <!-- Date and User Search Section -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Date Input -->
                        <div class="space-y-3">
                            <label for="history_date" class="block text-sm font-bold text-gray-800">
                                <i data-lucide="calendar" class="w-4 h-4 inline mr-1"></i>
                                Tanggal
                            </label>
                            <input type="date" name="date" id="history_date" value="{{ $date }}"
                                class="block w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-sky-100 focus:border-sky-500 transition-all duration-200 bg-gray-50 focus:bg-white">
                        </div>

                        <!-- User Search -->
                        <div class="space-y-3">
                            <label for="history_user_search" class="block text-sm font-bold text-gray-800">
                                <i data-lucide="search" class="w-4 h-4 inline mr-1"></i>
                                Cari Nama Karyawan
                            </label>

                            <!-- Search Container -->
                            <div class="relative" id="history_search_container">
                                <!-- Search Input -->
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                    <input type="text"
                                           id="history_user_search"
                                           class="block w-full pl-12 pr-12 py-4 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-sky-100 focus:border-sky-500 bg-gray-50 focus:bg-white transition-all duration-200"
                                           placeholder="Ketik untuk mencari karyawan..."
                                           autocomplete="off"
                                           value="{{ $search ?? '' }}">
                                    <input type="hidden" name="search" id="history_search_value" value="{{ $search ?? '' }}">

                                    <!-- Clear Button -->
                                    <button type="button" id="history_clear_search"
                                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 transition-colors hidden">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>

                                <!-- Search Results Dropdown -->
                                <div id="history_user_search_results"
                                     class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg max-h-60 overflow-y-auto hidden">
                                    <div id="history_user_search_loading" class="px-4 py-3 text-sm text-gray-500 text-center hidden">
                                        <svg class="animate-spin h-4 w-4 mx-auto mb-1" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Mencari karyawan...
                                    </div>
                                    <div id="history_user_search_no_results" class="px-4 py-3 text-sm text-gray-500 text-center hidden">
                                        Tidak ada karyawan ditemukan
                                    </div>
                                    <div id="history_user_search_results_list" class="divide-y divide-gray-100">
                                        <!-- Results will be populated here -->
                                    </div>
                                </div>
                            </div>

                            <!-- Selected User Display -->
                            <div id="history_selected_user_display" class="mt-3 hidden">
                                <div class="flex items-center justify-between p-3 bg-sky-50 border border-sky-200 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-sky-100 rounded-full flex items-center justify-center mr-3">
                                            <svg class="w-5 h-5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900" id="history_selected_user_name"></div>
                                            <div class="text-xs text-gray-500" id="history_selected_user_email"></div>
                                        </div>
                                    </div>
                                    <button type="button" onclick="clearHistoryUserSelection()"
                                            class="text-gray-400 hover:text-gray-600 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filter Button -->
                    <div class="flex justify-center">
                        <button type="submit" class="inline-flex items-center px-8 py-3 bg-sky-600 hover:bg-sky-700 text-white font-bold rounded-xl transition-all transform   focus:outline-none focus:ring-4 focus:ring-sky-200 shadow-sm hover:shadow-md">
                            <i data-lucide="search" class="w-5 h-5 mr-2"></i>
                            Filter Data
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Enhanced Table Section -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-8 py-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i data-lucide="users" class="w-5 h-5 mr-2"></i>
                    Data Absensi Karyawan
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b-2 border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider w-48">
                                <div class="flex items-center">
                                    <i data-lucide="user" class="w-4 h-4 text-sky-600 mr-2"></i>
                                    Nama
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider w-56">
                                <div class="flex items-center">
                                    <i data-lucide="clock" class="w-4 h-4 text-sky-600 mr-2"></i>
                                    Shift
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider w-48">
                                <div class="flex items-center">
                                    <i data-lucide="map-pin" class="w-4 h-4 text-sky-600 mr-2"></i>
                                    Lokasi
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider w-40">
                                <div class="flex items-center">
                                    <i data-lucide="calendar" class="w-4 h-4 text-sky-600 mr-2"></i>
                                    Tanggal
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider w-48">
                                <div class="flex items-center">
                                    <i data-lucide="log-in" class="w-4 h-4 text-sky-600 mr-2"></i>
                                    Check In
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider w-48">
                                <div class="flex items-center">
                                    <i data-lucide="log-out" class="w-4 h-4 text-sky-600 mr-2"></i>
                                    Check Out
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider w-32">
                                <div class="flex items-center">
                                    <i data-lucide="clock" class="w-4 h-4 text-sky-600 mr-2"></i>
                                    Jam Kerja
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider w-32">
                                <div class="flex items-center">
                                    <i data-lucide="activity" class="w-4 h-4 text-sky-600 mr-2"></i>
                                    Status
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider w-64">
                                <div class="flex items-center">
                                    <i data-lucide="message-circle" class="w-4 h-4 text-sky-600 mr-2"></i>
                                    Keterangan
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($schedules as $schedule)
                            @php
                                $attendance = $attendances->firstWhere('schedule_id', $schedule->id);
                                $permission = $permissions->firstWhere('schedule_id', $schedule->id);
                            @endphp
                            <tr class="hover:bg-sky-50 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gradient-to-br from-sky-100 to-sky-200 rounded-full flex items-center justify-center mr-3">
                                            <i data-lucide="user" class="w-5 h-5 text-sky-600"></i>
                                        </div>
                                        <div class="text-sm font-medium text-gray-900 max-w-[200px] truncate" title="{{ $schedule->user->name }}">{{ $schedule->user->name }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900 max-w-[160px] truncate" title="{{ $schedule->shift->shift_name ?? '-' }}">{{ $schedule->shift->shift_name ?? '-' }}</div>
                                        <div class="text-xs text-gray-500 mt-0.5">
                                            @if ($schedule->shift)
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-[10px] font-medium 
                                                    @if($schedule->shift->category == 'Pagi') bg-yellow-100 text-yellow-800
                                                    @elseif($schedule->shift->category == 'Siang') bg-orange-100 text-orange-800
                                                    @elseif($schedule->shift->category == 'Malam') bg-indigo-100 text-indigo-800
                                                    @else bg-gray-100 text-gray-800 @endif">
                                                    {{ $schedule->shift->category }}
                                                </span>
                                                <span class="ml-2">{{ \Carbon\Carbon::parse($schedule->shift->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->shift->end_time)->format('H:i') }}</span>
                                            @else
                                                -
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($attendance && $attendance->location)
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-gradient-to-br from-sky-100 to-sky-200 rounded-lg flex items-center justify-center mr-3">
                                                <i data-lucide="map-pin" class="w-4 h-4 text-sky-600"></i>
                                            </div>
                                            <div>
                                                <div class="text-sm font-semibold text-gray-900 max-w-[140px] truncate" title="{{ $attendance->location->name }}">{{ $attendance->location->name }}</div>
                                                <div class="text-xs text-gray-500">Radius: {{ $attendance->location->radius }}m</div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-gray-400 text-sm">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900">{{ \Carbon\Carbon::parse($schedule->schedule_date)->format('d M Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($schedule->schedule_date)->translatedFormat('l') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($attendance?->check_in_time)
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
                                    @if($attendance?->check_out_time)
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
                                        if ((($attendance && $attendance->status === 'alpha') || (!$attendance && !$permission))) {
                                            $hours = 0;
                                        } else {
                                            // Daily work hours based on shift durations across all schedules that day, minus 1 hour if any
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
                                                    $m = 0; // auto-alpha when absent without permission
                                                } else {
                                                    $m = $shiftMinutes;
                                                }
                                                $dayMinutesAcc += $m;
                                            }
                                            $dayMinutesAfterBreak = $dayMinutesAcc > 0 ? max(0, $dayMinutesAcc - 60) : 0;
                                            $hours = $dayMinutesAfterBreak / 60;
                                        }
                                    @endphp
                                    {{ $hours == floor($hours) ? floor($hours).' jam' : number_format($hours, 1).' jam' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        // Gather statuses on this schedule (single schedule context)
                                        $statusBase = $attendance->status ?? ($permission ? 'izin' : 'alpha');
                                        $hasForgot = ($statusBase === 'forgot_checkout');
                                        $hasEarly  = ($statusBase === 'early_checkout');
                                        // Check for early checkout permission like user history
                                        $hasEarlyPerm = false;
                                        if ($permission && $permission->type === 'izin') {
                                            $reasonStr = (string) ($permission->reason ?? '');
                                            if (strpos($reasonStr, '[EARLY_CHECKOUT]') === 0) {
                                                $hasEarlyPerm = true;
                                            }
                                        }
                                        $wasLate   = ($statusBase === 'telat') || ($attendance && $attendance->is_late);
                                        $wasPresent= ($statusBase === 'hadir') || ($attendance && $attendance->check_in_time);
                                        // Priority like index: izin > early_checkout > telat > hadir > forgot_checkout > alpha
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
                                <td class="px-6 py-4">
                                    <div class="max-w-[200px] line-clamp-2 text-sm text-gray-600 break-words" title="{{ $permission?->reason ?? '-' }}">
                                        {{ $permission?->reason ?? '-' }}
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                            <i data-lucide="search-x" class="w-8 h-8 text-gray-400"></i>
                                        </div>
                                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Tidak ada data</h3>
                                        <p class="text-gray-500">Tidak ada riwayat absensi untuk tanggal yang dipilih.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Enhanced Pagination -->
        @if(method_exists($schedules, 'links'))
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
            @foreach($users ?? [] as $user)
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
                    const matchedUser = historyUsersData.find(user => user.name.toLowerCase() === existingSearch.toLowerCase());
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
            if (!historyUserSearchResults || !historyUserSearchLoading || !historyUserSearchNoResults || !historyUserSearchResultsList) return;

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
    });
</script>
@endsection