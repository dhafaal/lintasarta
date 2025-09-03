@extends('layouts.user')

@section('title', 'My Attendances')

@section('content')
    <div class="space-y-8">

        {{-- Flash Message --}}
        @if (session('error'))
            <div class="mb-4 p-3 rounded-lg bg-red-100 text-red-700 border border-red-300">
                {{ session('error') }}
            </div>
        @endif
        @if (session('success'))
            <div class="mb-4 p-3 rounded-lg bg-green-100 text-green-700 border border-green-300">
                {{ session('success') }}
            </div>
        @endif

        <!-- Statistik Card -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-green-100 border border-green-300 rounded-lg p-4 shadow">
                <p class="text-sm text-green-700">Total Hadir</p>
                <p class="text-2xl font-bold text-green-800">{{ $totalHadir }}</p>
            </div>
            <div class="bg-yellow-100 border border-yellow-300 rounded-lg p-4 shadow">
                <p class="text-sm text-yellow-700">Total Izin</p>
                <p class="text-2xl font-bold text-yellow-800">{{ $totalIzin }}</p>
            </div>
            <div class="bg-red-100 border border-red-300 rounded-lg p-4 shadow">
                <p class="text-sm text-red-700">Total Alpha</p>
                <p class="text-2xl font-bold text-red-800">{{ $totalAlpha }}</p>
            </div>
            <div class="bg-sky-100 border border-sky-300 rounded-lg p-4 shadow">
                <p class="text-sm text-sky-700">Total Jadwal</p>
                <p class="text-2xl font-bold text-sky-800">{{ $totalSchedules }}</p>
            </div>
        </div>

        <!-- Attendance Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($schedules as $schedule)
                @php
                    $attendance = $schedule->attendances->first();
                    $permission = $schedule->permissions->where('user_id', Auth::id())->first();

                    $today = \Carbon\Carbon::today();
                    $scheduleDate = \Carbon\Carbon::parse($schedule->schedule_date);
                    $isToday = $scheduleDate->isSameDay($today);
                    $isPast = $scheduleDate->isBefore($today);
                    $isFuture = $scheduleDate->isAfter($today);

                    // Status utama
                    if ($permission) {
                        if ($permission->status === 'pending') {
                            $status = 'izin_pending';
                        } elseif ($permission->status === 'approved') {
                            $status = 'izin_approved';
                        } elseif ($permission->status === 'rejected') {
                            $status = 'izin_rejected';
                        }
                    } elseif ($attendance) {
                        $status = $attendance->status;
                    } elseif ($isPast) {
                        $status = 'alpha';
                    } else {
                        $status = 'belum';
                    }

                    // Check-in & Checkout display
                    if ($status === 'hadir' && $attendance) {
                        $checkIn = $attendance->check_in_time
                            ? \Carbon\Carbon::parse($attendance->check_in_time)->format('H:i')
                            : '-';
                        $checkOut = $attendance->check_out_time
                            ? \Carbon\Carbon::parse($attendance->check_out_time)->format('H:i')
                            : '-';
                    } else {
                        $checkIn = '-';
                        $checkOut = '-';
                    }

                    // Shift info
                    $shiftName = $schedule->shift->name ?? 'Shift';
                    $shiftStart = $schedule->shift->start_time ?? null;
                    $shiftEnd = $schedule->shift->end_time ?? null;
                @endphp

                <div class="bg-white border border-sky-200 rounded-xl shadow-lg p-5 flex flex-col justify-between">
                    <div class="flex justify-between items-center mb-3">
                        <div>
                            <p class="text-lg font-semibold text-sky-800">
                                {{ $shiftName }} ({{ $schedule->shift->category ?? '-' }})
                            </p>
                            <p class="text-sm text-sky-600">
                                {{ $scheduleDate->format('d M Y') }}
                            </p>
                            <p class="text-xs text-gray-500">
                                Jam: {{ $shiftStart }} - {{ $shiftEnd }}
                            </p>
                        </div>
                        <div>
                            @if ($status === 'hadir')
                                <span class="px-3 py-1 text-xs rounded-full bg-green-100 text-green-700">Hadir</span>
                            @elseif ($status === 'izin_pending')
                                <span class="px-3 py-1 text-xs rounded-full bg-yellow-100 text-yellow-700">Pending Izin</span>
                            @elseif ($status === 'izin_approved')
                                <span class="px-3 py-1 text-xs rounded-full bg-green-100 text-green-700">Izin Disetujui</span>
                            @elseif ($status === 'izin_rejected')
                                <span class="px-3 py-1 text-xs rounded-full bg-red-100 text-red-700">Izin Ditolak</span>
                            @elseif ($status === 'alpha')
                                <span class="px-3 py-1 text-xs rounded-full bg-red-100 text-red-700">Alpha</span>
                            @else
                                <span class="px-3 py-1 text-xs rounded-full bg-gray-100 text-gray-700">Belum Absen</span>
                            @endif
                        </div>
                    </div>

                    <!-- Check-in & Checkout -->
                    <div class="text-sm text-sky-700 space-y-1">
                        <p>✅ Check-in:
                            <span class="font-medium text-green-700">{{ $checkIn }}</span>
                        </p>
                        <p>⏰ Checkout:
                            <span class="font-medium text-red-700">{{ $checkOut }}</span>
                        </p>
                        {{-- Alasan izin tampil kalau ada --}}
                        @if ($permission)
                            <p>📝 Alasan: 
                                <span class="text-gray-600">{{ $permission->alasan }}</span>
                            </p>
                        @endif
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="mt-4 flex gap-2">
                        @if ($isToday)
                            @if ($status === 'belum')
                                <!-- Belum absen: bisa check-in atau izin -->
                                <form action="{{ route('user.attendance.store', $schedule->id) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="px-3 py-1 text-xs rounded-lg bg-green-600 text-white hover:bg-green-700">
                                        Check-in
                                    </button>
                                </form>

                                <!-- Tombol buka modal izin -->
                                <div x-data="{ open: false }">
                                    <button @click="open = true"
                                        class="px-3 py-1 text-xs rounded-lg bg-yellow-500 text-white hover:bg-yellow-600">
                                        Ajukan Izin
                                    </button>

                                    <!-- Modal izin -->
                                    <div x-show="open" class="fixed inset-0 flex items-center justify-center bg-black/50 z-50"
                                        x-transition>
                                        <div class="bg-white rounded-xl shadow-lg w-96 p-6">
                                            <h2 class="text-lg font-semibold text-gray-800 mb-4">Ajukan Izin</h2>
                                            <form action="{{ route('user.permission.store', $schedule->id) }}" method="POST"
                                                class="space-y-4">
                                                @csrf
                                                <textarea name="alasan" rows="3" required
                                                    class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-yellow-400 focus:outline-none"
                                                    placeholder="Tuliskan alasan izinmu..."></textarea>

                                                <div class="flex justify-end gap-3">
                                                    <button type="button" @click="open = false"
                                                        class="px-4 py-2 rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300">
                                                        Batal
                                                    </button>
                                                    <button type="submit"
                                                        class="px-4 py-2 rounded-lg bg-yellow-500 text-white hover:bg-yellow-600">
                                                        Kirim
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @elseif ($status === 'hadir' && !$attendance->check_out_time)
                                <!-- Checkout Modal -->
                                <div x-data="{ open: false }">
                                    <button @click="open = true"
                                        class="px-3 py-1 text-xs rounded-lg bg-red-600 text-white hover:bg-red-700">
                                        Checkout
                                    </button>

                                    <div x-show="open"
                                        class="fixed inset-0 flex items-center justify-center bg-black/50 z-50"
                                        x-transition>
                                        <div class="bg-white rounded-xl shadow-lg w-96 p-6">
                                            <h2 class="text-lg font-semibold text-gray-800 mb-3">Konfirmasi Checkout</h2>
                                            <p class="text-gray-600 mb-6">
                                                Apakah kamu yakin ingin melakukan checkout sekarang?
                                            </p>

                                            <div class="flex justify-end gap-3">
                                                <button @click="open = false"
                                                    class="px-4 py-2 rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300">
                                                    Batal
                                                </button>

                                                @php
                                                    $now = \Carbon\Carbon::now();
                                                    $shiftEndTime = $shiftEnd ? \Carbon\Carbon::parse($shiftEnd) : null;
                                                    $canCheckout = $shiftEndTime && $now->gte($shiftEndTime);
                                                @endphp

                                                @if ($canCheckout)
                                                    <form action="{{ route('user.attendance.checkout', $schedule->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        <button type="submit"
                                                            class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700">
                                                            Ya, Checkout
                                                        </button>
                                                    </form>
                                                @else
                                                    <button disabled
                                                        class="px-4 py-2 rounded-lg bg-red-300 text-white cursor-not-allowed">
                                                        Belum waktunya checkout
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @elseif($isFuture)
                            <button disabled
                                class="px-3 py-1 text-xs rounded-lg bg-gray-300 text-gray-600 cursor-not-allowed">
                                Belum waktunya absen
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-center text-sky-500 col-span-3">Belum ada jadwal</p>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $schedules->links() }}
        </div>
    </div>
@endsection
