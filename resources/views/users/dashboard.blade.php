@extends('layouts.user')

@section('title', 'Dashboard')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">
    <style>
        .fc-event {
            padding: 4px 8px !important;
            border-radius: 8px !important;
            font-size: 0.75rem !important;
            font-weight: 500 !important;
            border: none !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1) !important;
        }
        .fc-toolbar-title {
            font-size: 1.25rem !important;
            font-weight: 600 !important;
            color: #0f172a !important;
        }
        .fc-button {
            border-radius: 8px !important;
            font-weight: 500 !important;
            background: linear-gradient(to right, #0ea5e9, #0284c7) !important;
            border: none !important;
        }
        .fc-button:hover {
            background: linear-gradient(to right, #0284c7, #0369a1) !important;
        }
        .fc-button-active {
            background: linear-gradient(to right, #0284c7, #0369a1) !important;
        }
        .fc-daygrid-day:hover {
            background-color: rgba(14, 165, 233, 0.05) !important;
        }
    </style>
@endpush

@section('content')
<div class="min-h-screen bg-white">
    <div class="container mx-auto px-4 py-8 space-y-8">
        {{-- Hero Welcome Section --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-sky-500 to-sky-600 rounded-3xl shadow-lg mb-6">
                <i data-lucide="home" class="w-8 h-8 text-white"></i>
            </div>
            <h1 class="text-4xl font-bold text-gray-900 mb-3">Welcome back, {{ Auth::user()->name }}!</h1>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">Manage your attendance, view your schedule, and stay on top of your work commitments with ease.</p>
        </div>

        {{-- Quick Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">This Month</p>
                        <p class="text-2xl font-bold text-gray-900">{{ now()->format('M Y') }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-sky-100 to-sky-200 rounded-xl flex items-center justify-center">
                        <i data-lucide="calendar" class="w-5 h-5 text-sky-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Today</p>
                        <p class="text-2xl font-bold text-gray-900">{{ now()->format('d') }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-emerald-100 to-emerald-200 rounded-xl flex items-center justify-center">
                        <i data-lucide="calendar-check" class="w-5 h-5 text-emerald-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Week</p>
                        <p class="text-2xl font-bold text-gray-900">{{ now()->weekOfYear }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-purple-100 to-purple-200 rounded-xl flex items-center justify-center">
                        <i data-lucide="calendar-days" class="w-5 h-5 text-purple-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Days in Month</p>
                        <p class="text-2xl font-bold text-gray-900">{{ now()->daysInMonth }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-amber-100 to-amber-200 rounded-xl flex items-center justify-center">
                        <i data-lucide="hash" class="w-5 h-5 text-amber-600"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Shift Legend --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center space-x-3 mb-6">
                <div class="w-10 h-10 bg-gradient-to-br from-sky-100 to-sky-200 rounded-xl flex items-center justify-center">
                    <i data-lucide="sun" class="w-4 h-4 text-sky-600"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">Shift Legend</h3>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="flex items-center space-x-3 p-3 bg-blue-50 rounded-xl border border-blue-100">
                    <span class="w-4 h-4 rounded-full bg-blue-500 shadow-sm"></span>
                    <span class="text-sm font-medium text-blue-700">Morning</span>
                </div>
                <div class="flex items-center space-x-3 p-3 bg-yellow-50 rounded-xl border border-yellow-100">
                    <span class="w-4 h-4 rounded-full bg-yellow-500 shadow-sm"></span>
                    <span class="text-sm font-medium text-yellow-700">Afternoon</span>
                </div>
                <div class="flex items-center space-x-3 p-3 bg-purple-50 rounded-xl border border-purple-100">
                    <span class="w-4 h-4 rounded-full bg-purple-500 shadow-sm"></span>
                    <span class="text-sm font-medium text-purple-700">Night</span>
                </div>
                <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-xl border border-gray-100">
                    <span class="w-4 h-4 rounded-full bg-gray-500 shadow-sm"></span>
                    <span class="text-sm font-medium text-gray-700">Other</span>
                </div>
            </div>
        </div>

        {{-- Calendar Section --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-sky-50 to-sky-100 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-sky-500 to-sky-600 rounded-xl flex items-center justify-center">
                            <i data-lucide="calendar" class="w-4 h-4 text-white"></i>
                        </div>
                        <h2 class="text-xl font-semibold text-gray-900">Work Schedule</h2>
                    </div>
                    <div class="flex items-center space-x-3">
                        <select id="monthSelect" class="border border-gray-300 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-colors bg-white">
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $m == now()->month ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                        <select id="yearSelect" class="border border-gray-300 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-colors bg-white">
                            @for ($y = now()->year - 3; $y <= now()->year + 3; $y++)
                                <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div id="calendar" class="w-full"></div>
            </div>
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
