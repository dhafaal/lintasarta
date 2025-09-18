@extends('layouts.admin')

@section('title', 'Riwayat Jadwal')

@section('content')
<div class="min-h-screen bg-white sm:p-6 lg:p-8">
    <div class="mx-auto space-y-8">
        <!-- Enhanced Header Section -->
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-gradient-to-br from-sky-100 to-sky-200 rounded-xl flex items-center justify-center shadow-sm">
                    <i data-lucide="calendar-clock" class="w-6 h-6 text-sky-700"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-700 tracking-tight">Riwayat Jadwal</h1>
                    <p class="text-gray-500 mt-1">Kelola dan lihat riwayat penjadwalan karyawan</p>
                </div>
            </div>
        </div>

        <!-- Enhanced Filter Section -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-sky-500 to-sky-600 px-8 py-6">
                <h2 class="text-xl font-semibold text-white flex items-center">
                    <i data-lucide="filter" class="w-5 h-5 mr-2"></i>
                    Filter Periode
                </h2>
                <p class="text-sky-100 mt-1">Pilih rentang tanggal untuk melihat riwayat jadwal</p>
            </div>
            
            <div class="p-8">
                <form method="GET" action="{{ route('admin.schedules.history', $user->id) }}" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="space-y-3">
                        <label for="start_date" class="block text-sm font-bold text-gray-800">
                            <i data-lucide="calendar" class="w-4 h-4 inline mr-1"></i>
                            Dari Tanggal
                        </label>
                        <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}"
                            class="block w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-sky-100 focus:border-sky-500 transition-all duration-200 bg-gray-50 focus:bg-white">
                    </div>
                    
                    <div class="space-y-3">
                        <label for="end_date" class="block text-sm font-bold text-gray-800">
                            <i data-lucide="calendar" class="w-4 h-4 inline mr-1"></i>
                            Sampai Tanggal
                        </label>
                        <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}"
                            class="block w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-sky-100 focus:border-sky-500 transition-all duration-200 bg-gray-50 focus:bg-white">
                    </div>
                    
                    <div class="flex items-end">
                        <button type="submit" class="w-full inline-flex items-center justify-center px-6 py-3 bg-sky-600 hover:bg-sky-700 text-white font-bold rounded-xl transition-all transform hover:scale-105 focus:outline-none focus:ring-4 focus:ring-sky-200 shadow-sm hover:shadow-md">
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
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar-days mr-2">
                        <path d="M8 2v4"/>
                        <path d="M16 2v4"/>
                        <rect width="18" height="18" x="3" y="4" rx="2"/>
                        <path d="M3 10h18"/>
                        <path d="M8 14h.01"/>
                        <path d="M12 14h.01"/>
                        <path d="M16 14h.01"/>
                        <path d="M8 18h.01"/>
                        <path d="M12 18h.01"/>
                        <path d="M16 18h.01"/>
                    </svg>
                    Data Riwayat Jadwal
                </h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b-2 border-gray-200">
                        <tr>
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
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar text-sky-600 mr-2">
                                        <path d="M8 2v4"/>
                                        <path d="M16 2v4"/>
                                        <rect width="18" height="18" x="3" y="4" rx="2"/>
                                        <path d="M3 10h18"/>
                                    </svg>
                                    Tanggal
                                </div>
                            </th>
                            <th class="px-8 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock text-sky-600 mr-2">
                                        <circle cx="12" cy="12" r="10"/>
                                        <polyline points="12 6 12 12 16 14"/>
                                    </svg>
                                    Jam
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
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($schedules as $schedule)
                            @php
                                $attendance = $attendances->firstWhere('schedule_id', $schedule->id);
                                $permission = $permissions->firstWhere('schedule_id', $schedule->id);
                                $status = $attendance->status ?? ($permission ? 'izin' : 'alpha');
                            @endphp
                            <tr class="hover:bg-sky-50 transition-colors duration-200">
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if ($schedule->shift && $schedule->shift->category == 'Pagi')
                                            <div class="w-10 h-10 bg-gradient-to-br from-yellow-100 to-orange-100 rounded-xl flex items-center justify-center mr-4 shadow-sm">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-sun text-yellow-600">
                                                    <circle cx="12" cy="12" r="4"/>
                                                    <path d="M12 2v2"/>
                                                    <path d="M12 20v2"/>
                                                    <path d="m4.93 4.93 1.41 1.41"/>
                                                    <path d="m17.66 17.66 1.41 1.41"/>
                                                    <path d="M2 12h2"/>
                                                    <path d="M20 12h2"/>
                                                    <path d="m6.34 17.66-1.41 1.41"/>
                                                    <path d="m19.07 4.93-1.41 1.41"/>
                                                </svg>
                                            </div>
                                        @elseif($schedule->shift && $schedule->shift->category == 'Siang')
                                            <div class="w-10 h-10 bg-gradient-to-br from-orange-100 to-red-100 rounded-xl flex items-center justify-center mr-4 shadow-sm">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-sun text-orange-600">
                                                    <circle cx="12" cy="12" r="4"/>
                                                    <path d="M12 2v2"/>
                                                    <path d="M12 20v2"/>
                                                    <path d="m4.93 4.93 1.41 1.41"/>
                                                    <path d="m17.66 17.66 1.41 1.41"/>
                                                    <path d="M2 12h2"/>
                                                    <path d="M20 12h2"/>
                                                    <path d="m6.34 17.66-1.41 1.41"/>
                                                    <path d="m19.07 4.93-1.41 1.41"/>
                                                </svg>
                                            </div>
                                        @elseif($schedule->shift && $schedule->shift->category == 'Malam')
                                            <div class="w-10 h-10 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-xl flex items-center justify-center mr-4 shadow-sm">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-moon text-indigo-600">
                                                    <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9"/>
                                                </svg>
                                            </div>
                                        @else
                                            <div class="w-10 h-10 bg-gradient-to-br from-gray-100 to-gray-200 rounded-xl flex items-center justify-center mr-4 shadow-sm">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-help-circle text-gray-500">
                                                    <circle cx="12" cy="12" r="10"/>
                                                    <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/>
                                                    <path d="M12 17h.01"/>
                                                </svg>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="text-base font-bold text-gray-900">{{ $schedule->shift->shift_name ?? '-' }}</div>
                                            <div class="text-sm text-gray-500">
                                                @if ($schedule->shift)
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
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
                                    </div>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <div class="text-base font-semibold text-gray-900">{{ \Carbon\Carbon::parse($schedule->schedule_date)->format('d M Y') }}</div>
                                    <div class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($schedule->schedule_date)->translatedFormat('l') }}</div>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <div class="flex flex-col space-y-2">
                                        <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-sky-100 text-sky-800">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock mr-1">
                                                <circle cx="12" cy="12" r="10"/>
                                                <polyline points="12 6 12 12 16 14"/>
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
                                        @if($attendance && ($attendance->check_in_time || $attendance->check_out_time))
                                            <div class="text-xs text-gray-500">
                                                @if($attendance->check_in_time)
                                                    <span class="inline-flex items-center">
                                                        <svg class="w-3 h-3 mr-1 text-green-500" fill="currentColor" viewBox="0 0 8 8">
                                                            <circle cx="4" cy="4" r="3"/>
                                                        </svg>
                                                        Masuk: {{ \Carbon\Carbon::parse($attendance->check_in_time)->format('H:i') }}
                                                    </span>
                                                @endif
                                                @if($attendance->check_out_time)
                                                    <span class="inline-flex items-center ml-2">
                                                        <svg class="w-3 h-3 mr-1 text-red-500" fill="currentColor" viewBox="0 0 8 8">
                                                            <circle cx="4" cy="4" r="3"/>
                                                        </svg>
                                                        Keluar: {{ \Carbon\Carbon::parse($attendance->check_out_time)->format('H:i') }}
                                                    </span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <div class="flex flex-col space-y-2">
                                        @if($status === 'hadir')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check-circle mr-1">
                                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                                    <polyline points="22 4 12 14.01 9 11.01"/>
                                                </svg>
                                                Hadir
                                                @if($attendance && $attendance->is_late)
                                                    <span class="ml-1 text-xs">({{ $attendance->late_minutes }} mnt)</span>
                                                @endif
                                            </span>
                                        @elseif($status === 'telat')
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
                                        @elseif($status === 'izin')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-check mr-1">
                                                    <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7z"/>
                                                    <path d="M14 2v4a2 2 0 0 0 2 2h4"/>
                                                    <path d="m9 15 2 2 4-4"/>
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
                                        
                                        @if($permission && $permission->reason)
                                            <div class="text-xs text-gray-600 bg-gray-50 px-2 py-1 rounded max-w-xs truncate" title="{{ $permission->reason }}">
                                                <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 8 8">
                                                    <circle cx="4" cy="4" r="3"/>
                                                </svg>
                                                {{ $permission->reason }}
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-8 py-16 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-20 h-20 bg-gradient-to-br from-sky-100 to-sky-200 rounded-full flex items-center justify-center mb-6">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar text-sky-400">
                                                <path d="M8 2v4"/>
                                                <path d="M16 2v4"/>
                                                <rect width="18" height="18" x="3" y="4" rx="2"/>
                                                <path d="M3 10h18"/>
                                            </svg>
                                        </div>
                                        <h3 class="text-xl font-bold text-gray-900 mb-2">Belum ada riwayat jadwal</h3>
                                        <p class="text-gray-600 mb-6 max-w-sm">Tidak ada riwayat jadwal untuk periode yang dipilih</p>
                                        <a href="{{ route('admin.schedules.create') }}"
                                            class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-sky-500 to-sky-600 hover:from-sky-600 hover:to-sky-700 text-white font-bold rounded-xl transition-all duration-200 transform hover:scale-105 shadow-lg">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-plus mr-2">
                                                <path d="M12 5v14"/>
                                                <path d="M5 12h14"/>
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

        <!-- Enhanced Pagination -->
        @if(method_exists($schedules, 'links'))
            <div class="flex justify-center">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                    {{ $schedules->links() }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
