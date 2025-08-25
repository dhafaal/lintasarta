@extends('layouts.app')

@section('title', 'Daftar Jadwal')

@section('content')
<div class="min-h-screen bg-white sm:p-6 lg:p-8">
    <div class="max-w-7xl mx-auto space-y-8">
        <!-- Updated background to match shifts template gradient -->
        <!-- Enhanced Header Section matching shifts design -->
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-gradient-to-br from-sky-100 to-sky-200 rounded-xl flex items-center justify-center shadow-sm">
                    <svg class="w-6 h-6 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Manajemen Jadwal</h1>
                    <p class="text-gray-600 mt-1">Kelola semua jadwal kerja karyawan</p>
                </div>
            </div>

            <a href="{{ route('admin.schedules.create') }}" 
               class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-sky-500 to-sky-600 hover:from-sky-600 hover:to-sky-700 text-white font-bold rounded-xl transition-all duration-200 transform hover:scale-105 focus:outline-none focus:ring-4 focus:ring-sky-200 shadow-lg hover:shadow-xl">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Tambah Jadwal Baru
            </a>
        </div>

        <!-- Enhanced Stats Cards for schedules -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-gradient-to-br from-sky-500 to-sky-600 rounded-2xl p-6 text-white shadow-xl">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sky-100 text-sm font-medium uppercase tracking-wide">Total Jadwal</p>
                        <p class="text-3xl font-bold mt-2">{{ $schedules->count() }}</p>
                        <p class="text-sky-200 text-xs mt-1">Jadwal aktif</p>
                    </div>
                    <div class="w-14 h-14 bg-sky-400 bg-opacity-30 rounded-xl flex items-center justify-center">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white border-2 border-sky-100 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sky-600 text-sm font-bold uppercase tracking-wide">Hari Ini</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $schedules->where('schedule_date', today()->format('Y-m-d'))->count() }}</p>
                        <p class="text-gray-500 text-xs mt-1">{{ today()->format('d M Y') }}</p>
                    </div>
                    <div class="w-14 h-14 bg-gradient-to-br from-green-100 to-emerald-100 rounded-xl flex items-center justify-center">
                        <svg class="w-7 h-7 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white border-2 border-sky-100 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sky-600 text-sm font-bold uppercase tracking-wide">Minggu Ini</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $schedules->whereBetween('schedule_date', [now()->startOfWeek(), now()->endOfWeek()])->count() }}</p>
                        <p class="text-gray-500 text-xs mt-1">{{ now()->startOfWeek()->format('d M') }} - {{ now()->endOfWeek()->format('d M') }}</p>
                    </div>
                    <div class="w-14 h-14 bg-gradient-to-br from-blue-100 to-sky-100 rounded-xl flex items-center justify-center">
                        <svg class="w-7 h-7 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white border-2 border-sky-100 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sky-600 text-sm font-bold uppercase tracking-wide">Karyawan Aktif</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $schedules->pluck('user_id')->unique()->count() }}</p>
                        <p class="text-gray-500 text-xs mt-1">Memiliki jadwal</p>
                    </div>
                    <div class="w-14 h-14 bg-gradient-to-br from-purple-100 to-indigo-100 rounded-xl flex items-center justify-center">
                        <svg class="w-7 h-7 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Table Card with professional styling matching shifts template -->
        <div class="bg-white rounded-2xl border-2 border-sky-100 overflow-hidden shadow-xl">
            <div class="px-8 py-6 border-b border-sky-100 bg-gradient-to-r from-sky-50 to-blue-50">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">Daftar Jadwal</h2>
                        <p class="text-gray-600 mt-1">Semua jadwal kerja karyawan</p>
                    </div>
                    <!-- Enhanced search and filter options -->
                    <div class="flex items-center space-x-3">
                        <form method="GET" action="{{ route('admin.schedules.index') }}" class="flex items-center space-x-3">
                            <div class="relative">
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari jadwal..." 
                                       class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm">
                                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            
                            <select name="shift_filter" onchange="this.form.submit()" 
                                    class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm">
                                <option value="">Semua Shift</option>
                                <option value="Pagi" {{ request('shift_filter') == 'Pagi' ? 'selected' : '' }}>Pagi</option>
                                <option value="Siang" {{ request('shift_filter') == 'Siang' ? 'selected' : '' }}>Siang</option>
                                <option value="Malam" {{ request('shift_filter') == 'Malam' ? 'selected' : '' }}>Malam</option>
                            </select>

                            <input type="date" name="date_filter" value="{{ request('date_filter') }}" onchange="this.form.submit()" 
                                   class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm">

                            @if(request('search') || request('shift_filter') || request('date_filter'))
                                <a href="{{ route('admin.schedules.index') }}" 
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
                                Karyawan
                            </th>
                            <th class="px-8 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Shift
                            </th>
                            <th class="px-8 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Tanggal
                            </th>
                            <th class="px-8 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Jam Kerja
                            </th>
                            <th class="px-8 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($schedules as $schedule)
                            <tr class="hover:bg-sky-50 transition-colors duration-200 group">
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gradient-to-br from-sky-100 to-sky-200 rounded-xl flex items-center justify-center mr-4 group-hover:from-sky-200 group-hover:to-sky-300 transition-colors">
                                            <span class="text-sky-600 font-bold text-sm">{{ substr($schedule->user->name, 0, 1) }}</span>
                                        </div>
                                        <div>
                                            <div class="text-base font-bold text-gray-900">{{ $schedule->user->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $schedule->user->role ?? 'Karyawan' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if($schedule->shift->name == 'Pagi')
                                            <div class="w-8 h-8 bg-gradient-to-br from-orange-100 to-yellow-100 rounded-lg flex items-center justify-center mr-3">
                                                <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                                </svg>
                                            </div>
                                        @elseif($schedule->shift->name == 'Siang')
                                            <div class="w-8 h-8 bg-gradient-to-br from-sky-100 to-blue-100 rounded-lg flex items-center justify-center mr-3">
                                                <svg class="w-4 h-4 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                                </svg>
                                            </div>
                                        @else
                                            <div class="w-8 h-8 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-lg flex items-center justify-center mr-3">
                                                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                                                </svg>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="text-base font-bold text-gray-900">{{ $schedule->shift->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $schedule->shift->start_time }} - {{ $schedule->shift->end_time }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <div class="text-base font-semibold text-gray-900">{{ \Carbon\Carbon::parse($schedule->schedule_date)->format('d M Y') }}</div>
                                    <div class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($schedule->schedule_date)->format('l') }}</div>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-sky-100 text-sky-800">
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
                                <td class="px-8 py-6 whitespace-nowrap text-right">
                                    <div class="flex items-center justify-end space-x-3">
                                        <a href="{{ route('admin.schedules.edit', $schedule->id) }}" 
                                           class="inline-flex items-center px-4 py-2 bg-sky-100 hover:bg-sky-200 text-sky-700 font-semibold text-sm rounded-lg transition-all duration-200 hover:scale-105">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                            Edit
                                        </a>
                                        <form action="{{ route('admin.schedules.destroy', $schedule->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    onclick="return confirm('Yakin ingin menghapus jadwal ini? Tindakan ini tidak dapat dibatalkan.')"
                                                    class="inline-flex items-center px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 font-semibold text-sm rounded-lg transition-all duration-200 hover:scale-105">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <!-- Professional empty state -->
                            <tr>
                                <td colspan="5" class="px-8 py-16 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-20 h-20 bg-gradient-to-br from-sky-100 to-sky-200 rounded-full flex items-center justify-center mb-6">
                                            <svg class="w-10 h-10 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                        <h3 class="text-xl font-bold text-gray-900 mb-2">Belum ada jadwal</h3>
                                        <p class="text-gray-600 mb-6 max-w-sm">Mulai dengan membuat jadwal kerja pertama untuk mengatur waktu kerja karyawan</p>
                                        <a href="{{ route('admin.schedules.create') }}" 
                                           class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-sky-500 to-sky-600 hover:from-sky-600 hover:to-sky-700 text-white font-bold rounded-xl transition-all duration-200 transform hover:scale-105 shadow-lg">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
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

        <!-- Enhanced Work Hours Summary with professional styling matching shifts template -->
        @if($schedules->count() > 0 && isset($workHoursSummary))
        <div class="bg-white rounded-2xl shadow-xl border-2 border-sky-100 overflow-hidden">
            <div class="bg-gradient-to-r from-sky-500 to-sky-600 px-8 py-6">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-sky-400 bg-opacity-30 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-white">Total Jam Kerja per User</h2>
                        <p class="text-sky-100 mt-1">Ringkasan jam kerja karyawan</p>
                    </div>
                </div>
            </div>
            
            <div class="p-8">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b-2 border-gray-200">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        User
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Total Jam Kerja
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        Total Hari Kerja
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($workHoursSummary as $summary)
                                <tr class="hover:bg-sky-50 transition-colors duration-200 group">
                                    <td class="px-6 py-6 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-gradient-to-br from-sky-100 to-sky-200 rounded-xl flex items-center justify-center mr-4 group-hover:from-sky-200 group-hover:to-sky-300 transition-colors">
                                                <span class="text-sky-600 font-bold text-sm">{{ substr($summary['employee_name'], 0, 1) }}</span>
                                            </div>
                                            <div>
                                                <div class="text-base font-bold text-gray-900">{{ $summary['employee_name'] }}</div>
                                                <div class="text-sm text-gray-500">Karyawan</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-6 whitespace-nowrap">
                                        <div class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-sky-500 to-sky-600 text-white font-bold rounded-xl shadow-lg">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            {{ $summary['total_work_hours'] }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-6 whitespace-nowrap">
                                        <div class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-emerald-500 to-green-600 text-white font-bold rounded-xl shadow-lg">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            {{ $summary['total_work_days'] }} hari
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection