<div class="overflow-x-auto rounded-xl mt-4" id="users-table-wrapper">
    <table class="min-w-full bg-white border border-gray-200 shadow-md">
        <thead class="bg-gray-100 text-left">
            <tr class="rounded-t-xl">
                <th class="rounded-tl-xl px-4 py-3 uppercase font-base text-gray-500">
                    <input type="checkbox" id="select-all">
                </th>
                <th class="px-4 py-3 uppercase font-base text-gray-500">Nama</th>
                <th class="px-4 py-3 uppercase font-base text-gray-500">Email</th>
                <th class="px-4 py-3 uppercase font-base text-gray-500">Role</th>
                <th class="px-4 py-3 uppercase font-base text-gray-500">Shift</th>
                <th class="rounded-tr-xl px-4 py-3 uppercase font-base text-gray-500">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
            <tr class="border-b border-gray-200">
                <td class="p-4">
                    <input type="checkbox" name="selected_users[]" value="{{ $user->id }}">
                </td>
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
                <td class="p-4">
                    <span class="inline-block text-xs font-medium bg-blue-100 text-blue-800 px-3 py-1 rounded-full">
                        {{ $user->shift }}
                    </span>
                </td>
                <td class="p-4 space-x-2">
                    <a href="{{ route('admin.users.edit', $user) }}" class="font-semibold text-blue-700">Edit</a>
                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Hapus user ini?')" class="font-semibold text-red-600">
                            Delete
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center px-4 py-2">Tidak ada data.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
