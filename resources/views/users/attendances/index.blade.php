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

            {{-- Status Izin Hari Ini --}}
            @if($todayPermission)
                <div class="mb-6 p-6 bg-gradient-to-r from-amber-50 to-amber-100 border border-amber-200 rounded-2xl">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-amber-500 rounded-xl flex items-center justify-center">
                            <i data-lucide="file-check" class="w-5 h-5 text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-amber-900">Pengajuan Izin Terkirim</h3>
                            <p class="text-sm text-amber-700">Status: <span class="font-medium">{{ ucfirst($todayPermission->status) }}</span></p>
                            <p class="text-sm text-amber-600 mt-1">Alasan: {{ $todayPermission->reason }}</p>
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
                        <div class="w-16 h-16 bg-gray-300 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i data-lucide="x-circle" class="w-6 h-6 text-gray-600"></i>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">Aksi Dinonaktifkan</h4>
                        <p class="text-gray-600">Check-in dan check-out dinonaktifkan karena Anda telah mengajukan izin untuk hari ini.</p>
                        
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
</script>
@endsection
