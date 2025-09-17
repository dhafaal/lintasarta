@extends('layouts.user')

@section('title', 'Attendance')

@section('content')
<div class="p-6 space-y-4">
    {{-- Notifications --}}
    @if (session('success'))
        <div class="bg-green-50 text-green-700 px-4 py-2 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if (session('warning'))
        <div class="bg-yellow-50 text-yellow-700 px-4 py-2 rounded-lg text-sm">
            {{ session('warning') }}
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-50 text-red-700 px-4 py-2 rounded-lg text-sm">
            {{ session('error') }}
        </div>
    @endif

    {{-- Today's Schedule --}}
    @if ($schedule)
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Today's Schedule</h2>
            
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Shift</span>
                    <span class="font-medium">{{ $schedule->shift->name }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Time</span>
                    <span class="font-medium">{{ $schedule->shift->start_time }} - {{ $schedule->shift->end_time }}</span>
                </div>
                
                @if ($attendance)
                    <div class="pt-2 border-t border-gray-100">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-gray-600">Status</span>
                            <span class="px-2 py-1 text-xs font-medium rounded 
                                @if ($attendance->status === 'hadir') bg-green-100 text-green-700 
                                @elseif($attendance->status === 'izin') bg-yellow-100 text-yellow-700 
                                @elseif($attendance->status === 'sakit') bg-orange-100 text-orange-700 
                                @else bg-red-100 text-red-700 @endif">
                                {{ ucfirst($attendance->status) }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-600">Check In</span>
                            <span>{{ $attendance->check_in_time ? \Carbon\Carbon::parse($attendance->check_in_time)->format('H:i') : '-' }}</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-600">Check Out</span>
                            <span>{{ $attendance->check_out_time ? \Carbon\Carbon::parse($attendance->check_out_time)->format('H:i') : '-' }}</span>
                        </div>
                    </div>
                @else
                    <div class="pt-2 border-t border-gray-100 text-center">
                        <p class="text-sm text-red-600">Not checked in yet</p>
                    </div>
                @endif
            </div>
        </div>

        @if (session('debug_distance'))
            <div class="mb-4 p-4 bg-blue-100 text-blue-800 border border-blue-300 rounded">
                Jarak dari kantor: {{ session('debug_distance') }}
            </div>
        @endif

        {{-- Action Buttons --}}
        <div class="flex flex-wrap gap-3">
            {{-- Check In Button --}}
            @if (!$attendance || !$attendance->check_in_time)
                <form id="checkin-form" action="{{ route('user.attendances.checkin') }}" method="POST">
                    @csrf
                    <input type="hidden" name="schedule_id" value="{{ $schedule?->id }}">
                    <input type="hidden" name="latitude" id="latitude">
                    <input type="hidden" name="longitude" id="longitude">
                    <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
                        Check In
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
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                        Check Out
                    </button>
                </form>
            @endif

            {{-- Request Leave Button --}}
            @if (!$attendance || !$attendance->check_in_time)
                <button type="button"
                        onclick="document.getElementById('izin-modal').classList.remove('hidden')"
                        class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-lg transition-colors">
                    Request Leave
                </button>
            @endif

            {{-- View History Button --}}
            <a href="{{ route('user.attendances.history') }}"
               class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition-colors">
                History
            </a>
        </div>
    @else
        <div class="bg-white rounded-lg border border-gray-200 p-4 text-center">
            <p class="text-gray-600 mb-2">No schedule for today</p>
            <p class="text-sm text-gray-500">Please contact admin for schedule information</p>
        </div>
    @endif
</div>

{{-- Modal Form Request Leave --}}
<div id="izin-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg w-full max-w-md p-4 relative">
        <h2 class="text-lg font-medium mb-4">Request Leave</h2>
        <form action="{{ route('user.permissions.store') }}" method="POST" class="space-y-3">
            @csrf
            <input type="hidden" name="schedule_id" value="{{ $schedule?->id }}">

            <div>
                <label class="block text-sm font-medium mb-1">Type</label>
                <select name="type" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="izin">Leave</option>
                    <option value="sakit">Sick</option>
                    <option value="cuti">Vacation</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Reason</label>
                <textarea name="reason" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" rows="3" placeholder="Enter reason..."></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button"
                        onclick="document.getElementById('izin-modal').classList.add('hidden')"
                        class="px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm rounded-lg">
                    Cancel
                </button>
                <button type="submit" class="px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg">
                    Submit
                </button>
            </div>
        </form>

        <button type="button"
                onclick="document.getElementById('izin-modal').classList.add('hidden')"
                class="absolute top-3 right-3 text-gray-400 hover:text-gray-600">
            ✕
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
