@extends('layouts.admin')

@section('title', 'Daftar Shifts')

@section('content')
    <div class="min-h-screen bg-white sm:p-6 lg:p-8">
        <div class="mx-auto space-y-8">
            <!-- Enhanced Header Section -->
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="flex items-center space-x-4">
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-sky-100 to-sky-200 rounded-xl flex items-center justify-center shadow-sm">
                        <svg class="w-6 h-6 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 01-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Manajemen Shifts</h1>
                        <p class="text-gray-600 mt-1">Kelola semua shift kerja dalam sistem</p>
                    </div>
                </div>

                <a href="{{ route('admin.shifts.create') }}"
                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-sky-500 to-sky-600 hover:from-sky-600 hover:to-sky-700 text-white font-bold rounded-xl transition-all duration-200 transform hover:scale-105 focus:outline-none focus:ring-4 focus:ring-sky-200 shadow-lg hover:shadow-xl">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Tambah Shift Baru
                </a>
            </div>

            <!-- Enhanced Stats Cards using x-role-card component -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-gradient-to-br from-sky-500 to-sky-600 rounded-2xl p-6 text-white shadow-xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sky-100 text-sm font-medium uppercase tracking-wide">Total Shifts</p>
                            <p class="text-3xl font-bold mt-2">{{ $shifts->count() }}</p>
                            <p class="text-sky-200 text-xs mt-1">Shift Aktif</p>
                        </div>
                        <div class="w-14 h-14 bg-sky-400 bg-opacity-30 rounded-xl flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-clock-icon lucide-clock">
                                <path d="M12 6v6l4 2" />
                                <circle cx="12" cy="12" r="10" />
                            </svg>
                        </div>
                    </div>
                </div>

                <x-stats-card title="Shift Pagi" :count="$Pagi" subtitle="Pagi"
                    bgColor="bg-radial from-yellow-200 to-yellow-50"
                    icon='<svg class="w-7 h-7 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>' />

                <x-stats-card title="Shift Siang" :count="$Siang" subtitle="Siang"
                    bgColor="bg-radial from-orange-200 to-orange-50"
                    icon='<svg class="w-7 h-7 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>' />

                <x-stats-card title="Shift Malam" :count="$Malam" subtitle="Malam"
                    bgColor="bg-radial from-indigo-200 to-indigo-50"
                    icon='<svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                        </svg>' />
            </div>

            <!-- Enhanced Table Card -->
            <div class="bg-white rounded-2xl border-2 border-sky-100 overflow-hidden shadow-xl">
                <div class="px-8 py-6 border-b border-sky-100 bg-gradient-to-r from-sky-50 to-blue-50">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-xl font-bold text-sky-900">Daftar Shift Kerja</h2>
                            <p class="text-sky-700 mt-1">Kelola dan atur semua shift kerja</p>
                        </div>
                        <div class="flex items-center space-x-3">
                            <form method="GET" action="{{ route('admin.shifts.index') }}"
                                class="flex items-center space-x-3">
                                <!-- Search -->
                                <div class="relative">
                                    <input type="text" name="search" value="{{ request('search') }}"
                                        placeholder="Cari shift..." oninput="this.form.submit()"
                                        class="pl-10 pr-4 py-2 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm">
                                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-3" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>

                                <!-- Filter Dropdown -->
                                <select name="filter" onchange="this.form.submit()"
                                    class="px-3 py-2 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm">
                                    <option value="">Semua Shift</option>
                                    <option value="Pagi" {{ request('filter') == 'Pagi' ? 'selected' : '' }}>Pagi</option>
                                    <option value="Siang" {{ request('filter') == 'Siang' ? 'selected' : '' }}>Siang
                                    </option>
                                    <option value="Malam" {{ request('filter') == 'Malam' ? 'selected' : '' }}>Malam
                                    </option>
                                </select>

                                <!-- Reset -->
                                @if (request('search') || request('filter'))
                                    <a href="{{ route('admin.shifts.index') }}"
                                        class="px-3 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm hover:bg-gray-300 transition">
                                        Reset
                                    </a>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b-2 border-gray-200">
                            <tr>
                                <th class="px-8 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg""
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="lucide lucide-clock-icon lucide-clock text-sky-600 w-4 h-4 mr-2">
                                            <path d="M12 6v6l4 2" />
                                            <circle cx="12" cy="12" r="10" />
                                        </svg>
                                        Nama Shift
                                    </div>
                                </th>
                                <th class="px-8 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-tag text-sky-600 w-4 h-4 mr-2">
                                            <path d="M12 2H2v10l9.29 9.29c.94.94 2.48.94 3.42 0l6.58-6.58c.94-.94.94-2.48 0-3.42L12 2Z"/>
                                            <path d="M7 7h.01"/>
                                        </svg>
                                        Kategori
                                    </div>
                                </th>
                                <th class="px-8 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="lucide lucide-alarm-clock-plus-icon lucide-alarm-clock-plus text-sky-600 w-4 h-4 mr-2">
                                            <circle cx="12" cy="13" r="8" />
                                            <path d="M5 3 2 6" />
                                            <path d="m22 6-3-3" />
                                            <path d="M6.38 18.7 4 21" />
                                            <path d="M17.64 18.67 20 21" />
                                            <path d="M12 10v6" />
                                            <path d="M9 13h6" />
                                        </svg>
                                        Jam Mulai
                                    </div>
                                </th>
                                <th class="px-8 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round"
                                            class="lucide lucide-alarm-clock-minus-icon lucide-alarm-clock-minus text-sky-600 w-4 h-4 mr-2">
                                            <circle cx="12" cy="13" r="8" />
                                            <path d="M5 3 2 6" />
                                            <path d="m22 6-3-3" />
                                            <path d="M6.38 18.7 4 21" />
                                            <path d="M17.64 18.67 20 21" />
                                            <path d="M9 13h6" />
                                        </svg>
                                        Jam Selesai
                                    </div>
                                </th>
                                <th class="px-8 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round"
                                            class="lucide lucide-clock-fading-icon lucide-clock-fading text-sky-600 w-4 h-4 mr-2">
                                            <path d="M12 2a10 10 0 0 1 7.38 16.75" />
                                            <path d="M12 6v6l4 2" />
                                            <path d="M2.5 8.875a10 10 0 0 0-.5 3" />
                                            <path d="M2.83 16a10 10 0 0 0 2.43 3.4" />
                                            <path d="M4.636 5.235a10 10 0 0 1 .891-.857" />
                                            <path d="M8.644 21.42a10 10 0 0 0 7.631-.38" />
                                        </svg>
                                        Durasi
                                    </div>
                                </th>
                                <th class="px-8 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($shifts as $shift)
                                <tr class="hover:bg-gray-50 transition-colors duration-200 group">
                                    <td class="px-8 py-6 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex items-center justify-center mr-4 transition-colors">
                                                @if ($shift->category == 'Pagi')
                                                <div class="flex items-center justify-center bg-radial from-yellow-200 to-yellow-50 w-10 h-10 rounded-xl">
                                                    <svg class="w-5 h-5 text-yellow-600" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z">
                                                        </path>
                                                    </svg>
                                                </div>
                                                @elseif($shift->category == 'Siang')
                                                <div class="flex items-center justify-center bg-radial from-orange-200 to-orange-50 w-10 h-10 rounded-xl">
                                                    <svg class="w-5 h-5 text-orange-600" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z">
                                                        </path>
                                                    </svg>
                                                </div>
                                                @else
                                                <div class="flex items-center justify-center bg-radial from-indigo-200 to-indigo-50 w-10 h-10 rounded-xl">
                                                    <svg class="w-5 h-5 text-indigo-600" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z">
                                                        </path>
                                                    </svg>
                                                </div>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="text-base font-bold text-gray-900">{{ $shift->shift_name }}</div>
                                                <div class="text-sm text-gray-500">{{ $shift->category }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 whitespace-nowrap">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                            @if($shift->category == 'Pagi') bg-yellow-100 text-yellow-800
                                            @elseif($shift->category == 'Siang') bg-orange-100 text-orange-800
                                            @elseif($shift->category == 'Malam') bg-indigo-100 text-indigo-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ $shift->category }}
                                        </span>
                                    </td>
                                    <td class="px-8 py-6 whitespace-nowrap">
                                        <div class="text-base font-semibold text-gray-900">{{ $shift->start_time }}</div>
                                    </td>
                                    <td class="px-8 py-6 whitespace-nowrap">
                                        <div class="text-base font-semibold text-gray-900">{{ $shift->end_time }}</div>
                                    </td>
                                    <td class="px-8 py-6 whitespace-nowrap">
                                        <div
                                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-sky-100 text-sky-800">
                                            @php
                                                $start = \Carbon\Carbon::parse($shift->start_time);
                                                $end = \Carbon\Carbon::parse($shift->end_time);
                                                if ($end->lt($start)) {
                                                    $end->addDay();
                                                }
                                                $duration = $start->diffInHours($end);
                                            @endphp
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 01-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            {{ $duration }} jam
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 whitespace-nowrap text-right">
                                        <div class="flex items-center justify-end space-x-3">
                                            <a href="{{ route('admin.shifts.edit', $shift->id) }}"
                                                class="inline-flex items-center px-4 py-2 bg-sky-100 hover:bg-sky-200 text-sky-700 font-semibold text-sm rounded-lg transition-all duration-200 hover:scale-105">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                    </path>
                                                </svg>
                                                Edit
                                            </a>
                                            <form action="{{ route('admin.shifts.destroy', $shift->id) }}" method="POST"
                                                class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    onclick="return confirm('Yakin ingin menghapus shift {{ $shift->shift_name }}? Tindakan ini tidak dapat dibatalkan.')"
                                                    class="inline-flex items-center px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 font-semibold text-sm rounded-lg transition-all duration-200 hover:scale-105">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                        </path>
                                                    </svg>
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-8 py-16 text-center">
                                        <div class="flex flex-col items-center">
                                            <div
                                                class="w-20 h-20 bg-gradient-to-br from-sky-100 to-sky-200 rounded-full flex items-center justify-center mb-6">
                                                <svg class="w-10 h-10 text-sky-400" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 8v4l3 3m6-3a9 9 0 01-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </div>
                                            <h3 class="text-xl font-bold text-gray-900 mb-2">Belum ada shift</h3>
                                            <p class="text-gray-600 mb-6 max-w-sm">Mulai dengan membuat shift kerja pertama
                                                untuk mengatur jadwal karyawan</p>
                                            <a href="{{ route('admin.shifts.create') }}"
                                                class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-sky-500 to-sky-600 hover:from-sky-600 hover:to-sky-700 text-white font-bold rounded-xl transition-all duration-200 transform hover:scale-105 shadow-lg">
                                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                </svg>
                                                Tambah Shift Pertama
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
