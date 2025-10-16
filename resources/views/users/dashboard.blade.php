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
    <div class="px-4 sm:px-6 lg:px-8 py-6 space-y-6">
        {{-- Hero Welcome Section --}}
        <div class="mb-6">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 bg-sky-500 rounded-xl flex items-center justify-center">
                    <i data-lucide="home" class="w-6 h-6 text-white"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Welcome back, {{ Auth::user()->name }}!</h1>
                    <p class="text-sm text-gray-600">Manage your attendance and schedule</p>
                </div>
            </div>
        </div>

        {{-- Quick Stats Cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl border border-gray-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600 mb-1">This Month</p>
                        <p class="text-xl font-bold text-gray-900">{{ now()->format('M Y') }}</p>
                    </div>
                    <div class="w-10 h-10 bg-sky-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="calendar" class="w-5 h-5 text-sky-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600 mb-1">Today</p>
                        <p class="text-xl font-bold text-gray-900">{{ now()->format('d') }}</p>
                    </div>
                    <div class="w-10 h-10 bg-sky-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="calendar-check" class="w-5 h-5 text-sky-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600 mb-1">Week</p>
                        <p class="text-xl font-bold text-gray-900">{{ now()->weekOfYear }}</p>
                    </div>
                    <div class="w-10 h-10 bg-sky-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="calendar-days" class="w-5 h-5 text-sky-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600 mb-1">Days in Month</p>
                        <p class="text-xl font-bold text-gray-900">{{ now()->daysInMonth }}</p>
                    </div>
                    <div class="w-10 h-10 bg-sky-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="hash" class="w-5 h-5 text-sky-600"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Shift Legend --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-8 h-8 bg-sky-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="clock" class="w-4 h-4 text-sky-600"></i>
                </div>
                <h3 class="text-base font-bold text-gray-900">Shift Types</h3>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                <div class="flex items-center gap-3 p-3 bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg border border-blue-200">
                    <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i data-lucide="sunrise" class="w-4 h-4 text-white"></i>
                    </div>
                    <div>
                        <div class="text-sm font-bold text-blue-900">Pagi</div>
                        <div class="text-xs text-blue-700">Morning Shift</div>
                    </div>
                </div>
                <div class="flex items-center gap-3 p-3 bg-gradient-to-r from-amber-50 to-amber-100 rounded-lg border border-amber-200">
                    <div class="w-8 h-8 bg-amber-500 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i data-lucide="sun" class="w-4 h-4 text-white"></i>
                    </div>
                    <div>
                        <div class="text-sm font-bold text-amber-900">Siang</div>
                        <div class="text-xs text-amber-700">Afternoon Shift</div>
                    </div>
                </div>
                <div class="flex items-center gap-3 p-3 bg-gradient-to-r from-purple-50 to-purple-100 rounded-lg border border-purple-200">
                    <div class="w-8 h-8 bg-purple-500 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i data-lucide="moon" class="w-4 h-4 text-white"></i>
                    </div>
                    <div>
                        <div class="text-sm font-bold text-purple-900">Malam</div>
                        <div class="text-xs text-purple-700">Night Shift</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Calendar Section --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 bg-sky-50 border-b border-gray-200">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-sky-500 rounded-lg flex items-center justify-center">
                            <i data-lucide="calendar" class="w-4 h-4 text-white"></i>
                        </div>
                        <h2 class="text-base font-bold text-gray-900">Work Schedule</h2>
                    </div>
                    <div class="flex items-center gap-2">
                        <select id="monthSelect" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500 bg-white">
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $m == now()->month ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                        <select id="yearSelect" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500 bg-white">
                            @for ($y = now()->year - 3; $y <= now()->year + 3; $y++)
                                <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
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
