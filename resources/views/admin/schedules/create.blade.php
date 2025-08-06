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

    <label>Date:</label>
    <input type="date" name="schedule_date" value="{{ $schedule->schedule_date ?? old('schedule_date') }}" class="form-control" required>

    <button type="submit" class="btn btn-primary mt-2">Save</button>
</form>
