@extends('layouts.admin')

@section('title', 'Riwayat Absensi')

@section('content')
<div class="min-h-screen bg-white sm:p-6 lg:p-8">
    <div class="mx-auto space-y-8">
        <!-- Enhanced Header Section -->
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-gradient-to-br from-sky-100 to-sky-200 rounded-xl flex items-center justify-center shadow-sm">
                    <i data-lucide="clock" class="w-6 h-6 text-sky-700"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-700 tracking-tight">Riwayat Absensi</h1>
                    <p class="text-gray-500 mt-1">{{ \Carbon\Carbon::parse($date)->format('d F Y') }}</p>
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
                <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="space-y-3">
                        <label for="date" class="block text-sm font-bold text-gray-800">
                            <i data-lucide="calendar" class="w-4 h-4 inline mr-1"></i>
                            Tanggal
                        </label>
                        <input type="date" name="date" id="date" value="{{ $date }}" 
                            class="block w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-sky-100 focus:border-sky-500 transition-all duration-200 bg-gray-50 focus:bg-white">
                    </div>
                    
                    <div class="space-y-3">
                        <label for="search" class="block text-sm font-bold text-gray-800">
                            <i data-lucide="search" class="w-4 h-4 inline mr-1"></i>
                            Cari Nama
                        </label>
                        <input type="text" name="search" id="search" value="{{ $search }}" placeholder="Masukkan nama karyawan..."
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
                    <i data-lucide="users" class="w-5 h-5 mr-2"></i>
                    Data Absensi Karyawan
                </h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <i data-lucide="user" class="w-4 h-4 inline mr-1"></i>
                                Nama
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <i data-lucide="clock" class="w-4 h-4 inline mr-1"></i>
                                Shift
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <i data-lucide="calendar" class="w-4 h-4 inline mr-1"></i>
                                Tanggal
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <i data-lucide="log-in" class="w-4 h-4 inline mr-1"></i>
                                Check In
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <i data-lucide="log-out" class="w-4 h-4 inline mr-1"></i>
                                Check Out
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <i data-lucide="activity" class="w-4 h-4 inline mr-1"></i>
                                Status
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <i data-lucide="message-circle" class="w-4 h-4 inline mr-1"></i>
                                Keterangan
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($schedules as $schedule)
                            @php
                                $attendance = $attendances->firstWhere('schedule_id', $schedule->id);
                                $permission = $permissions->firstWhere('schedule_id', $schedule->id);
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gradient-to-br from-sky-100 to-sky-200 rounded-full flex items-center justify-center mr-3">
                                            <i data-lucide="user" class="w-5 h-5 text-sky-600"></i>
                                        </div>
                                        <div class="text-sm font-medium text-gray-900">{{ $schedule->user->name }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <i data-lucide="clock" class="w-3 h-3 mr-1"></i>
                                        {{ $schedule->shift->shift_name ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($schedule->schedule_date)->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($attendance?->check_in_time)
                                        <span class="inline-flex items-center text-green-700">
                                            <i data-lucide="check-circle" class="w-4 h-4 mr-1"></i>
                                            {{ \Carbon\Carbon::parse($attendance->check_in_time)->format('H:i') }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($attendance?->check_out_time)
                                        <span class="inline-flex items-center text-red-700">
                                            <i data-lucide="x-circle" class="w-4 h-4 mr-1"></i>
                                            {{ \Carbon\Carbon::parse($attendance->check_out_time)->format('H:i') }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($attendance?->status === 'hadir')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i data-lucide="check-circle" class="w-3 h-3 mr-1"></i>
                                            Hadir
                                        </span>
                                    @elseif($attendance?->status === 'telat')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                            <i data-lucide="clock-alert" class="w-3 h-3 mr-1"></i>
                                            Telat
                                            @if($attendance && $attendance->late_minutes)
                                                <span class="ml-1 text-xs">({{ $attendance->late_minutes }} mnt)</span>
                                            @endif
                                        </span>
                                    @elseif($attendance?->status === 'izin')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <i data-lucide="clock" class="w-3 h-3 mr-1"></i>
                                            Izin
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <i data-lucide="x-circle" class="w-3 h-3 mr-1"></i>
                                            Alpha
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <div class="max-w-xs truncate">
                                        {{ $permission?->reason ?? '-' }}
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <i data-lucide="search-x" class="w-12 h-12 text-gray-400 mb-4"></i>
                                        <h3 class="text-lg font-medium text-gray-900 mb-1">Tidak ada data</h3>
                                        <p class="text-gray-500">Tidak ada riwayat absensi untuk tanggal yang dipilih.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination if needed -->
        @if(method_exists($schedules, 'links'))
            <div class="flex justify-center">
                {{ $schedules->links() }}
            </div>
        @endif
    </div>
</div>

<script>
    // Initialize Lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
</script>
@endsection
