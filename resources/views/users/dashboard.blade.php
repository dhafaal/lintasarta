@extends('layouts.user')

@section('title', 'Dashboard')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet" />
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
        <div class="space-y-6 px-4 py-6 sm:px-6 lg:px-8">
            {{-- Hero Welcome Section --}}
            <div class="mb-6">
                <div class="mb-4 flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-sky-500">
                        <i data-lucide="home" class="h-6 w-6 text-white"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Welcome back, {{ Auth::user()->name }}!</h1>
                        <p class="text-sm text-gray-600">Manage your attendance and schedule</p>
                    </div>
                </div>
            </div>

            {{-- Quick Stats Cards --}}
            <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
                <div class="rounded-xl border border-gray-200 bg-white p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="mb-1 text-xs font-medium text-gray-600">This Month</p>
                            <p class="text-xl font-bold text-gray-900">{{ now()->format('M Y') }}</p>
                        </div>
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-sky-100">
                            <i data-lucide="calendar" class="h-5 w-5 text-sky-600"></i>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="mb-1 text-xs font-medium text-gray-600">Today</p>
                            <p class="text-xl font-bold text-gray-900">{{ now()->format('d') }}</p>
                        </div>
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-sky-100">
                            <i data-lucide="calendar-check" class="h-5 w-5 text-sky-600"></i>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="mb-1 text-xs font-medium text-gray-600">Week</p>
                            <p class="text-xl font-bold text-gray-900">{{ now()->weekOfYear }}</p>
                        </div>
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-sky-100">
                            <i data-lucide="calendar-days" class="h-5 w-5 text-sky-600"></i>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="mb-1 text-xs font-medium text-gray-600">Days in Month</p>
                            <p class="text-xl font-bold text-gray-900">{{ now()->daysInMonth }}</p>
                        </div>
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-sky-100">
                            <i data-lucide="hash" class="h-5 w-5 text-sky-600"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Shift Legend --}}
            <div class="rounded-xl border border-gray-200 bg-white p-5">
                <div class="mb-4 flex items-center gap-3">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-sky-100">
                        <i data-lucide="clock" class="h-4 w-4 text-sky-600"></i>
                    </div>
                    <h3 class="text-base font-bold text-gray-900">Shift Types</h3>
                </div>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    <div
                        class="flex items-center gap-3 rounded-lg border border-blue-200 bg-gradient-to-r from-blue-50 to-blue-100 p-3"
                    >
                        <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-blue-500">
                            <i data-lucide="sunrise" class="h-4 w-4 text-white"></i>
                        </div>
                        <div>
                            <div class="text-sm font-bold text-blue-900">Pagi</div>
                            <div class="text-xs text-blue-700">Morning Shift</div>
                        </div>
                    </div>
                    <div
                        class="flex items-center gap-3 rounded-lg border border-amber-200 bg-gradient-to-r from-amber-50 to-amber-100 p-3"
                    >
                        <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-amber-500">
                            <i data-lucide="sun" class="h-4 w-4 text-white"></i>
                        </div>
                        <div>
                            <div class="text-sm font-bold text-amber-900">Siang</div>
                            <div class="text-xs text-amber-700">Afternoon Shift</div>
                        </div>
                    </div>
                    <div
                        class="flex items-center gap-3 rounded-lg border border-purple-200 bg-gradient-to-r from-purple-50 to-purple-100 p-3"
                    >
                        <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-purple-500">
                            <i data-lucide="moon" class="h-4 w-4 text-white"></i>
                        </div>
                        <div>
                            <div class="text-sm font-bold text-purple-900">Malam</div>
                            <div class="text-xs text-purple-700">Night Shift</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Calendar Section --}}
            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
                <div class="border-b border-gray-200 bg-sky-50 px-5 py-4">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-sky-500">
                                <i data-lucide="calendar" class="h-4 w-4 text-white"></i>
                            </div>
                            <h2 class="text-base font-bold text-gray-900">Work Schedule</h2>
                        </div>
                        <div class="flex items-center gap-2">
                            <select
                                id="monthSelect"
                                class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-500"
                            >
                                @for ($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ $m == now()->month ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                    </option>
                                @endfor
                            </select>
                            <select
                                id="yearSelect"
                                class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-500"
                            >
                                @for ($y = now()->year - 3; $y <= now()->year + 3; $y++)
                                    <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>
                                        {{ $y }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </div>
                <div class="p-5">
                    <div id="calendar" class="w-full"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const calendarEl = document.getElementById('calendar');
            const monthSelect = document.getElementById('monthSelect');
            const yearSelect = document.getElementById('yearSelect');

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,listWeek',
                },
                locale: 'id',
                height: 'auto',
                events: '{{ route('user.calendar.data') }}',
                editable: false,
                selectable: false,
                dayMaxEvents: true,

                eventContent: function (arg) {
                    const shift = arg.event.extendedProps.shift || '';
                    const startTime = arg.event.extendedProps.start_time || '';
                    const endTime = arg.event.extendedProps.end_time || '';
                    return {
                        html: `<div class="font-semibold text-xs truncate">
                                ${shift} ${startTime} - ${endTime}
                              </div>`,
                    };
                },

                eventDidMount: function (info) {
                    const shift = info.event.extendedProps.shift || '';
                    const category = info.event.extendedProps.category || '';

                    // Debug log untuk melihat data yang diterima
                    console.log('Event:', shift, 'Category:', category);

                    info.el.setAttribute(
                        'title',
                        `${shift} | ${info.event.extendedProps.start_time} - ${info.event.extendedProps.end_time}`
                    );

                    // Warna berdasarkan kategori shift (sesuai dengan legend)
                    let backgroundColor = '#6b7280'; // Default gray

                    if (category === 'Pagi') {
                        backgroundColor = '#3b82f6'; // Blue-500
                    } else if (category === 'Siang') {
                        backgroundColor = '#f59e0b'; // Amber-500
                    } else if (category === 'Malam') {
                        backgroundColor = '#a855f7'; // Purple-500
                    }

                    console.log('Applied color:', backgroundColor, 'for category:', category);

                    info.el.style.backgroundColor = backgroundColor;
                    info.el.style.color = '#fff';
                    info.el.style.border = 'none';
                },

                datesSet: () => {
                    const date = calendar.getDate();
                    monthSelect.value = date.getMonth() + 1;
                    yearSelect.value = date.getFullYear();
                },
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
