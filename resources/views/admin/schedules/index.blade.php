@extends('layouts.admin')

@section('title', 'Ringkasan Jadwal Kerja')

@section('content')
    <div class="min-h-screen bg-white p-4 sm:p-6 lg:p-8">
        <div class="space-y-6 sm:space-y-8">
            <div class="flex flex-col gap-4 sm:gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex items-center space-x-3 sm:space-x-4">
                    <div
                        class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-sky-100 to-sky-200 shadow-sm sm:h-12 sm:w-12"
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
                    <div class="min-w-0">
                        <h1 class="text-xl font-bold tracking-tight text-gray-700 sm:text-2xl lg:text-3xl">
                            Ringkasan Jadwal Kerja
                        </h1>
                        <p class="mt-1 truncate text-xs text-gray-500 sm:text-sm">
                            Laporan total jam kerja per karyawan
                        </p>
                    </div>
                </div>
                <a
                    href="{{ route('admin.schedules.create') }}"
                    class="inline-flex transform items-center rounded-xl bg-sky-500 px-6 py-3 font-bold whitespace-normal text-white shadow-sm transition-all hover:bg-sky-600 hover:shadow-md focus:ring-4 focus:ring-sky-200 focus:outline-none"
                >
                    <svg
                        class="mr-1 h-4 w-4 sm:mr-2 sm:h-5 sm:w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"
                        ></path>
                    </svg>
                    <span class="hidden sm:inline">Tambah Jadwal Baru</span>
                    <span class="sm:hidden">Tambah Jadwal</span>
                </a>
            </div>

            <div class="xs:grid-cols-2 grid grid-cols-1 gap-3 sm:gap-4 lg:grid-cols-4 lg:gap-6">
                <div
                    class="rounded-xl bg-gradient-to-br from-sky-500 to-sky-600 p-4 text-sky-50 shadow-xl sm:rounded-2xl sm:p-6"
                >
                    <div class="flex items-center justify-between">
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-xs font-medium tracking-wide text-sky-100 uppercase sm:text-sm">
                                Total Karyawan Terjadwal
                            </p>
                            <p class="mt-1 text-2xl font-bold sm:mt-2 sm:text-3xl">
                                {{ $totalEmployeesWithSchedules }}
                            </p>
                            <p class="mt-1 truncate text-xs text-sky-200">Karyawan memiliki jadwal</p>
                        </div>
                        <div
                            class="bg-opacity-30 ml-2 flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-sky-400 sm:h-14 sm:w-14 sm:rounded-xl"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="24"
                                height="24"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                class="lucide lucide-calendar-days-icon lucide-calendar-days"
                            >
                                <path d="M8 2v4" />
                                <path d="M16 2v4" />
                                <rect width="18" height="18" x="3" y="4" rx="2" />
                                <path d="M3 10h18" />
                                <path d="M8 14h.01" />
                                <path d="M12 14h.01" />
                                <path d="M16 14h.01" />
                                <path d="M8 18h.01" />
                                <path d="M12 18h.01" />
                                <path d="M16 18h.01" />
                            </svg>
                        </div>
                    </div>
                </div>

                <x-stats-card
                    title="Jadwal Hari Ini"
                    :count="$todaySchedules"
                    :subtitle="today()->translatedFormat('d F Y')"
                    bgColor="bg-gradient-to-br from-green-100 to-emerald-100"
                    icon='<svg class="w-7 h-7 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>'
                />
                <x-stats-card
                    title="Jadwal Minggu Ini"
                    :count="$thisWeekSchedules"
                    :subtitle="now()->startOfWeek()->translatedFormat('d M') .
                        ' - ' .
                    now()->endOfWeek()->translatedFormat('d M')"
                    bgColor="bg-gradient-to-br from-blue-100 to-sky-100"
                    icon='<svg class="w-7 h-7 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>'
                />
                <x-stats-card
                    title="Jumlah Total Jadwal"
                    :count="$schedules->count()"
                    subtitle="Semua jadwal yang tercatat"
                    bgColor="bg-gradient-to-br from-purple-100 to-indigo-100"
                    icon='<svg class="w-7 h-7 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>'
                />
            </div>

            <div class="overflow-hidden rounded-xl border-2 border-sky-100 bg-white shadow-xl sm:rounded-2xl">
                <div
                    class="border-b border-sky-100 bg-gradient-to-r from-sky-50 to-blue-50 px-4 py-4 sm:px-6 sm:py-6 lg:px-8"
                >
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                        <div class="min-w-0">
                            <h2 class="text-lg font-bold text-sky-900 sm:text-xl">Rekap Total Jam & Shift</h2>
                            <p class="mt-1 truncate text-xs text-sky-700 sm:text-sm">
                                Laporan total jam kerja per karyawan
                            </p>
                        </div>
                        <div
                            class="flex w-full flex-col items-stretch gap-2 sm:flex-row sm:items-center sm:gap-3 lg:w-auto"
                        >
                            <form
                                method="GET"
                                action="{{ route('admin.schedules.index') }}"
                                class="flex w-full flex-col items-stretch gap-2 sm:w-auto sm:flex-row sm:items-center sm:gap-3"
                            >
                                <!-- User Search -->
                                <div class="relative flex-1 sm:flex-initial">
                                    <!-- Search Input -->
                                    <div class="relative">
                                        <div
                                            class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3"
                                        >
                                            <svg
                                                class="h-3 w-3 text-gray-400 sm:h-4 sm:w-4"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                                                ></path>
                                            </svg>
                                        </div>
                                        <input
                                            type="text"
                                            id="schedule_search"
                                            class="block w-full rounded-lg border border-gray-200 bg-gray-50 py-2 pr-3 pl-9 text-xs transition-all duration-200 focus:border-sky-500 focus:bg-white focus:ring-2 focus:ring-sky-500 sm:w-64 sm:pr-10 sm:pl-10 sm:text-sm"
                                            placeholder="Ketik untuk mencari karyawan..."
                                            autocomplete="off"
                                        />
                                        <input type="hidden" name="search" id="schedule_search_value" />
                                    </div>

                                    <!-- Search Results Dropdown -->
                                    <div
                                        id="schedule_search_results"
                                        class="absolute z-50 mt-1 hidden max-h-48 w-full overflow-y-auto rounded-lg border border-gray-200 bg-white shadow-lg sm:w-64"
                                    >
                                        <div
                                            id="schedule_search_loading"
                                            class="hidden px-3 py-2 text-center text-sm text-gray-500"
                                        >
                                            <svg
                                                class="mx-auto mb-1 h-4 w-4 animate-spin"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                            >
                                                <circle
                                                    class="opacity-25"
                                                    cx="12"
                                                    cy="12"
                                                    r="10"
                                                    stroke="currentColor"
                                                    stroke-width="4"
                                                ></circle>
                                                <path
                                                    class="opacity-75"
                                                    fill="currentColor"
                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                                ></path>
                                            </svg>
                                            Mencari karyawan...
                                        </div>
                                        <div
                                            id="schedule_search_no_results"
                                            class="hidden px-3 py-2 text-center text-sm text-gray-500"
                                        >
                                            Tidak ada karyawan ditemukan
                                        </div>
                                        <div id="schedule_search_results_list" class="divide-y divide-gray-100">
                                            <!-- Results will be populated here -->
                                        </div>
                                    </div>
                                </div>

                                @if (request('search') || request('shift_filter') || request('date_filter'))
                                    <a
                                        href="{{ route('admin.schedules.index') }}"
                                        class="inline-flex items-center justify-center rounded-lg bg-gray-200 px-3 py-2 text-xs whitespace-nowrap text-gray-700 transition hover:bg-gray-300 sm:text-sm"
                                    >
                                        Reset
                                    </a>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>
                <div class="-mx-4 overflow-x-auto sm:mx-0">
                    <table class="w-full min-w-[600px]">
                        <thead class="border-b-2 border-gray-200 bg-gray-50">
                            <tr>
                                <th
                                    class="px-4 py-3 text-left text-xs font-bold tracking-wider text-gray-700 uppercase sm:px-6 sm:py-4 lg:px-8"
                                >
                                    <div class="flex items-center">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            width="16"
                                            height="16"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            class="lucide lucide-user mr-2 text-sky-600"
                                        >
                                            <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" />
                                            <circle cx="12" cy="7" r="4" />
                                        </svg>
                                        Nama Karyawan
                                    </div>
                                </th>
                                <th
                                    class="px-8 py-4 text-left text-xs font-bold tracking-wider text-gray-700 uppercase"
                                >
                                    <div class="flex items-center">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            width="16"
                                            height="16"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            class="lucide lucide-calendar mr-2 text-sky-600"
                                        >
                                            <path d="M8 2v4" />
                                            <path d="M16 2v4" />
                                            <rect width="18" height="18" x="3" y="4" rx="2" />
                                            <path d="M3 10h18" />
                                        </svg>
                                        Total Shift
                                    </div>
                                </th>
                                <th
                                    class="px-8 py-4 text-left text-xs font-bold tracking-wider text-gray-700 uppercase"
                                >
                                    <div class="flex items-center">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            width="16"
                                            height="16"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            class="lucide lucide-clock mr-2 text-sky-600"
                                        >
                                            <circle cx="12" cy="12" r="10" />
                                            <polyline points="12 6 12 12 16 14" />
                                        </svg>
                                        Total Jam Kerja
                                    </div>
                                </th>
                                <th
                                    class="px-8 py-4 text-left text-xs font-bold tracking-wider text-gray-700 uppercase"
                                >
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($workHoursSummary as $summary)
                                <tr class="group transition-colors duration-200 hover:bg-sky-50">
                                    <td class="px-4 py-4 whitespace-nowrap sm:px-6 sm:py-6 lg:px-8">
                                        <div class="flex items-center">
                                            @php
                                                $scheduleUser = \App\Models\User::find($summary['user_id']);
                                            @endphp
                                            @if($scheduleUser && $scheduleUser->profile_photo)
                                                <img src="{{ Storage::url($scheduleUser->profile_photo) }}" alt="Profile Photo" class="mr-4 h-10 w-10 rounded-xl object-cover transition-colors hover:opacity-90">
                                            @else
                                                <div
                                                    class="mr-4 flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-sky-100 to-sky-200 transition-colors group-hover:from-sky-200 group-hover:to-sky-300"
                                                >
                                                    <span class="text-sm font-bold text-sky-600">
                                                        {{ substr($summary['employee_name'], 0, 1) }}
                                                    </span>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="text-base font-semibold text-gray-700">
                                                    {{ $summary['employee_name'] }}
                                                </div>
                                                <div class="text-sm text-gray-500">Karyawan</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-sm font-medium text-emerald-800"
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                width="16"
                                                height="16"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                class="lucide lucide-calendar mr-1"
                                            >
                                                <path d="M8 2v4" />
                                                <path d="M16 2v4" />
                                                <rect width="18" height="18" x="3" y="4" rx="2" />
                                                <path d="M3 10h18" />
                                            </svg>
                                            {{ $summary['total_work_days'] }} shift
                                        </span>
                                    </td>
                                    <td class="px-8 py-6 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center rounded-full bg-sky-100 px-3 py-1 text-sm font-medium text-sky-800"
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                width="16"
                                                height="16"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                class="lucide lucide-clock mr-1"
                                            >
                                                <circle cx="12" cy="12" r="10" />
                                                <polyline points="12 6 12 12 16 14" />
                                            </svg>
                                            {{ $summary['total_work_hours'] }}
                                        </span>
                                    </td>
                                    <td class="px-8 py-6 text-left whitespace-nowrap">
                                        <div class="flex items-center justify-start space-x-3">
                                            @php
                                                $hasFuture = \App\Models\Schedules::where('user_id', $summary['user_id'])
                                                    ->whereDate('schedule_date', '>', \Carbon\Carbon::today())
                                                    ->exists();
                                            @endphp
                                            @if($hasFuture)
                                                <button
                                                    onclick="openSwapModal({{ $summary['user_id'] }}, '{{ $summary['employee_name'] }}')"
                                                    class="inline-flex items-center rounded-lg bg-green-100 px-4 py-2 text-sm font-semibold text-green-700 transition-all duration-200 hover:bg-green-200"
                                                >
                                                    <svg
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        width="16"
                                                        height="16"
                                                        viewBox="0 0 24 24"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        stroke-width="2"
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        class="lucide lucide-arrow-left-right mr-2"
                                                    >
                                                        <path d="M8 3 4 7l4 4" />
                                                        <path d="M4 7h16" />
                                                        <path d="m16 21 4-4-4-4" />
                                                        <path d="M20 17H4" />
                                                    </svg>
                                                    Swap Jadwal
                                                </button>
                                            @else
                                                <button
                                                    class="inline-flex items-center rounded-lg bg-green-100 px-4 py-2 text-sm font-semibold text-green-700 opacity-50 cursor-not-allowed"
                                                    disabled
                                                    title="Tidak dapat swap (tidak ada jadwal masa depan)"
                                                >
                                                    <svg
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        width="16"
                                                        height="16"
                                                        viewBox="0 0 24 24"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        stroke-width="2"
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        class="lucide lucide-arrow-left-right mr-2"
                                                    >
                                                        <path d="M8 3 4 7l4 4" />
                                                        <path d="M4 7h16" />
                                                        <path d="m16 21 4-4-4-4" />
                                                        <path d="M20 17H4" />
                                                    </svg>
                                                    Swap Jadwal
                                                </button>
                                            @endif

                                            <a
                                                href="{{ route('admin.schedules.edit', ['schedule' => 'bulk']) }}?user_id={{ $summary['user_id'] }}"
                                                class="inline-flex items-center rounded-lg bg-sky-100 px-4 py-2 text-sm font-semibold text-sky-700 transition-all duration-200 hover:bg-sky-200"
                                            >
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    width="16"
                                                    height="16"
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    stroke-width="2"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    class="lucide lucide-edit mr-2"
                                                >
                                                    <path
                                                        d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"
                                                    />
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                                </svg>
                                                Lihat Jadwal
                                            </a>

                                            <a
                                                href="{{ route('admin.schedules.history', $summary['user_id']) }}"
                                                class="inline-flex items-center rounded-lg bg-emerald-100 px-4 py-2 text-sm font-semibold text-emerald-700 transition-all duration-200 hover:bg-emerald-200"
                                            >
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    width="16"
                                                    height="16"
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    stroke-width="2"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    class="lucide lucide-history mr-2"
                                                >
                                                    <path d="M3 3v5h5" />
                                                    <path d="M3.05 13A9 9 0 1 0 6 5.3L3 8" />
                                                    <path d="M12 7v5l4 2" />
                                                </svg>
                                                History
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-8 py-16 text-center">
                                        <div class="flex flex-col items-center">
                                            <div
                                                class="mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-gradient-to-br from-sky-100 to-sky-200"
                                            >
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    width="40"
                                                    height="40"
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    stroke-width="2"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    class="lucide lucide-calendar text-sky-400"
                                                >
                                                    <path d="M8 2v4" />
                                                    <path d="M16 2v4" />
                                                    <rect width="18" height="18" x="3" y="4" rx="2" />
                                                    <path d="M3 10h18" />
                                                </svg>
                                            </div>
                                            <h3 class="mb-2 text-xl font-bold text-gray-900">
                                                Belum ada jadwal yang tercatat
                                            </h3>
                                            <p class="mb-6 max-w-sm text-gray-600">
                                                Mulai dengan membuat jadwal kerja untuk melihat ringkasan
                                            </p>
                                            <a
                                                href="{{ route('admin.schedules.create') }}"
                                                class="inline-flex transform items-center rounded-xl bg-gradient-to-r from-sky-500 to-sky-600 px-6 py-3 font-bold text-white shadow-lg transition-all duration-200 hover:from-sky-600 hover:to-sky-700"
                                            >
                                                <svg
                                                    class="mr-2 h-5 w-5"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"
                                                    ></path>
                                                </svg>
                                                Tambah Jadwal Pertama
                                            </a>
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

    <!-- Swap Schedule Modal -->
    <div id="swapModal" class="fixed inset-0 z-50 hidden h-full w-full overflow-y-auto bg-gray-600/50">
        <div class="relative top-20 mx-auto w-11/12 rounded-2xl border bg-white p-5 shadow-lg md:w-3/4 lg:w-1/2">
            <div class="mt-3">
                <!-- Modal Header -->
                <div class="mb-6 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-green-100 to-green-200"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="20"
                                height="20"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                class="lucide lucide-arrow-left-right text-green-600"
                            >
                                <path d="M8 3 4 7l4 4" />
                                <path d="M4 7h16" />
                                <path d="m16 21 4-4-4-4" />
                                <path d="M20 17H4" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Swap Schedule</h3>
                            <p class="text-sm text-gray-600">Tukar jadwal antar karyawan</p>
                        </div>
                    </div>
                    <button onclick="closeSwapModal()" class="text-gray-400 transition-colors hover:text-gray-600">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="24"
                            height="24"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            class="lucide lucide-x"
                        >
                            <path d="M18 6 6 18" />
                            <path d="m6 6 12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Current User Info -->
                <div class="mb-6 rounded-xl bg-gray-50 p-4">
                    <h4 class="mb-2 font-semibold text-gray-800">Karyawan yang dipilih:</h4>
                    <div class="flex items-center space-x-2">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="16"
                            height="16"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            class="lucide lucide-user text-gray-500"
                        >
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                            <circle cx="12" cy="7" r="4" />
                        </svg>
                        <span id="currentUserName" class="font-medium text-gray-700"></span>
                    </div>
                </div>

                <!-- Step 1: Select Source Schedule -->
                <div class="mb-6">
                    <label for="sourceSchedule" class="mb-2 block text-sm font-bold text-gray-700">
                        Pilih Jadwal yang akan ditukar:
                    </label>
                    <div
                        id="sourceSchedulesList"
                        class="max-h-48 space-y-2 overflow-y-auto rounded-lg border border-gray-200 p-3"
                    >
                        <!-- Source schedules will be loaded here -->
                    </div>
                </div>

                <!-- Step 2: Select Target User -->
                <div class="mb-6">
                    <label for="targetUser" class="mb-2 block text-sm font-bold text-gray-700">
                        Pilih Karyawan untuk Swap:
                    </label>
                    <select
                        id="targetUser"
                        name="target_user_id"
                        onchange="loadTargetUserSchedules(this.value)"
                        class="w-full rounded-lg border border-gray-300 px-4 py-3 transition-colors focus:border-green-500 focus:ring-2 focus:ring-green-500"
                    >
                        <option value="">-- Pilih Karyawan --</option>
                    </select>
                </div>

                <!-- Step 3: Select Target Schedule -->
                <div id="targetScheduleContainer" class="mb-6 hidden">
                    <label for="targetSchedule" class="mb-2 block text-sm font-bold text-gray-700">
                        Pilih Jadwal untuk Ditukar:
                    </label>
                    <div
                        id="targetSchedulesList"
                        class="max-h-48 space-y-2 overflow-y-auto rounded-lg border border-gray-200 p-3"
                    >
                        <!-- Target schedules will be loaded here -->
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end space-x-3 border-t border-gray-200 pt-4">
                    <button
                        type="button"
                        onclick="closeSwapModal()"
                        class="rounded-lg bg-gray-200 px-6 py-3 font-semibold text-gray-700 transition-colors hover:bg-gray-300"
                    >
                        Batal
                    </button>
                    <button
                        type="button"
                        id="swapButton"
                        onclick="performSwap()"
                        disabled
                        class="rounded-lg bg-green-600 px-6 py-3 font-semibold text-white transition-colors hover:bg-green-700 disabled:cursor-not-allowed disabled:bg-gray-300"
                    >
                        Tukar Jadwal
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Schedule Search Functionality
        document.addEventListener("DOMContentLoaded", function() {
            const scheduleSearchInput = document.getElementById('schedule_search');
            const scheduleSearchResults = document.getElementById('schedule_search_results');
            const scheduleSearchResultsList = document.getElementById('schedule_search_results_list');
            const scheduleSearchLoading = document.getElementById('schedule_search_loading');
            const scheduleSearchNoResults = document.getElementById('schedule_search_no_results');
            const scheduleSearchValue = document.getElementById('schedule_search_value');

            // Users data - menggunakan data dari controller atau bisa diambil dari API
            const scheduleUsersData = [
                @foreach($workHoursSummary ?? [] as $summary)
                    {
                        id: {{ $summary['user_id'] ?? 0 }},
                        name: "{{ $summary['employee_name'] ?? '' }}",
                        email: "{{ $summary['employee_email'] ?? '' }}"
                    },
                @endforeach
            ];

            let scheduleSearchTimeout;

            // Initialize schedule user search
            if (scheduleSearchInput) {
                scheduleSearchInput.addEventListener('input', function() {
                    const query = this.value.trim();

                    // Clear previous timeout
                    clearTimeout(scheduleSearchTimeout);

                    // Hide results if query is empty
                    if (!query) {
                        hideScheduleSearchResults();
                        return;
                    }

                    // Show loading state
                    showScheduleLoadingState();

                    // Debounce search to avoid too many requests
                    scheduleSearchTimeout = setTimeout(() => {
                        performScheduleUserSearch(query);
                    }, 300);
                });

                // Hide results when clicking outside
                document.addEventListener('click', function(e) {
                    if (!scheduleSearchInput.contains(e.target) && !scheduleSearchResults.contains(e.target)) {
                        hideScheduleSearchResults();
                    }
                });
            }

            function performScheduleUserSearch(query) {
                // Filter users based on query
                const filteredUsers = scheduleUsersData.filter(user =>
                    user.name.toLowerCase().includes(query.toLowerCase()) ||
                    user.email.toLowerCase().includes(query.toLowerCase())
                );

                // Show results
                showScheduleSearchResults(filteredUsers, query);
            }

            function showScheduleSearchResults(users, query) {
                scheduleSearchResultsList.innerHTML = '';

                if (users.length === 0) {
                    hideScheduleLoadingState();
                    showScheduleNoResultsState();
                    return;
                }

                hideScheduleLoadingState();
                hideScheduleNoResultsState();

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
                                <div class="text-sm font-medium text-gray-900">${highlightScheduleText(user.name, query)}</div>
                                <div class="text-xs text-gray-500">${highlightScheduleText(user.email, query)}</div>
                            </div>
                        </div>
                    `;

                    userItem.addEventListener('click', () => selectScheduleUser(user));
                    scheduleSearchResultsList.appendChild(userItem);
                });

                scheduleSearchResults.classList.remove('hidden');
            }

            function highlightScheduleText(text, query) {
                if (!query || !text) return text;

                const regex = new RegExp(`(${query})`, 'gi');
                return text.replace(regex, '<mark class="bg-yellow-200 text-yellow-800">$1</mark>');
            }

            function selectScheduleUser(user) {
                scheduleSearchValue.value = user.name;
                scheduleSearchInput.value = user.name;

                // Hide search results
                hideScheduleSearchResults();

                // Submit form untuk filter
                scheduleSearchInput.form.submit();
            }

            function showScheduleLoadingState() {
                scheduleSearchResults.classList.remove('hidden');
                scheduleSearchLoading.classList.remove('hidden');
                scheduleSearchNoResults.classList.add('hidden');
                scheduleSearchResultsList.innerHTML = '';
            }

            function hideScheduleLoadingState() {
                scheduleSearchLoading.classList.add('hidden');
            }

            function showScheduleNoResultsState() {
                scheduleSearchResults.classList.remove('hidden');
                scheduleSearchNoResults.classList.remove('hidden');
            }

            function hideScheduleNoResultsState() {
                scheduleSearchNoResults.classList.add('hidden');
            }

            function hideScheduleSearchResults() {
                scheduleSearchResults.classList.add('hidden');
                hideScheduleLoadingState();
                hideScheduleNoResultsState();
            }
        });

        // Swap Schedule Functionality
        let selectedSourceSchedule = null;
        let selectedTargetSchedule = null;
        let currentUserId = null;

        function openSwapModal(userId, userName) {
            currentUserId = userId;
            document.getElementById('currentUserName').textContent = userName;

            // Reset form
            document.getElementById('targetUser').value = '';
            document.getElementById('targetScheduleContainer').classList.add('hidden');
            document.getElementById('swapButton').disabled = true;
            selectedSourceSchedule = null;
            selectedTargetSchedule = null;

            // Load source user schedules and target users
            loadSourceUserSchedules(userId);
            loadUsersForSwap();

            document.getElementById('swapModal').classList.remove('hidden');
        }

        function closeSwapModal() {
            document.getElementById('swapModal').classList.add('hidden');
        }

        function loadSourceUserSchedules(userId) {
            fetch(`/admin/schedules/user-schedules/${userId}`)
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('sourceSchedulesList');
                    container.innerHTML = '';

                    if (data.schedules.length === 0) {
                        container.innerHTML = '<p class="text-gray-500 text-center py-4">Tidak ada jadwal tersedia untuk karyawan ini</p>';
                    } else {
                        data.schedules.forEach(schedule => {
                            const scheduleDiv = document.createElement('div');
                            scheduleDiv.className = 'border border-gray-200 rounded-lg p-3 hover:bg-gray-50 cursor-pointer transition-colors';
                            scheduleDiv.onclick = () => selectSourceSchedule(schedule.id, scheduleDiv);

                            scheduleDiv.innerHTML = `
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex items-center space-x-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock text-gray-500">
                                                <circle cx="12" cy="12" r="10" />
                                                <polyline points="12 6 12 12 16 14" />
                                            </svg>
                                            <span class="font-medium">${schedule.shift_name}</span>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar text-gray-500">
                                                <path d="M8 2v4" />
                                                <path d="M16 2v4" />
                                                <rect width="18" height="18" x="3" y="4" rx="2" />
                                                <path d="M3 10h18" />
                                            </svg>
                                            <span class="text-gray-600">${schedule.formatted_date}</span>
                                        </div>
                                    </div>
                                    <div class="text-sm text-gray-500">${schedule.time_range}</div>
                                </div>
                            `;

                            container.appendChild(scheduleDiv);
                        });
                    }
                })
                .catch(error => {
                    console.error('Error loading source schedules:', error);
                    alert('Gagal memuat jadwal karyawan');
                });
        }

        function loadUsersForSwap() {
            fetch('/admin/schedules/users-with-schedules')
                .then(response => response.json())
                .then(data => {
                    const select = document.getElementById('targetUser');
                    select.innerHTML = '<option value="">-- Pilih Karyawan --</option>';

                    data.users.forEach(user => {
                        // Don't include current user in the list
                        if (user.id !== currentUserId) {
                            const option = document.createElement('option');
                            option.value = user.id;
                            option.textContent = user.name;
                            select.appendChild(option);
                        }
                    });
                })
                .catch(error => {
                    console.error('Error loading users:', error);
                    alert('Gagal memuat daftar karyawan');
                });
        }

        function loadTargetUserSchedules(userId) {
            if (!userId) {
                document.getElementById('targetScheduleContainer').classList.add('hidden');
                selectedTargetSchedule = null;
                updateSwapButton();
                return;
            }

            fetch(`/admin/schedules/user-schedules/${userId}`)
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('targetSchedulesList');
                    container.innerHTML = '';

                    if (data.schedules.length === 0) {
                        container.innerHTML = '<p class="text-gray-500 text-center py-4">Tidak ada jadwal tersedia untuk karyawan ini</p>';
                    } else {
                        data.schedules.forEach(schedule => {
                            const scheduleDiv = document.createElement('div');
                            scheduleDiv.className = 'border border-gray-200 rounded-lg p-3 hover:bg-gray-50 cursor-pointer transition-colors';
                            scheduleDiv.onclick = () => selectTargetSchedule(schedule.id, scheduleDiv);

                            scheduleDiv.innerHTML = `
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex items-center space-x-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock text-gray-500">
                                                <circle cx="12" cy="12" r="10" />
                                                <polyline points="12 6 12 12 16 14" />
                                            </svg>
                                            <span class="font-medium">${schedule.shift_name}</span>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar text-gray-500">
                                                <path d="M8 2v4" />
                                                <path d="M16 2v4" />
                                                <rect width="18" height="18" x="3" y="4" rx="2" />
                                                <path d="M3 10h18" />
                                            </svg>
                                            <span class="text-gray-600">${schedule.formatted_date}</span>
                                        </div>
                                    </div>
                                    <div class="text-sm text-gray-500">${schedule.time_range}</div>
                                </div>
                            `;

                            container.appendChild(scheduleDiv);
                        });
                    }

                    document.getElementById('targetScheduleContainer').classList.remove('hidden');
                    selectedTargetSchedule = null;
                    updateSwapButton();
                })
                .catch(error => {
                    console.error('Error loading target schedules:', error);
                    alert('Gagal memuat jadwal karyawan target');
                });
        }

        function selectSourceSchedule(scheduleId, element) {
            // Remove previous selection
            document.querySelectorAll('#sourceSchedulesList > div').forEach(div => {
                div.classList.remove('bg-blue-50', 'border-blue-300');
                div.classList.add('border-gray-200');
            });

            // Add selection to clicked element
            element.classList.remove('border-gray-200');
            element.classList.add('bg-blue-50', 'border-blue-300');

            selectedSourceSchedule = scheduleId;
            updateSwapButton();
        }

        function selectTargetSchedule(scheduleId, element) {
            // Remove previous selection
            document.querySelectorAll('#targetSchedulesList > div').forEach(div => {
                div.classList.remove('bg-green-50', 'border-green-300');
                div.classList.add('border-gray-200');
            });

            // Add selection to clicked element
            element.classList.remove('border-gray-200');
            element.classList.add('bg-green-50', 'border-green-300');

            selectedTargetSchedule = scheduleId;
            updateSwapButton();
        }

        function updateSwapButton() {
            const swapButton = document.getElementById('swapButton');
            swapButton.disabled = !(selectedSourceSchedule && selectedTargetSchedule);
        }

        function performSwap() {
            if (!selectedSourceSchedule || !selectedTargetSchedule) {
                alert('Pilih kedua jadwal yang akan ditukar');
                return;
            }

            const formData = new FormData();
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            formData.append('schedule_id', selectedSourceSchedule);
            formData.append('target_schedule_id', selectedTargetSchedule);

            // Show loading state
            const swapButton = document.getElementById('swapButton');
            const originalText = swapButton.textContent;
            swapButton.textContent = 'Menukar...';
            swapButton.disabled = true;

            fetch('/admin/schedules/swap', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Jadwal berhasil ditukar!');
                    location.reload();
                } else {
                    alert(data.message || 'Gagal menukar jadwal');
                    swapButton.textContent = originalText;
                    swapButton.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menukar jadwal');
                swapButton.textContent = originalText;
                swapButton.disabled = false;
            });
        }

        // Close modal when clicking outside
        document.getElementById('swapModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeSwapModal();
            }
        });
    </script>
@endsection
