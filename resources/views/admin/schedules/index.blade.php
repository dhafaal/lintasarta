@extends('layouts.admin')

@section('title', 'Ringkasan Jadwal Kerja')

@section('content')
    <div class="min-h-screen bg-white p-4 sm:p-6 lg:p-8">
        <div class="space-y-6 sm:space-y-8">
            <div class="flex flex-col gap-4 sm:gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex items-center space-x-3 sm:space-x-4">
                    <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-sky-100 to-sky-200 shadow-sm">
                        <i data-lucide="calendar" class="h-6 w-6 text-sky-600"></i>
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
                    class="inline-flex transform items-center rounded-xl bg-gradient-to-r from-sky-500 to-indigo-600 px-6 py-2.5 font-bold text-white shadow-lg transition-all duration-200 hover:from-sky-600 hover:to-indigo-700 hover:scale-105 active:scale-95 focus:ring-4 focus:ring-sky-200 focus:outline-none whitespace-nowrap"
                >
                    <i data-lucide="plus" class="mr-2 h-5 w-5"></i>
                    Tambah Jadwal Baru
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

            <div class="overflow-hidden rounded-2xl border-2 border-sky-100 bg-white shadow-xl">
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
                            <div class="flex w-full flex-col items-stretch gap-2 sm:w-auto sm:flex-row sm:items-center sm:gap-3">
                                <!-- User Search -->
                                <div class="relative flex-1 sm:flex-initial">
                                    <!-- Search Input -->
                                    <div class="relative">
                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <i data-lucide="search" class="h-4 w-4 text-gray-400"></i>
                                        </div>
                                        <input
                                            type="text"
                                            id="searchInput"
                                            class="block w-full rounded-lg border border-sky-100 bg-white py-2.5 pr-10 pl-10 text-sm font-semibold text-sky-900 transition-all duration-200 focus:border-sky-500 focus:ring-4 focus:ring-sky-100 sm:w-72"
                                            placeholder="Cari nama karyawan..."
                                            autocomplete="off"
                                        />
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table id="schedulesTable" class="w-full">
                        <thead class="border-b-2 border-gray-200 bg-gray-50">
                            <tr>
                                <th class="px-8 py-4 text-left text-xs font-bold tracking-wider text-gray-700 uppercase">
                                    <div class="flex items-center">
                                        <i data-lucide="user" class="mr-2 h-4 w-4 text-sky-600"></i>
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
                                <th class="px-8 py-4 text-left text-xs font-bold tracking-wider text-gray-700 uppercase">
                                    <div class="flex items-center">
                                        <i data-lucide="clock" class="mr-2 h-4 w-4 text-sky-600"></i>
                                        Total Jam Kerja
                                    </div>
                                </th>
                                <th class="px-8 py-4 text-center text-xs font-bold tracking-wider text-gray-700 uppercase">
                                    <div class="flex items-center justify-center">
                                        <i data-lucide="settings" class="mr-2 h-4 w-4 text-sky-600"></i>
                                        Aksi
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($workHoursSummary as $summary)
                                <tr class="group transition-colors duration-200 hover:bg-sky-50">
                                    <td class="px-8 py-6 whitespace-nowrap">
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
                                                <div class="text-base font-semibold text-gray-900">
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
                                        <div class="flex items-center justify-start space-x-2">
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
                                                href="{{ route('admin.schedules.history', ['user' => $summary['user_id']]) }}"
                                                class="inline-flex items-center rounded-lg bg-emerald-100 px-4 py-2 text-sm font-semibold text-emerald-700 transition-all duration-200 hover:bg-emerald-200"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1.5"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                                Jadwal
                                            </a>

                                            <a
                                                href="{{ route('admin.schedules.edit', ['schedule' => 'bulk']) }}?user_id={{ $summary['user_id'] }}"
                                                class="inline-flex items-center rounded-lg bg-sky-100 px-4 py-2 text-sm font-semibold text-sky-700 transition-all duration-200 hover:bg-sky-200"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                                Edit
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

                <!-- Manual Pagination Footer (Matches Locations Module) -->
                <div id="pagination-footer" class="flex items-center justify-between px-8 py-6 border-t border-gray-100 bg-white">
                    <div class="flex items-center space-x-3 text-sm font-medium text-gray-600">
                        <span>Tampilkan</span>
                        <select id="pageSize" class="rounded-lg border-gray-200 bg-white py-1.5 pl-3 pr-8 text-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-all cursor-pointer">
                            <option value="5">5</option>
                            <option value="10" selected>10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <span>data</span>
                    </div>
                    <div class="flex items-center space-x-6">
                        <div id="paginationInfo" class="text-sm font-medium text-gray-500"></div>
                        <div id="paginationButtons" class="flex items-center space-x-2"></div>
                    </div>
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

        // Manual Pagination logic for the work-hours summary table
        document.addEventListener('DOMContentLoaded', function () {
            const table = document.getElementById('schedulesTable');
            const tbody = table ? table.querySelector('tbody') : null;
            if (!tbody) return;

            const rows = Array.from(tbody.querySelectorAll('tr')).filter(row => !row.querySelector('td[colspan]'));
            const searchInput = document.getElementById('searchInput');
            const pageSizeSelect = document.getElementById('pageSize');
            const infoText = document.getElementById('paginationInfo');
            const buttonsContainer = document.getElementById('paginationButtons');

            let currentPage = 1;
            let pageSize = parseInt(pageSizeSelect.value);

            function render() {
                const searchTerm = searchInput.value.toLowerCase().trim();

                const filteredRows = rows.filter(row => {
                    const employeeName = row.cells[0].textContent.toLowerCase();
                    return employeeName.includes(searchTerm);
                });

                const total = filteredRows.length;
                const totalPages = Math.max(1, Math.ceil(total / pageSize));

                if (currentPage > totalPages) currentPage = totalPages;

                // Update info text
                const start = total === 0 ? 0 : (currentPage - 1) * pageSize + 1;
                const end = Math.min(currentPage * pageSize, total);
                infoText.textContent = total === 0 
                    ? 'Tidak ada data' 
                    : `Menampilkan ${start} – ${end} dari ${total} jadwal`;

                // Show/hide rows
                rows.forEach(row => row.style.display = 'none');
                filteredRows.slice((currentPage - 1) * pageSize, currentPage * pageSize).forEach(row => {
                    row.style.display = '';
                });

                // Render buttons
                buttonsContainer.innerHTML = '';
                
                const btnClass = 'inline-flex items-center justify-center min-w-[2.25rem] h-[2.25rem] px-2 rounded-xl text-sm font-bold transition-all duration-200 border-none';
                const inactiveClass = 'bg-sky-100 text-sky-700 hover:bg-sky-200';
                const activeClass = 'bg-sky-500 text-white shadow-md shadow-sky-200';
                const disabledClass = 'opacity-30 cursor-not-allowed bg-gray-100 text-gray-400';

                function createBtn(label, page, disabled = false, active = false) {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.innerHTML = label;
                    btn.className = `${btnClass} ${disabled ? disabledClass : (active ? activeClass : inactiveClass)}`;
                    if (!disabled && !active) {
                        btn.onclick = () => {
                            currentPage = page;
                            render();
                            window.scrollTo({ top: table.offsetTop - 100, behavior: 'smooth' });
                        };
                    }
                    buttonsContainer.appendChild(btn);
                }

                createBtn('‹', currentPage - 1, currentPage === 1);
                for (let i = 1; i <= totalPages; i++) {
                    createBtn(i, i, false, i === currentPage);
                }
                createBtn('›', currentPage + 1, currentPage === totalPages);
            }

            if (searchInput) {
                searchInput.addEventListener('input', () => { currentPage = 1; render(); });
            }
            
            pageSizeSelect.addEventListener('change', (e) => {
                pageSize = parseInt(e.target.value);
                currentPage = 1;
                render();
            });

            render();
        });
    </script>
@endsection
