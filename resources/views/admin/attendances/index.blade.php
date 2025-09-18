@extends('layouts.admin')

@section('title', 'Manajemen Absensi')

@section('content')
    <div class="min-h-screen bg-white sm:p-6 lg:p-8">
        <div class="mx-auto space-y-8">
            <!-- Enhanced Header Section -->
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-sky-100 to-sky-200 rounded-xl flex items-center justify-center shadow-sm">
                        <i data-lucide="calendar-check" class="w-6 h-6 text-sky-700"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-700 tracking-tight">Manajemen Absensi</h1>
                        <p class="text-gray-500 mt-1">{{ $todayFormated }} - Kelola data absensi harian</p>
                    </div>
                </div>
                
                <!-- Date Filter & Actions -->
                <div class="flex items-center space-x-3">
                    <form method="GET" class="flex items-center space-x-3">
                        <div class="relative">
                            <input type="date" name="date" value="{{ $today }}" 
                                class="pl-10 pr-4 py-2 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-sky-100 focus:border-sky-500 transition-all duration-200 bg-gray-50 focus:bg-white text-sm">
                            <i data-lucide="calendar" class="w-4 h-4 text-gray-400 absolute left-3 top-3"></i>
                        </div>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-xl transition-all">
                            <i data-lucide="search" class="w-4 h-4 mr-1"></i>
                            Filter
                        </button>
                    </form>
                    
                    <a href="{{ route('admin.attendances.history') }}"
                       class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-sky-500 to-sky-600 hover:from-sky-600 hover:to-sky-700 text-white font-bold rounded-xl transition-all transform hover:scale-105 focus:outline-none focus:ring-4 focus:ring-sky-200 shadow-sm hover:shadow-md">
                        <i data-lucide="history" class="w-5 h-5 mr-2"></i>
                        Riwayat Absensi
                    </a>
                </div>
            </div>

            <!-- Enhanced Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                <div class="bg-gradient-to-br from-sky-500 to-sky-600 rounded-2xl p-6 text-white shadow-xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sky-100 text-sm font-medium uppercase tracking-wide">Total Jadwal</p>
                            <p class="text-3xl font-bold mt-2">{{ $totalSchedules }}</p>
                            <p class="text-sky-200 text-xs mt-1">Jadwal hari ini</p>
                        </div>
                        <div class="w-14 h-14 bg-sky-400 bg-opacity-30 rounded-xl flex items-center justify-center">
                            <i data-lucide="calendar-days" class="w-6 h-6 text-white"></i>
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

            <!-- Enhanced Table Card -->
            <div class="bg-white rounded-2xl border-2 border-sky-100 overflow-hidden shadow-xl">
                <div class="px-8 py-6 border-b border-sky-100 bg-gradient-to-r from-sky-50 to-blue-50">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-xl font-bold text-sky-900">Daftar Absensi Karyawan</h2>
                            <p class="text-sky-700 mt-1">Data absensi untuk tanggal {{ $todayFormated }}</p>
                        </div>
                        <div class="flex items-center space-x-3">
                            <form method="GET" action="{{ route('admin.attendances.index') }}" class="flex items-center space-x-3">
                                <!-- Search -->
                                <div class="relative">
                                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari karyawan..." oninput="this.form.submit()"
                                           class="pl-10 pr-4 py-2 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm">
                                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>

                                <!-- Filter Status -->
                                <select name="status" onchange="this.form.submit()" class="px-3 py-2 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm">
                                    <option value="">Semua Status</option>
                                    <option value="hadir" {{ request('status') == 'hadir' ? 'selected' : '' }}>Hadir</option>
                                    <option value="telat" {{ request('status') == 'telat' ? 'selected' : '' }}>Telat</option>
                                    <option value="izin" {{ request('status') == 'izin' ? 'selected' : '' }}>Izin</option>
                                    <option value="alpha" {{ request('status') == 'alpha' ? 'selected' : '' }}>Alpha</option>
                                </select>

                                <!-- Reset -->
                                @if(request('search') || request('status'))
                                    <a href="{{ route('admin.attendances.index') }}" class="px-3 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm hover:bg-gray-300 transition">
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
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user text-sky-600 mr-2">
                                            <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
                                            <circle cx="12" cy="7" r="4"/>
                                        </svg>
                                        Nama Karyawan
                                    </div>
                                </th>
                                <th class="px-8 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock text-sky-600 mr-2">
                                            <circle cx="12" cy="12" r="10"/>
                                            <polyline points="12 6 12 12 16 14"/>
                                        </svg>
                                        Shift
                                    </div>
                                </th>
                                <th class="px-8 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-log-in text-sky-600 mr-2">
                                            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                                            <polyline points="10 17 15 12 10 7"/>
                                            <line x1="15" x2="3" y1="12" y2="12"/>
                                        </svg>
                                        Check In
                                    </div>
                                </th>
                                <th class="px-8 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-log-out text-sky-600 mr-2">
                                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                                            <polyline points="16 17 21 12 16 7"/>
                                            <line x1="21" x2="9" y1="12" y2="12"/>
                                        </svg>
                                        Check Out
                                    </div>
                                </th>
                                <th class="px-8 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-activity text-sky-600 mr-2">
                                            <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
                                        </svg>
                                        Status
                                    </div>
                                </th>
                                <th class="px-8 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-message-circle text-sky-600 mr-2">
                                            <path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/>
                                        </svg>
                                        Keterangan
                                    </div>
                                </th>
                                <th class="px-8 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($schedulesToday as $schedule)
                                @php
                                    $attendance = $attendances->firstWhere('schedule_id', $schedule->id);
                                    $permission = $permissions->firstWhere('schedule_id', $schedule->id);
                                @endphp
                                <tr class="hover:bg-sky-50 transition-colors duration-200 group">
                                    <td class="px-8 py-6 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-gradient-to-br from-sky-100 to-sky-200 rounded-xl flex items-center justify-center mr-4 group-hover:from-sky-200 group-hover:to-sky-300 transition-colors">
                                                <span class="text-sky-600 font-bold text-sm">{{ substr($schedule->user->name, 0, 1) }}</span>
                                            </div>
                                            <div>
                                                <div class="text-base font-semibold text-gray-700">{{ $schedule->user->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $schedule->user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 whitespace-nowrap">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                                            @if($schedule->shift && $schedule->shift->category == 'Pagi') bg-yellow-100 text-yellow-800
                                            @elseif($schedule->shift && $schedule->shift->category == 'Siang') bg-orange-100 text-orange-800
                                            @elseif($schedule->shift && $schedule->shift->category == 'Malam') bg-indigo-100 text-indigo-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock mr-1">
                                                <circle cx="12" cy="12" r="10"/>
                                                <polyline points="12 6 12 12 16 14"/>
                                            </svg>
                                            {{ $schedule->shift->shift_name ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="px-8 py-6 whitespace-nowrap text-base font-semibold text-gray-700">
                                        @if($attendance && $attendance->check_in_time)
                                            <span class="inline-flex items-center text-green-700">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check-circle mr-1">
                                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                                    <polyline points="22 4 12 14.01 9 11.01"/>
                                                </svg>
                                                {{ \Carbon\Carbon::parse($attendance->check_in_time)->format('H:i') }}
                                            </span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-8 py-6 whitespace-nowrap text-base font-semibold text-gray-700">
                                        @if($attendance && $attendance->check_out_time)
                                            <span class="inline-flex items-center text-red-700">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x-circle mr-1">
                                                    <circle cx="12" cy="12" r="10"/>
                                                    <path d="M15 9l-6 6"/>
                                                    <path d="M9 9l6 6"/>
                                                </svg>
                                                {{ \Carbon\Carbon::parse($attendance->check_out_time)->format('H:i') }}
                                            </span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-8 py-6 whitespace-nowrap">
                                        @if(($attendance->status ?? 'alpha') === 'hadir')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check-circle mr-1">
                                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                                    <polyline points="22 4 12 14.01 9 11.01"/>
                                                </svg>
                                                Hadir
                                            </span>
                                        @elseif(($attendance->status ?? 'alpha') === 'telat')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-orange-100 text-orange-800">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock-alert mr-1">
                                                    <circle cx="12" cy="12" r="10"/>
                                                    <polyline points="12 6 12 12 16 14"/>
                                                    <path d="M12 2v4"/>
                                                    <path d="M12 18v4"/>
                                                </svg>
                                                Telat
                                                @if($attendance && $attendance->late_minutes)
                                                    <span class="ml-1 text-xs">({{ $attendance->late_minutes }} mnt)</span>
                                                @endif
                                            </span>
                                        @elseif(($attendance->status ?? 'alpha') === 'izin')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock mr-1">
                                                    <circle cx="12" cy="12" r="10"/>
                                                    <polyline points="12 6 12 12 16 14"/>
                                                </svg>
                                                Izin
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x-circle mr-1">
                                                    <circle cx="12" cy="12" r="10"/>
                                                    <path d="M15 9l-6 6"/>
                                                    <path d="M9 9l6 6"/>
                                                </svg>
                                                Alpha
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-8 py-6 text-sm text-gray-900">
                                        <div class="max-w-xs">
                                            @if($permission)
                                                <div class="text-gray-900 font-medium">{{ $permission->reason }}</div>
                                                <div class="text-xs text-gray-500 mt-1">
                                                    Status: 
                                                    @if($permission->status === 'pending')
                                                        <span class="text-amber-600 font-medium">Menunggu Persetujuan</span>
                                                    @elseif($permission->status === 'approved')
                                                        <span class="text-green-600 font-medium">Disetujui</span>
                                                    @elseif($permission->status === 'rejected')
                                                        <span class="text-red-600 font-medium">Ditolak</span>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 whitespace-nowrap text-left">
                                        <div class="flex items-center justify-start space-x-2">
                                            @if($permission && $permission->status == 'pending')
                                                <form action="{{ route('admin.attendances.permission.approve', $permission) }}" method="post" class="inline" onsubmit="return confirm('Yakin ingin menyetujui izin ini?')">
                                                    @csrf
                                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-green-100 hover:bg-green-200 text-green-700 font-semibold text-xs rounded-lg transition-all duration-200 hover:scale-105">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check mr-1">
                                                            <polyline points="20 6 9 17 4 12"/>
                                                        </svg>
                                                        Setujui
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.attendances.permission.reject', $permission) }}" method="post" class="inline" onsubmit="return confirm('Yakin ingin menolak izin ini?')">
                                                    @csrf
                                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-700 font-semibold text-xs rounded-lg transition-all duration-200 hover:scale-105">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x mr-1">
                                                            <path d="M18 6 6 18"/>
                                                            <path d="m6 6 12 12"/>
                                                        </svg>
                                                        Tolak
                                                    </button>
                                                </form>
                                            @elseif($permission)
                                                <div class="text-center">
                                                    @if($permission->status === 'approved')
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 8 8">
                                                                <circle cx="4" cy="4" r="3"/>
                                                            </svg>
                                                            Disetujui
                                                        </span>
                                                    @elseif($permission->status === 'rejected')
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 8 8">
                                                                <circle cx="4" cy="4" r="3"/>
                                                            </svg>
                                                            Ditolak
                                                        </span>
                                                    @endif
                                                    @if($permission->approved_by)
                                                        <div class="text-xs text-gray-500 mt-1">
                                                            oleh {{ $permission->approver->name ?? 'Admin' }}
                                                        </div>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-gray-400 text-sm">-</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-8 py-16 text-center">
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
@endsection