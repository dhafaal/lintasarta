@extends('layouts.app')

@section('content')
<h2>{{ isset($shift) ? 'Edit' : 'Create' }} Shift</h2>

<form method="POST" action="{{ isset($shift) ? route('admin.shifts.update', $shift) : route('admin.shifts.store') }}">
    @csrf
    @if(isset($shift)) @method('PUT') @endif

    <label>Name:</label>
    <input type="text" name="name" value="{{ old('name', $shift->name ?? '') }}" required><br>

    <label>Start Time:</label>
    <input type="time" name="start_time" value="{{ old('start_time', $shift->start_time ?? '') }}" required><br>

    <label>End Time:</label>
    <input type="time" name="end_time" value="{{ old('end_time', $shift->end_time ?? '') }}" required><br>

    <button type="submit">Save</button>
</form>
@endsection
