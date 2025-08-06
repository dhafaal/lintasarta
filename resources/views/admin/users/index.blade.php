@extends('layouts.app')

@section('content')
    <h1>Users</h1>
    <a href="{{ route('admin.users.create') }}">Tambah User</a>
    <table>
        <thead>
            <tr>
                <th>Nama</th><th>Email</th><th>Role</th><th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <td>{{ $user->name }}</td><td>{{ $user->email }}</td><td>{{ $user->role }}</td>
                <td>
                    <a href="{{ route('admin.users.edit', $user) }}">Edit</a>
                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" style="display:inline;">
                        @csrf @method('DELETE')
                        <button onclick="return confirm('Hapus user ini?')">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
@endsection
