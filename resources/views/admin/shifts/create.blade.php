@extends('layouts.app')

@section('content')
<h2>Tambah Shift</h2>

<form method="POST" action="{{ route('admin.shifts.store') }}">
    @csrf
    <label>Nama Shift</label>
    <select name="name">
        <option value="Pagi">Pagi</option>
        <option value="Siang">Siang</option>
        <option value="Malam">Malam</option>
    </select>
    <br><br>

    <label>Jam Mulai</label>
    <input type="time" name="start_time" required>
    <br><br>

    <label>Jam Selesai</label>
    <input type="time" name="end_time" required>
    <br><br>

    <button type="submit">Simpan</button>
</form>
@endsection
