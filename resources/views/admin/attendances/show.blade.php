@extends('layouts.app')

@section('title', 'Detail Attendance')

@section('content')
    <div class="p-6">
        <h1 class="text-2xl font-bold text-sky-700 mb-4">Absensi: {{ $user->name }}</h1>

        {{-- Flash message --}}
        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-lg">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="mb-4 p-3 bg-red-100 text-red-700 rounded-lg">{{ session('error') }}</div>
        @endif

        {{-- Tabs filter --}}
        <div class="flex space-x-2 mb-6">
            <a href="{{ route('admin.attendances.show', [$user, 'filter' => 'today']) }}"
                class="px-4 py-2 rounded-lg text-sm font-medium transition 
           {{ $filter === 'today' ? 'bg-sky-600 text-white shadow' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                Hari Ini
            </a>
            <a href="{{ route('admin.attendances.show', [$user, 'filter' => 'month']) }}"
                class="px-4 py-2 rounded-lg text-sm font-medium transition
           {{ $filter === 'month' ? 'bg-sky-600 text-white shadow' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                Bulan Ini
            </a>
            <a href="{{ route('admin.attendances.show', [$user, 'filter' => 'all']) }}"
                class="px-4 py-2 rounded-lg text-sm font-medium transition
           {{ $filter === 'all' ? 'bg-sky-600 text-white shadow' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                Semua
            </a>
        </div>

        {{-- Tabel Absensi --}}
        <div class="overflow-x-auto bg-white shadow rounded-lg">
            <table class="min-w-full text-sm border-collapse">
                <thead class="bg-sky-100 text-sky-700 text-xs uppercase">
                    <tr>
                        <th class="px-3 py-2 border">Tanggal</th>
                        <th class="px-3 py-2 border">Shift</th>
                        <th class="px-3 py-2 border">Checkin</th>
                        <th class="px-3 py-2 border">Checkout</th>
                        <th class="px-3 py-2 border">Keterangan</th>
                        <th class="px-3 py-2 border">Status</th>
                        <th class="px-3 py-2 border">Approved By</th>
                        <th class="px-3 py-2 border">Approved At</th>
                        <th class="px-3 py-2 border text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($schedules as $schedule)
                        @php
                            $attendance = $schedule->attendances->first();
                            $permission = $schedule->permissions->first();
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="border px-3 py-2">{{ $schedule->schedule_date }}</td>
                            <td class="border px-3 py-2">{{ $schedule->shift->name ?? '-' }}</td>
                            <td class="border px-3 py-2">{{ $attendance->checkin_time ?? '-' }}</td>
                            <td class="border px-3 py-2">{{ $attendance->checkout_time ?? '-' }}</td>
                            <td class="border px-3 py-2">
                                @if ($permission)
                                    {{ $permission->alasan }}
                                @else
                                    {{ $attendance->keterangan ?? '-' }}
                                @endif
                            </td>
                            <td class="border px-3 py-2">
                                @if ($permission)
                                    <span
                                        class="px-2 py-1 text-xs rounded
                        @if ($permission->status == 'pending') bg-yellow-100 text-yellow-700
                        @elseif($permission->status == 'approved') bg-blue-100 text-blue-700
                        @elseif($permission->status == 'rejected') bg-red-100 text-red-700 @endif">
                                        Izin {{ ucfirst($permission->status) }}
                                    </span>
                                @elseif($attendance)
                                    <span
                                        class="px-2 py-1 text-xs rounded
                        @if ($attendance->status == 'hadir') bg-green-100 text-green-700
                        @elseif($attendance->status == 'alpha') bg-red-100 text-red-700
                        @else bg-gray-100 text-gray-700 @endif">
                                        {{ ucfirst($attendance->status) }}
                                    </span>
                                @else
                                    {{-- Tidak ada izin & tidak ada absensi → Alpha --}}
                                    <span class="px-2 py-1 text-xs bg-red-100 text-red-700 rounded">Alpha</span>
                                @endif
                            </td>
                            <td class="border px-3 py-2">
                                {{ $permission->approver->name ?? ($attendance->approver->name ?? '-') }}
                            </td>
                            <td class="border px-3 py-2">
                                {{ $permission->approved_at ?? ($attendance->approved_at ?? '-') }}
                            </td>
                            <td class="border px-3 py-2 text-center">
                                @if ($permission && $permission->status == 'pending')
                                    <form action="{{ route('admin.permissions.approve', $permission) }}" method="POST"
                                        class="inline">
                                        @csrf
                                        <input type="hidden" name="action" value="approve">
                                        <button type="submit"
                                            class="px-2 py-1 text-xs bg-green-600 text-white rounded hover:bg-green-700">Approve</button>
                                    </form>
                                    <form action="{{ route('admin.permissions.approve', $permission) }}" method="POST"
                                        class="inline">
                                        @csrf
                                        <input type="hidden" name="action" value="reject">
                                        <button type="submit"
                                            class="px-2 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700">Reject</button>
                                    </form>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-6 text-sky-500">Belum ada data absensi</td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $schedules->withQueryString()->links() }}
        </div>
    </div>
@endsection
