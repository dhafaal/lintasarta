@extends('layouts.admin')

@section('title', 'Activity Logs')

@section('content')
    <div class="min-h-screen bg-whiteo">
        <div class="mx-auto px-6 py-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <!-- Icon dengan background gradient -->
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-sky-500 to-sky-600 bg-opacity-30 rounded-xl flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-activity text-sky-50 text-center">
                                <path d="M22 12h-4l-3 9L9 3l-3 9H2" />
                            </svg>
                        </div>

                        <!-- Judul dan Deskripsi -->
                        <div class="flex-1">
                            <h1 class="text-3xl font-bold text-gray-700 tracking-tight">Activity Logs</h1>
                            <p class="text-gray-500 mt-1">Monitor and track all system activities</p>
                        </div>
                    </div>

                    <!-- Status Monitoring -->
                    <div class="flex items-center space-x-4">
                        <div class="text-right">
                            <div class="text-sm text-gray-500">System Status</div>
                            <div class="flex items-center space-x-2">
                                <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                                <span class="text-sm font-medium text-green-600">Live Monitoring</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Quick Summary -->
            <div class="mb-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-xs uppercase tracking-wide text-gray-500">Shifts</div>
                                <div class="text-2xl font-bold text-gray-900">{{ $shiftsLogs->total() ?? 0 }}</div>
                            </div>
                            <div class="w-10 h-10 rounded-lg bg-sky-50 flex items-center justify-center">
                                <i data-lucide="clock" class="w-5 h-5 text-sky-600"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-xs uppercase tracking-wide text-gray-500">Schedules</div>
                                <div class="text-2xl font-bold text-gray-900">{{ $schedulesLogs->total() ?? 0 }}</div>
                            </div>
                            <div class="w-10 h-10 rounded-lg bg-sky-50 flex items-center justify-center">
                                <i data-lucide="calendar" class="w-5 h-5 text-sky-600"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-xs uppercase tracking-wide text-gray-500">Attendances</div>
                                <div class="text-2xl font-bold text-gray-900">{{ $attendanceLogs->total() ?? 0 }}</div>
                            </div>
                            <div class="w-10 h-10 rounded-lg bg-sky-50 flex items-center justify-center">
                                <i data-lucide="user-check" class="w-5 h-5 text-sky-600"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-xs uppercase tracking-wide text-gray-500">Permissions</div>
                                <div class="text-2xl font-bold text-gray-900">{{ $permissionsLogs->total() ?? 0 }}</div>
                            </div>
                            <div class="w-10 h-10 rounded-lg bg-sky-50 flex items-center justify-center">
                                <i data-lucide="shield-check" class="w-5 h-5 text-sky-600"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden mb-8">
                <!-- Filter Header -->
                <div class="bg-gradient-to-r from-sky-500 to-sky-600 px-8 py-6">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-white bg-opacity-20 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-filter text-white">
                                <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-semibold text-white">Filter Activity Logs</h2>
                            <p class="text-sky-100 mt-1">Customize your log view with advanced filters</p>
                        </div>
                    </div>
                </div>

                <div class="p-8">
                    <form method="GET" action="{{ route('admin.activity-logs.index') }}" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            <!-- Log Type Filter -->
                            <div class="space-y-2">
                                <label class="block text-sm font-bold text-gray-800">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="lucide lucide-layers inline mr-2 text-sky-600">
                                        <path d="M12 2 2 7l10 5 10-5-10-5Z" />
                                        <path d="M2 17l10 5 10-5" />
                                        <path d="M2 12l10 5 10-5" />
                                    </svg>
                                    Log Type
                                </label>
                                <select name="type"
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-sky-100 focus:border-sky-500 bg-gray-50 focus:bg-white transition-all duration-200">
                                    <option value="all" {{ request('type') == 'all' ? 'selected' : '' }}>All Types
                                    </option>
                                    <option value="admin" {{ request('type') == 'admin' ? 'selected' : '' }}>Admin
                                        Activities</option>
                                    <option value="user" {{ request('type') == 'user' ? 'selected' : '' }}>User Activities
                                    </option>
                                    <option value="auth" {{ request('type') == 'auth' ? 'selected' : '' }}>Authentication
                                    </option>
                                </select>
                            </div>

                            <!-- Sub Type Filter -->
                            <div class="space-y-2">
                                <label class="block text-sm font-bold text-gray-800">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="lucide lucide-git-branch inline mr-2 text-sky-600">
                                        <line x1="6" x2="6" y1="3" y2="15" />
                                        <circle cx="18" cy="6" r="3" />
                                        <circle cx="6" cy="18" r="3" />
                                        <path d="M18 9a9 9 0 0 1-9 9" />
                                    </svg>
                                    Admin Sub Type
                                </label>
                                <select name="sub_type"
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-sky-100 focus:border-sky-500 bg-gray-50 focus:bg-white transition-all duration-200">
                                    <option value="all" {{ request('sub_type') == 'all' ? 'selected' : '' }}>All Admin
                                        Types</option>
                                    <option value="shifts" {{ request('sub_type') == 'shifts' ? 'selected' : '' }}>Shifts
                                        Management</option>
                                    <option value="attendances"
                                        {{ request('sub_type') == 'attendances' ? 'selected' : '' }}>Attendances Management
                                    </option>
                                    <option value="schedules" {{ request('sub_type') == 'schedules' ? 'selected' : '' }}>
                                        Schedules Management</option>
                                    <option value="permissions"
                                        {{ request('sub_type') == 'permissions' ? 'selected' : '' }}>Permissions Management
                                    </option>
                                    <option value="locations" {{ request('sub_type') == 'locations' ? 'selected' : '' }}>
                                        Locations Management</option>
                                </select>
                            </div>

                            <!-- Date From -->
                            <div class="space-y-2">
                                <label class="block text-sm font-bold text-gray-800">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="lucide lucide-calendar-days inline mr-2 text-sky-600">
                                        <path d="M8 2v4" />
                                        <path d="M16 2v4" />
                                        <rect width="18" height="18" x="3" y="4" rx="2" />
                                        <path d="M3 10h18" />
                                        <path d="M8 14h.01" />
                                        <path d="M12 14h.01" />
                                        <path d="M16 14h.01" />
                                        <path d="M8 18h.01" />
                                        <path d="M12 18h.01" />
                                        <path d="M16 18h.01" />
                                    </svg>
                                    Date From
                                </label>
                                <input type="date" name="date_from" value="{{ request('date_from') }}"
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-sky-100 focus:border-sky-500 bg-gray-50 focus:bg-white transition-all duration-200">
                            </div>

                            <!-- Date To -->
                            <div class="space-y-2">
                                <label class="block text-sm font-bold text-gray-800">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="lucide lucide-calendar-check inline mr-2 text-sky-600">
                                        <path d="M8 2v4" />
                                        <path d="M16 2v4" />
                                        <rect width="18" height="18" x="3" y="4" rx="2" />
                                        <path d="M3 10h18" />
                                        <path d="M9 16l2 2 4-4" />
                                    </svg>
                                    Date To
                                </label>
                                <input type="date" name="date_to" value="{{ request('date_to') }}"
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-sky-100 focus:border-sky-500 bg-gray-50 focus:bg-white transition-all duration-200">
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-4">
                            <!-- Search -->
                            <div class="flex-1 space-y-2">
                                <label class="block text-sm font-bold text-gray-800">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="lucide lucide-search inline mr-2 text-sky-600">
                                        <circle cx="11" cy="11" r="8" />
                                        <path d="M21 21l-4.35-4.35" />
                                    </svg>
                                    Search Logs
                                </label>
                                <input type="text" name="search" value="{{ request('search') }}"
                                    placeholder="Search logs, descriptions, users..."
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-sky-100 focus:border-sky-500 bg-gray-50 focus:bg-white transition-all duration-200">
                            </div>

                            <!-- Buttons -->
                            <div class="flex items-end space-x-3">
                                <button type="submit"
                                    class="inline-flex items-center px-6 py-3 bg-sky-600 hover:bg-sky-700 text-white font-semibold rounded-xl transition-all duration-200   shadow-lg hover:shadow-xl">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search mr-2">
                                        <circle cx="11" cy="11" r="8" />
                                        <path d="M21 21l-4.35-4.35" />
                                    </svg>
                                    Apply Filters
                                </button>

                                @if (request()->hasAny(['type', 'sub_type', 'search', 'date_from', 'date_to']))
                                    <a href="{{ route('admin.activity-logs.index') }}"
                                        class="inline-flex items-center px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-all duration-200  ">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x mr-2">
                                            <path d="M18 6 6 18" />
                                            <path d="m6 6 12 12" />
                                        </svg>
                                        Clear Filters
                                    </a>
                                @endif
                            </div>
                        </div>
                </div>
                </form>
            </div>

            <!-- Tabs for different log types -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                <!-- Tab Headers -->
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 border-b-2 border-gray-200 sticky top-0 z-10">
                    <nav class="flex flex-wrap gap-1 px-8 py-3" aria-label="Tabs">
                        @if (request('type') == 'all' || request('type') == 'admin')
                            @if (request('sub_type') == 'all' || request('sub_type') == 'shifts')
                                <a href="#shifts-logs"
                                    class="tab-link inline-flex items-center px-4 py-2 text-sm font-semibold rounded-lg transition-all duration-200 text-gray-600 hover:text-sky-700 hover:bg-sky-50 border border-transparent"
                                    data-tab="shifts-logs">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock mr-2">
                                        <circle cx="12" cy="12" r="10" />
                                        <polyline points="12 6 12 12 16 14" />
                                    </svg>
                                    Shifts Logs ({{ $shiftsLogs->total() ?? 0 }})
                                </a>
                            @endif
                            @if (request('sub_type') == 'all' || request('sub_type') == 'schedules')
                                <a href="#schedules-logs"
                                    class="tab-link inline-flex items-center px-4 py-3 text-sm font-semibold rounded-lg transition-all duration-200 text-gray-600 hover:text-sky-700 hover:bg-sky-50"
                                    data-tab="schedules-logs">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="lucide lucide-calendar mr-2">
                                        <path d="M8 2v4" />
                                        <path d="M16 2v4" />
                                        <rect width="18" height="18" x="3" y="4" rx="2" />
                                        <path d="M3 10h18" />
                                    </svg>
                                    Schedules Logs ({{ $schedulesLogs->total() ?? 0 }})
                                </a>
                            @endif
                            @if (request('sub_type') == 'all' || request('sub_type') == 'attendances')
                                <a href="#attendances-logs"
                                    class="tab-link inline-flex items-center px-4 py-3 text-sm font-semibold rounded-lg transition-all duration-200 text-gray-600 hover:text-sky-700 hover:bg-sky-50"
                                    data-tab="attendances-logs">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="lucide lucide-user-check mr-2">
                                        <path d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                        <circle cx="9" cy="7" r="4" />
                                        <polyline points="16 11 18 13 22 9" />
                                    </svg>
                                    Attendances Logs ({{ $attendanceLogs->total() ?? 0 }})
                                </a>
                            @endif
                            @if (request('sub_type') == 'all' || request('sub_type') == 'permissions')
                                <a href="#permissions-logs"
                                    class="tab-link inline-flex items-center px-4 py-3 text-sm font-semibold rounded-lg transition-all duration-200 text-gray-600 hover:text-sky-700 hover:bg-sky-50"
                                    data-tab="permissions-logs">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="lucide lucide-shield-check mr-2">
                                        <path
                                            d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z" />
                                        <path d="M9 12l2 2 4-4" />
                                    </svg>
                                    Permissions Logs ({{ $permissionsLogs->total() ?? 0 }})
                                </a>
                            @endif
                            @if (request('sub_type') == 'all' || request('sub_type') == 'locations')
                                <a href="#locations-logs"
                                    class="tab-link inline-flex items-center px-4 py-3 text-sm font-semibold rounded-lg transition-all duration-200 text-gray-600 hover:text-sky-700 hover:bg-sky-50"
                                    data-tab="locations-logs">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="lucide lucide-map-pin mr-2">
                                        <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
                                        <circle cx="12" cy="10" r="3" />
                                    </svg>
                                    Locations Logs ({{ $locationLogs->total() ?? 0 }})
                                </a>
                            @endif
                        @endif

                        @if (request('type') == 'all' || request('type') == 'user')
                            <a href="#user-logs"
                                class="tab-link inline-flex items-center px-4 py-3 text-sm font-semibold rounded-lg transition-all duration-200 text-gray-600 hover:text-sky-700 hover:bg-sky-50"
                                data-tab="user-logs">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user-check mr-2">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                    <circle cx="9" cy="7" r="4" />
                                    <polyline points="16 11 18 13 22 9" />
                                </svg>
                                User Activities ({{ $userLogs->total() ?? 0 }})
                            </a>
                        @endif

                        @if (request('type') == 'all' || request('type') == 'auth')
                            <a href="#auth-logs"
                                class="tab-link inline-flex items-center px-4 py-3 text-sm font-semibold rounded-lg transition-all duration-200 text-gray-600 hover:text-sky-700 hover:bg-sky-50"
                                data-tab="auth-logs">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-lock mr-2">
                                    <rect width="18" height="11" x="3" y="11" rx="2" ry="2" />
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                </svg>
                                Auth Logs ({{ $authLogs->total() ?? 0 }})
                            </a>
                        @endif
                    </nav>
                </div>

                <!-- Tab Contents -->
                <div class="p-6">
                    @if (request('type') == 'all' || request('type') == 'admin')
                        @if (request('sub_type') == 'all' || request('sub_type') == 'shifts')
                            @include('admin.activity-logs.partials.shifts-logs', ['logs' => $shiftsLogs])
                        @endif
                        @if (request('sub_type') == 'all' || request('sub_type') == 'attendances')
                            @include('admin.activity-logs.partials.attendances-logs', [
                                'logs' => $attendanceLogs,
                            ])
                        @endif
                        @if (request('sub_type') == 'all' || request('sub_type') == 'schedules')
                            @include('admin.activity-logs.partials.schedules-logs', [
                                'logs' => $schedulesLogs,
                            ])
                        @endif
                        @if (request('sub_type') == 'all' || request('sub_type') == 'permissions')
                            @include('admin.activity-logs.partials.permissions-logs', [
                                'logs' => $permissionsLogs,
                            ])
                        @endif
                        @if (request('sub_type') == 'all' || request('sub_type') == 'locations')
                            @include('admin.activity-logs.partials.location-logs', [
                                'locationLogs' => $locationLogs,
                            ])
                        @endif
                    @endif

                    @if (request('type') == 'all' || request('type') == 'user')
                        @include('admin.activity-logs.partials.user-logs', ['logs' => $userLogs])
                    @endif

                    @if (request('type') == 'all' || request('type') == 'auth')
                        @include('admin.activity-logs.partials.auth-logs', ['logs' => $authLogs])
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tab functionality
            const tabLinks = document.querySelectorAll('.tab-link');
            const tabContents = document.querySelectorAll('.tab-content');

            // Set first tab as active by default
            if (tabLinks.length > 0) {
                tabLinks[0].classList.add('bg-sky-100', 'text-sky-700', 'border-sky-200');
                tabLinks[0].classList.remove('text-gray-600', 'hover:text-sky-700', 'hover:bg-sky-50');
            }

            tabLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();

                    // Remove active class from all tabs
                    tabLinks.forEach(tab => {
                        tab.classList.remove('bg-sky-100', 'text-sky-700',
                        'border-sky-200');
                        tab.classList.add('text-gray-600', 'hover:text-sky-700',
                            'hover:bg-sky-50');
                    });

                    // Add active class to clicked tab
                    this.classList.add('bg-sky-100', 'text-sky-700', 'border-sky-200');
                    this.classList.remove('text-gray-600', 'hover:text-sky-700', 'hover:bg-sky-50');

                    // Show corresponding content
                    const targetTab = this.getAttribute('data-tab');
                    tabContents.forEach(content => {
                        content.style.display = 'none';
                    });

                    const targetContent = document.getElementById(targetTab);
                    if (targetContent) {
                        targetContent.style.display = 'block';
                    }

                    // Update URL hash without reloading
                    history.replaceState(null, '', `#${targetTab}`);
                });
            });

            // On load, activate tab based on URL hash or filters
            const hash = window.location.hash.replace('#', '');
            const defaultTabByFilter = (() => {
                const type = '{{ request('type') }}';
                const subType = '{{ request('sub_type') }}';
                if (type === 'auth') return 'auth-logs';
                if (type === 'user') return 'user-logs';
                if (type === 'admin' || type === 'all') {
                    if (subType === 'attendances') return 'attendances-logs';
                    if (subType === 'schedules') return 'schedules-logs';
                    if (subType === 'permissions') return 'permissions-logs';
                    if (subType === 'locations') return 'locations-logs';
                    if (subType === 'shifts') return 'shifts-logs';
                }
                return null;
            })();

            const target = hash || defaultTabByFilter;
            if (target) {
                const link = document.querySelector(`.tab-link[data-tab="${target}"]`);
                if (link) link.click();
            }
        });

        // Function to toggle details
        function toggleDetails(elementId) {
            const element = document.getElementById(elementId);
            if (element) {
                if (element.classList.contains('hidden')) {
                    element.classList.remove('hidden');
                } else {
                    element.classList.add('hidden');
                }
            }
        }
    </script>
@endsection
