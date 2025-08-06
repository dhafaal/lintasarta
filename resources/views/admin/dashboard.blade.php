@extends('layouts.app') {{-- sesuaikan dengan layout utama kamu --}}

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold text-gray-900  mb-6">Dashboard Admin</h1>

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-100 bg-green-900 text-green-800 text-green-200 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white p-6 rounded-xl border border-gray-300 shadow-sm hover:shadow-md transition-shadow">
                <h3 class="text-lg font-semibold text-gray-900  mb-2">Total Users</h3>
                <p class="text-2xl font-medium text-gray-700 ">{{ $totalUsers }}</p>
                <a href="{{ route('admin.users.index') }}" class="mt-4 inline-block text-blue-400 hover:underline">Kelola User</a>
            </div>

            <div class="bg-white p-6 rounded-xl border border-gray-300 shadow-sm hover:shadow-md transition-shadow">
                <h3 class="text-lg font-semibold text-gray-900  mb-2">Total Shifts</h3>
                <p class="text-2xl font-medium text-gray-700 ">{{ $totalShifts }}</p>
                <a href="{{ route('admin.shifts.index') }}" class="mt-4 inline-block text-blue-400 hover:underline">Kelola Shift</a>
            </div>

            <div class="bg-white p-6 rounded-xl border border-gray-300 shadow-sm hover:shadow-md transition-shadow">
                <h3 class="text-lg font-semibold text-gray-900  mb-2">Total Jadwal</h3>
                <p class="text-2xl font-medium text-gray-700 ">{{ $totalSchedules }}</p>
                <a href="{{ route('admin.schedules.index') }}" class="mt-4 inline-block text-blue-400 hover:underline">Kelola Jadwal</a>
            </div>

            <div class="bg-white p-6 rounded-xl border border-gray-300 shadow-sm hover:shadow-md transition-shadow">
                <h3 class="text-lg font-semibold text-gray-900  mb-2">Total Izin</h3>
                <p class="text-2xl font-medium text-gray-700 ">{{ $totalPermissions }}</p>
                <a href="{{ route('admin.permissions.index') }}" class="mt-4 inline-block text-blue-400 hover:underline">Kelola Izin</a>
            </div>
        </div>
    </div>
@endsection