@extends('layouts.app')

@section('content')
<h2>Shift List</h2>
<a href="{{ route('admin.shifts.create') }}">+ Add Shift</a>

@if(session('success'))
    <p style="color: green">{{ session('success') }}</p>
@endif

<table border="1">
    <tr>
        <th>Name</th>
        <th>Start</th>
        <th>End</th>
        <th>Action</th>
    </tr>
    @foreach ($shifts as $shift)
    <tr>
        <td>{{ $shift->name }}</td>
        <td>{{ $shift->start_time }}</td>
        <td>{{ $shift->end_time }}</td>
        <td>
            <a href="{{ route('admin.shifts.edit', $shift->id) }}">Edit</a> |
            <form action="{{ route('admin.shifts.destroy', $shift->id) }}" method="POST" style="display:inline">
                @csrf @method('DELETE')
                <button onclick="return confirm('Are you sure?')">Delete</button>
            </form>
        </td>
    </tr>
    @endforeach
</table>
@endsection
