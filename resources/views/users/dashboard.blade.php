@extends('layouts.user')

@section('title', 'Dashboard')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">
    <style>
        .fc-event {
            padding: 4px 8px !important;
            border-radius: 6px !important;
            font-size: 0.75rem !important;
            font-weight: 500 !important;
            border: none !important;
        }
        .fc-toolbar-title {
            font-size: 1.25rem !important;
            font-weight: 600 !important;
            color: #374151 !important;
        }
        .fc-button {
            border-radius: 6px !important;
            font-weight: 500 !important;
        }
    </style>
@endpush

@section('content')
<div class="p-6 space-y-4">
    <!-- Welcome -->
    <div class="bg-white rounded-lg border border-gray-200 p-4">
        <h1 class="text-xl font-medium text-gray-900 mb-1">Welcome, {{ Auth::user()->name }}</h1>
        <p class="text-sm text-gray-600">Manage your attendance and work schedule</p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">This Month</p>
                    <p class="text-lg font-medium text-gray-900">{{ now()->format('M Y') }}</p>
                </div>
                <i data-lucide="calendar" class="w-5 h-5 text-gray-400"></i>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Today</p>
                    <p class="text-lg font-medium text-gray-900">{{ now()->format('d') }}</p>
                </div>
                <i data-lucide="calendar-days" class="w-5 h-5 text-gray-400"></i>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Week</p>
                    <p class="text-lg font-medium text-gray-900">{{ now()->weekOfYear }}</p>
                </div>
                <i data-lucide="calendar-range" class="w-5 h-5 text-gray-400"></i>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Days in Month</p>
                    <p class="text-lg font-medium text-gray-900">{{ now()->daysInMonth }}</p>
                </div>
                <i data-lucide="hash" class="w-5 h-5 text-gray-400"></i>
            </div>
        </div>
    </div>

    <!-- Shift Legend -->
    <div class="bg-white rounded-lg border border-gray-200 p-4">
        <h3 class="text-sm font-medium text-gray-900 mb-3">Shift Legend</h3>
        <div class="flex flex-wrap gap-3">
            <div class="flex items-center space-x-2">
                <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                <span class="text-sm text-gray-700">Morning</span>
            </div>
            <div class="flex items-center space-x-2">
                <span class="w-3 h-3 rounded-full bg-yellow-500"></span>
                <span class="text-sm text-gray-700">Afternoon</span>
            </div>
            <div class="flex items-center space-x-2">
                <span class="w-3 h-3 rounded-full bg-purple-500"></span>
                <span class="text-sm text-gray-700">Night</span>
            </div>
            <div class="flex items-center space-x-2">
                <span class="w-3 h-3 rounded-full bg-gray-500"></span>
                <span class="text-sm text-gray-700">Other</span>
            </div>
        </div>
    </div>

    <!-- Calendar Section -->
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <div class="p-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-medium text-gray-900">Work Schedule</h2>
                <div class="flex items-center space-x-2">
                    <select id="monthSelect" class="border border-gray-300 rounded-lg px-3 py-1 text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        @for ($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $m == now()->month ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                    <select id="yearSelect" class="border border-gray-300 rounded-lg px-3 py-1 text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        @for ($y = now()->year - 3; $y <= now()->year + 3; $y++)
                            <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
            </div>
        </div>
        <div class="p-4">
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
                    info.el.setAttribute(
                        'title',
                        `${shift} | ${info.event.extendedProps.start_time} - ${info.event.extendedProps.end_time}`
                    );

                    // Warna berdasarkan shift
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
