@extends('layouts.user')

@section('title', 'Absensi')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    @if (session('success'))
        <div class="p-4 bg-green-100 text-green-800 border border-green-300 rounded-lg">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="p-4 bg-red-100 text-red-800 border border-red-300 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow border border-sky-200 p-6">
        @if ($schedule)
            <h2 class="text-xl font-semibold text-sky-800 mb-4">Jadwal Anda Hari Ini</h2>
            <p><strong>Shift:</strong> {{ $schedule->shift->name }}</p>
            <p><strong>Jam:</strong> {{ $schedule->shift->start_time }} - {{ $schedule->shift->end_time }}</p>

            <div class="flex flex-wrap gap-3 mt-6">
                @if (!$attendance || !$attendance->check_in_time)
                    <form id="checkin-form" action="{{ route('user.attendances.checkin') }}" method="POST">
                        @csrf
                        <input type="hidden" name="schedule_id" value="{{ $schedule->id }}">
                        <button type="submit"
                            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg shadow">
                            Check In
                        </button>
                    </form>

                    <form action="{{ route('user.attendances.absent') }}" method="POST"
                        onsubmit="return confirm('Yakin ingin menandai Alpha?')">
                        @csrf
                        <input type="hidden" name="schedule_id" value="{{ $schedule->id }}">
                        <button type="submit"
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg shadow">
                            Tandai Alpha
                        </button>
                    </form>
                @endif

                @if ($attendance && $attendance->check_in_time && !$attendance->check_out_time)
                    <form id="checkout-form" action="{{ route('user.attendances.checkout') }}" method="POST"
                        onsubmit="return confirm('Yakin ingin check out sekarang?')">
                        @csrf
                        <input type="hidden" name="schedule_id" value="{{ $schedule->id }}">
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow">
                            Check Out
                        </button>
                    </form>
                @endif
            </div>
        @else
            <p class="text-gray-600">Anda tidak memiliki jadwal hari ini.</p>
        @endif
    </div>
</div>
@endsection
