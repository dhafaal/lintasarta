@extends('layouts.admin')

@section('title', 'Activity Logs')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Activity Logs</h1>
            <p class="mt-1 text-sm text-gray-600">Monitor and track all system activities</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                <i data-lucide="activity" class="w-3 h-3 mr-1"></i>
                Live Monitoring
            </span>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Filter Logs</h3>
            <form method="GET" action="{{ route('admin.activity-logs.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Log Type Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Log Type</label>
                        <select name="type" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-sky-500 focus:border-sky-500 sm:text-sm">
                            <option value="all" {{ request('type') == 'all' ? 'selected' : '' }}>All Types</option>
                            <option value="admin" {{ request('type') == 'admin' ? 'selected' : '' }}>Admin Activities</option>
                            <option value="user" {{ request('type') == 'user' ? 'selected' : '' }}>User Activities</option>
                            <option value="auth" {{ request('type') == 'auth' ? 'selected' : '' }}>Authentication</option>
                        </select>
                    </div>

                    <!-- Admin Sub Type Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Admin Sub Type</label>
                        <select name="sub_type" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-sky-500 focus:border-sky-500 sm:text-sm">
                            <option value="all" {{ request('sub_type') == 'all' ? 'selected' : '' }}>All Admin Types</option>
                            <option value="shifts" {{ request('sub_type') == 'shifts' ? 'selected' : '' }}>Shifts Management</option>
                            <option value="users" {{ request('sub_type') == 'users' ? 'selected' : '' }}>Users Management</option>
                            <option value="schedules" {{ request('sub_type') == 'schedules' ? 'selected' : '' }}>Schedules Management</option>
                            <option value="permissions" {{ request('sub_type') == 'permissions' ? 'selected' : '' }}>Permissions Management</option>
                        </select>
                    </div>

                    <!-- Date From -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date From</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}" 
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-sky-500 focus:border-sky-500 sm:text-sm">
                    </div>

                    <!-- Date To -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date To</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}" 
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-sky-500 focus:border-sky-500 sm:text-sm">
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-4">
                    <!-- Search -->
                    <div class="flex-1">
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Search logs..." 
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-sky-500 focus:border-sky-500 sm:text-sm">
                    </div>
                    
                    <!-- Buttons -->
                    <div class="flex gap-2">
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-sky-600 hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500">
                            <i data-lucide="search" class="w-4 h-4 mr-2"></i>
                            Filter
                        </button>
                        <a href="{{ route('admin.activity-logs.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500">
                            <i data-lucide="x" class="w-4 h-4 mr-2"></i>
                            Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                @if(request('type') == 'all' || request('type') == 'admin')
                    @if(request('sub_type') == 'all' || request('sub_type') == 'shifts')
                        <button onclick="showTab('shifts-logs')" class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="shifts-logs">
                            <i data-lucide="settings" class="w-4 h-4 inline mr-2"></i>
                            Shifts ({{ $shiftsLogs->total() ?? 0 }})
                        </button>
                    @endif
                    @if(request('sub_type') == 'all' || request('sub_type') == 'users')
                        <button onclick="showTab('users-logs')" class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="users-logs">
                            <i data-lucide="users" class="w-4 h-4 inline mr-2"></i>
                            Users ({{ $usersLogs->total() ?? 0 }})
                        </button>
                    @endif
                    @if(request('sub_type') == 'all' || request('sub_type') == 'schedules')
                        <button onclick="showTab('schedules-logs')" class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="schedules-logs">
                            <i data-lucide="calendar" class="w-4 h-4 inline mr-2"></i>
                            Schedules ({{ $schedulesLogs->total() ?? 0 }})
                        </button>
                    @endif
                    @if(request('sub_type') == 'all' || request('sub_type') == 'permissions')
                        <button onclick="showTab('permissions-logs')" class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="permissions-logs">
                            <i data-lucide="shield-check" class="w-4 h-4 inline mr-2"></i>
                            Permissions ({{ $permissionsLogs->total() ?? 0 }})
                        </button>
                    @endif
                @endif
                
                @if(request('type') == 'all' || request('type') == 'user')
                    <button onclick="showTab('user-logs')" class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="user-logs">
                        <i data-lucide="user-check" class="w-4 h-4 inline mr-2"></i>
                        User Activities ({{ $userLogs->total() ?? 0 }})
                    </button>
                @endif
                
                @if(request('type') == 'all' || request('type') == 'auth')
                    <button onclick="showTab('auth-logs')" class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="auth-logs">
                        <i data-lucide="lock" class="w-4 h-4 inline mr-2"></i>
                        Auth ({{ $authLogs->total() ?? 0 }})
                    </button>
                @endif
            </nav>
        </div>

        <!-- Tab Contents -->
        <div class="p-6">
            @if(request('type') == 'all' || request('type') == 'admin')
                @if(request('sub_type') == 'all' || request('sub_type') == 'shifts')
                    @include('admin.logs.partials.shifts-logs', ['logs' => $shiftsLogs])
                @endif
                @if(request('sub_type') == 'all' || request('sub_type') == 'users')
                    @include('admin.logs.partials.users-logs', ['logs' => $usersLogs])
                @endif
                @if(request('sub_type') == 'all' || request('sub_type') == 'schedules')
                    @include('admin.logs.partials.schedules-logs', ['logs' => $schedulesLogs])
                @endif
                @if(request('sub_type') == 'all' || request('sub_type') == 'permissions')
                    @include('admin.logs.partials.permissions-logs', ['logs' => $permissionsLogs])
                @endif
            @endif
            
            @if(request('type') == 'all' || request('type') == 'user')
                @include('admin.logs.partials.user-logs', ['logs' => $userLogs])
            @endif
            
            @if(request('type') == 'all' || request('type') == 'auth')
                @include('admin.logs.partials.auth-logs', ['logs' => $authLogs])
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize first tab as active
    const firstTab = document.querySelector('.tab-button');
    if (firstTab) {
        showTab(firstTab.getAttribute('data-tab'));
        firstTab.classList.remove('border-transparent', 'text-gray-500');
        firstTab.classList.add('border-sky-500', 'text-sky-600');
    }
});

function showTab(tabId) {
    // Hide all tab contents
    const allTabs = document.querySelectorAll('.tab-content');
    allTabs.forEach(tab => {
        tab.style.display = 'none';
    });
    
    // Remove active class from all tab buttons
    const allButtons = document.querySelectorAll('.tab-button');
    allButtons.forEach(button => {
        button.classList.remove('border-sky-500', 'text-sky-600');
        button.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Show selected tab content
    const selectedTab = document.getElementById(tabId);
    if (selectedTab) {
        selectedTab.style.display = 'block';
    }
    
    // Add active class to clicked button
    const clickedButton = document.querySelector(`[data-tab="${tabId}"]`);
    if (clickedButton) {
        clickedButton.classList.remove('border-transparent', 'text-gray-500');
        clickedButton.classList.add('border-sky-500', 'text-sky-600');
    }
}

function toggleDetails(elementId) {
    const element = document.getElementById(elementId);
    if (element) {
        element.classList.toggle('hidden');
    }
}
</script>
@endsection
