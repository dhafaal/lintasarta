@extends('layouts.app')

@section('content')
<h2>{{ isset($schedule) ? 'Edit' : 'Add' }} Schedule</h2>

<form method="POST" action="{{ isset($schedule) ? route('admin.schedules.update', $schedule) : route('admin.schedules.store') }}">
    @csrf
    @if(isset($schedule)) @method('PUT') @endif

    <label>User:</label>
    <select name="user_id" class="form-control" required>
        @foreach($users as $user)
        <option value="{{ $user->id }}" {{ isset($schedule) && $schedule->user_id == $user->id ? 'selected' : '' }}>
            {{ $user->name }}
        </option>
        @endforeach
    </select>

    <label>Shift:</label>
    <select name="shift_id" class="form-control" required>
        @foreach($shifts as $shift)
        <option value="{{ $shift->id }}" {{ isset($schedule) && $schedule->shift_id == $shift->id ? 'selected' : '' }}>
            {{ $shift->name }}
        </option>
        @endforeach
    </select>

    <label>Schedule Date:</label>
    <input type="date" name="schedule_date" value="{{ old('schedule_date', $schedule->schedule_date ?? '') }}" required>

    <br><br>
    <button type="@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ isset($schedule) ? 'Edit' : 'Add' }} Schedule</h1>

    <form method="POST" action="{{ isset($schedule) ? route('admin.schedules.update', $schedule) : route('admin.schedules.store') }}">
        @csrf
        @if(isset($schedule)) @method('PUT') @endif

        <div class="mb-3">
            <label>User:</label>
            <select name="user_id" class="form-control" required>
                @foreach($users as $user)
                <option value="{{ $user->id }}" {{ isset($schedule) && $schedule->user_id == $user->id ? 'selected' : '' }}>
                    {{ $user->name }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Shift:</label>
            <select name="shift_id" class="form-control" required>
                @foreach($shifts as $shift)
                <option value="{{ $shift->id }}" {{ isset($schedule) && $schedule->shift_id == $shift->id ? 'selected' : '' }}>
                    {{ $shift->name }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Schedule Date:</label>
            <input type="date" name="schedule_date" class="form-control"
                   value="{{ isset($schedule) ? $schedule->schedule_date : old('schedule_date') }}" required>
        </div>

        <button type="submit" class="btn btn-success">Save</button>
    </form>
</div>
@endsection
submit" class="btn btn-primary">Save</button>
</form>
@endsection
