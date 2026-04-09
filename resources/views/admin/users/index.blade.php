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
                    href="{{ route('admin.users.create') }}"
                    class="inline-flex transform items-center rounded-xl bg-gradient-to-r from-sky-500 to-sky-600 px-6 py-3 font-bold text-white shadow-lg transition-all duration-200 hover:from-sky-600 hover:to-sky-700 focus:ring-4 focus:ring-sky-200 focus:outline-none"
                >
                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
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
                                <option value="Admin">Admin</option>
                                <option value="Operator">Operator</option>
                                <option value="User">User</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto overflow-y-visible">
                    <table id="usersTable" class="w-full">
                        <thead class="border-b-2 border-gray-200 bg-gray-50">
                            <tr>
                                <th
                                    class="px-8 py-4 text-left text-xs font-bold tracking-wider text-gray-700 uppercase"
                                >
                                    <div class="flex items-center">
                                        <i data-lucide="user" class="mr-2 h-4 w-4 text-sky-600"></i>
                                        User
                                    </div>
                                </th>
                                <th
                                    class="px-8 py-4 text-left text-xs font-bold tracking-wider text-gray-700 uppercase"
                                >
                                    <div class="flex items-center">
                                        <i data-lucide="mail" class="mr-2 h-4 w-4 text-sky-600"></i>
                                        Email
                                    </div>
                                </th>
                                <th
                                    class="px-8 py-4 text-left text-xs font-bold tracking-wider text-gray-700 uppercase"
                                >
                                    <div class="flex items-center">
                                        <i data-lucide="shield" class="mr-2 h-4 w-4 text-sky-600"></i>
                                        Role
                                    </div>
                                </th>
                                <th
                                    class="px-8 py-4 text-left text-xs font-bold tracking-wider text-gray-700 uppercase"
                                >
                                    <div class="flex items-center">
                                        <i data-lucide="calendar" class="mr-2 h-4 w-4 text-sky-600"></i>
                                        Bergabung
                                    </div>
                                </th>
                                <th
                                    class="px-8 py-4 text-center text-xs font-bold tracking-wider text-gray-700 uppercase"
                                >
                                    <div class="flex items-center justify-center">
                                        <i data-lucide="settings" class="mr-2 h-4 w-4 text-sky-600"></i>
                                        Aksi
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($users as $user)
                                <tr class="group transition-colors duration-200 hover:bg-sky-50" data-role="{{ $user->role }}">
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
                                                <div class="text-base font-semibold text-gray-900">
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
                                                text-red-700
                                            @elseif ($user->role == "Operator")
                                                bg-sky-100
                                                text-sky-700
                                            @else
                                                bg-green-100
                                                text-green-700
                                            @endif inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium"
                                        >
                                            <span class="mr-1.5 h-1.5 w-1.5 rounded-full 
                                                @if ($user->role == 'Admin') bg-red-500
                                                @elseif ($user->role == 'Operator') bg-sky-500
                                                @else bg-green-500 @endif
                                            "></span>
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
                                        <div class="flex items-center justify-start space-x-2">
                                            <a
                                                href="{{ route('admin.users.edit', $user->id) }}"
                                                class="inline-flex items-center rounded-lg bg-sky-100 px-4 py-2 text-sm font-semibold text-sky-700 transition-all duration-200 hover:bg-sky-200"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                                Edit
                                            </a>
                                            <form
                                                action="{{ route('admin.users.destroy', $user->id) }}"
                                                method="POST"
                                                class="inline-block"
                                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?')"
                                            >
                                                @csrf
                                                @method('DELETE')
                                                <button
                                                    type="submit"
                                                    class="inline-flex items-center rounded-lg bg-red-100 px-4 py-2 text-sm font-semibold text-red-700 transition-all duration-200 hover:bg-red-200"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1.5"><path d="M3 6h18"></path><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path><line x1="10" x2="10" y1="11" y2="17"></line><line x1="14" x2="14" y1="11" y2="17"></line></svg>
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

                <!-- Manual Pagination Footer (Matches Locations Module) -->
                <div id="pagination-footer" class="flex items-center justify-between px-8 py-6 border-t border-gray-100 bg-white">
                    <div class="flex items-center space-x-3 text-sm font-medium text-gray-600">
                        <span>Tampilkan</span>
                        <select id="pageSize" class="rounded-lg border-gray-200 bg-white py-1.5 pl-3 pr-8 text-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-all cursor-pointer">
                            <option value="5">5</option>
                            <option value="10" selected>10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <span>data</span>
                    </div>
                    <div class="flex items-center space-x-6">
                        <div id="paginationInfo" class="text-sm font-medium text-gray-500"></div>
                        <div id="paginationButtons" class="flex items-center space-x-2"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const table = document.getElementById('usersTable');
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr')).filter(row => !row.querySelector('td[colspan]'));
            const searchInput = document.getElementById('searchInput');
            const roleFilter = document.getElementById('roleFilter');
            const pageSizeSelect = document.getElementById('pageSize');
            const infoText = document.getElementById('paginationInfo');
            const buttonsContainer = document.getElementById('paginationButtons');

            let currentPage = 1;
            let pageSize = parseInt(pageSizeSelect.value);
            let filteredRows = [...rows];

            function render() {
                // Apply filters
                const searchTerm = searchInput.value.toLowerCase().trim();
                const roleTerm = roleFilter.value;

                filteredRows = rows.filter(row => {
                    const name = row.cells[0].textContent.toLowerCase();
                    const email = row.cells[1].textContent.toLowerCase();
                    const role = row.getAttribute('data-role') || ''; // Add data-role to TR if needed, or check cell text
                    
                    const matchesSearch = name.includes(searchTerm) || email.includes(searchTerm);
                    const matchesRole = roleTerm === '' || role === roleTerm;
                    
                    return matchesSearch && matchesRole;
                });

                const total = filteredRows.length;
                const totalPages = Math.max(1, Math.ceil(total / pageSize));

                if (currentPage > totalPages) currentPage = totalPages;

                // Update info text
                const start = total === 0 ? 0 : (currentPage - 1) * pageSize + 1;
                const end = Math.min(currentPage * pageSize, total);
                infoText.textContent = total === 0 
                    ? 'Tidak ada data' 
                    : `Menampilkan ${start} – ${end} dari ${total} pengguna`;

                // Show/hide rows
                rows.forEach(row => row.style.display = 'none');
                filteredRows.slice((currentPage - 1) * pageSize, currentPage * pageSize).forEach(row => {
                    row.style.display = '';
                });

                // Render buttons
                buttonsContainer.innerHTML = '';
                
                const btnClass = 'inline-flex items-center justify-center min-w-[2.25rem] h-[2.25rem] px-2 rounded-xl text-sm font-bold transition-all duration-200 border-none';
                const inactiveClass = 'bg-sky-100 text-sky-700 hover:bg-sky-200';
                const activeClass = 'bg-sky-500 text-white shadow-md shadow-sky-200';
                const disabledClass = 'opacity-30 cursor-not-allowed bg-gray-100 text-gray-400';

                function createBtn(label, page, disabled = false, active = false) {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.innerHTML = label;
                    btn.className = `${btnClass} ${disabled ? disabledClass : (active ? activeClass : inactiveClass)}`;
                    if (!disabled && !active) {
                        btn.onclick = () => {
                            currentPage = page;
                            render();
                            window.scrollTo({ top: table.offsetTop - 100, behavior: 'smooth' });
                        };
                    }
                    buttonsContainer.appendChild(btn);
                }

                // Prev
                createBtn('‹', currentPage - 1, currentPage === 1);

                // Page numbers
                // For simplicity, showing all pages. Can be optimized if many pages.
                for (let i = 1; i <= totalPages; i++) {
                    createBtn(i, i, false, i === currentPage);
                }

                // Next
                createBtn('›', currentPage + 1, currentPage === totalPages);
            }

            // Listeners
            searchInput.addEventListener('input', () => { currentPage = 1; render(); });
            roleFilter.addEventListener('change', () => { currentPage = 1; render(); });
            pageSizeSelect.addEventListener('change', (e) => {
                pageSize = parseInt(e.target.value);
                currentPage = 1;
                render();
            });

            // Initial render
            render();
        });
    </script>
@endsection

