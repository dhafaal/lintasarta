@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Schedule List</h2>

    <a href="{{ route('admin.schedules.create') }}" class="btn btn-primary mb-3">+ Add Schedule</a>

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
            @foreach ($schedules as $schedule)
            <tr>
                <td>{{ $schedule->user->name }}</td>
                <td>{{ $schedule->shift->name }}</td>
                <td>{{ \Carbon\Carbon::parse($schedule->schedule_date)->format('d-m-Y') }}</td>
                <td>
                    <a href="{{ route('admin.schedules.edit', $schedule->id) }}" class="btn btn-sm btn-warning">Edit</a>

                    <form action="{{ route('admin.schedules.destroy', $schedule->id) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('Are you sure to delete this schedule?')">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
