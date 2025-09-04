@extends('layouts.user')

@section('title', 'Kalender Jadwal')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">
    <style>
        /* FullCalendar styling */
        .fc-event {
            padding: 6px 8px !important;
            border-radius: 8px !important;
            font-size: 0.8rem !important;
            line-height: 1.2rem !important;
        }

        .fc-toolbar-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1f2937;
        }

        .fc-daygrid-event {
            white-space: normal !important;
        }
    </style>
@endpush

@section('content')
    <div class="min-h-screen bg-white p-4 sm:p-6 lg:p-8">
        <div class="max-w-7xl mx-auto space-y-6">

            <!-- Header -->
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="flex items-center space-x-4">
                    <div
                        class="w-14 h-14 bg-gradient-to-br from-sky-100 to-sky-200 rounded-xl flex items-center justify-center shadow">
                        <svg class="w-7 h-7 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Kalender Jadwal Pegawai</h1>
                        <p class="text-gray-600 text-sm">Lihat jadwal kerja Anda dalam bentuk kalender interaktif</p>
                    </div>
                </div>

                <!-- Filter Bulan & Tahun -->
                <div class="flex items-center gap-2">
                    <select id="monthSelect"
                        class="border border-gray-300 bg-white text-gray-700 px-3 py-2 rounded-md shadow-sm text-sm focus:outline-none focus:ring-2 focus:ring-sky-400 focus:border-sky-400">
                        @for ($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $m == now()->month ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>

                    <select id="yearSelect"
                        class="border border-gray-300 bg-white text-gray-700 px-3 py-2 rounded-md shadow-sm text-sm focus:outline-none focus:ring-2 focus:ring-sky-400 focus:border-sky-400">
                        @for ($y = now()->year - 3; $y <= now()->year + 3; $y++)
                            <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endfor
                    </select>
                </div>
            </div>

            <!-- Legend Shift -->
            <div class="flex flex-wrap gap-3 text-sm">
                <div class="flex items-center gap-2">
                    <span class="w-4 h-4 rounded bg-sky-500"></span> <span>Pagi</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-4 h-4 rounded bg-yellow-400"></span> <span>Siang</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-4 h-4 rounded bg-purple-600"></span> <span>Malam</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-4 h-4 rounded bg-gray-500"></span> <span>Lainnya</span>
                </div>
            </div>

            <!-- Kalender -->
            <div class="bg-white rounded-xl shadow-lg p-4">
                <div id="calendar" class="w-full"></div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');
            const monthSelect = document.getElementById('monthSelect');
            const yearSelect = document.getElementById('yearSelect');

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,listWeek'
                },
                locale: 'id',
                height: 'auto',
                events: "{{ route('user.calendar.data') }}",
                editable: false,
                selectable: false,
                dayMaxEvents: true,

                eventContent: function(arg) {
                    const shift = arg.event.extendedProps.shift || '';
                    const startTime = arg.event.extendedProps.start_time || '';
                    const endTime = arg.event.extendedProps.end_time || '';

                    return {
                        html: `<div class="font-semibold text-xs truncate">
                  ${shift} ${startTime} - ${endTime}
               </div>`
                    };
                },


                eventDidMount: function(info) {
                    const shift = info.event.extendedProps.shift || '';

                    // Tooltip
                    info.el.setAttribute('title',
                        `${shift} | ${info.event.extendedProps.start_time} - ${info.event.extendedProps.end_time}`
                        );

                    // Warna shift
                    if (shift === 'Pagi') info.el.style.backgroundColor = '#0ea5e9';
                    else if (shift === 'Siang') info.el.style.backgroundColor = '#facc15';
                    else if (shift === 'Malam') info.el.style.backgroundColor = '#9333ea';
                    else info.el.style.backgroundColor = '#6b7280';

                    info.el.style.color = '#fff';
                    info.el.style.border = 'none';
                },

                datesSet: () => {
                    const date = calendar.getDate();
                    monthSelect.value = date.getMonth() + 1;
                    yearSelect.value = date.getFullYear();
                }
            });

            calendar.render();

            // Filter bulan/tahun
            monthSelect.addEventListener('change', () => {
                calendar.gotoDate(new Date(yearSelect.value, monthSelect.value - 1, 1));
            });
            yearSelect.addEventListener('change', () => {
                calendar.gotoDate(new Date(yearSelect.value, monthSelect.value - 1, 1));
            });
        });
    </script>
@endpush
