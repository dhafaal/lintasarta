@extends('layouts.app')

@section('content')
<h2>Daftar Shift</h2>
<a href="{{ route('admin.shifts.create') }}">+ Tambah Shift</a>

@if(session('success'))
    <div>{{ session('success') }}</div>
@endif

<table border="1" cellpadding="10">
    <tr>
        <th>Nama Shift</th>
        <th>Jam Mulai</th>
        <th>Jam Selesai</th>
        <th>Aksi</th>
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
                <button onclick="return confirm('Hapus shift ini?')">Hapus</button>
            </form>
        </td>
    </tr>
    @endforeach
</table>
@endsection
