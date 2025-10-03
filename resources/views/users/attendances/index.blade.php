@extends('layouts.user')

@section('title', 'Attendance')

@section('content')
<div class="min-h-screen bg-white">
    <div class=" mx-auto px-4 py-8">
        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Attendance Dashboard</h1>
            <p class="text-gray-600">Manage your daily attendance and schedule</p>
        </div>

        {{-- Notifications --}}
        @if (session('success'))
            <div class="mb-6 p-4 bg-gradient-to-r from-sky-50 to-sky-100 border border-sky-200 rounded-xl">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-sky-500 rounded-full flex items-center justify-center">
                        <i data-lucide="check" class="w-4 h-4 text-white"></i>
                    </div>
                    <p class="text-sky-800 font-medium">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if (session('warning'))
            <div class="mb-6 p-4 bg-gradient-to-r from-amber-50 to-amber-100 border border-amber-200 rounded-xl">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-amber-500 rounded-full flex items-center justify-center">
                        <i data-lucide="alert-triangle" class="w-4 h-4 text-white"></i>
                    </div>
                    <p class="text-amber-800 font-medium">{{ session('warning') }}</p>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 p-4 bg-gradient-to-r from-red-50 to-red-100 border border-red-200 rounded-xl">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center">
                        <i data-lucide="x" class="w-4 h-4 text-white"></i>
                    </div>
                    <p class="text-red-800 font-medium">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        {{-- Main Content --}}
        @if ($schedule)
            {{-- Today's Schedule Card --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-sky-400 to-sky-600 rounded-xl flex items-center justify-center">
                            <i data-lucide="calendar" class="w-6 h-6 text-white"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900">Today's Schedule</h2>
                            <p class="text-sm text-gray-500">{{ \Carbon\Carbon::now()->format('l, d F Y') }}</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div class="bg-sky-50 rounded-xl p-4 border border-sky-100">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-sky-500 rounded-lg flex items-center justify-center">
                                <i data-lucide="clock" class="w-4 h-4 text-white"></i>
                            </div>
                            <div>
                                <p class="text-sm text-sky-600 font-medium">Shift</p>
                                <p class="text-lg font-semibold text-sky-900">{{ $schedule->shift->shift_name }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-sky-50 rounded-xl p-4 border border-sky-100">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-sky-500 rounded-lg flex items-center justify-center">
                                <i data-lucide="clock" class="w-4 h-4 text-white"></i>
                            </div>
                            <div>
                                <p class="text-sm text-sky-600 font-medium">Working Hours</p>
                                <p class="text-lg font-semibold text-sky-900">{{ $schedule->shift->start_time }} - {{ $schedule->shift->end_time }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- Attendance Status --}}
                @if ($attendance)
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-4 border-t border-gray-100">
                        <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-xl p-4 border border-emerald-200">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-emerald-500 rounded-lg flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white">
                                        <path d="M20 6L9 17l-5-5"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-emerald-600 font-medium">Status</p>
                                    <p class="text-lg font-semibold text-emerald-900">{{ ucfirst($attendance->status) }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-sky-50 to-sky-100 rounded-xl p-4 border border-sky-200">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-sky-500 rounded-lg flex items-center justify-center">
                                    <i data-lucide="sun" class="w-4 h-4 text-white"></i>
                                </div>
                                <div>
                                    <p class="text-sm text-sky-600 font-medium">Check In</p>
                                    <p class="text-lg font-semibold text-sky-900">{{ $attendance->check_in_time ? \Carbon\Carbon::parse($attendance->check_in_time)->format('H:i') : '-' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl p-4 border border-orange-200">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-orange-500 rounded-lg flex items-center justify-center">
                                    <i data-lucide="sunset" class="w-4 h-4 text-white"></i>
                                </div>
                                <div>
                                    <p class="text-sm text-orange-600 font-medium">Check Out</p>
                                    <p class="text-lg font-semibold text-orange-900">{{ $attendance->check_out_time ? \Carbon\Carbon::parse($attendance->check_out_time)->format('H:i') : '-' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            @if (session('debug_distance'))
                <div class="mb-6 p-4 bg-gradient-to-r from-sky-50 to-sky-100 border border-sky-200 rounded-xl">
                    <p class="text-sky-800 font-medium">Debug: Distance from office: {{ session('debug_distance') }}</p>
                </div>
            @endif

            {{-- Status Permission Hari Ini --}}
            @if($todayPermission)
                @if($todayPermission->status === 'pending')
                    <div class="mb-6 p-6 bg-gradient-to-r from-amber-50 to-amber-100 border border-amber-200 rounded-2xl">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-amber-500 rounded-xl flex items-center justify-center">
                                <i data-lucide="clock" class="w-5 h-5 text-white"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-amber-900">
                                    {{ $todayPermission->type === 'cuti' ? 'Pengajuan Cuti' : 'Pengajuan Izin' }} Menunggu Persetujuan
                                </h3>
                                <p class="text-sm text-amber-700">Status: <span class="font-medium">Menunggu Persetujuan</span></p>
                                <p class="text-sm text-amber-600 mt-1">Alasan: {{ $todayPermission->reason }}</p>
                            </div>
                        </div>
                    </div>
                @elseif($todayPermission->status === 'approved')
                    @if($todayPermission->type === 'cuti')
                        <div class="mb-6 p-6 bg-gradient-to-r from-purple-50 to-purple-100 border border-purple-200 rounded-2xl">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-purple-500 rounded-xl flex items-center justify-center">
                                    <i data-lucide="calendar-x" class="w-5 h-5 text-white"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-purple-900">Cuti Disetujui</h3>
                                    <p class="text-sm text-purple-700">Anda sedang dalam status <span class="font-medium">Cuti</span></p>
                                    <p class="text-sm text-purple-600 mt-1">Alasan: {{ $todayPermission->reason }}</p>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="mb-6 p-6 bg-gradient-to-r from-green-50 to-green-100 border border-green-200 rounded-2xl">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center">
                                    <i data-lucide="check-circle" class="w-5 h-5 text-white"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-green-900">
                                        {{ ucfirst($todayPermission->type) }} Disetujui
                                    </h3>
                                    <p class="text-sm text-green-700">Anda sedang dalam status <span class="font-medium">{{ ucfirst($todayPermission->type) }}</span></p>
                                    <p class="text-sm text-green-600 mt-1">Alasan: {{ $todayPermission->reason }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif
            @endif

            {{-- Check for rejected permissions to show info --}}
            @php
                $rejectedPermission = \App\Models\Permissions::where('user_id', Auth::id())
                    ->whereHas('schedule', function ($q) {
                        $q->whereDate('schedule_date', now()->toDateString());
                    })
                    ->where('status', 'rejected')
                    ->first();
            @endphp
            
            @if ($rejectedPermission)
                <div class="mb-6 p-6 bg-gradient-to-r from-blue-50 to-blue-100 border border-blue-200 rounded-2xl">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center">
                            <i data-lucide="info" class="w-5 h-5 text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-blue-900">Izin Ditolak</h3>
                            <p class="text-sm text-blue-700">Izin Anda telah ditolak. Silakan lakukan check-in untuk masuk kerja.</p>
                            <p class="text-sm text-blue-600 mt-1">Alasan izin: {{ $rejectedPermission->reason }}</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Action Buttons --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                
                @if(!$todayPermission)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        {{-- Check In Button --}}
                        @if (!$attendance || !$attendance->check_in_time)
                            <form id="checkin-form" action="{{ route('user.attendances.checkin') }}" method="POST">
                                @csrf
                                <input type="hidden" name="schedule_id" value="{{ $schedule?->id }}">
                                <input type="hidden" name="latitude" id="latitude">
                                <input type="hidden" name="longitude" id="longitude">
                                <button type="submit" class="w-full bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white font-medium py-3 px-4 rounded-xl transition-all duration-200 transform hover:scale-105 shadow-sm hover:shadow-md flex items-center justify-center space-x-2">
                                    <i data-lucide="log-in" class="w-4 h-4"></i>
                                    <span>Check In</span>
                                </button>
                            </form>
                        @endif

                        {{-- Check Out Button --}}
                        @if ($attendance && $attendance->check_in_time && !$attendance->check_out_time)
                            <form id="checkout-form" action="{{ route('user.attendances.checkout') }}" method="POST">
                                @csrf
                                <input type="hidden" name="schedule_id" value="{{ $schedule?->id }}">
                                <input type="hidden" name="latitude" id="checkout-latitude">
                                <input type="hidden" name="longitude" id="checkout-longitude">
                                <button type="submit" class="w-full bg-gradient-to-r from-sky-500 to-sky-600 hover:from-sky-600 hover:to-sky-700 text-white font-medium py-3 px-4 rounded-xl transition-all duration-200 transform hover:scale-105 shadow-sm hover:shadow-md flex items-center justify-center space-x-2">
                                    <i data-lucide="log-out" class="w-4 h-4"></i>
                                    <span>Check Out</span>
                                </button>
                            </form>
                        @endif

                        {{-- Request Permission Button --}}
                        @if (!$attendance || !$attendance->check_in_time)
                            <button type="button"
                                    onclick="document.getElementById('izin-modal').classList.remove('hidden')"
                                    class="w-full bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white font-medium py-3 px-4 rounded-xl transition-all duration-200 transform hover:scale-105 shadow-sm hover:shadow-md flex items-center justify-center space-x-2">
                                <i data-lucide="file-text" class="w-4 h-4"></i>
                                <span>Ajukan Izin</span>
                            </button>
                        @endif

                        {{-- Request Leave Button --}}
                        <button type="button"
                                onclick="document.getElementById('cuti-modal').classList.remove('hidden'); loadUserSchedules()"
                                class="w-full bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white font-medium py-3 px-4 rounded-xl transition-all duration-200 transform hover:scale-105 shadow-sm hover:shadow-md flex items-center justify-center space-x-2">
                            <i data-lucide="calendar-x" class="w-4 h-4"></i>
                            <span>Ajukan Cuti</span>
                        </button>


                        {{-- View History Button --}}
                        <a href="{{ route('user.attendances.history') }}"
                           class="w-full bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white font-medium py-3 px-4 rounded-xl transition-all duration-200 transform hover:scale-105 shadow-sm hover:shadow-md flex items-center justify-center space-x-2">
                            <i data-lucide="history" class="w-4 h-4"></i>
                            <span>Lihat Riwayat</span>
                        </a>
                    </div>
                @else
                    {{-- Pesan saat aksi dinonaktifkan --}}
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 border border-gray-200 rounded-xl p-6 text-center">
                        @if($todayPermission->status === 'pending')
                            <div class="w-16 h-16 bg-amber-300 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i data-lucide="clock" class="w-6 h-6 text-amber-700"></i>
                            </div>
                            <h4 class="text-lg font-semibold text-gray-900 mb-2">Menunggu Persetujuan</h4>
                            <p class="text-gray-600">
                                Check-in dan check-out dinonaktifkan karena {{ $todayPermission->type === 'cuti' ? 'pengajuan cuti' : 'pengajuan izin' }} Anda sedang menunggu persetujuan.
                            </p>
                        @elseif($todayPermission->status === 'approved')
                            @if($todayPermission->type === 'cuti')
                                <div class="w-16 h-16 bg-purple-300 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i data-lucide="calendar-x" class="w-6 h-6 text-purple-700"></i>
                                </div>
                                <h4 class="text-lg font-semibold text-gray-900 mb-2">Sedang Cuti</h4>
                                <p class="text-gray-600">
                                    Anda sedang dalam status cuti. Check-in dan check-out tidak diperlukan.
                                </p>
                            @else
                                <div class="w-16 h-16 bg-green-300 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i data-lucide="check-circle" class="w-6 h-6 text-green-700"></i>
                                </div>
                                <h4 class="text-lg font-semibold text-gray-900 mb-2">Sedang {{ ucfirst($todayPermission->type) }}</h4>
                                <p class="text-gray-600">
                                    Anda sedang dalam status {{ $todayPermission->type }}. Check-in dan check-out tidak diperlukan.
                                </p>
                            @endif
                        @endif
                        
                        <div class="mt-4">
                            <a href="{{ route('user.attendances.history') }}"
                               class="inline-flex items-center space-x-2 bg-gradient-to-r from-sky-500 to-sky-600 hover:from-sky-600 hover:to-sky-700 text-white font-medium py-2 px-4 rounded-lg transition-all duration-200">
                                <i data-lucide="history" class="w-4 h-4"></i>
                                <span>Lihat Riwayat</span>
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        @else
            {{-- No Schedule State --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8 text-center">
                <div class="w-20 h-20 bg-gradient-to-br from-sky-100 to-sky-200 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i data-lucide="calendar" class="w-8 h-8 text-sky-600"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No Schedule Today</h3>
                <p class="text-gray-600 mb-6">You don't have any scheduled shifts for today. Please contact your administrator for schedule information.</p>
                
                <a href="{{ route('user.attendances.history') }}"
                   class="inline-flex items-center space-x-2 bg-gradient-to-r from-sky-500 to-sky-600 hover:from-sky-600 hover:to-sky-700 text-white font-medium py-3 px-6 rounded-xl transition-all duration-200 transform hover:scale-105 shadow-sm hover:shadow-md">
                    <i data-lucide="history" class="w-4 h-4"></i>
                    <span>View History</span>
                </a>
            </div>
        @endif
    </div>
</div>
</div>

{{-- Modal Form Request Permission --}}
<div id="izin-modal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg relative transform transition-all border border-gray-100">
        <!-- Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-100 bg-gradient-to-r from-sky-50 to-sky-100 rounded-t-2xl">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-gradient-to-br from-sky-500 to-sky-600 rounded-xl flex items-center justify-center shadow-sm">
                    <i data-lucide="file-text" class="w-5 h-5 text-white"></i>
                </div>
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">Pengajuan Izin</h2>
                    <p class="text-sm text-sky-600">Ajukan permohonan izin Anda</p>
                </div>
            </div>
            <button type="button"
                    onclick="document.getElementById('izin-modal').classList.add('hidden')"
                    class="w-10 h-10 flex items-center justify-center rounded-xl text-gray-400 hover:text-gray-600 hover:bg-white/50 transition-all duration-200">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <!-- Form -->
        <form action="{{ route('user.permissions.store') }}" method="POST" class="p-6 space-y-6">
            @csrf
            <input type="hidden" name="schedule_id" value="{{ $schedule?->id }}">
            <input type="hidden" name="type" value="izin">

            <!-- Schedule Info -->
            @if($schedule)
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-sky-500 to-sky-600 rounded-2xl shadow-lg mb-4">
                        <i data-lucide="clock" class="w-7 h-7 text-white"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($schedule->schedule_date)->format('l, d F Y') }}</p>
                        <p class="text-xs text-gray-500">{{ $schedule->shift->shift_name ?? 'No Shift' }} • {{ $schedule->shift->start_time ?? '' }} - {{ $schedule->shift->end_time ?? '' }}</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Alasan Izin -->
            <div class="space-y-2">
                <label class="block text-sm font-semibold text-gray-900">
                    Alasan Izin
                    <span class="text-red-500">*</span>
                </label>
                <textarea name="reason" 
                          class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm placeholder-gray-400 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-colors resize-none" 
                          rows="4" 
                          placeholder="Tuliskan alasan izin Anda secara jelas..."
                          required></textarea>
                <p class="text-xs text-gray-500">Minimal 10 karakter</p>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100">
                <button type="button"
                        onclick="document.getElementById('izin-modal').classList.add('hidden')"
                        class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-gray-300">
                    Batal
                </button>
                <button type="submit" 
                        class="px-6 py-2.5 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 shadow-sm">
                    <span class="flex items-center space-x-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 6L9 17l-5-5"/>
                        </svg>
                        <span>Kirim Permohonan</span>
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Form Request Leave (Cuti) --}}
<div id="cuti-modal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl relative transform transition-all border border-gray-100 max-h-[90vh] overflow-y-auto">
        <!-- Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-100 bg-gradient-to-r from-purple-50 to-purple-100 rounded-t-2xl sticky top-0">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center shadow-sm">
                    <i data-lucide="calendar-x" class="w-5 h-5 text-white"></i>
                </div>
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">Pengajuan Cuti</h2>
                    <p class="text-sm text-purple-600">Pilih jadwal yang ingin dicuti</p>
                </div>
            </div>
            <button type="button"
                    onclick="document.getElementById('cuti-modal').classList.add('hidden')"
                    class="w-10 h-10 flex items-center justify-center rounded-xl text-gray-400 hover:text-gray-600 hover:bg-white/50 transition-all duration-200">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <!-- Form -->
        <form action="{{ route('user.permissions.store-leave') }}" method="POST" class="p-6 space-y-6">
            @csrf
            <input type="hidden" name="type" value="cuti">

            <!-- Schedule Selection -->
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <label class="block text-sm font-semibold text-gray-900">
                        Pilih Jadwal untuk Cuti
                        <span class="text-red-500">*</span>
                    </label>
                    <div class="flex space-x-2">
                        <button type="button" onclick="selectAllSchedules()" class="text-xs px-3 py-1 bg-purple-100 text-purple-700 rounded-lg hover:bg-purple-200 transition-colors">
                            Pilih Semua
                        </button>
                        <button type="button" onclick="clearAllSchedules()" class="text-xs px-3 py-1 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                            Batal Semua
                        </button>
                    </div>
                </div>
                
                <div id="schedules-loading" class="text-center py-8">
                    <div class="inline-flex items-center space-x-2 text-gray-500">
                        <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-purple-600"></div>
                        <span class="text-sm">Memuat jadwal...</span>
                    </div>
                </div>

                <div id="schedules-container" class="hidden space-y-3 max-h-60 overflow-y-auto border border-gray-200 rounded-lg p-4">
                    <!-- Schedules will be loaded here -->
                </div>
            </div>

            <!-- Alasan Cuti -->
            <div class="space-y-2">
                <label class="block text-sm font-semibold text-gray-900">
                    Alasan Cuti
                    <span class="text-red-500">*</span>
                </label>
                <textarea name="reason" 
                          class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm placeholder-gray-400 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors resize-none" 
                          rows="4" 
                          placeholder="Tuliskan alasan cuti Anda secara jelas..."
                          required></textarea>
                <p class="text-xs text-gray-500">Minimal 10 karakter</p>
            </div>

            <!-- Selected Schedules Summary -->
            <div id="selected-summary" class="hidden bg-purple-50 rounded-lg p-4 border border-purple-200">
                <h4 class="text-sm font-semibold text-purple-900 mb-2">Jadwal yang Dipilih:</h4>
                <div id="selected-list" class="text-sm text-purple-700"></div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100">
                <button type="button"
                        onclick="document.getElementById('cuti-modal').classList.add('hidden')"
                        class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-gray-300">
                    Batal
                </button>
                <button type="submit" 
                        class="px-6 py-2.5 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 shadow-sm">
                    <span class="flex items-center space-x-2">
                        <i data-lucide="send" class="w-4 h-4"></i>
                        <span>Kirim Permohonan</span>
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const geoOptions = {
        enableHighAccuracy: true,
        timeout: 10000,
        maximumAge: 60000
    };

    function handleLocationAndSubmit(form, latId, lngId) {
        const button = form.querySelector('button[type="submit"]');
        const originalText = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '📍 Mengambil lokasi...';

        if (!navigator.geolocation) {
            alert('Browser tidak mendukung geolocation');
            button.disabled = false;
            button.innerHTML = originalText;
            return;
        }

        navigator.geolocation.getCurrentPosition((pos) => {
            document.getElementById(latId).value = pos.coords.latitude;
            document.getElementById(lngId).value = pos.coords.longitude;
            form.submit();
        }, (err) => {
            alert('Gagal mengambil lokasi: ' + err.message);
            button.disabled = false;
            button.innerHTML = originalText;
        }, geoOptions);
    }

    document.getElementById('checkin-form')?.addEventListener('submit', function(e) {
        e.preventDefault();
        handleLocationAndSubmit(this, 'latitude', 'longitude');
    });

    document.getElementById('checkout-form')?.addEventListener('submit', function(e) {
        e.preventDefault();
        handleLocationAndSubmit(this, 'checkout-latitude', 'checkout-longitude');
    });

    // Leave Modal Functions
    let userSchedules = [];
    let selectedSchedules = [];


    async function loadUserSchedules() {
        const loadingDiv = document.getElementById('schedules-loading');
        const containerDiv = document.getElementById('schedules-container');
        
        try {
            loadingDiv.classList.remove('hidden');
            containerDiv.classList.add('hidden');

            console.log('Loading schedules from:', '{{ route("user.schedules.upcoming") }}');
            
            const response = await fetch('{{ route("user.schedules.upcoming") }}', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json'
                }
            });

            console.log('Response status:', response.status);
            
            if (!response.ok) {
                const errorText = await response.text();
                console.error('Response error:', errorText);
                throw new Error(`HTTP ${response.status}: ${errorText}`);
            }

            const data = await response.json();
            console.log('Received data:', data);
            
            userSchedules = data.schedules || [];
            console.log('User schedules:', userSchedules);
            
            renderSchedules();

        } catch (error) {
            console.error('Error loading schedules:', error);
            containerDiv.innerHTML = `<div class="text-center text-red-500 py-4">Gagal memuat jadwal: ${error.message}</div>`;
        } finally {
            loadingDiv.classList.add('hidden');
            containerDiv.classList.remove('hidden');
        }
    }

    function renderSchedules() {
        const container = document.getElementById('schedules-container');
        
        console.log('Rendering schedules:', userSchedules);
        
        if (userSchedules.length === 0) {
            container.innerHTML = '<div class="text-center text-gray-500 py-4">Tidak ada jadwal yang tersedia untuk cuti.</div>';
            return;
        }

        const schedulesHtml = userSchedules.map(schedule => {
            console.log('Processing schedule:', schedule);
            
            const date = new Date(schedule.schedule_date);
            const formattedDate = date.toLocaleDateString('id-ID', { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            });

            // Handle missing shift data
            const shiftName = schedule.shift ? schedule.shift.shift_name : 'No Shift';
            const startTime = schedule.shift ? schedule.shift.start_time : '--:--';
            const endTime = schedule.shift ? schedule.shift.end_time : '--:--';

            return `
                <div class="flex items-center space-x-3 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <input type="checkbox" 
                           name="schedule_ids[]" 
                           value="${schedule.id}" 
                           id="schedule_${schedule.id}"
                           onchange="updateSelectedSchedules()"
                           class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                    <label for="schedule_${schedule.id}" class="flex-1 cursor-pointer">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-purple-100 to-purple-200 rounded-lg flex items-center justify-center">
                                <i data-lucide="calendar" class="w-4 h-4 text-purple-600"></i>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-900">${formattedDate}</div>
                                <div class="text-xs text-gray-500">${shiftName} • ${startTime} - ${endTime}</div>
                            </div>
                        </div>
                    </label>
                </div>
            `;
        }).join('');

        container.innerHTML = schedulesHtml;
        
        // Re-initialize Lucide icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }

    function selectAllSchedules() {
        const checkboxes = document.querySelectorAll('input[name="schedule_ids[]"]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = true;
        });
        updateSelectedSchedules();
    }

    function clearAllSchedules() {
        const checkboxes = document.querySelectorAll('input[name="schedule_ids[]"]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
        updateSelectedSchedules();
    }

    function updateSelectedSchedules() {
        const checkboxes = document.querySelectorAll('input[name="schedule_ids[]"]:checked');
        const summaryDiv = document.getElementById('selected-summary');
        const listDiv = document.getElementById('selected-list');
        
        selectedSchedules = Array.from(checkboxes).map(cb => {
            const scheduleId = parseInt(cb.value);
            return userSchedules.find(s => s.id === scheduleId);
        }).filter(Boolean);

        if (selectedSchedules.length > 0) {
            summaryDiv.classList.remove('hidden');
            const summaryText = selectedSchedules.map(schedule => {
                const date = new Date(schedule.schedule_date);
                const formattedDate = date.toLocaleDateString('id-ID', { 
                    weekday: 'short', 
                    day: 'numeric', 
                    month: 'short' 
                });
                return `${formattedDate} (${schedule.shift.shift_name})`;
            }).join(', ');
            listDiv.textContent = summaryText;
        } else {
            summaryDiv.classList.add('hidden');
        }
    }
</script>
@endsection
