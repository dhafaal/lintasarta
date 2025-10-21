@extends('layouts.admin')

@section('title', 'Manajemen Absensi')

@section('content')
    <div class="min-h-screen bg-white p-4 sm:p-6 lg:p-8">
        <div class="mx-auto space-y-6 sm:space-y-8">
            <!-- Enhanced Header Section -->
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 sm:gap-6">
                <div class="flex items-center space-x-3 sm:space-x-4">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-sky-100 to-sky-200 rounded-xl flex items-center justify-center shadow-sm flex-shrink-0">
                        <i data-lucide="calendar-check" class="w-5 h-5 sm:w-6 sm:h-6 text-sky-700"></i>
                    </div>

            
                    <div class="min-w-0">
                        <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-700 tracking-tight">Manajemen Absensi</h1>
                        <p class="text-xs sm:text-sm text-gray-500 mt-1 truncate">{{ $todayFormated }} - Kelola data absensi harian</p>
                    </div>
                </div>
                
                <!-- Date Filter & Actions -->
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 sm:gap-3 w-full lg:w-auto">
                    <form method="GET" class="flex items-center gap-2 sm:gap-3 w-full sm:w-auto">
                        <div class="relative flex-1 sm:flex-initial">
                            <input type="date" name="date" value="{{ $today }}" 
                                class="w-full pl-9 sm:pl-10 pr-3 sm:pr-4 py-2 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-sky-100 focus:border-sky-500 transition-all duration-200 bg-gray-50 focus:bg-white text-xs sm:text-sm">
                            <i data-lucide="calendar" class="w-3 h-3 sm:w-4 sm:h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                        </div>
                        <button type="submit" class="inline-flex items-center justify-center px-4 sm:px-6 py-2 sm:py-3 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-xl transition-all text-xs sm:text-sm whitespace-nowrap">
                            <i data-lucide="search" class="w-3 h-3 sm:w-4 sm:h-4 mr-1"></i>
                            <span class="xs:inline">Filter</span>
                        </button>
                    </form>
                    
                    <a href="{{ route('admin.attendances.history') }}"
                       class="inline-flex items-center justify-center px-4 sm:px-6 py-2 sm:py-3 bg-gradient-to-r from-sky-500 to-sky-600 hover:from-sky-600 hover:to-sky-700 text-white font-medium rounded-xl transition-all transform focus:outline-none focus:ring-4 focus:ring-sky-200 shadow-sm hover:shadow-md text-xs sm:text-sm whitespace-nowrap">
                        <i data-lucide="history" class="w-4 h-4 sm:w-5 sm:h-5 mr-1 sm:mr-2"></i>
                        <span class="hidden sm:inline">Riwayat Absensi</span>
                        <span class="sm:hidden">Riwayat</span>
                    </a>

                    <!-- Export Dropdown -->
                    <details class="relative w-full sm:w-auto">
                        <summary class="list-none inline-flex items-center justify-center w-full sm:w-auto px-3 sm:px-4 py-2 bg-white border-2 border-sky-200 text-sky-700 font-semibold rounded-xl hover:bg-sky-50 cursor-pointer select-none text-xs sm:text-sm">
                            <i data-lucide="download" class="w-3 h-3 sm:w-4 sm:h-4 mr-1 sm:mr-2"></i>
                            Export
                            <svg class="w-3 h-3 sm:w-4 sm:h-4 ml-1 sm:ml-2 text-sky-600" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.24 4.5a.75.75 0 01-1.08 0l-4.24-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg>
                        </summary>
                        <div class="absolute right-0 left-0 sm:left-auto mt-2 w-full sm:w-[28rem] bg-white rounded-xl shadow-xl border border-sky-100 p-3 sm:p-4 z-20 max-h-[80vh] overflow-y-auto">
                            <div class="grid grid-cols-1 gap-4">
                                <!-- Monthly Export -->
                                <div class="border border-gray-100 rounded-lg p-3">
                                    <div class="flex items-center mb-2">
                                        <i data-lucide="calendar" class="w-4 h-4 text-sky-600 mr-2"></i>
                                        <span class="text-sm font-semibold text-gray-700">Export Bulanan</span>
                                    </div>
                                    <form method="GET" action="{{ route('admin.attendances.export.monthly') }}" class="flex items-center space-x-2">
                                        <select name="month" class="px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm">
                                            @for($m=1;$m<=12;$m++)
                                                <option value="{{ $m }}" {{ (int)date('n') === $m ? 'selected' : '' }}>{{ str_pad($m,2,'0',STR_PAD_LEFT) }}</option>
                                            @endfor
                                        </select>
                                        <select name="year" class="px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm">
                                            @for($y = now()->year; $y >= now()->year - 5; $y--)
                                                <option value="{{ $y }}" {{ now()->year === $y ? 'selected' : '' }}>{{ $y }}</option>
                                            @endfor
                                        </select>
                                        <button type="submit" class="inline-flex items-center px-3 py-2 bg-sky-600 hover:bg-sky-700 text-white text-sm font-semibold rounded-lg">
                                            <i data-lucide="file-spreadsheet" class="w-4 h-4 mr-1"></i>
                                            Export
                                        </button>
                                    </form>
                                </div>

                                <!-- Yearly Export -->
                                <div class="border border-gray-100 rounded-lg p-3">
                                    <div class="flex items-center mb-2">
                                        <i data-lucide="calendar-range" class="w-4 h-4 text-sky-600 mr-2"></i>
                                        <span class="text-sm font-semibold text-gray-700">Export Tahunan</span>
                                    </div>
                                    <form method="GET" action="{{ route('admin.attendances.export.yearly') }}" class="flex items-center space-x-2">
                                        <select name="year" class="px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm">
                                            @for($y = now()->year; $y >= now()->year - 5; $y--)
                                                <option value="{{ $y }}" {{ now()->year === $y ? 'selected' : '' }}>{{ $y }}</option>
                                            @endfor
                                        </select>
                                        <button type="submit" class="inline-flex items-center px-3 py-2 bg-sky-600 hover:bg-sky-700 text-white text-sm font-semibold rounded-lg">
                                            <i data-lucide="file-spreadsheet" class="w-4 h-4 mr-1"></i>
                                            Export
                                        </button>
                                    </form>
                                </div>

                                <!-- Export All Data -->
                                <div class="border border-gray-100 rounded-lg p-3">
                                    <div class="flex items-center mb-2">
                                        <i data-lucide="database" class="w-4 h-4 text-sky-600 mr-2"></i>
                                        <span class="text-sm font-semibold text-gray-700">Export Seluruh Data</span>
                                    </div>
                                    <form method="GET" action="{{ route('admin.attendances.export.all') }}" class="flex items-center justify-between">
                                        <span class="text-xs text-gray-500">Semua karyawan, semua waktu</span>
                                        <button type="submit" class="inline-flex items-center px-3 py-2 bg-sky-600 hover:bg-sky-700 text-white text-sm font-semibold rounded-lg">
                                            <i data-lucide="file-spreadsheet" class="w-4 h-4 mr-1"></i>
                                            Export
                                        </button>
                                    </form>
                                </div>

                                <!-- Per User Export -->
                                <div class="border border-gray-100 rounded-lg p-3">
                                    <div class="flex items-center mb-2">
                                        <i data-lucide="user-round" class="w-4 h-4 text-sky-600 mr-2"></i>
                                        <span class="text-sm font-semibold text-gray-700">Export per User</span>
                                    </div>
                                    <form method="GET" action="{{ route('admin.attendances.export.per-user') }}" class="space-y-3">

                                        <!-- User Search Section -->
                                        <div class="relative">
                                            <!-- Search Input -->
                                            <div class="relative">
                                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                                    </svg>
                                                </div>
                                                <input type="text"
                                                       id="export_user_search"
                                                       class="block w-full pl-10 pr-10 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 bg-gray-50 focus:bg-white transition-all duration-200 text-sm"
                                                       placeholder="Ketik untuk mencari pengguna..."
                                                       autocomplete="off">
                                                <input type="hidden" name="user_id" id="export_selected_user_id" required>
                                            </div>

                                            <!-- Search Results Dropdown -->
                                            <div id="export_user_search_results"
                                                 class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-48 overflow-y-auto hidden">
                                                <div id="export_user_search_loading" class="px-3 py-2 text-sm text-gray-500 text-center hidden">
                                                    <svg class="animate-spin h-4 w-4 mx-auto mb-1" fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                    </svg>
                                                    Mencari pengguna...
                                                </div>
                                                <div id="export_user_search_no_results" class="px-3 py-2 text-sm text-gray-500 text-center hidden">
                                                    Tidak ada pengguna ditemukan
                                                </div>
                                                <div id="export_user_search_results_list" class="divide-y divide-gray-100">
                                                    <!-- Results will be populated here -->
                                                </div>
                                            </div>

                                            <!-- Selected User Display -->
                                            <div id="export_selected_user_display" class="mt-2 hidden">
                                                <div class="flex items-center justify-between p-2 bg-sky-50 border border-sky-200 rounded-lg">
                                                    <div class="flex items-center">
                                                        <div class="w-6 h-6 bg-sky-100 rounded-full flex items-center justify-center mr-2">
                                                            <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                            </svg>
                                                        </div>
                                                        <div>
                                                            <div class="text-sm font-medium text-gray-900" id="export_selected_user_name"></div>
                                                            <div class="text-xs text-gray-500" id="export_selected_user_email"></div>
                                                        </div>
                                                    </div>
                                                    <button type="button" onclick="clearExportUserSelection()"
                                                            class="text-gray-400 hover:text-gray-600 transition-colors">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Month and Year Selection -->
                                        <div class="grid grid-cols-2 gap-2">
                                            <select name="month" class="px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                                                <option value="">Bulan (opsional)</option>
                                                @for($m=1;$m<=12;$m++)
                                                    <option value="{{ $m }}">{{ str_pad($m,2,'0',STR_PAD_LEFT) }}</option>
                                                @endfor
                                            </select>
                                            <select name="year" class="px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                                                <option value="">Tahun (opsional)</option>
                                                @for($y = now()->year; $y >= now()->year - 5; $y--)
                                                    <option value="{{ $y }}">{{ $y }}</option>
                                                @endfor
                                            </select>
                                        </div>

                                        <!-- Export Button -->
                                        <button type="submit" class="w-full inline-flex items-center justify-center px-3 py-2 bg-sky-600 hover:bg-sky-700 text-white text-sm font-semibold rounded-lg transition-all duration-200">
                                            <i data-lucide="file-spreadsheet" class="w-4 h-4 mr-1"></i>
                                            Export
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </details>
                </div>
            </div>

            <!-- Enhanced Stats Cards -->
            <div class="grid grid-cols-1 xs:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4 lg:gap-6">
                <div class="bg-gradient-to-br from-sky-500 to-sky-600 rounded-xl sm:rounded-2xl p-4 sm:p-6 text-white shadow-xl">
                    <div class="flex items-center justify-between">
                        <div class="min-w-0 flex-1">
                            <p class="text-sky-100 text-xs sm:text-sm font-medium uppercase tracking-wide truncate">Total Jadwal</p>
                            <p class="text-2xl sm:text-3xl font-bold mt-1 sm:mt-2">{{ $totalSchedules }}</p>
                            <p class="text-sky-200 text-xs mt-1 truncate">Jadwal hari ini</p>
                        </div>
                        <div class="w-10 h-10 sm:w-14 sm:h-14 bg-sky-400 bg-opacity-30 rounded-lg sm:rounded-xl flex items-center justify-center flex-shrink-0 ml-2">
                            <i data-lucide="calendar-days" class="w-5 h-5 sm:w-6 sm:h-6 text-white"></i>
                        </div>
                    </div>
                </div>
                <x-stats-card
                    title="Hadir"
                    :count="$totalHadir"
                    subtitle="Kehadiran hari ini"
                    bgColor="bg-gradient-to-br from-green-100 to-green-200"
                    icon='<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-7 h-7 text-green-600 lucide lucide-check-circle-2"><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/><path d="m9 12 2 2 4-4"/></svg>'
                />
                <x-stats-card
                    title="Telat"
                    :count="$totalTelat"
                    subtitle="Terlambat hari ini"
                    bgColor="bg-gradient-to-br from-orange-100 to-orange-200"
                    icon='<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-7 h-7 text-orange-600 lucide lucide-clock-alert"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/><path d="M12 2v4"/><path d="M12 18v4"/></svg>'
                />
                <x-stats-card
                    title="Izin"
                    :count="$totalIzin"
                    subtitle="Izin hari ini"
                    bgColor="bg-gradient-to-br from-yellow-100 to-yellow-200"
                    icon='<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-7 h-7 text-yellow-600 lucide lucide-clock"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>'
                />
                <x-stats-card
                    title="Alpha"
                    :count="$totalAlpha"
                    subtitle="Ketidakhadiran"
                    bgColor="bg-gradient-to-br from-red-100 to-red-200"
                    icon='<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-7 h-7 text-red-600 lucide lucide-x-circle"><circle cx="12" cy="12" r="10"/><line x1="15" x2="9" y1="9" y2="15"/><line x1="9" x2="15" y1="9" y2="15"/></svg>'
                />
            </div>

            @if(isset($overnightOpenAttendances) && $overnightOpenAttendances->isNotEmpty())
            <!-- Overnight Open Attendances Alert -->
            <div class="bg-amber-50 border border-amber-200 rounded-xl sm:rounded-2xl p-4 sm:p-5">
                <div class="flex items-start">
                    <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center mr-4">
                        <i data-lucide="moon" class="w-5 h-5 text-amber-700"></i>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-base font-semibold text-amber-900">Overnight open check-ins dari kemarin</h3>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                                {{ $overnightOpenAttendances->count() }} pengguna
                            </span>
                        </div>
                        <p class="text-sm text-amber-800 mb-3">Pengguna berikut masih tercatat check-in kemarin tanpa check-out. Sistem mengizinkan checkout pada hari ini untuk shift malam.</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2 sm:gap-3">
                            @foreach($overnightOpenAttendances as $oa)
                                <div class="bg-white rounded-xl border border-amber-200 p-3">
                                    <div class="flex items-center justify-between mb-1">
                                        <div class="text-sm font-semibold text-gray-900">{{ optional($oa->user)->name ?? '-' }}</div>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-indigo-100 text-indigo-800">
                                            {{ optional($oa->schedule->shift)->shift_name ?? 'Shift' }}
                                        </span>
                                    </div>
                                    <div class="text-xs text-gray-600 flex items-center space-x-2">
                                        <span class="inline-flex items-center">
                                            <i data-lucide="calendar" class="w-3 h-3 mr-1"></i>
                                            {{ \Carbon\Carbon::parse(optional($oa->schedule)->schedule_date)->format('d M Y') }}
                                        </span>
                                        <span class="inline-flex items-center">
                                            <i data-lucide="log-in" class="w-3 h-3 mr-1"></i>
                                            Check-in: {{ \Carbon\Carbon::parse($oa->check_in_time)->format('H:i') }}
                                        </span>
                                        @if($oa->location)
                                        <span class="inline-flex items-center">
                                            <i data-lucide="map-pin" class="w-3 h-3 mr-1"></i>
                                            {{ $oa->location->name }}
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Enhanced Table Card -->
            <div class="bg-white rounded-xl sm:rounded-2xl border-2 border-sky-100 overflow-hidden shadow-xl">
                <div class="px-4 sm:px-6 lg:px-8 py-4 sm:py-6 border-b border-sky-100 bg-gradient-to-r from-sky-50 to-blue-50">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                        <div class="min-w-0">
                            <h2 class="text-lg sm:text-xl font-bold text-sky-900">Daftar Absensi Karyawan</h2>
                            <p class="text-xs sm:text-sm text-sky-700 mt-1 truncate">Data absensi untuk tanggal {{ $todayFormated }}</p>
                        </div>
                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 sm:gap-3 w-full lg:w-auto">
                            <!-- Search -->
                            <div class="relative flex-1 sm:flex-initial">
                                <input type="text" id="searchInput" placeholder="Cari karyawan..."
                                       class="w-full pl-9 pr-3 py-2 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-xs sm:text-sm">
                                <svg class="w-3 h-3 sm:w-4 sm:h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>

                            <!-- Filter Status -->
                            <select id="statusFilter" class="w-full sm:w-auto px-3 py-2 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-xs sm:text-sm">
                                <option value="">Semua Status</option>
                                <option value="hadir">Hadir</option>
                                <option value="telat">Telat</option>
                                <option value="izin">Izin</option>
                                <option value="early_checkout">Early Checkout</option>
                                <option value="forgot_checkout">Forgot Checkout</option>
                                <option value="alpha">Alpha</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Mobile Card View -->
                <div class="block md:hidden space-y-4 px-4">
                    @php $groups = $schedulesToday->groupBy('user_id'); @endphp
                    @forelse($groups as $userId => $userSchedules)
                        @php
                            $user = optional($userSchedules->first())->user;
                            $scheduleIds = $userSchedules->pluck('id');
                            $attGroup = $attendances->whereIn('schedule_id', $scheduleIds);
                            $permGroup = $permissions->whereIn('schedule_id', $scheduleIds);

                            // Location: pick any available
                            $firstWithLocation = $attGroup->first(function($a){ return $a && $a->location; });
                            $location = $firstWithLocation ? $firstWithLocation->location : null;

                            // Times
                            $checkInTime = optional($attGroup->whereNotNull('check_in_time')->sortBy('check_in_time')->first())->check_in_time;
                            $checkOutTime = optional($attGroup->whereNotNull('check_out_time')->sortByDesc('check_out_time')->first())->check_out_time;

                            // Status priority: izin > early_checkout > telat > hadir > forgot_checkout > alpha
                            $statusText = '-';
                            if ($attGroup->where('status','izin')->isNotEmpty()) { $statusText = 'izin'; }
                            elseif ($attGroup->where('status','early_checkout')->isNotEmpty()) { $statusText = 'early_checkout'; }
                            elseif ($attGroup->where('status','telat')->isNotEmpty()) { $statusText = 'telat'; }
                            elseif ($attGroup->where('status','hadir')->isNotEmpty()) { $statusText = 'hadir'; }
                            elseif ($attGroup->where('status','forgot_checkout')->isNotEmpty()) { $statusText = 'forgot_checkout'; }
                            elseif ($attGroup->isNotEmpty()) { $statusText = optional($attGroup->first())->status ?: '-'; }
                            if ($statusText === '-' && $userSchedules->isNotEmpty() && $attGroup->isEmpty() && $permGroup->isEmpty()) {
                                $statusText = 'alpha';
                            }

                            $statusColor = 'bg-gray-100 text-gray-700';
                            if($statusText === 'hadir') { $statusColor = 'bg-green-100 text-green-800'; }
                            if($statusText === 'telat') { $statusColor = 'bg-orange-100 text-orange-800'; }
                            if($statusText === 'izin') { $statusColor = 'bg-yellow-100 text-yellow-800'; }
                            if($statusText === 'early_checkout') { $statusColor = 'bg-amber-100 text-amber-800'; }
                            if($statusText === 'forgot_checkout') { $statusColor = 'bg-rose-100 text-rose-800'; }
                            if($statusText === 'alpha') { $statusColor = 'bg-red-100 text-red-800'; }

                            $hasForgot = $attGroup->where('status','forgot_checkout')->isNotEmpty();
                            $hasEarly  = $attGroup->where('status','early_checkout')->isNotEmpty();
                            $wasLate   = $attGroup->filter(function($a){ return $a && $a->is_late; })->isNotEmpty() || $attGroup->where('status','telat')->isNotEmpty();
                            $wasPresent= $attGroup->filter(function($a){ return $a && !is_null($a->check_in_time); })->isNotEmpty() || $attGroup->where('status','hadir')->isNotEmpty();
                            $showStacked = ($hasForgot || $hasEarly) && ($wasLate || $wasPresent);
                            $forgotColor = 'bg-rose-100 text-rose-800';
                            $earlyColor  = 'bg-amber-100 text-amber-800';

                            $order = ['Pagi' => 1, 'Siang' => 2, 'Malam' => 3];
                            $sortedSchedules = $userSchedules->sortBy(function($s) use ($order){ return $order[$s->shift->category ?? ''] ?? 99; });

                            $earlyPending = $permGroup->first(function($p){
                                return $p->status === 'pending' && $p->type === 'izin' && is_string($p->reason) && preg_match('/^\[EARLY_CHECKOUT\]/', $p->reason);
                            });
                            $otherPending = $permGroup->first(function($p){
                                return $p->status === 'pending' && (!$p->reason || !preg_match('/^\[EARLY_CHECKOUT\]/', (string)$p->reason));
                            });
                            $latestPerm = $permGroup->sortByDesc('created_at')->first();
                            $isEarly = $latestPerm && is_string($latestPerm->reason ?? '') && preg_match('/^\[EARLY_CHECKOUT\]/', $latestPerm->reason);
                        @endphp
                        
                        <div class="bg-white rounded-xl border-2 border-sky-100 p-4 shadow-sm hover:shadow-md transition-shadow">
                            <!-- User Info -->
                            <div class="flex items-center mb-4 pb-4 border-b border-gray-100">
                                <div class="w-12 h-12 bg-gradient-to-br from-sky-100 to-sky-200 rounded-xl flex items-center justify-center mr-3 flex-shrink-0">
                                    <span class="text-sky-600 font-bold text-base">{{ substr($user->name ?? '-', 0, 1) }}</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-base font-bold text-gray-900 truncate">{{ $user->name ?? '-' }}</div>
                                    <div class="text-xs text-gray-500 truncate">{{ $user->email ?? '-' }}</div>
                                </div>
                                @if($showStacked)
                                    @php
                                        $primaryText = $wasLate ? 'telat' : 'hadir';
                                        $primaryColor = $wasLate ? 'bg-orange-100 text-orange-800' : 'bg-green-100 text-green-800';
                                    @endphp
                                    <div class="flex flex-col gap-1">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $primaryColor }}">
                                            {{ ucwords($primaryText) }}
                                        </span>
                                        @if($hasForgot)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $forgotColor }}">
                                                Forgot
                                            </span>
                                        @endif
                                        @if($hasEarly)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $earlyColor }}">
                                                Early
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $statusColor }} flex-shrink-0">
                                        {{ ucwords(str_replace('_',' ', $statusText)) }}
                                    </span>
                                @endif
                            </div>

                            <!-- Details Grid -->
                            <div class="space-y-3">
                                <!-- Shift -->
                                <div class="flex items-start">
                                    <div class="w-6 h-6 flex items-center justify-center mr-2 flex-shrink-0">
                                        <i data-lucide="clock" class="w-4 h-4 text-sky-600"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="text-xs text-gray-500 mb-1">Shift</div>
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($sortedSchedules as $us)
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                                                    @if($us->shift && $us->shift->category == 'Pagi') bg-yellow-100 text-yellow-800
                                                    @elseif($us->shift && $us->shift->category == 'Siang') bg-orange-100 text-orange-800
                                                    @elseif($us->shift && $us->shift->category == 'Malam') bg-indigo-100 text-indigo-800
                                                    @else bg-gray-100 text-gray-800 @endif">
                                                    {{ $us->shift->shift_name ?? '-' }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <!-- Location -->
                                @if($location)
                                <div class="flex items-start">
                                    <div class="w-6 h-6 flex items-center justify-center mr-2 flex-shrink-0">
                                        <i data-lucide="map-pin" class="w-4 h-4 text-sky-600"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="text-xs text-gray-500 mb-1">Lokasi</div>
                                        <div class="text-sm font-semibold text-gray-900 truncate">{{ $location->name }}</div>
                                        <div class="text-xs text-gray-500">{{ ucfirst($location->type ?? '-') }}</div>
                                    </div>
                                </div>
                                @endif

                                <!-- Check In/Out -->
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="flex items-start">
                                        <div class="w-6 h-6 flex items-center justify-center mr-2 flex-shrink-0">
                                            <i data-lucide="log-in" class="w-4 h-4 text-green-600"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="text-xs text-gray-500 mb-1">Check In</div>
                                            @if($checkInTime)
                                                <div class="text-sm font-semibold text-gray-900">{{ \Carbon\Carbon::parse($checkInTime)->format('H:i') }}</div>
                                                <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($checkInTime)->format('d M') }}</div>
                                            @else
                                                <span class="text-xs text-gray-400">-</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="flex items-start">
                                        <div class="w-6 h-6 flex items-center justify-center mr-2 flex-shrink-0">
                                            <i data-lucide="log-out" class="w-4 h-4 text-red-600"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="text-xs text-gray-500 mb-1">Check Out</div>
                                            @if($checkOutTime)
                                                <div class="text-sm font-semibold text-gray-900">{{ \Carbon\Carbon::parse($checkOutTime)->format('H:i') }}</div>
                                                <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($checkOutTime)->format('d M') }}</div>
                                            @else
                                                <span class="text-xs text-gray-400">-</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Permission/Notes -->
                                @if($latestPerm)
                                <div class="flex items-start">
                                    <div class="w-6 h-6 flex items-center justify-center mr-2 flex-shrink-0">
                                        <i data-lucide="message-circle" class="w-4 h-4 text-sky-600"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="text-xs text-gray-500 mb-1">Keterangan</div>
                                        @php
                                            $cleanReason = $isEarly
                                                ? preg_replace('/^\[EARLY_CHECKOUT\]\s*/', '', (string)($latestPerm->reason ?? ''))
                                                : ($latestPerm->reason ?? '');
                                        @endphp
                                        <div class="text-xs font-medium {{ $latestPerm->status === 'rejected' ? 'text-red-600' : ($latestPerm->status === 'pending' ? 'text-yellow-700' : 'text-gray-700') }}">
                                            @if($isEarly)Early Checkout: @endif{{ $cleanReason }}
                                        </div>
                                        <div class="text-xs mt-1">
                                            Status: <span class="font-semibold {{ $latestPerm->status === 'rejected' ? 'text-red-600' : ($latestPerm->status === 'pending' ? 'text-yellow-700' : 'text-green-700') }}">{{ ucfirst($latestPerm->status) }}</span>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <!-- Actions -->
                                @if($earlyPending || $otherPending)
                                <div class="pt-3 border-t border-gray-100 flex gap-2">
                                    @if($earlyPending)
                                        <form action="{{ route('admin.attendances.permission.approve', $earlyPending) }}" method="post" class="flex-1" onsubmit="return confirm('Setujui early checkout ini?')">
                                            @csrf
                                            <button type="submit" class="w-full inline-flex items-center justify-center px-3 py-2 bg-green-100 hover:bg-green-200 text-green-700 font-semibold text-xs rounded-lg transition-all">
                                                <i data-lucide="check" class="w-3 h-3 mr-1"></i>
                                                Setujui
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.attendances.permission.reject', $earlyPending) }}" method="post" class="flex-1" onsubmit="return confirm('Tolak early checkout ini?')">
                                            @csrf
                                            <button type="submit" class="w-full inline-flex items-center justify-center px-3 py-2 bg-red-100 hover:bg-red-200 text-red-700 font-semibold text-xs rounded-lg transition-all">
                                                <i data-lucide="x" class="w-3 h-3 mr-1"></i>
                                                Tolak
                                            </button>
                                        </form>
                                    @elseif($otherPending)
                                        <form action="{{ route('admin.attendances.permission.approve', $otherPending) }}" method="post" class="flex-1" onsubmit="return confirm('Yakin ingin menyetujui izin ini?')">
                                            @csrf
                                            <button type="submit" class="w-full inline-flex items-center justify-center px-3 py-2 bg-green-100 hover:bg-green-200 text-green-700 font-semibold text-xs rounded-lg transition-all">
                                                <i data-lucide="check" class="w-3 h-3 mr-1"></i>
                                                Setujui
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.attendances.permission.reject', $otherPending) }}" method="post" class="flex-1" onsubmit="return confirm('Yakin ingin menolak izin ini?')">
                                            @csrf
                                            <button type="submit" class="w-full inline-flex items-center justify-center px-3 py-2 bg-red-100 hover:bg-red-200 text-red-700 font-semibold text-xs rounded-lg transition-all">
                                                <i data-lucide="x" class="w-3 h-3 mr-1"></i>
                                                Tolak
                                            </button>
                                        </form>
                                    @endif
                                </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="bg-white rounded-xl border-2 border-sky-100 p-8 text-center">
                            <div class="w-16 h-16 bg-gradient-to-br from-sky-100 to-sky-200 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i data-lucide="calendar-x" class="w-8 h-8 text-sky-400"></i>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">Belum ada data absensi</h3>
                            <p class="text-sm text-gray-600">Data absensi untuk tanggal ini belum tersedia</p>
                        </div>
                    @endforelse
                </div>

                <!-- Desktop Table View -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b-2 border-gray-200">
                            <tr>
                                <th class="px-4 sm:px-6 lg:px-8 py-3 sm:py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user text-sky-600 mr-2">
                                            <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
                                            <circle cx="12" cy="7" r="4"/>
                                        </svg>
                                        Nama Karyawan
                                    </div>
                                </th>
                                <th class="px-4 sm:px-6 lg:px-8 py-3 sm:py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock text-sky-600 mr-2">
                                            <circle cx="12" cy="12" r="10"/>
                                            <polyline points="12 6 12 12 16 14"/>
                                        </svg>
                                        Shift
                                    </div>
                                </th>
                                <th class="px-3 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <i data-lucide="map-pin" class="w-4 h-4 text-sky-600 mr-1"></i>
                                        Lokasi
                                    </div>
                                </th>
                                <th class="px-3 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-log-in text-sky-600 mr-1">
                                            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                                            <polyline points="10 17 15 12 10 7"/>
                                            <line x1="15" x2="3" y1="12" y2="12"/>
                                        </svg>
                                        Check In
                                    </div>
                                </th>
                                <th class="px-3 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-log-out text-sky-600 mr-1">
                                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                                            <polyline points="16 17 21 12 16 7"/>
                                            <line x1="21" x2="9" y1="12" y2="12"/>
                                        </svg>
                                        Check Out
                                    </div>
                                </th>
                                <th class="px-3 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-activity text-sky-600 mr-1">
                                            <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
                                        </svg>
                                        Status
                                    </div>
                                </th>
                                <th class="px-3 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-message-circle text-sky-600 mr-1">
                                            <path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/>
                                        </svg>
                                        Keterangan
                                    </div>
                                </th>
                                <th class="px-3 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @php $groups = $schedulesToday->groupBy('user_id'); @endphp
                            @forelse($groups as $userId => $userSchedules)
                                @php
                                    $user = optional($userSchedules->first())->user;
                                    $scheduleIds = $userSchedules->pluck('id');
                                    $attGroup = $attendances->whereIn('schedule_id', $scheduleIds);
                                    $permGroup = $permissions->whereIn('schedule_id', $scheduleIds);

                                    // Location: pick any available
                                    $firstWithLocation = $attGroup->first(function($a){ return $a && $a->location; });
                                    $location = $firstWithLocation ? $firstWithLocation->location : null;

                                    // Times
                                    $checkInTime = optional($attGroup->whereNotNull('check_in_time')->sortBy('check_in_time')->first())->check_in_time;
                                    $checkOutTime = optional($attGroup->whereNotNull('check_out_time')->sortByDesc('check_out_time')->first())->check_out_time;

                                    // Status priority: izin > early_checkout > telat > hadir > forgot_checkout > alpha
                                    $statusText = '-';
                                    if ($attGroup->where('status','izin')->isNotEmpty()) { $statusText = 'izin'; }
                                    elseif ($attGroup->where('status','early_checkout')->isNotEmpty()) { $statusText = 'early_checkout'; }
                                    elseif ($attGroup->where('status','telat')->isNotEmpty()) { $statusText = 'telat'; }
                                    elseif ($attGroup->where('status','hadir')->isNotEmpty()) { $statusText = 'hadir'; }
                                    elseif ($attGroup->where('status','forgot_checkout')->isNotEmpty()) { $statusText = 'forgot_checkout'; }
                                    elseif ($attGroup->isNotEmpty()) { $statusText = optional($attGroup->first())->status ?: '-'; }
                                    // Fallback: if there are schedules but absolutely no attendance and no permissions, mark as alpha (covers night shift users who didn't show up)
                                    if ($statusText === '-' && $userSchedules->isNotEmpty() && $attGroup->isEmpty() && $permGroup->isEmpty()) {
                                        $statusText = 'alpha';
                                    }

                                    $statusColor = 'bg-gray-100 text-gray-700';
                                    if($statusText === 'hadir') { $statusColor = 'bg-green-100 text-green-800'; }
                                    if($statusText === 'telat') { $statusColor = 'bg-orange-100 text-orange-800'; }
                                    if($statusText === 'izin') { $statusColor = 'bg-yellow-100 text-yellow-800'; }
                                    if($statusText === 'early_checkout') { $statusColor = 'bg-amber-100 text-amber-800'; }
                                    if($statusText === 'forgot_checkout') { $statusColor = 'bg-rose-100 text-rose-800'; }
                                    if($statusText === 'alpha') { $statusColor = 'bg-red-100 text-red-800'; }

                                    // Determine if we need stacked badges (e.g., Telat/Hadir + Forgot/Early Checkout)
                                    $hasForgot = $attGroup->where('status','forgot_checkout')->isNotEmpty();
                                    $hasEarly  = $attGroup->where('status','early_checkout')->isNotEmpty();
                                    // Derive base presence/late from attendance fields (status may have been overwritten to forgot_checkout)
                                    $wasLate   = $attGroup->filter(function($a){ return $a && $a->is_late; })->isNotEmpty() || $attGroup->where('status','telat')->isNotEmpty();
                                    $wasPresent= $attGroup->filter(function($a){ return $a && !is_null($a->check_in_time); })->isNotEmpty() || $attGroup->where('status','hadir')->isNotEmpty();
                                    $showStacked = ($hasForgot || $hasEarly) && ($wasLate || $wasPresent);
                                    $forgotColor = 'bg-rose-100 text-rose-800';
                                    $earlyColor  = 'bg-amber-100 text-amber-800';

                                    // Shifts sorted Pagi -> Siang -> Malam
                                    $order = ['Pagi' => 1, 'Siang' => 2, 'Malam' => 3];
                                    $sortedSchedules = $userSchedules->sortBy(function($s) use ($order){ return $order[$s->shift->category ?? ''] ?? 99; });

                                    // Permission resolution
                                    $earlyPending = $permGroup->first(function($p){
                                        return $p->status === 'pending' && $p->type === 'izin' && is_string($p->reason) && preg_match('/^\[EARLY_CHECKOUT\]/', $p->reason);
                                    });
                                    $otherPending = $permGroup->first(function($p){
                                        return $p->status === 'pending' && (!$p->reason || !preg_match('/^\[EARLY_CHECKOUT\]/', (string)$p->reason));
                                    });
                                    $latestPerm = $permGroup->sortByDesc('created_at')->first();
                                    $isEarly = $latestPerm && is_string($latestPerm->reason ?? '') && preg_match('/^\[EARLY_CHECKOUT\]/', $latestPerm->reason);
                                @endphp
                                <tr class="hover:bg-sky-50 transition-colors duration-200 group">
                                    <td class="px-4 sm:px-6 lg:px-8 py-4 sm:py-6 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-gradient-to-br from-sky-100 to-sky-200 rounded-xl flex items-center justify-center mr-4 group-hover:from-sky-200 group-hover:to-sky-300 transition-colors">
                                                <span class="text-sky-600 font-bold text-sm">{{ substr($user->name ?? '-', 0, 1) }}</span>
                                            </div>
                                            <div>
                                                <div class="text-base font-semibold text-gray-700">{{ $user->name ?? '-' }}</div>
                                                <div class="text-sm text-gray-500">{{ $user->email ?? '-' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="flex flex-col space-y-1">
                                            @foreach($sortedSchedules as $us)
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                                                    @if($us->shift && $us->shift->category == 'Pagi') bg-yellow-100 text-yellow-800
                                                    @elseif($us->shift && $us->shift->category == 'Siang') bg-orange-100 text-orange-800
                                                    @elseif($us->shift && $us->shift->category == 'Malam') bg-indigo-100 text-indigo-800
                                                    @else bg-gray-100 text-gray-800 @endif">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock mr-1">
                                                        <circle cx="12" cy="12" r="10"/>
                                                        <polyline points="12 6 12 12 16 14"/>
                                                    </svg>
                                                    {{ $us->shift->shift_name ?? '-' }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="px-3 py-4 whitespace-nowrap">
                                        @if($location)
                                            <div class="flex items-center">
                                                <div class="w-7 h-7 bg-gradient-to-br from-sky-100 to-sky-200 rounded-lg flex items-center justify-center mr-2">
                                                    <i data-lucide="map-pin" class="w-3 h-3 text-sky-600"></i>
                                                </div>
                                                <div>
                                                    <div class="text-xs font-semibold text-gray-900">{{ $location->name }}</div>
                                                    <div class="text-xs text-gray-500"><span class="uppercase">{{ ucfirst($location->type ?? '-') }}</span></div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-gray-400 text-xs">-</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-4 whitespace-nowrap">
                                        @if($checkInTime)
                                            <div class="flex items-start">
                                                <div class="w-7 h-7 bg-green-100 rounded-lg flex items-center justify-center mr-2 flex-shrink-0">
                                                    <i data-lucide="log-in" class="w-3 h-3 text-green-600"></i>
                                                </div>
                                                <div>
                                                    <div class="text-xs font-semibold text-gray-900">{{ \Carbon\Carbon::parse($checkInTime)->format('H:i') }}</div>
                                                    <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($checkInTime)->format('d M') }}</div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-gray-400 text-xs">-</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-4 whitespace-nowrap">
                                        @if($checkOutTime)
                                            <div class="flex items-start">
                                                <div class="w-7 h-7 bg-red-100 rounded-lg flex items-center justify-center mr-2 flex-shrink-0">
                                                    <i data-lucide="log-out" class="w-3 h-3 text-red-600"></i>
                                                </div>
                                                <div>
                                                    <div class="text-xs font-semibold text-gray-900">{{ \Carbon\Carbon::parse($checkOutTime)->format('H:i') }}</div>
                                                    <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($checkOutTime)->format('d M') }}</div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-gray-400 text-xs">-</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-4 whitespace-nowrap">
                                        @if($showStacked)
                                            @php
                                                $primaryText = $wasLate ? 'telat' : 'hadir';
                                                $primaryColor = $wasLate ? 'bg-orange-100 text-orange-800' : 'bg-green-100 text-green-800';
                                            @endphp
                                            <div class="flex flex-col space-y-1">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $primaryColor }}">
                                                    {{ ucwords($primaryText) }}
                                                </span>
                                                @if($hasForgot)
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $forgotColor }}">
                                                        Forgot Checkout
                                                    </span>
                                                @endif
                                                @if($hasEarly)
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $earlyColor }}">
                                                        Early Checkout
                                                    </span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $statusColor }}">
                                                {{ ucwords(str_replace('_',' ', $statusText)) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-4 text-xs text-gray-700">
                                        @if($latestPerm)
                                            @php
                                                $cleanReason = $isEarly
                                                    ? preg_replace('/^\[EARLY_CHECKOUT\]\s*/', '', (string)($latestPerm->reason ?? ''))
                                                    : ($latestPerm->reason ?? '');
                                            @endphp
                                            <div>
                                                <div class="font-medium {{ $latestPerm->status === 'rejected' ? 'text-red-600' : ($latestPerm->status === 'pending' ? 'text-yellow-700' : 'text-gray-700') }}">
                                                    @if($isEarly)
                                                        Early Checkout: {{ $cleanReason }}
                                                    @else
                                                        {{ $cleanReason }}
                                                    @endif
                                                </div>
                                                <div class="text-xs mt-1">
                                                    Status:
                                                    <span class="font-semibold {{ $latestPerm->status === 'rejected' ? 'text-red-600' : ($latestPerm->status === 'pending' ? 'text-yellow-700' : 'text-green-700') }}">
                                                        {{ ucfirst($latestPerm->status) }}
                                                    </span>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-4 whitespace-nowrap text-left">
                                        <div class="flex items-center justify-start space-x-1">
                                            @if($earlyPending)
                                                <form action="{{ route('admin.attendances.permission.approve', $earlyPending) }}" method="post" class="inline" onsubmit="return confirm('Setujui early checkout ini?')">
                                                    @csrf
                                                    <button type="submit" class="inline-flex items-center px-2 py-1 bg-green-100 hover:bg-green-200 text-green-700 font-semibold text-xs rounded-lg transition-all duration-200">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check mr-1">
                                                            <polyline points="20 6 9 17 4 12"/>
                                                        </svg>
                                                        Setujui
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.attendances.permission.reject', $earlyPending) }}" method="post" class="inline" onsubmit="return confirm('Tolak early checkout ini?')">
                                                    @csrf
                                                    <button type="submit" class="inline-flex items-center px-2 py-1 bg-red-100 hover:bg-red-200 text-red-700 font-semibold text-xs rounded-lg transition-all duration-200">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x mr-1">
                                                            <path d="M18 6 6 18"/>
                                                            <path d="m6 6 12 12"/>
                                                        </svg>
                                                        Tolak
                                                    </button>
                                                </form>
                                            @elseif($otherPending)
                                                <form action="{{ route('admin.attendances.permission.approve', $otherPending) }}" method="post" class="inline" onsubmit="return confirm('Yakin ingin menyetujui izin ini?')">
                                                    @csrf
                                                    <button type="submit" class="inline-flex items-center px-2 py-1 bg-green-100 hover:bg-green-200 text-green-700 font-semibold text-xs rounded-lg transition-all duration-200">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check mr-1">
                                                            <polyline points="20 6 9 17 4 12"/>
                                                        </svg>
                                                        Setujui
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.attendances.permission.reject', $otherPending) }}" method="post" class="inline" onsubmit="return confirm('Yakin ingin menolak izin ini?')">
                                                    @csrf
                                                    <button type="submit" class="inline-flex items-center px-2 py-1 bg-red-100 hover:bg-red-200 text-red-700 font-semibold text-xs rounded-lg transition-all duration-200">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x mr-1">
                                                            <path d="M18 6 6 18"/>
                                                            <path d="m6 6 12 12"/>
                                                        </svg>
                                                        Tolak
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-gray-400 text-sm">-</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <div class="w-20 h-20 bg-gradient-to-br from-sky-100 to-sky-200 rounded-full flex items-center justify-center mb-6">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar-x text-sky-400">
                                                    <path d="M8 2v4"/>
                                                    <path d="M16 2v4"/>
                                                    <rect width="18" height="18" x="3" y="4" rx="2"/>
                                                    <path d="M3 10h18"/>
                                                    <path d="M14 14l-4 4"/>
                                                    <path d="M10 14l4 4"/>
                                                </svg>
                                            </div>
                                            <h3 class="text-xl font-bold text-gray-900 mb-2">Belum ada data absensi</h3>
                                            <p class="text-gray-600 mb-6 max-w-sm">Data absensi untuk tanggal ini belum tersedia</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Export User Search Functionality
        document.addEventListener("DOMContentLoaded", function() {
            const exportUserSearchInput = document.getElementById('export_user_search');
            const exportUserSearchResults = document.getElementById('export_user_search_results');
            const exportUserSearchResultsList = document.getElementById('export_user_search_results_list');
            const exportUserSearchLoading = document.getElementById('export_user_search_loading');
            const exportUserSearchNoResults = document.getElementById('export_user_search_no_results');
            const exportSelectedUserId = document.getElementById('export_selected_user_id');
            const exportSelectedUserDisplay = document.getElementById('export_selected_user_display');
            const exportSelectedUserName = document.getElementById('export_selected_user_name');
            const exportSelectedUserEmail = document.getElementById('export_selected_user_email');

            // Users data
            const exportUsersData = [
                @foreach($users as $user)
                    {
                        id: {{ $user->id }},
                        name: "{{ $user->name }}",
                        email: "{{ $user->email ?? '' }}"
                    },
                @endforeach
            ];

            let exportSearchTimeout;

            // Initialize export user search
            if (exportUserSearchInput) {
                exportUserSearchInput.addEventListener('input', function() {
                    const query = this.value.trim();

                    // Clear previous timeout
                    clearTimeout(exportSearchTimeout);

                    // Hide results if query is empty
                    if (!query) {
                        hideExportSearchResults();
                        return;
                    }

                    // Show loading state
                    showExportLoadingState();

                    // Debounce search to avoid too many requests
                    exportSearchTimeout = setTimeout(() => {
                        performExportUserSearch(query);
                    }, 300);
                });

                // Hide results when clicking outside
                document.addEventListener('click', function(e) {
                    if (!exportUserSearchInput.contains(e.target) && !exportUserSearchResults.contains(e.target)) {
                        hideExportSearchResults();
                    }
                });
            }

            function performExportUserSearch(query) {
                // Filter users based on query
                const filteredUsers = exportUsersData.filter(user =>
                    user.name.toLowerCase().includes(query.toLowerCase()) ||
                    user.email.toLowerCase().includes(query.toLowerCase())
                );

                // Show results
                showExportSearchResults(filteredUsers, query);
            }

            function showExportSearchResults(users, query) {
                exportUserSearchResultsList.innerHTML = '';

                if (users.length === 0) {
                    hideExportLoadingState();
                    showExportNoResultsState();
                    return;
                }

                hideExportLoadingState();
                hideExportNoResultsState();

                users.forEach(user => {
                    const userItem = document.createElement('div');
                    userItem.className = 'px-3 py-2 hover:bg-gray-50 cursor-pointer transition-colors';
                    userItem.innerHTML = `
                        <div class="flex items-center">
                            <div class="w-6 h-6 bg-sky-100 rounded-full flex items-center justify-center mr-2">
                                <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-900">${highlightExportText(user.name, query)}</div>
                                <div class="text-xs text-gray-500">${highlightExportText(user.email, query)}</div>
                            </div>
                        </div>
                    `;

                    userItem.addEventListener('click', () => selectExportUser(user));
                    exportUserSearchResultsList.appendChild(userItem);
                });

                exportUserSearchResults.classList.remove('hidden');
            }

            function highlightExportText(text, query) {
                if (!query || !text) return text;

                const regex = new RegExp(`(${query})`, 'gi');
                return text.replace(regex, '<mark class="bg-yellow-200 text-yellow-800">$1</mark>');
            }

            function selectExportUser(user) {
                exportSelectedUserId.value = user.id;
                exportSelectedUserName.textContent = user.name;
                exportSelectedUserEmail.textContent = user.email;
                exportSelectedUserDisplay.classList.remove('hidden');
                exportUserSearchInput.value = user.name;

                // Hide search results
                hideExportSearchResults();
            }

            function clearExportUserSelection() {
                exportSelectedUserId.value = '';
                exportSelectedUserName.textContent = '';
                exportSelectedUserEmail.textContent = '';
                exportSelectedUserDisplay.classList.add('hidden');
                exportUserSearchInput.value = '';
            }

            function showExportLoadingState() {
                exportUserSearchResults.classList.remove('hidden');
                exportUserSearchLoading.classList.remove('hidden');
                exportUserSearchNoResults.classList.add('hidden');
                exportUserSearchResultsList.innerHTML = '';
            }

            function hideExportLoadingState() {
                exportUserSearchLoading.classList.add('hidden');
            }

            function showExportNoResultsState() {
                exportUserSearchResults.classList.remove('hidden');
                exportUserSearchNoResults.classList.remove('hidden');
            }

            function hideExportNoResultsState() {
                exportUserSearchNoResults.classList.add('hidden');
            }

            function hideExportSearchResults() {
                exportUserSearchResults.classList.add('hidden');
                hideExportLoadingState();
                hideExportNoResultsState();
            }

            // Realtime search and filter for attendance table
            const searchInput = document.getElementById('searchInput');
            const statusFilter = document.getElementById('statusFilter');

            function filterAttendances() {
                const searchTerm = searchInput.value.toLowerCase();
                const statusValue = statusFilter.value.toLowerCase();
                let visibleCount = 0;

                // Filter table rows (desktop view)
                const tableRows = document.querySelectorAll('tbody tr:not([class*="empty"])');
                tableRows.forEach(row => {
                    // Skip if it's an empty state row
                    if (row.querySelector('td[colspan]')) {
                        return;
                    }

                    const userName = row.querySelector('td:nth-child(1)')?.textContent.toLowerCase() || '';
                    const shift = row.querySelector('td:nth-child(2)')?.textContent.toLowerCase() || '';
                    const location = row.querySelector('td:nth-child(3)')?.textContent.toLowerCase() || '';
                    const checkIn = row.querySelector('td:nth-child(4)')?.textContent.toLowerCase() || '';
                    const checkOut = row.querySelector('td:nth-child(5)')?.textContent.toLowerCase() || '';
                    const status = row.querySelector('td:nth-child(6)')?.textContent.toLowerCase() || '';

                    const matchesSearch = userName.includes(searchTerm) || 
                                        shift.includes(searchTerm) ||
                                        location.includes(searchTerm) ||
                                        status.includes(searchTerm) ||
                                        checkIn.includes(searchTerm) ||
                                        checkOut.includes(searchTerm);
                    
                    const matchesStatus = !statusValue || status.includes(statusValue);

                    if (matchesSearch && matchesStatus) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                // Filter mobile cards
                const mobileContainer = document.querySelector('.block.md\\:hidden.space-y-4');
                if (mobileContainer) {
                    const mobileCards = mobileContainer.querySelectorAll('.bg-white.rounded-xl');
                    mobileCards.forEach(card => {
                        // Skip empty state
                        if (card.querySelector('[data-lucide="calendar-x"]')) {
                            return;
                        }

                        const userName = card.querySelector('.text-base.font-bold')?.textContent.toLowerCase() || '';
                        const statusBadges = Array.from(card.querySelectorAll('.inline-flex.items-center.px-2, .inline-flex.items-center.px-3'))
                            .map(badge => badge.textContent.toLowerCase()).join(' ');
                        const allText = card.textContent.toLowerCase();

                        const matchesSearch = userName.includes(searchTerm) || 
                                            statusBadges.includes(searchTerm) ||
                                            allText.includes(searchTerm);
                        
                        const matchesStatus = !statusValue || statusBadges.includes(statusValue);

                        if (matchesSearch && matchesStatus) {
                            card.style.display = '';
                            visibleCount++;
                        } else {
                            card.style.display = 'none';
                        }
                    });
                }

                // Show/hide empty state
                const emptyRow = document.querySelector('tbody tr td[colspan]')?.parentElement;
                if (emptyRow) {
                    emptyRow.style.display = visibleCount === 0 ? '' : 'none';
                }
            }

            searchInput?.addEventListener('input', filterAttendances);
            statusFilter?.addEventListener('change', filterAttendances);
        });
    </script>
@endsection