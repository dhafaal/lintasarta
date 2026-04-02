@extends("layouts.admin")

@section("title", "Daftar Users")

@section("content")
    <div class="min-h-screen bg-white sm:p-6 lg:p-8">
        <div class="mx-auto space-y-8">
            <!-- Enhanced Header Section -->
            <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex items-center space-x-4">
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-sky-100 to-sky-200 shadow-sm"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="24"
                            height="24"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            class="lucide lucide-users-icon lucide-users text-sky-700"
                        >
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                            <path d="M16 3.128a4 4 0 0 1 0 7.744" />
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                            <circle cx="9" cy="7" r="4" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight text-gray-700">Manajemen Users</h1>
                        <p class="mt-1 text-gray-500">Kelola semua pengguna dalam sistem</p>
                    </div>
                </div>

                <a
                    href="{{ route("admin.users.create") }}"
                    class="inline-flex transform items-center rounded-xl bg-sky-500 px-6 py-3 font-bold whitespace-normal text-white shadow-sm transition-all hover:bg-sky-600 hover:shadow-md focus:ring-4 focus:ring-sky-200 focus:outline-none"
                >
                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"
                        ></path>
                    </svg>
                    Tambah User
                </a>
            </div>

            <!-- Enhanced Stats Cards -->
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                <div class="rounded-2xl bg-gradient-to-br from-sky-500 to-sky-600 p-6 text-white shadow-xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium tracking-wide text-sky-100 uppercase">Total Users</p>
                            <p class="mt-2 text-3xl font-bold">{{ $users->count() }}</p>
                            <p class="mt-1 text-xs text-sky-200">Pengguna aktif</p>
                        </div>
                        <div class="bg-opacity-30 flex h-14 w-14 items-center justify-center rounded-xl bg-sky-400">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="24"
                                height="24"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                class="lucide lucide-users-icon lucide-users text-white"
                            >
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                <path d="M16 3.128a4 4 0 0 1 0 7.744" />
                                <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                                <circle cx="9" cy="7" r="4" />
                            </svg>
                        </div>
                    </div>
                </div>

                <x-stats-card
                    title="Admin"
                    :count="$countAdmin"
                    subtitle="Akses penuh"
                    bgColor="bg-gradient-to-br from-red-100 to-red-200"
                    icon='<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="w-7 h-7 text-red-600 lucide lucide-shield-user-icon lucide-shield-user"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="M6.376 18.91a6 6 0 0 1 11.249.003"/><circle cx="12" cy="11" r="4"/></svg>'
                />

                <x-stats-card
                    title="User"
                    :count="$countUser"
                    subtitle="Akses terbatas"
                    bgColor="bg-gradient-to-br from-green-100 to-emerald-100"
                    icon='<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-7 h-7 text-green-600 lucide lucide-circle-user-round-icon lucide-circle-user-round"><path d="M18 20a6 6 0 0 0-12 0"/><circle cx="12" cy="10" r="4"/><circle cx="12" cy="12" r="10"/></svg>'
                />
            </div>

            <!-- Enhanced Table Card -->
            <div class="overflow-hidden rounded-2xl border-2 border-sky-100 bg-white shadow-xl">
                <div class="border-b border-sky-100 bg-gradient-to-r from-sky-50 to-blue-50 px-8 py-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-xl font-bold text-sky-900">Daftar Users</h2>
                            <p class="mt-1 text-sky-700">Semua pengguna yang terdaftar dalam sistem</p>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="relative">
                                <input
                                    type="text"
                                    id="searchInput"
                                    placeholder="Cari user..."
                                    class="w-64 rounded-lg border border-gray-200 bg-white py-2 pr-4 pl-10 text-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-500"
                                />
                                <svg
                                    class="absolute top-3 left-3 h-4 w-4 text-gray-400"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                                    ></path>
                                </svg>
                            </div>
                            <select
                                id="roleFilter"
                                class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-500"
                            >
                                <option value="">Semua Role</option>
                                <option value="admin">Admin</option>
                                <option value="user">User</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="-mx-4 overflow-x-auto sm:mx-0">
                    <table class="w-full min-w-[700px]">
                        <thead class="border-b-2 border-gray-200 bg-gray-50">
                            <tr>
                                <th
                                    class="px-4 py-3 text-left text-xs font-bold tracking-wider text-gray-700 uppercase sm:px-6 sm:py-4 lg:px-8"
                                >
                                    <div class="flex items-center">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            width="16"
                                            height="16"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            class="lucide lucide-user mr-2 text-sky-600"
                                        >
                                            <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" />
                                            <circle cx="12" cy="7" r="4" />
                                        </svg>
                                        User
                                    </div>
                                </th>
                                <th
                                    class="px-8 py-4 text-left text-xs font-bold tracking-wider text-gray-700 uppercase"
                                >
                                    <div class="flex items-center">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            width="16"
                                            height="16"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            class="lucide lucide-mail mr-2 text-sky-600"
                                        >
                                            <path
                                                d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2Z"
                                            />
                                            <path d="m22 6-10 7L2 6" />
                                        </svg>
                                        Email
                                    </div>
                                </th>
                                <th
                                    class="px-8 py-4 text-left text-xs font-bold tracking-wider text-gray-700 uppercase"
                                >
                                    <div class="flex items-center">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            width="16"
                                            height="16"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            class="lucide lucide-shield mr-2 text-sky-600"
                                        >
                                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10" />
                                        </svg>
                                        Role
                                    </div>
                                </th>
                                <th
                                    class="px-8 py-4 text-left text-xs font-bold tracking-wider text-gray-700 uppercase"
                                >
                                    <div class="flex items-center">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            width="16"
                                            height="16"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            class="lucide lucide-calendar mr-2 text-sky-600"
                                        >
                                            <path d="M8 2v4" />
                                            <path d="M16 2v4" />
                                            <rect width="18" height="18" x="3" y="4" rx="2" />
                                            <path d="M3 10h18" />
                                        </svg>
                                        Bergabung
                                    </div>
                                </th>
                                <th
                                    class="px-8 py-4 text-left text-xs font-bold tracking-wider text-gray-700 uppercase"
                                >
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($users as $user)
                                <tr class="group transition-colors duration-200 hover:bg-sky-50">
                                    <td class="px-8 py-6 whitespace-nowrap">
                                        <div class="flex items-center">
                                            @if($user && $user->profile_photo)
                                                <img src="{{ Storage::url($user->profile_photo) }}" alt="Profile Photo" class="mr-4 h-10 w-10 rounded-xl object-cover transition-colors hover:opacity-90">
                                            @else
                                                <div
                                                    class="mr-4 flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-sky-100 to-sky-200 transition-colors group-hover:from-sky-200 group-hover:to-sky-300"
                                                >
                                                    <span class="text-sm font-bold text-sky-600">
                                                        {{ substr($user->name, 0, 1) }}
                                                    </span>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="text-base font-semibold text-gray-700">
                                                    {{ $user->name }}
                                                </div>
                                                <div class="text-xs text-gray-500">ID: {{ $user->id }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 whitespace-nowrap">
                                        <div class="text-sm text-gray-700">{{ $user->email }}</div>
                                    </td>
                                    <td class="px-8 py-6 whitespace-nowrap">
                                        <span
                                            class="@if ($user->role == "Admin")
                                                bg-red-100
                                                text-red-800
                                            @elseif ($user->role == "Operator")
                                                bg-sky-100
                                                text-sky-800
                                            @else
                                                bg-green-100
                                                text-green-800
                                            @endif inline-flex items-center rounded-full px-3 py-1 text-sm font-medium"
                                        >
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td class="px-8 py-6 whitespace-nowrap">
                                        <div class="text-base font-semibold text-gray-700">
                                            {{ $user->created_at->format("d F Y") }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $user->updated_at->format("d F Y") }}
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 text-left whitespace-nowrap">
                                        <div class="flex items-center justify-start space-x-3">
                                            <a
                                                href="{{ route("admin.users.edit", $user->id) }}"
                                                class="inline-flex items-center rounded-lg bg-sky-100 px-4 py-2 text-sm font-semibold text-sky-700 transition-all duration-200 hover:bg-sky-200"
                                            >
                                                Edit
                                            </a>
                                            <form
                                                action="{{ route("admin.users.destroy", $user->id) }}"
                                                method="POST"
                                                class="inline"
                                            >
                                                @csrf
                                                @method("DELETE")
                                                <button
                                                    type="submit"
                                                    onclick="return confirm('Yakin ingin menghapus user {{ $user->name }}? Tindakan ini tidak dapat dibatalkan.')"
                                                    class="inline-flex items-center rounded-lg bg-red-100 px-4 py-2 text-sm font-semibold text-red-700 transition-all duration-200 hover:bg-red-200"
                                                >
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-8 py-16 text-center">
                                        <div class="flex flex-col items-center">
                                            <div
                                                class="mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-gradient-to-br from-sky-100 to-sky-200"
                                            >
                                                <svg
                                                    class="h-10 w-10 text-sky-400"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"
                                                    ></path>
                                                </svg>
                                            </div>
                                            <h3 class="mb-2 text-xl font-bold text-gray-900">Belum ada user</h3>
                                            <p class="mb-6 max-w-sm text-gray-600">
                                                Mulai dengan membuat user pertama untuk mengakses sistem
                                            </p>
                                            <a
                                                href="{{ route("admin.users.create") }}"
                                                class="inline-flex transform items-center rounded-xl bg-gradient-to-r from-sky-500 to-sky-600 px-6 py-3 font-bold text-white shadow-lg transition-all duration-200 hover:from-sky-600 hover:to-sky-700"
                                            >
                                                <svg
                                                    class="mr-2 h-5 w-5"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"
                                                    ></path>
                                                </svg>
                                                Tambah User Pertama
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('searchInput');
            const roleFilter = document.getElementById('roleFilter');
            const tableRows = document.querySelectorAll('tbody tr');

            function filterTable() {
                const searchTerm = searchInput.value.toLowerCase();
                const roleValue = roleFilter.value.toLowerCase();
                let visibleCount = 0;

                tableRows.forEach((row) => {
                    // Skip empty state row
                    if (row.querySelector('td[colspan]')) {
                        return;
                    }

                    const userName = row.querySelector('td:nth-child(1)')?.textContent.toLowerCase() || '';
                    const userEmail = row.querySelector('td:nth-child(2)')?.textContent.toLowerCase() || '';
                    const userRole = row.querySelector('td:nth-child(3)')?.textContent.toLowerCase() || '';
                    const joinDate = row.querySelector('td:nth-child(4)')?.textContent.toLowerCase() || '';

                    const matchesSearch =
                        userName.includes(searchTerm) ||
                        userEmail.includes(searchTerm) ||
                        userRole.includes(searchTerm) ||
                        joinDate.includes(searchTerm);

                    const matchesRole = !roleValue || userRole.includes(roleValue);

                    if (matchesSearch && matchesRole) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                // Show/hide empty state
                const emptyRow = document.querySelector('tbody tr td[colspan]')?.parentElement;
                if (emptyRow) {
                    emptyRow.style.display = visibleCount === 0 ? '' : 'none';
                }
            }

            searchInput.addEventListener('input', filterTable);
            roleFilter.addEventListener('change', filterTable);
        });
    </script>
@endsection
