@extends('layouts.admin')

@section('title', 'Activity Logs')

@section('content')
    <div class="min-h-screen bg-white">
        <div class="mx-auto px-4 py-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center space-x-4">
                    <!-- Ikon dengan background gradient -->
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-sky-100 to-sky-200 rounded-xl flex items-center justify-center shadow-sm">
                        <svg class="w-6 h-6 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M22 12h-4l-3 9L9 3l-3 9H2" />
                        </svg>
                    </div>

                    <!-- Judul dan Deskripsi -->
                    <div class="flex-1">
                        <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Activity Logs</h1>
                        <p class="text-gray-600">Monitor and track all system activities</p>
                    </div>

                    <!-- Status Monitoring -->
                    <div>
                        <span
                            class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M22 12h-4l-3 9L9 3l-3 9H2" />
                            </svg>
                            Live Monitoring
                        </span>
                    </div>
                </div>
            </div>


            <!-- Filters -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                <form method="GET" action="{{ route('admin.activity-logs.index') }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <!-- Log Type Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Log Type</label>
                            <select name="type"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="all" {{ request('type') == 'all' ? 'selected' : '' }}>All Types</option>
                                <option value="admin" {{ request('type') == 'admin' ? 'selected' : '' }}>Admin Activities
                                </option>
                                <option value="user" {{ request('type') == 'user' ? 'selected' : '' }}>User Activities
                                </option>
                                <option value="auth" {{ request('type') == 'auth' ? 'selected' : '' }}>Authentication
                                </option>
                            </select>
                        </div>

                        <!-- Sub Type Filter (for admin) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Admin Sub Type</label>
                            <select name="sub_type"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="all" {{ request('sub_type') == 'all' ? 'selected' : '' }}>All Admin Types
                                </option>
                                <option value="shifts" {{ request('sub_type') == 'shifts' ? 'selected' : '' }}>Shifts
                                    Management</option>
                                <option value="users" {{ request('sub_type') == 'users' ? 'selected' : '' }}>Users
                                    Management</option>
                                <option value="schedules" {{ request('sub_type') == 'schedules' ? 'selected' : '' }}>
                                    Schedules Management</option>
                                <option value="permissions" {{ request('sub_type') == 'permissions' ? 'selected' : '' }}>
                                    Permissions Management</option>
                            </select>
                        </div>

                        <!-- Date From -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Date From</label>
                            <input type="date" name="date_from" value="{{ request('date_from') }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <!-- Date To -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Date To</label>
                            <input type="date" name="date_to" value="{{ request('date_to') }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-4">
                        <!-- Search -->
                        <div class="flex-1">
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Search logs..."
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <!-- Buttons -->
                        <div class="flex gap-2">
                            <button type="submit"
                                class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                                <i data-lucide="search" class="w-4 h-4 inline mr-2"></i>
                                Filter
                            </button>
                            <a href="{{ route('admin.activity-logs.index') }}"
                                class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                                <i data-lucide="x" class="w-4 h-4 inline mr-2"></i>
                                Clear
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Tabs for different log types -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <!-- Tab Headers -->
                <div class="border-b border-gray-200">
                    <nav class="flex space-x-8 px-6" aria-label="Tabs">
                        @if (request('type') == 'all' || request('type') == 'admin')
                            @if (request('sub_type') == 'all' || request('sub_type') == 'shifts')
                                <a href="#shifts-logs"
                                    class="tab-link border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300"
                                    data-tab="shifts-logs">
                                    <i data-lucide="settings" class="w-4 h-4 inline mr-2"></i>
                                    Shifts Logs ({{ $shiftsLogs->total() ?? 0 }})
                                </a>
                            @endif
                            @if (request('sub_type') == 'all' || request('sub_type') == 'users')
                                <a href="#users-logs"
                                    class="tab-link border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300"
                                    data-tab="users-logs">
                                    <i data-lucide="users" class="w-4 h-4 inline mr-2"></i>
                                    Users Logs ({{ $usersLogs->total() ?? 0 }})
                                </a>
                            @endif
                            @if (request('sub_type') == 'all' || request('sub_type') == 'schedules')
                                <a href="#schedules-logs"
                                    class="tab-link border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300"
                                    data-tab="schedules-logs">
                                    <i data-lucide="calendar" class="w-4 h-4 inline mr-2"></i>
                                    Schedules Logs ({{ $schedulesLogs->total() ?? 0 }})
                                </a>
                            @endif
                            @if (request('sub_type') == 'all' || request('sub_type') == 'permissions')
                                <a href="#permissions-logs"
                                    class="tab-link border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300"
                                    data-tab="permissions-logs">
                                    <i data-lucide="shield-check" class="w-4 h-4 inline mr-2"></i>
                                    Permissions Logs ({{ $permissionsLogs->total() ?? 0 }})
                                </a>
                            @endif
                        @endif

                        @if (request('type') == 'all' || request('type') == 'user')
                            <a href="#user-logs"
                                class="tab-link border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300"
                                data-tab="user-logs">
                                <i data-lucide="user-check" class="w-4 h-4 inline mr-2"></i>
                                User Activities ({{ $userLogs->total() ?? 0 }})
                            </a>
                        @endif

                        @if (request('type') == 'all' || request('type') == 'auth')
                            <a href="#auth-logs"
                                class="tab-link border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300"
                                data-tab="auth-logs">
                                <i data-lucide="lock" class="w-4 h-4 inline mr-2"></i>
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
                        @if (request('sub_type') == 'all' || request('sub_type') == 'users')
                            @include('admin.activity-logs.partials.users-logs', ['logs' => $usersLogs])
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
                tabLinks[0].classList.add('border-indigo-500', 'text-indigo-600');
                tabLinks[0].classList.remove('border-transparent', 'text-gray-500');
            }

            tabLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();

                    // Remove active class from all tabs
                    tabLinks.forEach(tab => {
                        tab.classList.remove('border-indigo-500', 'text-indigo-600');
                        tab.classList.add('border-transparent', 'text-gray-500');
                    });

                    // Add active class to clicked tab
                    this.classList.add('border-indigo-500', 'text-indigo-600');
                    this.classList.remove('border-transparent', 'text-gray-500');

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
                    if (subType === 'users') return 'users-logs';
                    if (subType === 'schedules') return 'schedules-logs';
                    if (subType === 'permissions') return 'permissions-logs';
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
