@extends('layouts.app')

@section('title', 'Kalender Jadwal')

@section('content')
    <div class="min-h-screen bg-white sm:p-6 lg:p-8">
        <div class="max-w-7xl mx-auto space-y-8">

            {{-- Header --}}
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="flex items-center space-x-4">
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-sky-100 to-sky-200 rounded-xl flex items-center justify-center shadow-sm">
                        <svg class="w-6 h-6 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 tracking-tight">📅 Kalender Jadwal Pegawai</h1>
                        <p class="text-gray-600 mt-1">Visualisasi jadwal kerja karyawan</p>
                    </div>
                </div>

                {{-- Export --}}
                <form action="{{ route('admin.calendar.export') }}" method="GET" class="flex gap-3 items-center">
                    @php
                        $currentMonth = request('month', $month ?? now()->month);
                        $currentYear = request('year', $year ?? now()->year);
                    @endphp
                    <select id="monthSelect" name="month"
                        class="px-3 py-2 border rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm">
                        @for ($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ (int) $m === (int) $currentMonth ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                    <select id="yearSelect" name="year"
                        class="px-3 py-2 border rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm">
                        @for ($y = now()->year - 5; $y <= now()->year + 5; $y++)
                            <option value="{{ $y }}" {{ (int) $y === (int) $currentYear ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endfor
                    </select>
                    <button type="submit"
                        class="px-5 py-2 bg-gradient-to-r from-sky-500 to-sky-600 text-white font-semibold rounded-lg shadow hover:from-sky-600 hover:to-sky-700 transition">
                        📊 Export Excel
                    </button>
                </form>
            </div>

            {{-- Tabs --}}
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8">
                    <button class="tab-btn border-sky-500 text-sky-600 pb-3 px-1 border-b-2 font-medium text-sm"
                        data-tab="calendar">
                        Kalender
                    </button>
                    <button
                        class="tab-btn border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 pb-3 px-1 border-b-2 font-medium text-sm"
                        data-tab="table">
                        Tabel
                    </button>
                </nav>
            </div>

            {{-- Kalender --}}
            <div id="tab-calendar" class="tab-content">
                <div class="bg-white rounded-2xl border border-gray-200 shadow">
                    <div class="px-8 py-4 border-b border-gray-200 bg-gradient-to-r from-sky-50 to-blue-50">
                        <h2 class="text-lg font-bold text-gray-900">Kalender Jadwal</h2>
                    </div>

                    {{-- Legend --}}
                    <div class="flex flex-wrap gap-4 text-sm px-6 py-3">
                        <div class="flex items-center gap-2"><span class="w-4 h-4 rounded bg-[#22C7FD]"></span> Pagi</div>
                        <div class="flex items-center gap-2"><span class="w-4 h-4 rounded bg-[#FACC15]"></span> Siang</div>
                        <div class="flex items-center gap-2"><span class="w-4 h-4 rounded bg-[#A855F7]"></span> Malam</div>
                        <div class="flex items-center gap-2"><span class="w-4 h-4 rounded bg-[#6B7280]"></span> Lainnya
                        </div>
                    </div>

                    <div class="p-6">
                        <div id="calendar"></div>
                    </div>
                </div>
            </div>

            {{-- Tabel --}}
            <div id="tab-table" class="tab-content hidden">
                <div class="bg-white rounded-2xl border border-gray-200 shadow">
                    <div
                        class="px-8 py-4 border-b border-gray-200 bg-gradient-to-r from-sky-50 to-blue-50 flex items-center justify-between">
                        <h2 class="text-lg font-bold text-gray-900">Tabel Jadwal</h2>
                        {{-- Filter --}}
                        <form method="GET" action="{{ route('admin.calendar.view') }}" class="flex gap-2 items-center">
                            <select name="month" class="px-3 py-2 border rounded-lg text-sm">
                                @for ($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ $m == $month ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                    </option>
                                @endfor
                            </select>
                            <select name="year" class="px-3 py-2 border rounded-lg text-sm">
                                @for ($y = now()->year - 3; $y <= now()->year + 2; $y++)
                                    <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>
                                        {{ $y }}</option>
                                @endfor
                            </select>
                            <button type="submit"
                                class="px-4 py-2 bg-sky-500 text-white rounded-lg hover:bg-sky-600 transition">
                                🔍 Tampilkan
                            </button>
                        </form>
                    </div>

                    {{-- Table --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full border-collapse text-sm">
                            <thead>
                                {{-- Baris tanggal --}}
                                <tr class="bg-gray-100 text-gray-700">
                                    <th class="px-2 py-2 border">NO</th>
                                    <th class="px-4 py-2 border">NAMA</th>
                                    @for ($d = 1; $d <= $daysInMonth; $d++)
                                        <th class="px-2 py-2 border">{{ $d }}</th>
                                    @endfor
                                    <th class="px-4 py-2 border">TOTAL JAM</th>
                                </tr>
                                {{-- Baris hari --}}
                                <tr class="bg-gray-50 text-gray-500">
                                    <th class="px-2 py-1 border"></th>
                                    <th class="px-4 py-1 border"></th>
                                    @for ($d = 1; $d <= $daysInMonth; $d++)
                                        <th class="px-2 py-1 border">
                                            {{ \Carbon\Carbon::createFromDate($year, $month, $d)->translatedFormat('D') }}
                                        </th>
                                    @endfor
                                    <th class="px-4 py-1 border"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($data as $index => $row)
                                    {{-- Baris shift --}}
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-2 py-2 border text-center align-top" rowspan="2">
                                            {{ $index + 1 }}</td>
                                        <td class="px-4 py-2 border text-left font-medium">{{ $row['nama'] }}</td>
                                        @for ($d = 1; $d <= $daysInMonth; $d++)
                                            <td class="px-2 py-2 border text-center">
                                                {{ $row['shifts'][$d]['shift'] }}
                                            </td>
                                        @endfor
                                        <td class="px-4 py-2 border text-center" rowspan="2">{{ $row['total_jam'] }}
                                        </td>
                                    </tr>
                                    {{-- Baris jam kerja --}}
                                    <tr class="hover:bg-gray-50 text-gray-600">
                                        <td class="px-4 py-2 border text-left">JAM KERJA</td>
                                        @for ($d = 1; $d <= $daysInMonth; $d++)
                                            <td class="px-2 py-2 border text-center">
                                                {{ $row['shifts'][$d]['hours'] }}
                                            </td>
                                        @endfor
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ $daysInMonth + 3 }}" class="text-center text-gray-500 py-6">
                                            Tidak ada jadwal untuk bulan ini
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet" />
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
    <script>
        // Tab switch
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('border-sky-500',
                    'text-sky-600'));
                this.classList.add('border-sky-500', 'text-sky-600');

                document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));
                const tab = this.getAttribute('data-tab');
                document.getElementById('tab-' + tab).classList.remove('hidden');
            });
        });

        // FullCalendar
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,listWeek'
                },
                events: "{{ route('admin.calendar.data') }}",
                height: 700,
                editable: false,
                selectable: true,
                dayMaxEvents: true,
                locale: 'id',

                eventDidMount: function(info) {
                    const shift = info.event.extendedProps.shift || '';
                    info.el.setAttribute('title',
                        `${shift} | ${info.event.extendedProps.start_time ?? ''} - ${info.event.extendedProps.end_time ?? ''}`
                        );

                    let bg = '#6B7280';
                    if (shift === 'Pagi') bg = '#22C7FD';
                    else if (shift === 'Siang') bg = '#FACC15';
                    else if (shift === 'Malam') bg = '#A855F7';

                    info.el.style.backgroundColor = bg;
                    info.el.style.color = '#fff';
                    info.el.style.border = 'none';
                }
            });
            calendar.render();
        });
    </script>
@endpush
