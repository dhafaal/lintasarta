@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<x-section-content title="Edit User" subtitle="Perbarui informasi pengguna dan peran.">
    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-5">
        @csrf
        @method('PUT')

        <x-input label="Nama" name="name" :value="$user->name" required />
        <x-input label="Email" name="email" type="email" :value="$user->email" required />
        <x-input label="Password (Opsional)" name="password" type="password" placeholder="Kosongkan jika tidak ingin ganti" />

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
            <select name="role"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="operator" {{ $user->role == 'operator' ? 'selected' : '' }}>Operator</option>
                <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>User</option>
            </select>
        </div>

        <div class="flex justify-between items-center">
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline px-6">← Back</a>
            <button type="submit"
                class="btn btn-primary">
                Update
            </button>
        </div>
    </form>
</x-section-content>
@endsection