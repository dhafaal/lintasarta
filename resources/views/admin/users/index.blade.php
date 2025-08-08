@extends('layouts.app')

@section('title', 'Users Table')

@section('content')
    <x-section-content title="Users" subtitle="Manage all users data and Export everywhere">
        <x-slot:actions>
            <form method="GET" action="{{ route('admin.users.index') }}" id="filter-form"
                class="flex flex-wrap gap-4 items-center">
                {{-- Role Dropdown --}}
                <x-custom-dropdown name="role" :options="['All' => 'All', 'Admin' => 'Admin', 'Operator' => 'Operator', 'User' => 'User']" selected="{{ request('role', 'All') }}"
                    placeholder="Select role" />

                {{-- Shift Dropdown --}}
                <select name="shift" id="shift" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    @foreach (['All', 'Pagi', 'Siang', 'Malam'] as $shift)
                        <option value="{{ $shift }}" {{ request('shift', 'All') === $shift ? 'selected' : '' }}>
                            {{ $shift }}
                        </option>
                    @endforeach
                </select>

                {{-- Search --}}
                <input type="text" name="search" id="search" placeholder="Cari user..."
                    value="{{ request('search') }}" class="border border-gray-300 rounded-lg px-3 py-2 text-sm" />


                {{-- Buttons --}}
                <div class="flex gap-2">
                    <a href="{{ route('admin.users.exportPdf', request()->query()) }}" class="btn btn-outline">Export</a>
                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">Create User</a>
                </div>
            </form>
        </x-slot:actions>

        {{-- Include the users table partial --}}
        <div id="users-table-container">
            @include('admin.users.table')
        </div>

        <!-- Rest of the page (bulk delete form, etc.) -->
    </x-section-content>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('filter-form');
            const usersTableContainer = document.getElementById('users-table-container');

            form.addEventListener('change', handleFilterChange);
            document.getElementById('search').addEventListener('input', debounce(handleFilterChange, 500));

            function handleFilterChange(e) {
                usersTableContainer.innerHTML = '<div class="text-center py-10">Loading...</div>';
                const formData = new FormData(form);
                const params = new URLSearchParams(formData).toString();
                const url = form.action + '?' + params;

                fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.text())
                    .then(html => {
                        usersTableContainer.innerHTML = html;
                        initSelectAllCheckbox();
                    })
                    .catch(err => {
                        usersTableContainer.innerHTML =
                            '<div class="text-center text-red-600 py-10">Failed to load data.</div>';
                        console.error('Fetch error:', err);
                    });
            }


            function initSelectAllCheckbox() {
                const selectAllCheckbox = document.getElementById('select-all');
                if (selectAllCheckbox) {
                    selectAllCheckbox.addEventListener('click', () => {
                        const checkboxes = document.querySelectorAll('input[name="selected_users[]"]');
                        checkboxes.forEach(cb => cb.checked = selectAllCheckbox.checked);
                    });
                }
            }

            function debounce(fn, delay) {
                let timer;
                return function(...args) {
                    clearTimeout(timer);
                    timer = setTimeout(() => fn.apply(this, args), delay);
                };
            }
            initSelectAllCheckbox();

        });
    </script>
@endsection
