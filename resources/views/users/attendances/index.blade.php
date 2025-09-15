@extends('layouts.user')

@section('title', 'Attendance')

@section('content')
    <div class="max-w-5xl mx-auto">
        {{-- Notifikasi --}}
        @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-800 border border-green-300 rounded">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-4 p-4 bg-red-100 text-red-800 border border-red-300 rounded">
                {{ session('error') }}
            </div>
        @endif

        {{-- Jadwal Hari Ini --}}
        @if ($schedule)
            <div class="bg-white rounded-lg shadow border border-sky-200 p-6 mb-6">
                <h2 class="text-lg font-semibold text-sky-800 mb-3">Jadwal Anda Hari Ini</h2>
                <p class="text-sky-700 mb-2">
                    <strong>Shift:</strong> {{ $schedule->shift->name }} <br>
                    <strong>Jam:</strong> {{ $schedule->shift->start_time }} - {{ $schedule->shift->end_time }}
                </p>

                @if ($attendance)
                    <p class="text-gray-700 mb-2">
                        <strong>Status:</strong>
                        <span
                            class="px-2 py-1 text-xs rounded 
                            @if ($attendance->status === 'hadir') bg-green-100 text-green-700 
                            @elseif($attendance->status === 'izin') bg-yellow-100 text-yellow-700 
                            @else bg-red-100 text-red-700 @endif">
                            {{ ucfirst($attendance->status) }}
                        </span>
                        <br>
                        <strong>Check In:</strong> {{ $attendance->check_in_time ?? '-' }} <br>
                        <strong>Check Out:</strong> {{ $attendance->check_out_time ?? '-' }}
                    </p>
                @else
                    <p class="text-gray-600">Status: <span class="text-red-500 font-medium">Belum Absen</span></p>
                @endif
            </div>

            {{-- Tombol Aksi --}}
            <div class="flex flex-wrap gap-3 mb-8">
                @php
                    $pendingPermission = $schedule->permissions
                        ->where('user_id', Auth::id())
                        ->where('status', 'pending')
                        ->first();
                @endphp

                {{-- Belum check in --}}
                @if (!$attendance || !$attendance->check_in_time)
                    {{-- Check In --}}
                    <form id="checkin-form" action="{{ route('user.attendances.checkin', $schedule->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="latitude" id="latitude">
                        <input type="hidden" name="longitude" id="longitude">
                        <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded shadow">
                            Check In
                        </button>
                    </form>

                    {{-- Mark as Absent --}}
                    @if (!$attendance || $attendance->status !== 'alpha')
                        <form action="{{ route('user.attendances.absent') }}" method="POST"
                            onsubmit="return confirm('Yakin ingin menandai sebagai absent/alpha?')">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded shadow">
                                Mark as Absent
                            </button>
                        </form>
                    @endif

                    {{-- Ajukan izin atau batalkan --}}
                    @if (!$pendingPermission)
                        <a href="{{ route('user.attendances.permission.create', $schedule->id) }}"
                            class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded shadow">
                            Ajukan Izin
                        </a>
                    @else
                        <form action="{{ route('user.attendances.permission.cancel', $schedule->id) }}" method="POST"
                            onsubmit="return confirm('Batalkan pengajuan izin untuk hari ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded shadow">
                                Batalkan Pengajuan
                            </button>
                        </form>
                    @endif
                @endif

                {{-- Sudah check in tapi belum check out --}}
                @if ($attendance && $attendance->check_in_time && !$attendance->check_out_time)
                    <form id="checkout-form" action="{{ route('user.attendances.checkout', $schedule->id) }}" method="POST"
                        onsubmit="return confirm('Yakin ingin Check Out sekarang?')">
                        @csrf
                        <input type="hidden" name="latitude" id="checkout-latitude">
                        <input type="hidden" name="longitude" id="checkout-longitude">
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded shadow">
                            Check Out
                        </button>
                    </form>
                @endif

                {{-- History --}}
                <a href="{{ route('user.attendances.history') }}"
                    class="px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white rounded shadow">
                    Lihat History
                </a>
            </div>
        @else
            <div class="p-6 bg-white border border-sky-200 rounded-lg shadow text-center mb-8">
                <p class="text-sky-700 mb-4">Anda tidak memiliki jadwal hari ini.</p>
                <a href="{{ route('user.attendances.permission.create') }}"
                    class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded shadow">
                    Ajukan Izin
                </a>
            </div>
        @endif

        {{-- Daftar Jadwal User --}}
        <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-sky-800 mb-4">Daftar Jadwal Anda</h2>
            <div class="overflow-x-auto">
                <table class="w-full border-collapse border border-gray-200 text-sm">
                    <thead class="bg-sky-100 text-sky-800">
                        <tr>
                            <th class="border border-gray-200 px-4 py-2 text-left">Tanggal</th>
                            <th class="border border-gray-200 px-4 py-2 text-left">Shift</th>
                            <th class="border border-gray-200 px-4 py-2 text-left">Jam</th>
                            <th class="border border-gray-200 px-4 py-2 text-left">Status</th>
                            <th class="border border-gray-200 px-4 py-2 text-left">Izin</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($schedules as $sch)
                            @php
                                $att = $sch->attendances->first();
                                $perm = $sch->permissions->where('user_id', Auth::id())->first();
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="border border-gray-200 px-4 py-2">
                                    {{ \Carbon\Carbon::parse($sch->schedule_date)->format('d M Y') }}
                                </td>
                                <td class="border border-gray-200 px-4 py-2">{{ $sch->shift->name }}</td>
                                <td class="border border-gray-200 px-4 py-2">
                                    {{ $sch->shift->start_time }} - {{ $sch->shift->end_time }}
                                </td>
                                <td class="border border-gray-200 px-4 py-2">
                                    <span
                                        class="px-2 py-1 text-xs rounded
                                        @if ($att && $att->status === 'hadir') bg-green-100 text-green-700
                                        @elseif($att && $att->status === 'izin') bg-yellow-100 text-yellow-700
                                        @elseif($att && $att->status === 'alpha') bg-red-100 text-red-700
                                        @else bg-gray-100 text-gray-700 @endif">
                                        {{ $att->status ?? 'Belum Absen' }}
                                    </span>
                                </td>
                                <td class="border border-gray-200 px-4 py-2">
                                    @if ($perm)
                                        <span
                                            class="px-2 py-1 text-xs rounded
                                            @if ($perm->status === 'pending') bg-yellow-100 text-yellow-700
                                            @elseif($perm->status === 'approved') bg-green-100 text-green-700
                                            @else bg-red-100 text-red-700 @endif">
                                            {{ ucfirst($perm->status) }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-gray-500 py-4">Tidak ada jadwal ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

<script>
// Enhanced geolocation options for better accuracy
const geoOptions = {
    enableHighAccuracy: true,
    timeout: 10000,
    maximumAge: 60000
};

// Show loading state
function showLoadingState(button) {
    button.disabled = true;
    button.innerHTML = '<span class="animate-spin inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full mr-2"></span>Mengambil lokasi...';
}

// Reset button state
function resetButtonState(button, originalText) {
    button.disabled = false;
    button.innerHTML = originalText;
}

// Handle geolocation errors with detailed messages
function handleGeolocationError(error, button, originalText) {
    resetButtonState(button, originalText);
    
    let errorMessage = '';
    switch(error.code) {
        case error.PERMISSION_DENIED:
            errorMessage = 'Akses lokasi ditolak. Silakan izinkan akses lokasi di browser Anda.';
            break;
        case error.POSITION_UNAVAILABLE:
            errorMessage = 'Informasi lokasi tidak tersedia. Pastikan GPS aktif.';
            break;
        case error.TIMEOUT:
            errorMessage = 'Timeout mengambil lokasi. Silakan coba lagi.';
            break;
        default:
            errorMessage = 'Terjadi kesalahan saat mengambil lokasi: ' + error.message;
            break;
    }
    alert(errorMessage);
}

// Handle Check In form with enhanced location tracking
document.getElementById('checkin-form')?.addEventListener('submit', function (e) {
    e.preventDefault();
    
    const submitButton = e.target.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;

    if (!navigator.geolocation) {
        alert('Browser Anda tidak mendukung geolocation. Silakan gunakan browser yang lebih baru.');
        return;
    }

    showLoadingState(submitButton);

    navigator.geolocation.getCurrentPosition(
        (position) => {
            document.getElementById('latitude').value = position.coords.latitude;
            document.getElementById('longitude').value = position.coords.longitude;
            
            // Show coordinates for debugging (optional)
            console.log('Check-in Location:', {
                lat: position.coords.latitude,
                lng: position.coords.longitude,
                accuracy: position.coords.accuracy
            });
            
            e.target.submit();
        },
        (error) => {
            handleGeolocationError(error, submitButton, originalText);
        },
        geoOptions
    );
});

// Handle Check Out form with enhanced location tracking
document.getElementById('checkout-form')?.addEventListener('submit', function (e) {
    e.preventDefault();
    
    const submitButton = e.target.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;

    if (!navigator.geolocation) {
        alert('Browser Anda tidak mendukung geolocation. Silakan gunakan browser yang lebih baru.');
        return;
    }

    showLoadingState(submitButton);

    navigator.geolocation.getCurrentPosition(
        (position) => {
            document.getElementById('checkout-latitude').value = position.coords.latitude;
            document.getElementById('checkout-longitude').value = position.coords.longitude;
            
            // Show coordinates for debugging (optional)
            console.log('Check-out Location:', {
                lat: position.coords.latitude,
                lng: position.coords.longitude,
                accuracy: position.coords.accuracy
            });
            
            e.target.submit();
        },
        (error) => {
            handleGeolocationError(error, submitButton, originalText);
        },
        geoOptions
    );
});

// Optional: Show current location on page load for debugging
if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(
        (position) => {
            console.log('Current Location:', {
                lat: position.coords.latitude,
                lng: position.coords.longitude,
                accuracy: position.coords.accuracy + ' meters'
            });
        },
        (error) => {
            console.log('Location error:', error.message);
        },
        geoOptions
    );
}
</script>
