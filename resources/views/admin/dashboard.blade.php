@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="bg-gradient-to-r from-sky-50 to-sky-100 rounded-xl p-6 border border-sky-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="p-3 bg-gradient-to-br from-sky-100 to-sky-200 rounded-xl shadow-sm">
                        <i data-lucide="layout-dashboard" class="w-8 h-8 text-sky-600"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
                        <p class="text-gray-600">Monitor attendance and manage your workforce</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-sm font-medium text-gray-700">{{ now()->format('l, d F Y') }}</div>
                    <div class="text-xs text-gray-500">Last updated: {{ now()->format('H:i') }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 rounded-xl p-4">
            <div class="flex items-center">
                <i data-lucide="check-circle" class="w-5 h-5 text-green-600 mr-3"></i>
                <p class="text-green-700 font-medium">{{ session('success') }}</p>
                <button type="button" class="ml-auto text-green-500 hover:text-green-700" onclick="this.closest('.mb-6').style.display='none';">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        </div>
    @endif

    <!-- Today's Attendance Summary -->
    <div class="mb-8">
        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i data-lucide="clipboard-check" class="w-5 h-5 text-sky-600 mr-2"></i>
            Today's Attendance
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Total Schedules -->
            <div class="bg-white rounded-xl border border-gray-200 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Schedules</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $todaySchedules }}</p>
                    </div>
                    <div class="p-3 bg-gray-100 rounded-lg">
                        <i data-lucide="calendar" class="w-6 h-6 text-gray-600"></i>
                    </div>
                </div>
            </div>

            <!-- Hadir -->
            <div class="bg-white rounded-xl border border-green-200 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-green-600">Hadir</p>
                        <p class="text-2xl font-bold text-green-700">{{ $todayHadir }}</p>
                    </div>
                    <div class="p-3 bg-green-100 rounded-lg">
                        <i data-lucide="check-circle" class="w-6 h-6 text-green-600"></i>
                    </div>
                </div>
            </div>

            <!-- Izin -->
            <div class="bg-white rounded-xl border border-yellow-200 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-yellow-600">Izin</p>
                        <p class="text-2xl font-bold text-yellow-700">{{ $todayIzin }}</p>
                    </div>
                    <div class="p-3 bg-yellow-100 rounded-lg">
                        <i data-lucide="clock" class="w-6 h-6 text-yellow-600"></i>
                    </div>
                </div>
            </div>

            <!-- Alpha -->
            <div class="bg-white rounded-xl border border-red-200 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-red-600">Alpha</p>
                        <p class="text-2xl font-bold text-red-700">{{ $todayAlpha }}</p>
                    </div>
                    <div class="p-3 bg-red-100 rounded-lg">
                        <i data-lucide="x-circle" class="w-6 h-6 text-red-600"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Attendance Chart -->
    <div class="mb-8">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-gradient-to-br from-sky-100 to-sky-200 rounded-lg">
                        <i data-lucide="bar-chart-3" class="w-6 h-6 text-sky-600"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-800">Attendance Statistics</h2>
                        <p class="text-sm text-gray-600">{{ $currentMonth }} - Daily attendance overview</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <form method="GET" action="{{ route('admin.dashboard') }}" class="flex items-center space-x-3">
                        <div class="flex items-center space-x-2">
                            <label for="selected_month" class="text-sm font-medium text-gray-700">Month:</label>
                            <select name="selected_month" 
                                    id="selected_month"
                                    class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500"
                                    onchange="this.form.submit()">
                                <option value="1" {{ $selectedMonth == 1 ? 'selected' : '' }}>January</option>
                                <option value="2" {{ $selectedMonth == 2 ? 'selected' : '' }}>February</option>
                                <option value="3" {{ $selectedMonth == 3 ? 'selected' : '' }}>March</option>
                                <option value="4" {{ $selectedMonth == 4 ? 'selected' : '' }}>April</option>
                                <option value="5" {{ $selectedMonth == 5 ? 'selected' : '' }}>May</option>
                                <option value="6" {{ $selectedMonth == 6 ? 'selected' : '' }}>June</option>
                                <option value="7" {{ $selectedMonth == 7 ? 'selected' : '' }}>July</option>
                                <option value="8" {{ $selectedMonth == 8 ? 'selected' : '' }}>August</option>
                                <option value="9" {{ $selectedMonth == 9 ? 'selected' : '' }}>September</option>
                                <option value="10" {{ $selectedMonth == 10 ? 'selected' : '' }}>October</option>
                                <option value="11" {{ $selectedMonth == 11 ? 'selected' : '' }}>November</option>
                                <option value="12" {{ $selectedMonth == 12 ? 'selected' : '' }}>December</option>
                            </select>
                        </div>
                        <div class="flex items-center space-x-2">
                            <label for="selected_year" class="text-sm font-medium text-gray-700">Year:</label>
                            <select name="selected_year" 
                                    id="selected_year"
                                    class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500"
                                    onchange="this.form.submit()">
                                @for($year = 2020; $year <= 2030; $year++)
                                    <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>{{ $year }}</option>
                                @endfor
                            </select>
                        </div>
                    </form>
                </div>
            </div>
            <div class="h-80">
                <canvas id="attendanceChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Total Users Card -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="p-2 bg-gradient-to-br from-sky-100 to-sky-200 rounded-lg">
                            <i data-lucide="users" class="w-6 h-6 text-sky-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Users</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($totalUsers) }}</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.users.index') }}" class="inline-flex items-center text-sm font-medium text-sky-600 hover:text-sky-700">
                        Manage Users
                        <i data-lucide="chevron-right" class="w-4 h-4 ml-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Total Shifts Card -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="p-2 bg-gradient-to-br from-sky-100 to-sky-200 rounded-lg">
                            <i data-lucide="clock" class="w-6 h-6 text-sky-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Shifts</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($totalShifts) }}</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.shifts.index') }}" class="inline-flex items-center text-sm font-medium text-sky-600 hover:text-sky-700">
                        Manage Shifts
                        <i data-lucide="chevron-right" class="w-4 h-4 ml-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Total Schedules Card -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="p-2 bg-gradient-to-br from-sky-100 to-sky-200 rounded-lg">
                            <i data-lucide="calendar-days" class="w-6 h-6 text-sky-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Schedules</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($totalSchedules) }}</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.schedules.index') }}" class="inline-flex items-center text-sm font-medium text-sky-600 hover:text-sky-700">
                        Manage Schedules
                        <i data-lucide="chevron-right" class="w-4 h-4 ml-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Section -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <div class="flex items-center space-x-3 mb-6">
            <div class="p-2 bg-gradient-to-br from-sky-100 to-sky-200 rounded-lg">
                <i data-lucide="zap" class="w-6 h-6 text-sky-600"></i>
            </div>
            <div>
                <h2 class="text-lg font-semibold text-gray-800">Quick Actions</h2>
                <p class="text-sm text-gray-600">Manage your system efficiently</p>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('admin.users.create') }}" class="flex items-center p-4 bg-sky-50 border border-sky-200 rounded-xl hover:bg-sky-100 hover:shadow-md transition-all group">
                <div class="p-2 bg-sky-100 rounded-lg mr-3">
                    <svg class="w-5 h-5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    </svg>
                </div>
                <div>
                    <p class="font-medium text-gray-800 group-hover:text-sky-700">Add User</p>
                    <p class="text-xs text-gray-500">Create new employee</p>
                </div>
            </a>
            <a href="{{ route('admin.shifts.create') }}" class="flex items-center p-4 bg-sky-50 border border-sky-200 rounded-xl hover:bg-sky-100 hover:shadow-md transition-all group">
                <div class="p-2 bg-sky-100 rounded-lg mr-3">
                    <i data-lucide="clock" class="w-5 h-5 text-sky-600"></i>
                </div>
                <div>
                    <p class="font-medium text-gray-800 group-hover:text-sky-700">Create Shift</p>
                    <p class="text-xs text-gray-500">Add work schedule</p>
                </div>
            </a>
            <a href="{{ route('admin.schedules.create') }}" class="flex items-center p-4 bg-sky-50 border border-sky-200 rounded-xl hover:bg-sky-100 hover:shadow-md transition-all group">
                <div class="p-2 bg-sky-100 rounded-lg mr-3">
                    <i data-lucide="calendar-plus" class="w-5 h-5 text-sky-600"></i>
                </div>
                <div>
                    <p class="font-medium text-gray-800 group-hover:text-sky-700">New Schedule</p>
                    <p class="text-xs text-gray-500">Assign employee shifts</p>
                </div>
            </a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('attendanceChart').getContext('2d');
    
    // Data from controller
    const attendanceData = @json($attendanceData);
    const chartDates = @json($chartDates);
    
    // Extract data for chart
    const hadirData = attendanceData.map(item => item.hadir);
    const izinData = attendanceData.map(item => item.izin);
    const alphaData = attendanceData.map(item => item.alpha);
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: chartDates,
            datasets: [
                {
                    label: 'Hadir',
                    data: hadirData,
                    backgroundColor: '#10b981',
                    borderColor: '#059669',
                    borderWidth: 1,
                    borderRadius: 4,
                    borderSkipped: false,
                },
                {
                    label: 'Izin',
                    data: izinData,
                    backgroundColor: '#f59e0b',
                    borderColor: '#d97706',
                    borderWidth: 1,
                    borderRadius: 4,
                    borderSkipped: false,
                },
                {
                    label: 'Alpha',
                    data: alphaData,
                    backgroundColor: '#ef4444',
                    borderColor: '#dc2626',
                    borderWidth: 1,
                    borderRadius: 4,
                    borderSkipped: false,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: false,
                        padding: 20,
                        font: {
                            size: 13,
                            weight: '600'
                        },
                        generateLabels: function(chart) {
                            return chart.data.datasets.map((dataset, i) => ({
                                text: dataset.label,
                                fillStyle: dataset.backgroundColor,
                                strokeStyle: dataset.borderColor,
                                lineWidth: 0,
                                hidden: !chart.isDatasetVisible(i),
                                index: i
                            }));
                        }
                    }
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    backgroundColor: 'rgba(255, 255, 255, 0.95)',
                    titleColor: '#374151',
                    bodyColor: '#374151',
                    borderColor: '#e5e7eb',
                    borderWidth: 1,
                    cornerRadius: 8,
                    displayColors: true,
                    callbacks: {
                        title: function(context) {
                            return 'Tanggal ' + context[0].label;
                        },
                        label: function(context) {
                            return context.dataset.label + ': ' + context.parsed.y + ' orang';
                        }
                    }
                }
            },
            scales: {
                x: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Tanggal',
                        font: {
                            size: 13,
                            weight: '600'
                        },
                        color: '#374151'
                    },
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            size: 11
                        },
                        color: '#6b7280'
                    }
                },
                y: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Jumlah Orang',
                        font: {
                            size: 13,
                            weight: '600'
                        },
                        color: '#374151'
                    },
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)',
                        lineWidth: 1
                    },
                    ticks: {
                        font: {
                            size: 11
                        },
                        color: '#6b7280',
                        stepSize: 1
                    }
                }
            },
            interaction: {
                mode: 'index',
                intersect: false
            },
            elements: {
                bar: {
                    borderWidth: 1,
                }
            }
        }
    });
});
</script>
@endsection