@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Schedule List</h1>
    <a href="{{ route('admin.schedules.create') }}" class="btn btn-primary mb-3">+ Add Schedule</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>User</th>
                <th>Shift</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($schedules as $schedule)
            <tr>
                <td>{{ $schedule->user->name }}</td>
                <td>{{ $schedule->shift->name }}</td>
                <td>{{ $schedule->schedule_date }}</td>
                <td>
                    <a href="{{ route('admin.schedules.edit', $schedule) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('admin.schedules.destroy', $schedule) }}" method="POST" style="display:inline-block;">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
