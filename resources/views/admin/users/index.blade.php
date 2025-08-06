@extends('layouts.app')

@section('title', 'Users Table')

@section('content')
<x-section-content title="Users" subtitle="Manage all users data and Export everywhere">
    <x-slot:actions>
        <a href="{{ route('admin.users.exportPdf') }}" class="btn btn-outline">Export PDF</a>
        <a href="{{ route('admin.users.exportExcel') }}" class="btn btn-outline">Export Excel</a>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">Create User</a>
    </x-slot:actions>

    <form method="GET" action="{{ route('admin.users.index') }}" class="mb-4">
        <input type="text" name="search" placeholder="Cari user..." value="{{ request('search') }}"
            class="border border-gray-300 rounded-lg px-3 py-2 w-1/3">
        <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-lg ml-2">Search</button>
    </form>

    <form method="POST" action="{{ route('admin.users.bulkDelete') }}">
        @csrf
        @method('DELETE')

        <div class="overflow-x-auto rounded-xl">
            <table class="min-w-full bg-white border border-gray-200 shadow-md">
                <thead class="bg-gray-100 text-left">
                    <tr class="rounded-t-xl">
                        <th class="rounded-tl-xl px-4 py-3 uppercase font-base text-gray-500"><input type="checkbox" id="select-all"></th>
                        <th class="px-4 py-3 uppercase font-base text-gray-500">Nama</th>
                        <th class="px-4 py-3 uppercase font-base text-gray-500">Email</th>
                        <th class="px-4 py-3 uppercase font-base text-gray-500">Role</th>
                        <th class="rounded-tr-xl px-4 py-3 uppercase font-base text-gray-500">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr class="border-b border-gray-200">
                        <td class="p-4"><input type="checkbox" name="selected_users[]" value="{{ $user->id }}"></td>
                        <td class="p-4">{{ $user->name }}</td>
                        <td class="p-4">{{ $user->email }}</td>
                        <td class="p-4">
                            @php
                                $roleClass = match ($user->role) {
                                    'Admin' => 'bg-green-100 text-green-800',
                                    'Operator' => 'bg-yellow-100 text-yellow-800',
                                    default => 'bg-gray-100 text-gray-800',
                                };
                            @endphp

                            <span class="inline-block text-xs font-semibold rounded-full px-3 py-1 {{ $roleClass }}">
                                {{ $user->role }}
                            </span>
                        </td>
                        <td class="p-4 space-x-2">
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
                class="bg-red-500 hover:bg-red-600 text-white px-4 py-1.5 rounded-lg">
                Delete Selected
            </button>
        </div>
    </form>
</x-section-content>

<script>
    document.getElementById('select-all').addEventListener('click', function() {
        const checkboxes = document.querySelectorAll('input[name="selected_users[]"]');
        checkboxes.forEach(cb => cb.checked = this.checked);
    });
</script>
@endsection