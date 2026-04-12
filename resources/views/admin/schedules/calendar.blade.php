@extends('layouts.admin')

@section('title', 'Schedules Table')

@section('content')
    <div class="min-h-screen bg-white">
        {{-- Header Section --}}
        <div class="bg-white px-4 py-3 sm:px-6 sm:py-4">
            <div class="mx-auto max-w-[1600px]">
                <div class="flex items-center space-x-3">
                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-sky-100 to-sky-200 sm:h-12 sm:w-12"
                    >
                        <svg
                            class="h-5 w-5 text-sky-600 sm:h-6 sm:w-6"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                            ></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-800 sm:text-2xl">Kalender Jadwal Pegawai</h1>
                        <p class="text-xs text-gray-500 sm:text-sm">Visualisasi kalender & tabel jadwal kerja</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Content --}}
        <div class="mx-auto max-w-[1600px] space-y-4 px-4 py-4 sm:space-y-6 sm:px-6 sm:py-6">
            {{-- Filter & Export Section --}}
            <div class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    {{-- Filter Form --}}
                    <form
                        action="{{ route('admin.calendar.view') }}"
                        method="GET"
                        class="flex flex-wrap items-center gap-2 sm:gap-3"
                    >
                        @php
                            $currentMonth = request('month', $month ?? now()->month);
                            $currentYear = request('year', $year ?? now()->year);
                        @endphp

                        <div class="flex w-full items-center gap-2 sm:w-auto">
                            <svg
                                class="h-4 w-4 flex-shrink-0 text-sky-600 sm:h-5 sm:w-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"
                                />
                            </svg>
                            <label class="text-xs font-semibold whitespace-nowrap text-gray-700 sm:text-sm">
                                Filter Periode:
                            </label>
                        </div>
                        <select
                            name="month"
                            class="min-w-[100px] flex-1 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs focus:border-sky-500 focus:ring-2 focus:ring-sky-500 sm:flex-none sm:px-4 sm:py-2 sm:text-sm"
                        >
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ (int) $m === (int) $currentMonth ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                        <select
                            name="year"
                            class="min-w-[80px] flex-1 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs focus:border-sky-500 focus:ring-2 focus:ring-sky-500 sm:flex-none sm:px-4 sm:py-2 sm:text-sm"
                        >
                            @for ($y = now()->year - 5; $y <= now()->year + 5; $y++)
                                <option value="{{ $y }}" {{ (int) $y === (int) $currentYear ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endfor
                        </select>
                        <button
                            type="submit"
                            class="flex-1 rounded-lg bg-sky-500 px-4 py-1.5 text-xs font-semibold whitespace-nowrap text-white transition-colors duration-200 hover:bg-sky-600 sm:flex-none sm:px-6 sm:py-2 sm:text-sm"
                        >
                            Tampilkan
                        </button>
                    </form>

                    {{-- Export Form --}}
                    <form
                        action="{{ route('admin.calendar.export') }}"
                        method="GET"
                        class="flex w-full flex-wrap items-center gap-2 lg:w-auto"
                    >
                        <select
                            name="month"
                            class="min-w-[100px] flex-1 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs focus:border-sky-500 focus:ring-2 focus:ring-sky-500 sm:flex-none sm:px-4 sm:py-2 sm:text-sm"
                        >
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ (int) $m === (int) $currentMonth ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                        <select
                            name="year"
                            class="min-w-[80px] flex-1 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs focus:border-sky-500 focus:ring-2 focus:ring-sky-500 sm:flex-none sm:px-4 sm:py-2 sm:text-sm"
                        >
                            @for ($y = now()->year - 5; $y <= now()->year + 5; $y++)
                                <option value="{{ $y }}" {{ (int) $y === (int) $currentYear ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endfor
                        </select>
                        <button
                            type="submit"
                            class="inline-flex flex-1 items-center justify-center rounded-lg bg-sky-500 px-4 py-1.5 text-xs font-semibold whitespace-nowrap text-white transition-colors duration-200 hover:bg-sky-600 sm:flex-none sm:px-5 sm:py-2 sm:text-sm"
                        >
                            <svg
                                class="mr-1 h-3 w-3 sm:mr-2 sm:h-4 sm:w-4"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"
                                />
                            </svg>
                            <span class="hidden sm:inline">Export Excel</span>
                            <span class="sm:hidden">Export</span>
                        </button>
                    </form>
                </div>
            </div>

            {{-- Legend Status Kehadiran --}}
            <div class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5">
                <div class="mb-3 flex items-center gap-2 sm:mb-4">
                    <svg
                        class="h-4 w-4 text-sky-600 sm:h-5 sm:w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                    </svg>
                    <p class="text-xs font-semibold text-gray-700 sm:text-sm">Keterangan Status Kehadiran:</p>
                </div>
                <div class="grid grid-cols-2 gap-2 sm:grid-cols-2 sm:gap-3 md:grid-cols-5">
                    <div
                        class="flex items-center gap-2 rounded-lg border border-green-200 bg-green-50 px-3 py-1.5 sm:px-4 sm:py-2"
                    >
                        <span class="h-2 w-2 flex-shrink-0 rounded-full bg-green-500 sm:h-2.5 sm:w-2.5"></span>
                        <span class="text-xs font-medium text-green-800 sm:text-sm">Hadir</span>
                    </div>
                    <div
                        class="flex items-center gap-2 rounded-lg border border-orange-200 bg-orange-50 px-3 py-1.5 sm:px-4 sm:py-2"
                    >
                        <span class="h-2 w-2 flex-shrink-0 rounded-full bg-orange-500 sm:h-2.5 sm:w-2.5"></span>
                        <span class="text-xs font-medium text-orange-800 sm:text-sm">Telat</span>
                    </div>
                    <div
                        class="flex items-center gap-2 rounded-lg border border-yellow-200 bg-yellow-50 px-3 py-1.5 sm:px-4 sm:py-2"
                    >
                        <span class="h-2 w-2 flex-shrink-0 rounded-full bg-yellow-500 sm:h-2.5 sm:w-2.5"></span>
                        <span class="text-xs font-medium text-yellow-800 sm:text-sm">Izin</span>
                    </div>
                    <div
                        class="flex items-center gap-2 rounded-lg border border-purple-200 bg-purple-50 px-3 py-1.5 sm:px-4 sm:py-2"
                    >
                        <span class="h-2 w-2 flex-shrink-0 rounded-full bg-purple-500 sm:h-2.5 sm:w-2.5"></span>
                        <span class="text-xs font-medium text-purple-800 sm:text-sm">Cuti</span>
                    </div>
                    <div
                        class="flex items-center gap-2 rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 sm:px-4 sm:py-2"
                    >
                        <span class="h-2 w-2 flex-shrink-0 rounded-full bg-red-500 sm:h-2.5 sm:w-2.5"></span>
                        <span class="text-xs font-medium text-red-800 sm:text-sm">Alpha</span>
                    </div>
                </div>
            </div>

            {{-- Tabel Jadwal Kerja --}}
            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 bg-gradient-to-r from-sky-50 to-blue-50 px-4 py-3 sm:px-6 sm:py-4">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-8 w-8 items-center justify-center rounded-lg bg-sky-500 shadow-sm sm:h-10 sm:w-10"
                            >
                                <svg
                                    class="h-4 w-4 text-white sm:h-5 sm:w-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"
                                    />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-base font-bold text-gray-900 sm:text-lg">Tabel Jadwal Kerja</h2>
                                <p class="text-xs text-gray-600 sm:text-sm">Detail jadwal dan jam kerja karyawan</p>
                            </div>
                        </div>

                        {{-- Mobile View Toggle --}}
                        <div class="flex items-center gap-2 sm:hidden">
                            <span class="text-xs text-gray-600">View:</span>
                            <button
                                onclick="toggleMobileView()"
                                class="rounded-lg bg-sky-100 px-3 py-1 text-xs font-medium text-sky-700"
                            >
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
                                <th
                                    class="sticky left-0 z-30 w-12 border-r border-b-2 border-gray-300 bg-gradient-to-r from-gray-50 to-gray-100 px-2 py-2 text-center text-xs font-bold tracking-wider text-gray-800 uppercase shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)] sm:w-16 sm:px-4 sm:py-3"
                                >
                                    NO
                                </th>
                                <th
                                    class="sticky left-12 z-30 min-w-[180px] border-r border-b-2 border-gray-300 bg-gradient-to-r from-gray-50 to-gray-100 px-3 py-2 text-left text-xs font-bold tracking-wider text-gray-800 uppercase shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)] sm:left-16 sm:min-w-[220px] sm:px-5 sm:py-3"
                                >
                                    <span class="hidden sm:inline">NAMA</span>
                                    <span class="sm:hidden">NAMA</span>
                                </th>
                                @for ($d = 1; $d <= $daysInMonth; $d++)
                                    @php
                                        $dayOfWeek = \Carbon\Carbon::createFromDate($year, $month, $d)->dayOfWeek;
                                        $isWeekend = $dayOfWeek === 0 || $dayOfWeek === 6;
                                    @endphp

                                    <th
                                        class="min-w-[60px] border-r border-b-2 border-gray-300 px-2 py-2 text-center text-[10px] font-bold uppercase sm:min-w-[80px] sm:px-3 sm:py-3 sm:text-xs"
                                    >
                                        {{ $d }}
                                    </th>
                                @endfor

                                <th
                                    class="sticky right-0 z-30 min-w-[100px] border-b-2 border-l-2 border-gray-300 bg-gradient-to-l from-gray-50 to-gray-100 px-3 py-2 text-center text-xs font-bold tracking-wider text-gray-800 uppercase shadow-[-2px_0_5px_-2px_rgba(0,0,0,0.1)] sm:min-w-[130px] sm:px-5 sm:py-3"
                                >
                                    <span class="hidden sm:inline">TOTAL JAM</span>
                                    <span class="sm:hidden">JAM</span>
                                </th>
                            </tr>
                            <tr class="bg-gradient-to-r from-gray-50 to-gray-100">
                                <th
                                    class="sticky left-0 z-20 border-r border-b border-gray-300 bg-gradient-to-r from-gray-50 to-gray-100 px-2 py-1.5 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)] sm:px-4 sm:py-2"
                                ></th>
                                <th
                                    class="sticky left-12 z-20 border-r border-b border-gray-300 bg-gradient-to-r from-gray-50 to-gray-100 px-3 py-1.5 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)] sm:left-16 sm:px-5 sm:py-2"
                                ></th>
                                @for ($d = 1; $d <= $daysInMonth; $d++)
                                    @php
                                        $dayOfWeek = \Carbon\Carbon::createFromDate($year, $month, $d)->dayOfWeek;
                                        $isWeekend = $dayOfWeek === 0 || $dayOfWeek === 6;
                                    @endphp

                                    <th
                                        class="border-r border-b border-gray-300 px-2 py-1.5 text-center text-[10px] font-semibold uppercase sm:px-3 sm:py-2 sm:text-xs"
                                    >
                                        {{ \Carbon\Carbon::createFromDate($year, $month, $d)->translatedFormat('D') }}
                                    </th>
                                @endfor

                                <th
                                    class="sticky right-0 z-20 border-b border-l-2 border-gray-300 bg-gradient-to-l from-gray-50 to-gray-100 px-3 py-1.5 shadow-[-2px_0_5px_-2px_rgba(0,0,0,0.1)] sm:px-5 sm:py-2"
                                ></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse ($data as $index => $row)
                                <tr class="group transition-all duration-150 hover:bg-sky-50">
                                    <td
                                        class="sticky left-0 z-20 border-r border-b border-gray-300 bg-white px-2 py-2 text-center align-middle font-bold text-gray-800 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)] group-hover:bg-sky-50 sm:px-4 sm:py-4"
                                        rowspan="2"
                                    >
                                        <div class="flex items-center justify-center">
                                            <span
                                                class="flex h-6 w-6 items-center justify-center rounded-lg bg-sky-100 text-xs font-bold text-sky-600 sm:h-8 sm:w-8 sm:text-sm"
                                            >
                                                {{ $index + 1 }}
                                            </span>
                                        </div>
                                    </td>
                                    <td
                                        class="sticky left-12 z-20 border-r border-b border-gray-300 bg-white px-3 py-2 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)] group-hover:bg-sky-50 sm:left-16 sm:px-5 sm:py-4"
                                    >
                                        <div class="flex items-center gap-2 sm:gap-3">
                                            <div
                                                class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-sky-100 to-sky-200 shadow-sm sm:h-11 sm:w-11"
                                            >
                                                <span class="text-xs font-bold text-sky-600 sm:text-base">
                                                    {{ substr($row['nama'], 0, 1) }}
                                                </span>
                                            </div>
                                            <div class="min-w-0">
                                                <div class="truncate text-xs font-bold text-gray-900 sm:text-sm">
                                                    {{ $row['nama'] }}
                                                </div>
                                                <div
                                                    class="hidden text-[10px] font-medium text-gray-500 sm:block sm:text-xs"
                                                >
                                                    Karyawan
                                                </div>
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
                                                $textClass = 'font-semibold text-amber-800';
                                                $borderClass = 'border-amber-100';
                                            } elseif ($attendanceStatus === 'forgot_checkout') {
                                                $cellBgClass = 'bg-rose-50';
                                                $textClass = 'font-semibold text-rose-800';
                                                $borderClass = 'border-rose-100';
                                            } elseif ($attendanceStatus === 'hadir') {
                                                $cellBgClass = 'bg-green-50';
                                                $textClass = 'font-semibold text-green-800';
                                                $borderClass = 'border-green-100';
                                            } elseif ($attendanceStatus === 'telat') {
                                                $cellBgClass = 'bg-orange-50';
                                                $textClass = 'font-semibold text-orange-800';
                                                $borderClass = 'border-orange-100';
                                            } elseif ($attendanceStatus === 'izin') {
                                                $cellBgClass = 'bg-yellow-50';
                                                $textClass = 'font-semibold text-yellow-800';
                                                $borderClass = 'border-yellow-100';
                                            } elseif ($attendanceStatus === 'cuti') {
                                                $cellBgClass = 'bg-purple-50';
                                                $textClass = 'font-semibold text-purple-800';
                                                $borderClass = 'border-purple-100';
                                            } elseif ($attendanceStatus === 'alpha') {
                                                $cellBgClass = 'bg-red-50';
                                                $textClass = 'font-semibold text-red-800';
                                                $borderClass = 'border-red-100';
                                            }
                                        @endphp

                                        <td
                                            class="{{ $borderClass }} {{ $cellBgClass }} border-r border-b px-2 py-1.5 text-center transition-colors sm:px-3 sm:py-3"
                                            title="Status: {{ ucfirst($attendanceStatus ?? 'Belum ada data') }}"
                                        >
                                            @if ($shiftName)
                                                <div class="flex items-center justify-center">
                                                    <span
                                                        class="{{ $textClass }} rounded px-1 py-0.5 text-[10px] font-semibold sm:px-2 sm:py-1 sm:text-xs"
                                                    >
                                                        {{ $shiftName }}
                                                    </span>
                                                </div>
                                            @else
                                                <span class="text-[10px] text-gray-400 sm:text-xs">-</span>
                                            @endif
                                        </td>
                                    @endfor

                                    <td
                                        class="sticky right-0 z-20 border-b border-l-2 border-gray-300 bg-white px-3 py-2 text-center shadow-[-2px_0_5px_-2px_rgba(0,0,0,0.05)] group-hover:bg-sky-50 sm:px-5 sm:py-4"
                                        rowspan="2"
                                    >
                                        <div class="flex flex-col items-center justify-center">
                                            <span class="text-lg font-bold text-sky-600 sm:text-2xl">
                                                {{ $row['total_jam'] }}
                                            </span>
                                            <span
                                                class="mt-0.5 text-[10px] font-medium text-gray-500 sm:mt-1 sm:text-xs"
                                            >
                                                Jam
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                                <tr
                                    class="group border-b-2 border-gray-300 transition-all duration-150 hover:bg-sky-50"
                                >
                                    <td
                                        class="sticky left-12 z-20 border-r border-b-2 border-gray-300 bg-gray-100 px-3 py-1.5 text-[10px] font-bold text-gray-700 uppercase shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)] group-hover:bg-sky-100 sm:left-16 sm:px-5 sm:py-2.5 sm:text-xs"
                                    >
                                        <div class="flex items-center gap-1 sm:gap-2">
                                            <svg
                                                class="h-3 w-3 text-gray-600 sm:h-3.5 sm:w-3.5"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                                />
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
                                                $textClass = 'font-medium text-amber-700';
                                                $borderClass = 'border-amber-100';
                                            } elseif ($attendanceStatus === 'forgot_checkout') {
                                                $cellBgClass = 'bg-rose-50';
                                                $textClass = 'font-medium text-rose-700';
                                                $borderClass = 'border-rose-100';
                                            } elseif ($attendanceStatus === 'hadir') {
                                                $cellBgClass = 'bg-green-50';
                                                $textClass = 'font-medium text-green-700';
                                                $borderClass = 'border-green-100';
                                            } elseif ($attendanceStatus === 'telat') {
                                                $cellBgClass = 'bg-orange-50';
                                                $textClass = 'font-medium text-orange-700';
                                                $borderClass = 'border-orange-100';
                                            } elseif ($attendanceStatus === 'izin') {
                                                $cellBgClass = 'bg-yellow-50';
                                                $textClass = 'font-medium text-yellow-700';
                                                $borderClass = 'border-yellow-100';
                                            } elseif ($attendanceStatus === 'cuti') {
                                                $cellBgClass = 'bg-purple-50';
                                                $textClass = 'font-medium text-purple-700';
                                                $borderClass = 'border-purple-100';
                                            } elseif ($attendanceStatus === 'alpha') {
                                                $cellBgClass = 'bg-red-50';
                                                $textClass = 'font-medium text-red-700';
                                                $borderClass = 'border-red-100';
                                            }
                                        @endphp

                                        <td
                                            class="{{ $borderClass }} {{ $cellBgClass }} border-r border-b-2 px-2 py-1 text-center transition-colors sm:px-3 sm:py-2.5"
                                        >
                                            @if ($row['shifts'][$d]['hours'])
                                                <div class="flex items-center justify-center">
                                                    <span
                                                        class="{{ $textClass }} rounded bg-white px-1 py-0.5 text-[10px] font-bold sm:px-2 sm:text-xs"
                                                    >
                                                        {{ $row['shifts'][$d]['hours'] }}
                                                    </span>
                                                </div>
                                            @else
                                                <span class="text-[10px] text-gray-400 sm:text-xs">-</span>
                                            @endif
                                        </td>
                                    @endfor
                                </tr>
                            @empty
                                <tr>
                                    <td
                                        colspan="{{ $daysInMonth + 3 }}"
                                        class="border-b-2 border-gray-300 py-10 text-center sm:py-20"
                                    >
                                        <div
                                            class="flex flex-col items-center justify-center space-y-3 px-4 sm:space-y-4"
                                        >
                                            <div
                                                class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-gray-100 to-gray-200 shadow-sm sm:h-20 sm:w-20"
                                            >
                                                <svg
                                                    class="h-8 w-8 text-gray-400 sm:h-10 sm:w-10"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                                    />
                                                </svg>
                                            </div>
                                            <div class="text-center">
                                                <h3 class="mb-1 text-lg font-bold text-gray-800 sm:mb-2 sm:text-xl">
                                                    Belum ada jadwal untuk bulan ini
                                                </h3>
                                                <p class="text-xs text-gray-500 sm:text-sm">
                                                    Silakan pilih periode lain atau tambahkan jadwal baru
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Mobile Card View (Hidden by default) --}}
                <div id="mobileView" class="hidden space-y-4 p-4">
                    @forelse ($data as $index => $row)
                        <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                            <div class="mb-3 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-sky-400 to-sky-600"
                                    >
                                        <span class="text-sm font-bold text-white">
                                            {{ substr($row['nama'], 0, 1) }}
                                        </span>
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-gray-900">{{ $row['nama'] }}</div>
                                        <div class="text-xs text-gray-500">Karyawan</div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-lg font-bold text-sky-600">{{ $row['total_jam'] }}</div>
                                    <div class="text-xs text-gray-500">Total Jam</div>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <div class="mb-2 text-xs font-semibold text-gray-700">Jadwal Bulan Ini:</div>
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
                                            } elseif ($attendanceStatus === 'cuti') {
                                                $cellBgClass = 'bg-purple-100';
                                                $textClass = 'text-purple-800';
                                            } elseif ($attendanceStatus === 'alpha') {
                                                $cellBgClass = 'bg-red-100';
                                                $textClass = 'text-red-800';
                                            }
                                        @endphp

                                        <div
                                            class="{{ $cellBgClass }} flex aspect-square flex-col items-center justify-center rounded p-1"
                                        >
                                            <div
                                                class="{{ $isWeekend ? 'text-red-600' : 'text-gray-700' }} text-[10px] font-bold"
                                            >
                                                {{ $d }}
                                            </div>
                                            @if ($shiftName)
                                                <div
                                                    class="{{ $textClass }} w-full truncate text-center text-[8px] font-semibold"
                                                >
                                                    {{ substr($shiftName, 0, 3) }}
                                                </div>
                                            @endif
                                        </div>
                                    @endfor
                                </div>
                                @if ($daysInMonth > 28)
                                    <div class="mt-2 text-center text-xs text-gray-500">
                                        +{{ $daysInMonth - 28 }} hari lainnya...
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="py-10 text-center">
                            <div class="flex flex-col items-center justify-center space-y-3">
                                <div
                                    class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-gray-100 to-gray-200 shadow-sm"
                                >
                                    <svg
                                        class="h-8 w-8 text-gray-400"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                        />
                                    </svg>
                                </div>
                                <div class="text-center">
                                    <h3 class="mb-1 text-lg font-bold text-gray-800">
                                        Belum ada jadwal untuk bulan ini
                                    </h3>
                                    <p class="text-sm text-gray-500">
                                        Silakan pilih periode lain atau tambahkan jadwal baru
                                    </p>
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
