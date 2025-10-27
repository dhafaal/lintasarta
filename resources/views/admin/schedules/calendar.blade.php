@extends('layouts.admin')

@section('title', 'Schedules Table')

@section('content')
    <div class="min-h-screen bg-white">
        {{-- Header Section --}}
        <div class="bg-white px-4 sm:px-6 py-3 sm:py-4">
            <div class="max-w-[1600px] mx-auto">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-sky-100 to-sky-200 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl sm:text-2xl font-bold text-gray-800">Kalender Jadwal Pegawai</h1>
                        <p class="text-xs sm:text-sm text-gray-500">Visualisasi kalender & tabel jadwal kerja</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Content --}}
        <div class="max-w-[1600px] mx-auto px-4 sm:px-6 py-4 sm:py-6 space-y-4 sm:space-y-6">

            {{-- Filter & Export Section --}}
            <div class="bg-white rounded-xl border border-gray-200 p-4 sm:p-5">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    {{-- Filter Form --}}
                    <form action="{{ route('admin.calendar.view') }}" method="GET" class="flex flex-wrap gap-2 sm:gap-3 items-center">
                        @php
                            $currentMonth = request('month', $month ?? now()->month);
                            $currentYear = request('year', $year ?? now()->year);
                        @endphp
                        <div class="flex items-center gap-2 w-full sm:w-auto">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-sky-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                            </svg>
                            <label class="text-xs sm:text-sm font-semibold text-gray-700 whitespace-nowrap">Filter Periode:</label>
                        </div>
                        <select name="month"
                            class="flex-1 sm:flex-none px-3 py-1.5 sm:px-4 sm:py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-xs sm:text-sm bg-white min-w-[100px]">
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ (int) $m === (int) $currentMonth ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                        <select name="year"
                            class="flex-1 sm:flex-none px-3 py-1.5 sm:px-4 sm:py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-xs sm:text-sm bg-white min-w-[80px]">
                            @for ($y = now()->year - 5; $y <= now()->year + 5; $y++)
                                <option value="{{ $y }}" {{ (int) $y === (int) $currentYear ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endfor
                        </select>
                        <button type="submit"
                            class="flex-1 sm:flex-none px-4 py-1.5 sm:px-6 sm:py-2 bg-sky-500 hover:bg-sky-600 text-white font-semibold rounded-lg transition-colors duration-200 text-xs sm:text-sm whitespace-nowrap">
                            Tampilkan
                        </button>
                    </form>

                    {{-- Export Form --}}
                    <form action="{{ route('admin.calendar.export') }}" method="GET" class="flex flex-wrap gap-2 items-center w-full lg:w-auto">
                        <select name="month"
                            class="flex-1 sm:flex-none px-3 py-1.5 sm:px-4 sm:py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-xs sm:text-sm bg-white min-w-[100px]">
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ (int) $m === (int) $currentMonth ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                        <select name="year"
                            class="flex-1 sm:flex-none px-3 py-1.5 sm:px-4 sm:py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-xs sm:text-sm bg-white min-w-[80px]">
                            @for ($y = now()->year - 5; $y <= now()->year + 5; $y++)
                                <option value="{{ $y }}" {{ (int) $y === (int) $currentYear ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endfor
                        </select>
                        <button type="submit"
                            class="flex-1 sm:flex-none inline-flex items-center justify-center px-4 py-1.5 sm:px-5 sm:py-2 bg-sky-500 hover:bg-sky-600 text-white font-semibold rounded-lg transition-colors duration-200 text-xs sm:text-sm whitespace-nowrap">
                            <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            <span class="hidden sm:inline">Export Excel</span>
                            <span class="sm:hidden">Export</span>
                        </button>
                    </form>
                </div>
            </div>

            {{-- Legend Status Kehadiran --}}
            <div class="bg-white rounded-xl border border-gray-200 p-4 sm:p-5">
                <div class="flex items-center gap-2 mb-3 sm:mb-4">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-xs sm:text-sm font-semibold text-gray-700">Keterangan Status Kehadiran:</p>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-4 gap-2 sm:gap-3">
                    <div class="flex items-center gap-2 px-3 py-1.5 sm:px-4 sm:py-2 bg-green-50 rounded-lg border border-green-200">
                        <span class="w-2 h-2 sm:w-2.5 sm:h-2.5 rounded-full bg-green-500 flex-shrink-0"></span>
                        <span class="text-xs sm:text-sm font-medium text-green-800">Hadir</span>
                    </div>
                    <div class="flex items-center gap-2 px-3 py-1.5 sm:px-4 sm:py-2 bg-orange-50 rounded-lg border border-orange-200">
                        <span class="w-2 h-2 sm:w-2.5 sm:h-2.5 rounded-full bg-orange-500 flex-shrink-0"></span>
                        <span class="text-xs sm:text-sm font-medium text-orange-800">Telat</span>
                    </div>
                    <div class="flex items-center gap-2 px-3 py-1.5 sm:px-4 sm:py-2 bg-yellow-50 rounded-lg border border-yellow-200">
                        <span class="w-2 h-2 sm:w-2.5 sm:h-2.5 rounded-full bg-yellow-500 flex-shrink-0"></span>
                        <span class="text-xs sm:text-sm font-medium text-yellow-800">Izin</span>
                    </div>
                    <div class="flex items-center gap-2 px-3 py-1.5 sm:px-4 sm:py-2 bg-red-50 rounded-lg border border-red-200">
                        <span class="w-2 h-2 sm:w-2.5 sm:h-2.5 rounded-full bg-red-500 flex-shrink-0"></span>
                        <span class="text-xs sm:text-sm font-medium text-red-800">Alpha</span>
                    </div>
                </div>
            </div>

            {{-- Tabel Jadwal Kerja --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-4 sm:px-6 py-3 sm:py-4 bg-gradient-to-r from-sky-50 to-blue-50 border-b border-gray-200">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 sm:w-10 sm:h-10 bg-sky-500 rounded-lg flex items-center justify-center shadow-sm">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-base sm:text-lg font-bold text-gray-900">Tabel Jadwal Kerja</h2>
                                <p class="text-xs sm:text-sm text-gray-600">Detail jadwal dan jam kerja karyawan</p>
                            </div>
                        </div>
                        
                        {{-- Mobile View Toggle --}}
                        <div class="flex items-center gap-2 sm:hidden">
                            <span class="text-xs text-gray-600">View:</span>
                            <button onclick="toggleMobileView()" class="px-3 py-1 bg-sky-100 text-sky-700 rounded-lg text-xs font-medium">
                                <span id="viewToggleText">Table</span>
                            </button>
                        </div>
                    </div>
                </div>
                
                {{-- Desktop Table View --}}
                <div id="desktopView" class="overflow-x-auto">
                    <table class="min-w-full border-collapse">
                        <thead>
                            <tr class="bg-gradient-to-r from-gray-50 to-gray-100">
                                <th class="sticky left-0 bg-gradient-to-r from-gray-50 to-gray-100 z-30 px-2 sm:px-4 py-2 sm:py-3 border-b-2 border-r border-gray-300 font-bold text-xs text-gray-800 uppercase tracking-wider text-center w-12 sm:w-16 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)]">
                                    NO
                                </th>
                                <th class="sticky left-12 sm:left-16 bg-gradient-to-r from-gray-50 to-gray-100 z-30 px-3 sm:px-5 py-2 sm:py-3 border-b-2 border-r border-gray-300 font-bold text-xs text-gray-800 uppercase tracking-wider text-left min-w-[180px] sm:min-w-[220px] shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)]">
                                    <span class="hidden sm:inline">NAMA</span>
                                    <span class="sm:hidden">NAMA</span>
                                </th>
                                @for ($d = 1; $d <= $daysInMonth; $d++)
                                    @php
                                        $dayOfWeek = \Carbon\Carbon::createFromDate($year, $month, $d)->dayOfWeek;
                                        $isWeekend = $dayOfWeek === 0 || $dayOfWeek === 6;
                                    @endphp
                                    <th class="px-2 py-2 sm:px-3 sm:py-3 border-b-2 border-r border-gray-300 font-bold text-[10px] sm:text-xs uppercase min-w-[60px] sm:min-w-[80px] text-center ">
                                        {{ $d }}
                                    </th>
                                @endfor
                                <th class="sticky right-0 bg-gradient-to-l from-gray-50 to-gray-100 z-30 px-3 sm:px-5 py-2 sm:py-3 border-b-2 border-l-2 border-gray-300 font-bold text-xs text-gray-800 uppercase tracking-wider text-center min-w-[100px] sm:min-w-[130px] shadow-[-2px_0_5px_-2px_rgba(0,0,0,0.1)]">
                                    <span class="hidden sm:inline">TOTAL JAM</span>
                                    <span class="sm:hidden">JAM</span>
                                </th>
                            </tr>
                            <tr class="bg-gradient-to-r from-gray-50 to-gray-100">
                                <th class="sticky left-0 bg-gradient-to-r from-gray-50 to-gray-100 z-20 px-2 sm:px-4 py-1.5 sm:py-2 border-b border-r border-gray-300 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)]"></th>
                                <th class="sticky left-12 sm:left-16 bg-gradient-to-r from-gray-50 to-gray-100 z-20 px-3 sm:px-5 py-1.5 sm:py-2 border-b border-r border-gray-300 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)]"></th>
                                @for ($d = 1; $d <= $daysInMonth; $d++)
                                    @php
                                        $dayOfWeek = \Carbon\Carbon::createFromDate($year, $month, $d)->dayOfWeek;
                                        $isWeekend = $dayOfWeek === 0 || $dayOfWeek === 6;
                                    @endphp
                                    <th class="px-2 py-1.5 sm:px-3 sm:py-2 border-b border-r border-gray-300 text-[10px] sm:text-xs uppercase font-semibold text-center">
                                        {{ \Carbon\Carbon::createFromDate($year, $month, $d)->translatedFormat('D') }}
                                    </th>
                                @endfor
                                <th class="sticky right-0 bg-gradient-to-l from-gray-50 to-gray-100 z-20 px-3 sm:px-5 py-1.5 sm:py-2 border-b border-l-2 border-gray-300 shadow-[-2px_0_5px_-2px_rgba(0,0,0,0.1)]"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($data as $index => $row)
                                <tr class="hover:bg-sky-50 transition-all duration-150 group">
                                    <td class="sticky left-0 bg-white group-hover:bg-sky-50 z-20 px-2 sm:px-4 py-2 sm:py-4 border-b border-r border-gray-300 text-center align-middle font-bold text-gray-800 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)]"
                                        rowspan="2">
                                        <div class="flex items-center justify-center">
                                            <span class="w-6 h-6 sm:w-8 sm:h-8 bg-sky-100 rounded-lg flex items-center justify-center text-sky-600 font-bold text-xs sm:text-sm">
                                                {{ $index + 1 }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="sticky left-12 sm:left-16 bg-white group-hover:bg-sky-50 z-20 px-3 sm:px-5 py-2 sm:py-4 border-b border-r border-gray-300 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)]">
                                        <div class="flex items-center gap-2 sm:gap-3">
                                            <div class="w-8 h-8 sm:w-11 sm:h-11 bg-gradient-to-br from-sky-100 to-sky-200 rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm">
                                                <span class="text-sky-600 font-bold text-xs sm:text-base">{{ substr($row['nama'], 0, 1) }}</span>
                                            </div>
                                            <div class="min-w-0">
                                                <div class="font-bold text-gray-900 text-xs sm:text-sm truncate">{{ $row['nama'] }}</div>
                                                <div class="text-[10px] sm:text-xs text-gray-500 font-medium hidden sm:block">Karyawan</div>
                                            </div>
                                        </div>
                                    </td>
                                    @for ($d = 1; $d <= $daysInMonth; $d++)
                                        @php
                                            $dayOfWeek = \Carbon\Carbon::createFromDate($year, $month, $d)->dayOfWeek;
                                            $isWeekend = $dayOfWeek === 0 || $dayOfWeek === 6;
                                            // Always use primary_attendance (first shift status) for coloring
                                            $attendanceStatus = $row['shifts'][$d]['primary_attendance'] ?? null;
                                            $shiftName = $row['shifts'][$d]['shift_name'] ?? null;

                                            $cellBgClass = '';
                                            $textClass = '';
                                            $borderClass = 'border-gray-200';
                                            
                                            // Prioritize status: early_checkout/forgot_checkout > hadir/telat > izin > alpha
                                            if ($attendanceStatus === 'early_checkout') {
                                                $cellBgClass = 'bg-amber-50';
                                                $textClass = 'text-amber-800 font-semibold';
                                                $borderClass = 'border-amber-100';
                                            } elseif ($attendanceStatus === 'forgot_checkout') {
                                                $cellBgClass = 'bg-rose-50';
                                                $textClass = 'text-rose-800 font-semibold';
                                                $borderClass = 'border-rose-100';
                                            } elseif ($attendanceStatus === 'hadir') {
                                                $cellBgClass = 'bg-green-50';
                                                $textClass = 'text-green-800 font-semibold';
                                                $borderClass = 'border-green-100';
                                            } elseif ($attendanceStatus === 'telat') {
                                                $cellBgClass = 'bg-orange-50';
                                                $textClass = 'text-orange-800 font-semibold';
                                                $borderClass = 'border-orange-100';
                                            } elseif ($attendanceStatus === 'izin') {
                                                $cellBgClass = 'bg-yellow-50';
                                                $textClass = 'text-yellow-800 font-semibold';
                                                $borderClass = 'border-yellow-100';
                                            } elseif ($attendanceStatus === 'alpha') {
                                                $cellBgClass = 'bg-red-50';
                                                $textClass = 'text-red-800 font-semibold';
                                                $borderClass = 'border-red-100';
                                            }
                                        @endphp
                                        <td class="px-2 py-1.5 sm:px-3 sm:py-3 border-b border-r {{ $borderClass }} text-center {{ $cellBgClass }} transition-colors"
                                            title="Status: {{ ucfirst($attendanceStatus ?? 'Belum ada data') }}">
                                            @if ($shiftName)
                                                <div class="flex items-center justify-center">
                                                    <span class="{{ $textClass }} text-[10px] sm:text-xs font-semibold px-1 sm:px-2 py-0.5 sm:py-1 rounded">{{ $shiftName }}</span>
                                                </div>
                                            @else
                                                <span class="text-gray-400 text-[10px] sm:text-xs">-</span>
                                            @endif
                                        </td>
                                    @endfor
                                    <td class="sticky right-0 bg-white group-hover:bg-sky-50 z-20 px-3 sm:px-5 py-2 sm:py-4 border-b border-l-2 border-gray-300 text-center shadow-[-2px_0_5px_-2px_rgba(0,0,0,0.05)]" rowspan="2">
                                        <div class="flex flex-col items-center justify-center">
                                            <span class="text-lg sm:text-2xl font-bold text-sky-600">{{ $row['total_jam'] }}</span>
                                            <span class="text-[10px] sm:text-xs text-gray-500 font-medium mt-0.5 sm:mt-1">Jam</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="hover:bg-sky-50 transition-all duration-150 group border-b-2 border-gray-300">
                                    <td class="sticky left-12 sm:left-16 bg-gray-100 group-hover:bg-sky-100 z-20 px-3 sm:px-5 py-1.5 sm:py-2.5 border-b-2 border-r border-gray-300 text-[10px] sm:text-xs font-bold uppercase text-gray-700 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)]">
                                        <div class="flex items-center gap-1 sm:gap-2">
                                            <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <span class="hidden sm:inline">JAM KERJA</span>
                                            <span class="sm:hidden">JAM</span>
                                        </div>
                                    </td>
                                    @for ($d = 1; $d <= $daysInMonth; $d++)
                                        @php
                                            $dayOfWeek = \Carbon\Carbon::createFromDate($year, $month, $d)->dayOfWeek;
                                            $isWeekend = $dayOfWeek === 0 || $dayOfWeek === 6;
                                            // Always use primary_attendance (first shift status) for coloring
                                            $attendanceStatus = $row['shifts'][$d]['primary_attendance'] ?? null;

                                            $cellBgClass = '';
                                            $textClass = '';
                                            $borderClass = 'border-gray-200';
                                            
                                            // Prioritize status: early_checkout/forgot_checkout > hadir/telat > izin > alpha
                                            if ($attendanceStatus === 'early_checkout') {
                                                $cellBgClass = 'bg-amber-50';
                                                $textClass = 'text-amber-700 font-medium';
                                                $borderClass = 'border-amber-100';
                                            } elseif ($attendanceStatus === 'forgot_checkout') {
                                                $cellBgClass = 'bg-rose-50';
                                                $textClass = 'text-rose-700 font-medium';
                                                $borderClass = 'border-rose-100';
                                            } elseif ($attendanceStatus === 'hadir') {
                                                $cellBgClass = 'bg-green-50';
                                                $textClass = 'text-green-700 font-medium';
                                                $borderClass = 'border-green-100';
                                            } elseif ($attendanceStatus === 'telat') {
                                                $cellBgClass = 'bg-orange-50';
                                                $textClass = 'text-orange-700 font-medium';
                                                $borderClass = 'border-orange-100';
                                            } elseif ($attendanceStatus === 'izin') {
                                                $cellBgClass = 'bg-yellow-50';
                                                $textClass = 'text-yellow-700 font-medium';
                                                $borderClass = 'border-yellow-100';
                                            } elseif ($attendanceStatus === 'alpha') {
                                                $cellBgClass = 'bg-red-50';
                                                $textClass = 'text-red-700 font-medium';
                                                $borderClass = 'border-red-100';
                                            }
                                        @endphp
                                        <td class="px-2 py-1 sm:px-3 sm:py-2.5 border-b-2 border-r {{ $borderClass }} text-center {{ $cellBgClass }} transition-colors">
                                            @if ($row['shifts'][$d]['hours'])
                                                <div class="flex items-center justify-center">
                                                    <span class="{{ $textClass }} text-[10px] sm:text-xs font-bold bg-white px-1 sm:px-2 py-0.5 rounded">{{ $row['shifts'][$d]['hours'] }}</span>
                                                </div>
                                            @else
                                                <span class="text-gray-400 text-[10px] sm:text-xs">-</span>
                                            @endif
                                        </td>
                                    @endfor
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $daysInMonth + 3 }}" class="text-center py-10 sm:py-20 border-b-2 border-gray-300">
                                        <div class="flex flex-col items-center justify-center space-y-3 sm:space-y-4 px-4">
                                            <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-gray-100 to-gray-200 rounded-2xl flex items-center justify-center shadow-sm">
                                                <svg class="w-8 h-8 sm:w-10 sm:h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                            </div>
                                            <div class="text-center">
                                                <h3 class="text-lg sm:text-xl font-bold text-gray-800 mb-1 sm:mb-2">Belum ada jadwal untuk bulan ini</h3>
                                                <p class="text-xs sm:text-sm text-gray-500">Silakan pilih periode lain atau tambahkan jadwal baru</p>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Mobile Card View (Hidden by default) --}}
                <div id="mobileView" class="hidden p-4 space-y-4">
                    @forelse ($data as $index => $row)
                        <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-gradient-to-br from-sky-400 to-sky-600 rounded-xl flex items-center justify-center flex-shrink-0">
                                        <span class="text-white font-bold text-sm">{{ substr($row['nama'], 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 text-sm">{{ $row['nama'] }}</div>
                                        <div class="text-xs text-gray-500">Karyawan</div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-lg font-bold text-sky-600">{{ $row['total_jam'] }}</div>
                                    <div class="text-xs text-gray-500">Total Jam</div>
                                </div>
                            </div>
                            
                            <div class="space-y-2">
                                <div class="text-xs font-semibold text-gray-700 mb-2">Jadwal Bulan Ini:</div>
                                <div class="grid grid-cols-7 gap-1">
                                    @for ($d = 1; $d <= min($daysInMonth, 28); $d++)
                                        @php
                                            $dayOfWeek = \Carbon\Carbon::createFromDate($year, $month, $d)->dayOfWeek;
                                            $isWeekend = $dayOfWeek === 0 || $dayOfWeek === 6;
                                            $attendanceStatus = $row['shifts'][$d]['primary_attendance'] ?? null;
                                            $shiftName = $row['shifts'][$d]['shift_name'] ?? null;

                                            $cellBgClass = '';
                                            $textClass = '';
                                            if ($attendanceStatus === 'hadir') {
                                                $cellBgClass = 'bg-green-100';
                                                $textClass = 'text-green-800';
                                            } elseif ($attendanceStatus === 'telat') {
                                                $cellBgClass = 'bg-orange-100';
                                                $textClass = 'text-orange-800';
                                            } elseif ($attendanceStatus === 'izin') {
                                                $cellBgClass = 'bg-yellow-100';
                                                $textClass = 'text-yellow-800';
                                            } elseif ($attendanceStatus === 'alpha') {
                                                $cellBgClass = 'bg-red-100';
                                                $textClass = 'text-red-800';
                                            }
                                        @endphp
                                        <div class="aspect-square flex flex-col items-center justify-center rounded {{ $cellBgClass }} p-1">
                                            <div class="text-[10px] font-bold {{ $isWeekend ? 'text-red-600' : 'text-gray-700' }}">{{ $d }}</div>
                                            @if ($shiftName)
                                                <div class="text-[8px] {{ $textClass }} font-semibold truncate w-full text-center">{{ substr($shiftName, 0, 3) }}</div>
                                            @endif
                                        </div>
                                    @endfor
                                </div>
                                @if ($daysInMonth > 28)
                                    <div class="text-xs text-gray-500 text-center mt-2">
                                        +{{ $daysInMonth - 28 }} hari lainnya...
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-10">
                            <div class="flex flex-col items-center justify-center space-y-3">
                                <div class="w-16 h-16 bg-gradient-to-br from-gray-100 to-gray-200 rounded-2xl flex items-center justify-center shadow-sm">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div class="text-center">
                                    <h3 class="text-lg font-bold text-gray-800 mb-1">Belum ada jadwal untuk bulan ini</h3>
                                    <p class="text-sm text-gray-500">Silakan pilih periode lain atau tambahkan jadwal baru</p>
                                </div>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleMobileView() {
            const desktopView = document.getElementById('desktopView');
            const mobileView = document.getElementById('mobileView');
            const toggleText = document.getElementById('viewToggleText');
            
            if (desktopView.classList.contains('hidden')) {
                desktopView.classList.remove('hidden');
                mobileView.classList.add('hidden');
                toggleText.textContent = 'Table';
            } else {
                desktopView.classList.add('hidden');
                mobileView.classList.remove('hidden');
                toggleText.textContent = 'Cards';
            }
        }

        // Auto-show mobile view on small screens
        function checkScreenSize() {
            const mobileViewToggle = document.querySelector('button[onclick="toggleMobileView()"]');
            if (window.innerWidth < 768) {
                // Show mobile view by default on small screens
                const desktopView = document.getElementById('desktopView');
                const mobileView = document.getElementById('mobileView');
                const toggleText = document.getElementById('viewToggleText');
                
                if (desktopView && mobileView && !desktopView.classList.contains('hidden')) {
                    desktopView.classList.add('hidden');
                    mobileView.classList.remove('hidden');
                    toggleText.textContent = 'Cards';
                }
            }
        }

        // Check on load and resize
        window.addEventListener('load', checkScreenSize);
        window.addEventListener('resize', checkScreenSize);
    </script>
@endsection