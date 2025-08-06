@extends('layouts.app')

@section('content')
    <h1>Edit User</h1>
    <form method="POST" action="{{ route('admin.users.update', $user) }}">
        @csrf @method('PUT')
        <input type="text" name="name" value="{{ $user->name }}" required><br>
        <input type="email" name="email" value="{{ $user->email }}" required><br>
        <input type="password" name="password" placeholder="Ganti Password (opsional)"><br>
        <select name="role">
            <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
            <option value="operator" {{ $user->role == 'operator' ? 'selected' : '' }}>Operator</option>
            <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>User</option>
        </select><br>
        <button type="submit">Update</button>
    </form>
@endsection
