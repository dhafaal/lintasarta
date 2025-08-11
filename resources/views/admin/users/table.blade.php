<div class="overflow-x-auto rounded-xl" id="users-table-wrapper">
    <table class="min-w-full bg-white">
        <thead class="bg-slate-50">
            <tr>
                <th class="px-6 py-4 text-sm font-semibold text-slate-600 text-left">
                    <input type="checkbox" id="select-all" class="rounded border-slate-300 focus:ring-indigo-200">
                </th>
                <th class="px-6 py-4 text-sm font-semibold text-slate-600 text-left">Nama</th>
                <th class="px-6 py-4 text-sm font-semibold text-slate-600 text-left">Email</th>
                <th class="px-6 py-4 text-sm font-semibold text-slate-600 text-left">Role</th>
                <th class="px-6 py-4 text-sm font-semibold text-slate-600 text-left">Shift</th>
                <th class="px-6 py-4 text-sm font-semibold text-slate-600 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
            <tr class="border-b border-slate-100 hover:bg-slate-50 transition">
                <td class="px-6 py-4 text-left">
                    <input type="checkbox" name="selected_users[]" value="{{ $user->id }}" class="rounded border-slate-300 focus:ring-indigo-200">
                </td>
                <td class="px-6 py-4 text-sm text-slate-700 text-left">{{ $user->name }}</td>
                <td class="px-6 py-4 text-sm text-slate-700 text-left">{{ $user->email }}</td>
                <td class="px-6 py-4 text-left">
                    @php
                        $roleClass = match ($user->role) {
                            'Admin' => 'bg-indigo-100 text-indigo-800',
                            'Operator' => 'bg-amber-100 text-amber-800',
                            default => 'bg-slate-100 text-slate-800',
                        };
                    @endphp
                    <span class="inline-block text-xs font-semibold rounded-full px-3 py-1 {{ $roleClass }}">
                        {{ $user->role }}
                    </span>
                </td>
                <td class="px-6 py-4 text-left">
                    <span class="inline-block text-xs font-semibold bg-blue-100 text-blue-800 px-3 py-1 rounded-full">
                        {{ $user->shifts->pluck('name')->implode(', ') ?: ($user->shift ?? 'N/A') }}
                    </span>
                </td>
                <td class="px-6 py-4 text-center space-x-4">
                    <a href="{{ route('admin.users.edit', $user) }}"
                       class="text-indigo-600 hover:text-indigo-800 text-sm font-semibold transition">Edit</a>
                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Hapus user ini?')"
                                class="text-red-600 hover:text-red-800 text-sm font-semibold transition">
                            Delete
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center px-6 py-6 text-slate-500 text-sm">Tidak ada data.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
