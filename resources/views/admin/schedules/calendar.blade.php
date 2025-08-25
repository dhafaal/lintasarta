@extends('layouts.app')

@section('title', 'Kalender Jadwal')

@section('content')
<div class="min-h-screen bg-white sm:p-6 lg:p-8">
    <div class="max-w-7xl mx-auto space-y-8">
        <!-- Enhanced Header Section matching schedule design -->
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-gradient-to-br from-sky-100 to-sky-200 rounded-xl flex items-center justify-center shadow-sm">
                    <svg class="w-6 h-6 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 tracking-tight">📅 Kalender Jadwal Pegawai</h1>
                    <p class="text-gray-600 mt-1">Visualisasi jadwal kerja karyawan dalam bentuk kalender</p>
                </div>
            </div>

            <!-- Enhanced Export Form with professional styling -->
            <form action="{{ route('admin.calendar.export') }}" method="GET" class="flex flex-wrap gap-3 items-center">
                @php
                    $currentMonth = request('month', now()->month);
                    $currentYear = request('year', now()->year);
                @endphp

                <!-- Month Selector -->
                <select id="monthSelect" name="month" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm font-medium bg-white">
                    @for ($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $m == $currentMonth ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>

                <!-- Year Selector -->
                <select id="yearSelect" name="year" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm font-medium bg-white">
                    @for ($y = now()->year - 5; $y <= now()->year + 5; $y++)
                        <option value="{{ $y }}" {{ $y == $currentYear ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>

                <button type="submit" class="inline-flex items-center px-6 py-2 bg-gradient-to-r from-sky-500 to-sky-600 hover:from-sky-600 hover:to-sky-700 text-white font-bold rounded-lg transition-all duration-200 transform hover:scale-105 focus:outline-none focus:ring-4 focus:ring-sky-200 shadow-lg hover:shadow-xl">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    📊 Export Excel
                </button>
            </form>
        </div>

        <!-- Enhanced Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-gradient-to-br from-sky-500 to-sky-600 rounded-2xl p-6 text-white shadow-xl">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sky-100 text-sm font-medium uppercase tracking-wide">Bulan Ini</p>
                        <p class="text-3xl font-bold mt-2">{{ now()->format('M Y') }}</p>
                        <p class="text-sky-200 text-xs mt-1">Periode aktif</p>
                    </div>
                    <div class="w-14 h-14 bg-sky-400 bg-opacity-30 rounded-xl flex items-center justify-center">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white border-2 border-sky-100 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sky-600 text-sm font-bold uppercase tracking-wide">Hari Ini</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ now()->format('d') }}</p>
                        <p class="text-gray-500 text-xs mt-1">{{ now()->format('l') }}</p>
                    </div>
                    <div class="w-14 h-14 bg-gradient-to-br from-green-100 to-emerald-100 rounded-xl flex items-center justify-center">
                        <svg class="w-7 h-7 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white border-2 border-sky-100 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sky-600 text-sm font-bold uppercase tracking-wide">Minggu Ini</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ now()->weekOfYear }}</p>
                        <p class="text-gray-500 text-xs mt-1">Minggu ke-{{ now()->weekOfYear }}</p>
                    </div>
                    <div class="w-14 h-14 bg-gradient-to-br from-blue-100 to-sky-100 rounded-xl flex items-center justify-center">
                        <svg class="w-7 h-7 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white border-2 border-sky-100 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sky-600 text-sm font-bold uppercase tracking-wide">Total Hari</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ now()->daysInMonth }}</p>
                        <p class="text-gray-500 text-xs mt-1">Hari dalam bulan</p>
                    </div>
                    <div class="w-14 h-14 bg-gradient-to-br from-purple-100 to-indigo-100 rounded-xl flex items-center justify-center">
                        <svg class="w-7 h-7 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2-2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Calendar Card with professional styling -->
        <div class="bg-white rounded-2xl border-2 border-sky-100 overflow-hidden shadow-xl">
            <div class="px-8 py-6 border-b border-sky-100 bg-gradient-to-r from-sky-50 to-blue-50">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">Kalender Jadwal</h2>
                        <p class="text-gray-600 mt-1">Visualisasi jadwal kerja karyawan</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-gradient-to-br from-sky-100 to-sky-200 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="p-8">
                <div id="calendar"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css' rel='stylesheet' />
<style>
    /* Enhanced Calendar Styling to match sky theme */
    .fc-toolbar-title {
        font-size: 1.75rem !important;
        font-weight: 800 !important;
        color: #0f172a !important;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.05) !important;
    }

    /* Enhanced Buttons with sky theme */
    .fc-button-group {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
        border-radius: 0.75rem !important;
        overflow: hidden !important;
    }

    .fc-button {
        background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%) !important;
        border: none !important;
        color: white !important;
        padding: 0.75rem 1.25rem !important;
        font-weight: 700 !important;
        font-size: 0.875rem !important;
        transition: all 0.3s ease !important;
        text-transform: uppercase !important;
        letter-spacing: 0.025em !important;
    }
    
    .fc-button:hover {
        background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%) !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 8px 25px -5px rgba(14, 165, 233, 0.4) !important;
    }

    .fc-button:focus {
        box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.3) !important;
        outline: none !important;
    }

    .fc-button-active {
        background: linear-gradient(135deg, #0369a1 0%, #1e40af 100%) !important;
        box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1) !important;
    }

    /* Enhanced Header Cells */
    .fc-col-header-cell {
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%) !important;
        font-weight: 800 !important;
        color: #0369a1 !important;
        border-color: #bae6fd !important;
        padding: 1rem 0.5rem !important;
        text-transform: uppercase !important;
        letter-spacing: 0.05em !important;
        font-size: 0.75rem !important;
    }

    .fc-col-header-cell-cushion {
        padding: 0.5rem !important;
    }

    /* Enhanced Day Cells */
    .fc-daygrid-day {
        transition: all 0.2s ease !important;
        position: relative !important;
    }

    .fc-daygrid-day:hover {
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%) !important;
        transform: scale(1.02) !important;
    }

    .fc-daygrid-day-number {
        font-weight: 600 !important;
        color: #374151 !important;
        font-size: 0.875rem !important;
        padding: 0.5rem !important;
        transition: all 0.2s ease !important;
    }

    /* Today Styling */
    .fc-day-today {
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%) !important;
        border: 2px solid #3b82f6 !important;
        position: relative !important;
    }

    .fc-day-today .fc-daygrid-day-number {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%) !important;
        color: white !important;
        border-radius: 50% !important;
        width: 2rem !important;
        height: 2rem !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-weight: 700 !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
    }

    /* Weekend Styling */
    .fc-day-sat, .fc-day-sun {
        background: linear-gradient(135deg, #fef7f7 0%, #fef2f2 100%) !important;
    }

    .fc-day-sat .fc-daygrid-day-number, 
    .fc-day-sun .fc-daygrid-day-number {
        color: #dc2626 !important;
        font-weight: 700 !important;
    }

    /* Enhanced Events */
    .fc-daygrid-event {
        background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%) !important;
        color: white !important;
        border-radius: 0.5rem !important;
        padding: 4px 8px !important;
        font-size: 0.75rem !important;
        font-weight: 700 !important;
        border: none !important;
        box-shadow: 0 2px 8px rgba(14, 165, 233, 0.3) !important;
        transition: all 0.3s ease !important;
        margin: 2px !important;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1) !important;
    }

    .fc-daygrid-event:hover {
        background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%) !important;
        transform: translateY(-2px) scale(1.05) !important;
        box-shadow: 0 8px 25px rgba(14, 165, 233, 0.4) !important;
        z-index: 10 !important;
    }

    /* Different colors for different shift types */
    .fc-daygrid-event[title*="Pagi"] {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
        box-shadow: 0 2px 8px rgba(245, 158, 11, 0.3) !important;
    }

    .fc-daygrid-event[title*="Siang"] {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
        box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3) !important;
    }

    .fc-daygrid-event[title*="Malam"] {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%) !important;
        box-shadow: 0 2px 8px rgba(139, 92, 246, 0.3) !important;
    }

    /* Event Text */
    .fc-event-title {
        font-weight: 700 !important;
        font-size: 0.75rem !important;
        line-height: 1.2 !important;
    }

    /* More Events Link */
    .fc-daygrid-more-link {
        background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%) !important;
        color: white !important;
        border-radius: 0.375rem !important;
        padding: 2px 6px !important;
        font-size: 0.625rem !important;
        font-weight: 600 !important;
        text-decoration: none !important;
        transition: all 0.2s ease !important;
    }

    .fc-daygrid-more-link:hover {
        background: linear-gradient(135deg, #4b5563 0%, #374151 100%) !important;
        transform: scale(1.05) !important;
    }

    /* Mobile Responsiveness */
    @media (max-width: 768px) {
        .fc-toolbar {
            flex-direction: column !important;
            gap: 1rem !important;
            padding: 1rem !important;
        }

        .fc-toolbar-title {
            font-size: 1.25rem !important;
            text-align: center !important;
        }

        .fc-button {
            padding: 0.5rem 0.75rem !important;
            font-size: 0.75rem !important;
        }

        .fc-daygrid-event {
            font-size: 0.625rem !important;
            padding: 2px 4px !important;
        }

        .fc-col-header-cell {
            padding: 0.5rem 0.25rem !important;
            font-size: 0.625rem !important;
        }
    }

    /* Loading State */
    .fc-loading {
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%) !important;
        border-radius: 0.5rem !important;
        padding: 2rem !important;
        text-align: center !important;
        color: #0369a1 !important;
        font-weight: 600 !important;
    }

    /* Popover Styling */
    .fc-popover {
        background: white !important;
        border: 2px solid #e0f2fe !important;
        border-radius: 0.75rem !important;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1) !important;
    }

    .fc-popover-header {
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%) !important;
        color: #0369a1 !important;
        font-weight: 700 !important;
        padding: 0.75rem 1rem !important;
        border-bottom: 1px solid #bae6fd !important;
    }
</style>
@endpush

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
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
        events: "{{ route('admin.calendar.data') }}", // keeping original data source
        height: 700,
        editable: false,
        selectable: true,
        dayMaxEvents: true,
        locale: 'id', // added Indonesian locale
        
        datesSet: function() {
            const currentDate = calendar.getDate();
            const month = currentDate.getMonth() + 1;
            const year = currentDate.getFullYear();

            monthSelect.value = month;
            yearSelect.value = year;
        }
    });

    calendar.render();

    function updateCalendar() {
        const month = parseInt(monthSelect.value);
        const year = parseInt(yearSelect.value);
        const newDate = new Date(year, month - 1, 1);
        calendar.gotoDate(newDate);
    }

    monthSelect.addEventListener('change', updateCalendar);
    yearSelect.addEventListener('change', updateCalendar);
});
</script>
@endpush
