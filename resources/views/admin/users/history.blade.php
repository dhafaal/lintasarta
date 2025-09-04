@extends('layouts.app')

@section('title', 'History Jadwal - ' . $user->name)

@section('content')
<div class="mx-auto p-6 bg-white rounded-xl shadow">
    <h2 class="text-2xl font-bold mb-4">Riwayat Jadwal - {{ $user->name }}</h2>
    <p class="text-gray-600 mb-6">Menampilkan semua jadwal yang sudah lewat.</p>

    <div class="overflow-x-auto">
        <table class="w-full border border-gray-200 rounded-lg overflow-hidden">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Shift</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jam</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($histories as $schedule)
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ \Carbon\Carbon::parse($schedule->schedule_date)->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ $schedule->shift->name }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ \Carbon\Carbon::parse($schedule->shift->start_time)->format('H:i') }} -
                            {{ \Carbon\Carbon::parse($schedule->shift->end_time)->format('H:i') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-6 py-8 text-center text-gray-500">
                            Belum ada riwayat jadwal.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $histories->links() }}
    </div>

    <div class="mt-6">
        <a href="{{ route('admin.users.index') }}"
           class="px-4 py-2 bg-sky-500 text-white rounded-lg hover:bg-sky-600">
           Kembali
        </a>
    </div>
</div>
@endsection
