@extends('layouts.app')

@section('title', 'Manajemen Users')

@section('content')
<div class="flex justify-between items-center mb-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-700">Users</h1>
        <p class="text-sm font-medium text-gray-500">Manage all users data and Export everywhere</p>
    </div>
    <div class="space-x-2">
        <a href="{{ route('admin.users.create') }}" class="btn text-white bg-blue-600 hover:bg-blue-700">Create User</a>
        <a href="{{ route('admin.users.exportPdf') }}" class="btn text-white bg-red-600 hover:bg-red-700">Export PDF</a>
        <a href="{{ route('admin.users.exportExcel') }}" class="btn text-white bg-green-600 hover:bg-green-700">Export Excel</a>
    </div>
</div>

<form method="GET" action="{{ route('admin.users.index') }}" class="mb-4">
    <input type="text" name="search" placeholder="Cari user..." value="{{ request('search') }}"
        class="border border-gray-300 rounded-lg px-3 py-2 w-1/3">
    <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-lg ml-2">Search</button>
</form>

<form method="POST" action="{{ route('admin.users.bulkDelete') }}">
    @csrf
    @method('DELETE')

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border-b border-gray-200 rounded shadow-md">
            <thead class="bg-gray-100 text-left">
                <tr>
                    <th class="px-4 py-2"><input type="checkbox" id="select-all"></th>
                    <th class="px-4 py-2">Nama</th>
                    <th class="px-4 py-2">Email</th>
                    <th class="px-4 py-2">Role</th>
                    <th class="px-4 py-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr class="border-b border-gray-200">
                    <td class="px-4 py-2"><input type="checkbox" name="selected_users[]" value="{{ $user->id }}"></td>
                    <td class="px-4 py-2">{{ $user->name }}</td>
                    <td class="px-4 py-2">{{ $user->email }}</td>
                    <td class="px-4 py-2">{{ $user->role }}</td>
                    <td class="px-4 py-2 space-x-2">
                        <a href="{{ route('admin.users.edit', $user) }}" class="font-semibold text-blue-700">Edit</a>
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display:inline;">
                            @csrf @method('DELETE')
                            <button type="submit" onclick="return confirm('Hapus user ini?')" class="font-semibold text-red-600">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center px-4 py-2">Tidak ada data.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        <button type="submit" onclick="return confirm('Yakin hapus semua user yang dipilih?')"
            class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
            Hapus yang Dipilih
        </button>
    </div>
</form>

<script>
    document.getElementById('select-all').addEventListener('click', function() {
        const checkboxes = document.querySelectorAll('input[name="selected_users[]"]');
        checkboxes.forEach(cb => cb.checked = this.checked);
    });
</script>
@endsection