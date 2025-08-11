@extends('layouts.app')

@section('content')
<div class="p-6">
    <h1 class="text-xl font-bold mb-4">User Management</h1>

    <form method="GET" action="{{ route('admin.users.index') }}" id="filter-form" class="flex gap-4 mb-4">
        <select name="role" class="border rounded p-2">
            @foreach (['All', 'Admin', 'Operator', 'User'] as $role)
                <option value="{{ strtolower($role) }}" {{ strtolower(request('role', 'all')) === strtolower($role) ? 'selected' : '' }}>
                    {{ $role }}
                </option>
            @endforeach
        </select>

        <select name="shift" class="border rounded p-2">
            <option value="all" {{ request('shift', 'all') === 'all' ? 'selected' : '' }}>All Shifts</option>
            @foreach ($shifts as $shift)
                <option value="{{ strtolower($shift) }}" {{ strtolower(request('shift', 'all')) === strtolower($shift) ? 'selected' : '' }}>
                    {{ $shift }}
                </option>
            @endforeach
        </select>

        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search user..."
               class="border rounded p-2 flex-1">

        <a href="{{ route('admin.users.exportPdf', request()->query()) }}" class="bg-gray-200 p-2 rounded">Export PDF</a>
        <a href="{{ route('admin.users.create') }}" class="bg-blue-500 text-white p-2 rounded">Create</a>
    </form>

    <div id="users-table-container">
        @include('admin.users.table', ['users' => $users])
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('filter-form');
    const tableContainer = document.getElementById('users-table-container');

    form.querySelectorAll('select').forEach(el => el.addEventListener('change', loadData));
    form.querySelector('input[name="search"]').addEventListener('input', debounce(loadData, 400));

    function loadData() {
        const params = new URLSearchParams(new FormData(form)).toString();
        fetch(form.action + '?' + params, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(res => res.text())
            .then(html => tableContainer.innerHTML = html);
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
