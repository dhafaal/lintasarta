@extends('layouts.app')

@section('title', 'User Management')

@section('content')
<div class="p-6 md:p-8">
    <div class="max-w-5xl mx-auto">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-800">User Management</h1>
            <a href="{{ route('admin.users.create') }}"
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-800 transition text-sm font-semibold shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Create User
            </a>
        </div>

        <!-- Filter Form -->
        <form method="GET" action="{{ route('admin.users.index') }}" id="filter-form" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8 bg-white p-6 rounded-2xl shadow-md border border-gray-300">
            <!-- Role Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Role</label>
                <select name="role" class="w-full border-gray-300 rounded-lg p-3 text-sm text-gray-700 focus:ring-2 focus:ring-blue-200 focus:border-blue-600 transition bg-white">
                    @foreach (['All', 'Admin', 'Operator', 'User'] as $role)
                        <option value="{{ strtolower($role) }}" {{ strtolower(request('role', 'all')) === strtolower($role) ? 'selected' : '' }}>
                            {{ $role }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Shift Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Shift</label>
                <select name="shift" class="w-full border-gray-300 rounded-lg p-3 text-sm text-gray-700 focus:ring-2 focus:ring-blue-200 focus:border-blue-600 transition bg-white">
                    <option value="all" {{ request('shift', 'all') === 'all' ? 'selected' : '' }}>All Shifts</option>
                    @foreach ($shifts as $shift)
                        <option value="{{ strtolower($shift) }}" {{ strtolower(request('shift', 'all')) === strtolower($shift) ? 'selected' : '' }}>
                            {{ $shift }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Search Input -->
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search users..."
                       class="w-full border-gray-300 rounded-lg p-3 text-sm text-gray-700 focus:ring-2 focus:ring-blue-200 focus:border-blue-600 transition">
            </div>

            <!-- Export Button -->
            <div class="flex items-end">
                <a href="{{ route('admin.users.exportPdf', request()->query()) }}"
                   class="inline-flex items-center px-4 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm font-semibold w-full justify-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H3a2 2 0 01-2-2V3a2 2 0 012-2h18a2 2 0 012 2v16a2 2 0 01-2 2z"></path>
                    </svg>
                    Export PDF
                </a>
            </div>
        </form>

        <!-- Users Table -->
        <div id="users-table-container" class="bg-white rounded-2xl shadow-md border border-gray-300">
            <div id="loading" class="hidden text-center py-4 text-gray-500 text-sm">Loading...</div>
            @include('admin.users.table', ['users' => $users])
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('filter-form');
    const tableContainer = document.getElementById('users-table-container');
    const loading = document.getElementById('loading');

    form.querySelectorAll('select').forEach(el => el.addEventListener('change', loadData));
    form.querySelector('input[name="search"]').addEventListener('input', debounce(loadData, 400));

    function loadData() {
        loading.classList.remove('hidden');
        tableContainer.querySelector('#users-table-wrapper').classList.add('opacity-50');
        const params = new URLSearchParams(new FormData(form)).toString();
        fetch(form.action + '?' + params, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(res => res.text())
            .then(html => {
                tableContainer.innerHTML = html;
                loading.classList.add('hidden');
            })
            .catch(err => {
                console.error('Error loading data:', err);
                loading.classList.add('hidden');
                tableContainer.querySelector('#users-table-wrapper').classList.remove('opacity-50');
            });
    }

    function debounce(fn, delay) {
        let timeout;
        return (...args) => {
            clearTimeout(timeout);
            timeout = setTimeout(() => fn.apply(this, args), delay);
        };
    }
});
</script>
@endsection