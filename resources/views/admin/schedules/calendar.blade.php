@extends('layouts.app')

@section('title', 'Kalender Jadwal')

@section('content')
    <div class="min-h-screen bg-white sm:p-6 lg:p-8">
        <div class="mx-auto space-y-8">

            {{-- Header --}}
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="flex items-center space-x-4">
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-sky-100 to-sky-200 rounded-xl flex items-center justify-center shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round"
                            class="lucide lucide-calendar-icon lucide-calendar text-sky-700">
                            <path d="M8 2v4" />
                            <path d="M16 2v4" />
                            <rect width="18" height="18" x="3" y="4" rx="2" />
                            <path d="M3 10h18" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-700 tracking-tight">
                            Kalender Jadwal Pegawai</h1>
                        <p class="text-gray-500 mt-1">Visualisasi kalender & tabel jadwal kerja</p>
                    </div>
                </div>

                {{-- Export --}}
                <form action="{{ route('admin.calendar.export') }}" method="GET" class="flex gap-3 items-center">
                    @php
                        $currentMonth = request('month', $month ?? now()->month);
                        $currentYear = request('year', $year ?? now()->year);
                    @endphp
                    <select name="month"
                        class="px-3 py-2 border rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm">
                        @for ($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ (int) $m === (int) $currentMonth ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                    <select name="year"
                        class="px-3 py-2 border rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm">
                        @for ($y = now()->year - 5; $y <= now()->year + 5; $y++)
                            <option value="{{ $y }}" {{ (int) $y === (int) $currentYear ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endfor
                    </select>
                    <button type="submit"
                        class="px-5 py-2 bg-sky-600 text-white font-semibold rounded-lg shadow hover:bg-sky-700 transition">
                        Export Excel
                    </button>
                </form>
            </div>

            {{-- Filter Bulan untuk Tabel --}}
            <form action="{{ route('admin.calendar.view') }}" method="GET" class="flex gap-3 items-center">
                <select name="month"
                    class="px-3 py-2 border rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm">
                    @for ($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ (int) $m === (int) $currentMonth ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
                <select name="year"
                    class="px-3 py-2 border rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm">
                    @for ($y = now()->year - 5; $y <= now()->year + 5; $y++)
                        <option value="{{ $y }}" {{ (int) $y === (int) $currentYear ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endfor
                </select>
                <button type="submit"
                    class="px-5 py-2 bg-sky-500 text-white font-semibold rounded-lg shadow hover:bg-sky-600 transition">
                    Tampilkan
                </button>
            </form>

            {{-- Tabel Jadwal --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow">
                <div
                    class="px-8 py-4 border-b border-gray-200 bg-gradient-to-r from-sky-50 to-blue-50 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-gray-900">Tabel Jadwal</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-100 text-gray-700">
                                <th class="sticky left-0 bg-gray-100 z-20 px-2 py-2 border">NO</th>
                                <th class="sticky left-[50px] bg-gray-100 z-20 px-4 py-2 border">NAMA</th>
                                @for ($d = 1; $d <= $daysInMonth; $d++)
                                    @php
                                        $dayOfWeek = \Carbon\Carbon::createFromDate($year, $month, $d)->dayOfWeek;
                                        $isWeekend = $dayOfWeek === 0 || $dayOfWeek === 6;
                                    @endphp
                                    <th class="px-2 py-2 border {{ $isWeekend ? 'bg-red-100 text-red-600' : '' }}">
                                        {{ $d }}
                                    </th>
                                @endfor
                                <th class="sticky right-0 bg-gray-100 z-20 px-4 py-2 border">TOTAL JAM</th>
                            </tr>
                            <tr class="bg-gray-50 text-gray-500">
                                <th class="sticky left-0 bg-gray-50 z-10 px-2 py-1 border"></th>
                                <th class="sticky left-[50px] bg-gray-50 z-10 px-4 py-1 border"></th>
                                @for ($d = 1; $d <= $daysInMonth; $d++)
                                    @php
                                        $dayOfWeek = \Carbon\Carbon::createFromDate($year, $month, $d)->dayOfWeek;
                                        $isWeekend = $dayOfWeek === 0 || $dayOfWeek === 6;
                                    @endphp
                                    <th class="px-2 py-1 border {{ $isWeekend ? 'bg-red-50 text-red-500' : '' }}">
                                        {{ \Carbon\Carbon::createFromDate($year, $month, $d)->translatedFormat('D') }}
                                    </th>
                                @endfor
                                <th class="sticky right-0 bg-gray-50 z-10 px-4 py-1 border"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($data as $index => $row)
                                <tr class="hover:bg-gray-50">
                                    <td class="sticky left-0 bg-white z-10 px-2 py-2 border text-center align-top"
                                        rowspan="2">
                                        {{ $index + 1 }}
                                    </td>
                                    <td
                                        class="sticky left-[50px] bg-white z-10 px-4 py-2 border text-left font-medium">
                                        {{ $row['nama'] }}
                                    </td>
                                    @for ($d = 1; $d <= $daysInMonth; $d++)
                                        @php
                                            $dayOfWeek = \Carbon\Carbon::createFromDate($year, $month, $d)->dayOfWeek;
                                            $isWeekend = $dayOfWeek === 0 || $dayOfWeek === 6;
                                        @endphp
                                        <td
                                            class="px-2 py-2 border text-center {{ $isWeekend ? 'bg-red-50 text-red-500' : '' }}">
                                            {{ $row['shifts'][$d]['shift'] }}
                                        </td>
                                    @endfor
                                    <td class="sticky right-0 bg-white z-10 px-4 py-2 border text-center"
                                        rowspan="2">
                                        {{ $row['total_jam'] }}
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50 text-gray-600">
                                    <td class="sticky left-[50px] bg-white z-10 px-4 py-2 border text-left">JAM KERJA</td>
                                    @for ($d = 1; $d <= $daysInMonth; $d++)
                                        @php
                                            $dayOfWeek = \Carbon\Carbon::createFromDate($year, $month, $d)->dayOfWeek;
                                            $isWeekend = $dayOfWeek === 0 || $dayOfWeek === 6;
                                        @endphp
                                        <td
                                            class="px-2 py-2 border text-center {{ $isWeekend ? 'bg-red-50 text-red-500' : '' }}">
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

            {{-- Kalender --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow">
                <div
                    class="px-8 py-4 border-b border-gray-200 bg-gradient-to-r from-sky-50 to-blue-50 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-gray-900">Kalender Jadwal</h2>
                </div>

                {{-- Legend --}}
                <div class="flex flex-wrap gap-4 text-sm px-6 py-3">
                    <div class="flex items-center gap-2"><span class="w-4 h-4 rounded bg-[#22C7FD]"></span> Pagi</div>
                    <div class="flex items-center gap-2"><span class="w-4 h-4 rounded bg-[#FACC15]"></span> Siang</div>
                    <div class="flex items-center gap-2"><span class="w-4 h-4 rounded bg-[#A855F7]"></span> Malam</div>
                    <div class="flex items-center gap-2"><span class="w-4 h-4 rounded bg-[#6B7280]"></span> Lainnya</div>
                </div>

                <div class="p-6">
                    <div id="calendar"></div>
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
