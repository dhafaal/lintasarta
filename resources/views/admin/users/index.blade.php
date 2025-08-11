@extends('layouts.app')

@section('title', 'User Management')

@section('content')
    <x-section-content title="User Management" subtitle="Manage your application users">
        <x-slot:actions>
            <div class="flex flex-wrap items-center gap-3">
                <form method="GET" action="{{ route('admin.users.index') }}" id="filter-form"
                      class="flex flex-wrap items-center gap-3">

                    {{-- Role Filter --}}
                    <select name="role" class="border border-gray-300 rounded-lg py-1.5 px-4">
                        <option value="all" {{ request('role', 'all') === 'all' ? 'selected' : '' }}>
                            All Roles
                        </option>
                        @foreach (['Admin', 'Operator', 'User'] as $role)
                            <option value="{{ strtolower($role) }}"
                                {{ strtolower(request('role', 'all')) === strtolower($role) ? 'selected' : '' }}>
                                {{ $role }}
                            </option>
                        @endforeach
                    </select>

                    {{-- Shift Filter --}}
                    <select name="shift" class="border border-gray-300 rounded-lg py-1.5 px-4">
                        <option value="all" {{ request('shift', 'all') === 'all' ? 'selected' : '' }}>
                            All Shifts
                        </option>
                        @foreach ($shifts as $shift)
                            <option value="{{ strtolower($shift) }}"
                                {{ strtolower(request('shift', 'all')) === strtolower($shift) ? 'selected' : '' }}>
                                {{ $shift }}
                            </option>
                        @endforeach
                    </select>

                    {{-- Search --}}
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Search users..."
                           class="border border-gray-300 rounded-lg p-2 text-sm text-gray-700 
                                  focus:ring-2 focus:ring-blue-200 focus:border-blue-600">

                    {{-- Export PDF --}}
                    <a href="{{ route('admin.users.exportPdf', request()->query()) }}"
                       class="btn btn-outline inline-flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H3a2 2 0 01-2-2V3a2 2 0 012-2h18a2 2 0 012 2v16a2 2 0 01-2 2z"/>
                        </svg>
                        Export PDF
                    </a>

                    {{-- Create User --}}
                    <a href="{{ route('admin.users.create') }}"
                       class="btn btn-primary inline-flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 4v16m8-8H4"/>
                        </svg>
                        Create User
                    </a>
                </form>
            </div>
        </x-slot:actions>

        {{-- Table --}}
        <div id="users-table-container" class="bg-white rounded-2xl shadow-md border border-gray-300">
            <div id="loading" class="hidden text-center py-4 text-gray-500 text-sm">Loading...</div>
            @include('admin.users.table', ['users' => $users])
        </div>
    </x-section-content>

    {{-- Script --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('filter-form');
            const tableContainer = document.getElementById('users-table-container');
            const loading = document.getElementById('loading');

            form.querySelectorAll('select').forEach(el =>
                el.addEventListener('change', loadData)
            );
            form.querySelector('input[name="search"]').addEventListener(
                'input',
                debounce(loadData, 400)
            );

            function loadData() {
                loading.classList.remove('hidden');
                tableContainer.querySelector('#users-table-wrapper')?.classList.add('opacity-50');

                const params = new URLSearchParams(new FormData(form)).toString();
                fetch(`${form.action}?${params}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(res => res.text())
                    .then(html => {
                        tableContainer.innerHTML = html;
                        loading.classList.add('hidden');
                    })
                    .catch(err => {
                        console.error('Error loading data:', err);
                        loading.classList.add('hidden');
                        tableContainer.querySelector('#users-table-wrapper')?.classList.remove('opacity-50');
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
