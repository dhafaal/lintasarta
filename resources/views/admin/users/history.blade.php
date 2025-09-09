@extends('layouts.app')

@section('title', 'History Jadwal - ' . $user->name)

@section('content')
    <div class="min-h-screen bg-white/90 backdrop-blur-lg">
        <div class="mx-auto px-4 py-6 sm:px-6 lg:px-8">
            <div class="bg-white/90 backdrop-blur-lg border border-sky-200 rounded-xl overflow-hidden">
                <!-- Table Header -->
                <div class="px-6 py-4 border-b border-sky-100 bg-gradient-to-r from-sky-50 to-blue-50">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-semibold text-gray-700 tracking-tight">Riwayat Jadwal - {{ $user->name }}</h2>
                            <p class="text-gray-500 mt-1 text-base">Menampilkan semua jadwal yang sudah lewat.</p>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-sky-50 border-b-2 border-sky-200">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Shift</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Jam</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-sky-100">
                            @forelse ($histories as $schedule)
                                <tr class="hover:bg-sky-50 transition duration-150 ease-in-out">
                                    <td class="px-6 py-4 text-sm font-semibold text-gray-700">
                                        {{ \Carbon\Carbon::parse($schedule->schedule_date)->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-sm font-semibold text-gray-700">
                                        {{ $schedule->shift->name }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($schedule->shift->start_time)->format('H:i') }} -
                                        {{ \Carbon\Carbon::parse($schedule->shift->end_time)->format('H:i') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center">
                                            <div class="w-20 h-20 bg-gradient-to-br from-sky-100 to-sky-200 rounded-full flex items-center justify-center mb-6">
                                                <i data-lucide="calendar" class="w-10 h-10 text-sky-600"></i>
                                            </div>
                                            <h3 class="text-xl font-semibold text-gray-700 mb-2">Belum ada riwayat jadwal</h3>
                                            <p class="text-gray-500 mb-6 max-w-sm">Jadwal untuk {{ $user->name }} belum tersedia.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4">
                    {{ $histories->links() }}
                </div>
            </div>

            <!-- Back Button -->
            <div class="mt-6">
                <a href="{{ route('admin.users.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-sky-500 to-sky-600 hover:from-sky-600 hover:to-sky-700 text-white font-semibold text-sm rounded-xl transition duration-150 ease-in-out hover:scale-105 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2">
                    <i data-lucide="arrow-left" class="w-5 h-5 mr-2"></i>
                    Kembali
                </a>
            </div>
        </div>
    </div>
@endsection