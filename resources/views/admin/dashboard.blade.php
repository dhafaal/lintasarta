@extends('layouts.app') {{-- sesuaikan dengan layout utama kamu --}}

@section('content')
    <div class="container">
        <h1>Dashboard Admin</h1>

        @if(session('success'))
            <div style="color: green;">{{ session('success') }}</div>
        @endif

        <div style="display: flex; gap: 20px; margin-top: 20px;">
            <div style="border: 1px solid #ccc; padding: 20px; width: 200px;">
                <h3>Total Users</h3>
                <p>{{ $totalUsers }}</p>
                <a href="{{ route('admin.users.index') }}">Kelola User</a>
            </div>

            <div style="border: 1px solid #ccc; padding: 20px; width: 200px;">
                <h3>Total Shifts</h3>
                <p>{{ $totalShifts }}</p>
                <a href="{{ route('admin.shifts.index') }}">Kelola Shift</a>
            </div>

            <div style="border: 1px solid #ccc; padding: 20px; width: 200px;">
                <h3>Total Jadwal</h3>
                <p>{{ $totalSchedules }}</p>
                <a href="{{ route('admin.schedules.index') }}">Kelola Jadwal</a>
            </div>

            <div style="border: 1px solid #ccc; padding: 20px; width: 200px;">
                <h3>Total Izin</h3>
                <p>{{ $totalPermissions }}</p>
                <a href="{{ route('admin.permissions.index') }}">Kelola Izin</a>
            </div>
        </div>
    </div>
@endsection
