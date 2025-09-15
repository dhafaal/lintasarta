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
                    <span class="px-2 py-1 text-xs rounded 
                        @if ($attendance->status === 'hadir') bg-green-100 text-green-700 
                        @elseif($attendance->status === 'izin') bg-yellow-100 text-yellow-700 
                        @else bg-red-100 text-red-700 @endif">
                        {{ ucfirst($attendance->status) }}
                    </span><br>
                    <strong>Check In:</strong> {{ $attendance->check_in_time ?? '-' }} <br>
                    <strong>Check Out:</strong> {{ $attendance->check_out_time ?? '-' }}
                </p>
            @else
                <p class="text-gray-600">Status: <span class="text-red-500 font-medium">Belum Absen</span></p>
            @endif
        </div>

        @if (session('debug_distance'))
            <div class="mb-4 p-4 bg-blue-100 text-blue-800 border border-blue-300 rounded">
                Jarak dari kantor: {{ session('debug_distance') }}
            </div>
        @endif

        {{-- Tombol Aksi --}}
        <div class="flex flex-wrap gap-3 mb-8">
            {{-- Belum check in --}}
            @if (!$attendance || !$attendance->check_in_time)
                <form id="checkin-form" action="{{ route('user.attendances.checkin') }}" method="POST">
                    @csrf
                    <input type="hidden" name="schedule_id" value="{{ $schedule->id }}">
                    <input type="hidden" name="latitude" id="latitude">
                    <input type="hidden" name="longitude" id="longitude">
                    <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded shadow">
                        Check In
                    </button>
                </form>
            @endif

            {{-- Sudah check in tapi belum check out --}}
            @if ($attendance && $attendance->check_in_time && !$attendance->check_out_time)
                <form id="checkout-form" action="{{ route('user.attendances.checkout') }}" method="POST">
                    @csrf
                    <input type="hidden" name="schedule_id" value="{{ $schedule->id }}">
                    <input type="hidden" name="latitude" id="checkout-latitude">
                    <input type="hidden" name="longitude" id="checkout-longitude">
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded shadow">
                        Check Out
                    </button>
                </form>
            @endif

            {{-- Tombol Ajukan Izin --}}
            @if (!$attendance || !$attendance->check_in_time)
                <button type="button"
                        onclick="document.getElementById('izin-modal').classList.remove('hidden')"
                        class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded shadow">
                    Ajukan Izin
                </button>
            @endif

            <a href="{{ route('user.attendances.history') }}"
               class="px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white rounded shadow">
               Lihat History
            </a>
        </div>
    @else
        <div class="p-6 bg-white border border-sky-200 rounded-lg shadow text-center mb-8">
            <p class="text-sky-700 mb-4">Anda tidak memiliki jadwal hari ini.</p>
        </div>
    @endif
</div>

{{-- Modal Form Ajukan Izin --}}
<div id="izin-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
        <h2 class="text-lg font-bold mb-4">Ajukan Izin</h2>
        <form action="{{ route('user.permissions.store') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="schedule_id" value="{{ $schedule->id }}">

            <div>
                <label class="block text-sm font-medium mb-1">Tipe Izin</label>
                <select name="type" class="w-full border-gray-300 rounded-lg">
                    <option value="izin">Izin</option>
                    <option value="sakit">Sakit</option>
                    <option value="cuti">Cuti</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Alasan</label>
                <textarea name="reason" class="w-full border-gray-300 rounded-lg" rows="3" placeholder="Tuliskan alasan..."></textarea>
            </div>

            <div class="flex justify-end gap-2">
                <button type="button"
                        onclick="document.getElementById('izin-modal').classList.add('hidden')"
                        class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded-lg">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                    Ajukan
                </button>
            </div>
        </form>

        {{-- Tombol Close di pojok kanan atas --}}
        <button type="button"
                onclick="document.getElementById('izin-modal').classList.add('hidden')"
                class="absolute top-3 right-3 text-gray-500 hover:text-gray-700">
            ✖
        </button>
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
