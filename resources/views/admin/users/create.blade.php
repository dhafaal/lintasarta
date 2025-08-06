@extends('layouts.app')

@section('content')
    <h1>Tambah User</h1>
    <form method="POST" action="{{ route('admin.users.store') }}">
        @csrf
        <input type="text" name="name" placeholder="Nama" required><br>
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <select name="role">
            <option value="admin">Admin</option>
            <option value="operator">Operator</option>
            <option value="user">User</option>
        </select><br>
        <button type="submit">Simpan</button>
    </form>
@endsection
