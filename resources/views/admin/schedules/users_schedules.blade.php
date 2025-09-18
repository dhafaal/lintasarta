@extends('layouts.admin')

@section('title', 'Detail Jadwal')

@section('content')
    <div class="min-h-screen bg-white sm:p-6 lg:p-8">
        <div class="mx-auto space-y-8">
            <!-- Header -->
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="flex items-center space-x-4">
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-sky-100 to-sky-200 rounded-xl flex items-center justify-center shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="lucide lucide-calendar text-sky-600">
                            <path d="M8 2v4" />
                            <path d="M16 2v4" />
                            <rect width="18" height="18" x="3" y="4" rx="2" />
                            <path d="M3 10h18" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-700 tracking-tight">Detail Jadwal</h1>
                        <p class="text-gray-500 mt-1">Karyawan: <span class="font-semibold">{{ $user->name }}</span></p>
                    </div>
                </div>
                <a href="{{ route('admin.schedules.index') }}"
                    class="inline-flex items-center px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold rounded-xl transition-all duration-200 transform hover:scale-105 focus:outline-none focus:ring-4 focus:ring-gray-200 shadow-lg hover:shadow-xl">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-arrow-left mr-2">
                        <path d="M15 19l-7-7 7-7" />
                    </svg>
                    Kembali
                </a>
            </div>

            <!-- Detail Jadwal User -->
            <div class="bg-white rounded-2xl shadow-xl border-2 border-sky-100 overflow-hidden">
                <div class="bg-gradient-to-r from-sky-50 to-blue-50 px-8 py-6 border-b border-sky-100">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div
                                class="w-12 h-12 bg-gradient-to-br from-sky-500 to-sky-600 bg-opacity-30 rounded-xl flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="lucide lucide-calendar text-sky-50 text-center">
                                    <path d="M8 2v4" />
                                    <path d="M16 2v4" />
                                    <rect width="18" height="18" x="3" y="4" rx="2" />
                                    <path d="M3 10h18" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-sky-900">Jadwal Kerja {{ $user->name }}</h2>
                                <p class="text-sky-700 mt-1">Daftar semua shift & tanggal kerja</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3">
                            <form method="GET" action="{{ route('admin.schedules.user', $user->id) }}"
                                class="flex items-center space-x-3">
                                <select name="shift_filter" onchange="this.form.submit()"
                                    class="px-3 py-2 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm">
                                    <option value="">Semua Shift</option>
                                    <option value="Pagi" {{ request('shift_filter') == 'Pagi' ? 'selected' : '' }}>Pagi
                                    </option>
                                    <option value="Siang" {{ request('shift_filter') == 'Siang' ? 'selected' : '' }}>Siang
                                    </option>
                                    <option value="Malam" {{ request('shift_filter') == 'Malam' ? 'selected' : '' }}>Malam
                                    </option>
                                </select>
                                <input type="date" name="date_filter" value="{{ request('date_filter') }}"
                                    onchange="this.form.submit()"
                                    class="px-3 py-2 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm">
                                @if (request('shift_filter') || request('date_filter'))
                                    <a href="{{ route('admin.schedules.user', $user->id) }}"
                                        class="px-3 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm hover:bg-gray-300 transition">
                                        Reset
                                    </a>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b-2 border-gray-200">
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
                                            class="lucide lucide-clock text-sky-600 mr-2">
                                            <circle cx="12" cy="12" r="10" />
                                            <polyline points="12 6 12 12 16 14" />
                                        </svg>
                                        Jam
                                    </div>
                                </th>
                                <th class="px-8 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($schedules as $schedule)
                                <tr class="hover:bg-sky-50 transition-colors duration-200">
                                    <td class="px-8 py-6 whitespace-nowrap">
                                        <div class="flex items-center">
                                            @if ($schedule->shift && $schedule->shift->category == 'Pagi')
                                                <div
                                                    class="w-8 h-8 bg-gradient-to-br from-orange-100 to-yellow-100 rounded-lg flex items-center justify-center mr-3">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                        class="lucide lucide-sun text-orange-500">
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
                                            @elseif($schedule->shift && $schedule->shift->category == 'Siang')
                                                <div
                                                    class="w-8 h-8 bg-gradient-to-br from-sky-100 to-blue-100 rounded-lg flex items-center justify-center mr-3">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                        class="lucide lucide-sun text-sky-500">
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
                                            @elseif($schedule->shift && $schedule->shift->category == 'Malam')
                                                <div
                                                    class="w-8 h-8 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-lg flex items-center justify-center mr-3">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                        class="lucide lucide-moon text-indigo-500">
                                                        <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9" />
                                                    </svg>
                                                </div>
                                            @else
                                                <div
                                                    class="w-8 h-8 bg-gradient-to-br from-gray-100 to-gray-200 rounded-lg flex items-center justify-center mr-3">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                        class="lucide lucide-x text-gray-500">
                                                        <path d="M18 6 6 18" />
                                                        <path d="m6 6 12 12" />
                                                    </svg>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="text-base font-bold text-gray-900">
                                                    {{ $schedule->shift->shift_name ?? '-' }}</div>
                                                <div class="text-sm text-gray-500">
                                                    @if ($schedule->shift)
                                                        {{ \Carbon\Carbon::parse($schedule->shift->start_time)->format('H:i') }}
                                                        -
                                                        {{ \Carbon\Carbon::parse($schedule->shift->end_time)->format('H:i') }}
                                                    @else
                                                        -
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 whitespace-nowrap">
                                        <div class="text-base font-semibold text-gray-900">
                                            {{ \Carbon\Carbon::parse($schedule->schedule_date)->format('d M Y') }}</div>
                                        <div class="text-sm text-gray-500">
                                            {{ \Carbon\Carbon::parse($schedule->schedule_date)->translatedFormat('l') }}
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 whitespace-nowrap">
                                        <div
                                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-sky-100 text-sky-800">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                class="lucide lucide-clock mr-1">
                                                <circle cx="12" cy="12" r="10" />
                                                <polyline points="12 6 12 12 16 14" />
                                            </svg>
                                            @php
                                                $start = \Carbon\Carbon::parse($schedule->shift->start_time);
                                                $end = \Carbon\Carbon::parse($schedule->shift->end_time);
                                                if ($end->lt($start)) {
                                                    $end->addDay();
                                                }
                                                $duration = $start->diffInHours($end);
                                            @endphp
                                            {{ $duration }} jam
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 whitespace-nowrap text-left">
                                        <div class="flex items-center justify-start space-x-3">
                                            <button onclick="openSwapModal({{ $schedule->id }}, '{{ $schedule->user->name }}', '{{ $schedule->shift->shift_name ?? '-' }}', '{{ \Carbon\Carbon::parse($schedule->schedule_date)->format('d M Y') }}')"
                                                class="inline-flex items-center px-4 py-2 bg-green-100 hover:bg-green-200 text-green-700 font-semibold text-sm rounded-lg transition-all duration-200 hover:scale-105">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="lucide lucide-arrow-left-right mr-2">
                                                    <path d="M8 3 4 7l4 4" />
                                                    <path d="M4 7h16" />
                                                    <path d="m16 21 4-4-4-4" />
                                                    <path d="M20 17H4" />
                                                </svg>
                                                Swap
                                            </button>
                                            <a href="{{ route('admin.schedules.edit', $schedule->id) }}"
                                                class="inline-flex items-center px-4 py-2 bg-sky-100 hover:bg-sky-200 text-sky-700 font-semibold text-sm rounded-lg transition-all duration-200 hover:scale-105">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="lucide lucide-edit mr-2">
                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                                </svg>
                                                Edit
                                            </a>
                                            <form action="{{ route('admin.schedules.destroy', $schedule->id) }}"
                                                method="POST" class="inline-block"
                                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus jadwal ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="inline-flex items-center px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 font-semibold text-sm rounded-lg transition-all duration-200 hover:scale-105">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                        class="lucide lucide-trash-2 mr-2">
                                                        <path d="M3 6h18" />
                                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6" />
                                                        <path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                                        <line x1="10" x2="10" y1="11"
                                                            y2="17" />
                                                        <line x1="14" x2="14" y1="11"
                                                            y2="17" />
                                                    </svg>
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-8 py-16 text-center">
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
                                            <h3 class="text-xl font-bold text-gray-900 mb-2">Belum ada jadwal</h3>
                                            <p class="text-gray-600 mb-6 max-w-sm">Mulai dengan membuat jadwal kerja untuk
                                                karyawan ini</p>
                                            <a href="{{ route('admin.schedules.create') }}"
                                                class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-sky-500 to-sky-600 hover:from-sky-600 hover:to-sky-700 text-white font-bold rounded-xl transition-all duration-200 transform hover:scale-105 shadow-lg">
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
        </div>
    </div>

    <!-- Swap Schedule Modal -->
    <div id="swapModal" class="fixed inset-0 bg-gray-600/50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-2xl bg-white">
            <div class="mt-3">
                <!-- Modal Header -->
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-green-100 to-green-200 rounded-xl flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left-right text-green-600">
                                <path d="M8 3 4 7l4 4" />
                                <path d="M4 7h16" />
                                <path d="m16 21 4-4-4-4" />
                                <path d="M20 17H4" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Swap Schedule</h3>
                            <p class="text-gray-600 text-sm">Tukar jadwal dengan karyawan lain</p>
                        </div>
                    </div>
                    <button onclick="closeSwapModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x">
                            <path d="M18 6 6 18" />
                            <path d="m6 6 12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Current Schedule Info -->
                <div class="bg-gray-50 rounded-xl p-4 mb-6">
                    <h4 class="font-semibold text-gray-800 mb-2">Jadwal yang akan ditukar:</h4>
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center space-x-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user text-gray-500">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                <circle cx="12" cy="7" r="4" />
                            </svg>
                            <span id="currentUser" class="text-gray-700 font-medium"></span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock text-gray-500">
                                <circle cx="12" cy="12" r="10" />
                                <polyline points="12 6 12 12 16 14" />
                            </svg>
                            <span id="currentShift" class="text-gray-700 font-medium"></span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar text-gray-500">
                                <path d="M8 2v4" />
                                <path d="M16 2v4" />
                                <rect width="18" height="18" x="3" y="4" rx="2" />
                                <path d="M3 10h18" />
                            </svg>
                            <span id="currentDate" class="text-gray-700 font-medium"></span>
                        </div>
                    </div>
                </div>

                <!-- Swap Form -->
                <form id="swapForm" method="POST">
                    @csrf
                    <input type="hidden" id="scheduleId" name="schedule_id" value="">
                    
                    <!-- Step 1: Select User -->
                    <div class="mb-6">
                        <label for="targetUser" class="block text-sm font-bold text-gray-700 mb-2">
                            Pilih Karyawan untuk Swap:
                        </label>
                        <select id="targetUser" name="target_user_id" onchange="loadUserSchedules(this.value)" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
                            <option value="">-- Pilih Karyawan --</option>
                        </select>
                    </div>

                    <!-- Step 2: Select Target Schedule -->
                    <div id="targetScheduleContainer" class="mb-6 hidden">
                        <label for="targetSchedule" class="block text-sm font-bold text-gray-700 mb-2">
                            Pilih Jadwal untuk Ditukar:
                        </label>
                        <div id="schedulesList" class="space-y-2 max-h-60 overflow-y-auto">
                            <!-- Schedules will be loaded here -->
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200">
                        <button type="button" onclick="closeSwapModal()" 
                                class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-lg transition-colors">
                            Batal
                        </button>
                        <button type="submit" id="swapButton" disabled
                                class="px-6 py-3 bg-green-600 hover:bg-green-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-semibold rounded-lg transition-colors">
                            Tukar Jadwal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let selectedTargetSchedule = null;

        function openSwapModal(scheduleId, userName, shiftName, scheduleDate) {
            document.getElementById('scheduleId').value = scheduleId;
            document.getElementById('currentUser').textContent = userName;
            document.getElementById('currentShift').textContent = shiftName;
            document.getElementById('currentDate').textContent = scheduleDate;
            
            // Reset form
            document.getElementById('targetUser').value = '';
            document.getElementById('targetScheduleContainer').classList.add('hidden');
            document.getElementById('swapButton').disabled = true;
            selectedTargetSchedule = null;
            
            // Load users
            loadUsers();
            
            document.getElementById('swapModal').classList.remove('hidden');
        }

        function closeSwapModal() {
            document.getElementById('swapModal').classList.add('hidden');
        }

        function loadUsers() {
            fetch('/admin/schedules/users-with-schedules')
                .then(response => response.json())
                .then(data => {
                    const select = document.getElementById('targetUser');
                    select.innerHTML = '<option value="">-- Pilih Karyawan --</option>';
                    
                    data.users.forEach(user => {
                        const option = document.createElement('option');
                        option.value = user.id;
                        option.textContent = user.name;
                        select.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error loading users:', error);
                    alert('Gagal memuat daftar karyawan');
                });
        }

        function loadUserSchedules(userId) {
            if (!userId) {
                document.getElementById('targetScheduleContainer').classList.add('hidden');
                return;
            }

            fetch(`/admin/schedules/user-schedules/${userId}`)
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('schedulesList');
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
                })
                .catch(error => {
                    console.error('Error loading schedules:', error);
                    alert('Gagal memuat jadwal karyawan');
                });
        }

        function selectTargetSchedule(scheduleId, element) {
            // Remove previous selection
            document.querySelectorAll('#schedulesList > div').forEach(div => {
                div.classList.remove('bg-green-50', 'border-green-300');
                div.classList.add('border-gray-200');
            });
            
            // Add selection to clicked element
            element.classList.remove('border-gray-200');
            element.classList.add('bg-green-50', 'border-green-300');
            
            selectedTargetSchedule = scheduleId;
            document.getElementById('swapButton').disabled = false;
        }

        document.getElementById('swapForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (!selectedTargetSchedule) {
                alert('Pilih jadwal yang akan ditukar');
                return;
            }
            
            const formData = new FormData();
            formData.append('_token', document.querySelector('input[name="_token"]').value);
            formData.append('schedule_id', document.getElementById('scheduleId').value);
            formData.append('target_schedule_id', selectedTargetSchedule);
            
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
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menukar jadwal');
            });
        });

        // Close modal when clicking outside
        document.getElementById('swapModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeSwapModal();
            }
        });
    </script>
@endsection
