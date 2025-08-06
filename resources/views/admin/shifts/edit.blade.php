@extends('layouts.app')

@section('content')
<h2>Edit Shift</h2>

<form method="POST" action="{{ route('admin.shifts.update', $shift->id) }}">
    @csrf
    @method('PUT')

    <label>Nama Shift</label>
    <select name="name">
        <option value="Pagi" {{ $shift->name == 'Pagi' ? 'selected' : '' }}>Pagi</option>
        <option value="Siang" {{ $shift->name == 'Siang' ? 'selected' : '' }}>Siang</option>
        <option value="Malam" {{ $shift->name == 'Malam' ? 'selected' : '' }}>Malam</option>
    </select>
    <br><br>

    <label>Jam Mulai</label>
    <input type="time" name="start_time" value="{{ $shift->start_time }}" required>
    <br><br>

    <label>Jam Selesai</label>
    <input type="time" name="end_time" value="{{ $shift->end_time }}" required>
    <br><br>

    <button type="submit">Update</button>
</form>
@endsection
