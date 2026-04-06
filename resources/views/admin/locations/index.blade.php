@extends('layouts.admin')

@section('title', 'Manajemen Lokasi')

@section('content')
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
                            class="lucide lucide-map-pin text-sky-700"
                        >
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                            <circle cx="12" cy="10" r="3"></circle>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight text-gray-700">Manajemen Lokasi</h1>
                        <p class="mt-1 text-gray-500">Kelola lokasi untuk check-in dan check-out karyawan</p>
                    </div>
                </div>
                <a
                    href="{{ route('admin.locations.create') }}"
                    class="inline-flex transform items-center rounded-xl bg-sky-500 px-6 py-3 font-bold whitespace-normal text-white shadow-sm transition-all hover:bg-sky-600 hover:shadow-md focus:ring-4 focus:ring-sky-200 focus:outline-none"
                >
                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah Lokasi Baru
                </a>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-2">
                <div class="rounded-2xl bg-gradient-to-br from-sky-500 to-sky-600 p-6 text-white shadow-xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium tracking-wide text-sky-100 uppercase">Total Lokasi</p>
                            <p class="mt-2 text-3xl font-bold">{{ $locations->count() }}</p>
                            <p class="mt-1 text-xs text-sky-200">Lokasi terdaftar</p>
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
                                class="lucide lucide-map-pin"
                            >
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                <circle cx="12" cy="10" r="3"></circle>
                            </svg>
                        </div>
                    </div>
                </div>
                <x-stats-card
                    title="Lokasi Aktif"
                    :count="$locations->where('is_active', true)->count()"
                    subtitle="Siap digunakan"
                    bgColor="bg-gradient-to-br from-green-100 to-green-200"
                    icon='<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-7 h-7 text-green-600 lucide lucide-check-circle-2"><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/><path d="m9 12 2 2 4-4"/></svg>'
                />
            </div>

            <!-- Enhanced Table Card -->
            <div class="overflow-hidden rounded-2xl border-2 border-sky-100 bg-white shadow-xl">
                <div class="border-b border-sky-100 bg-gradient-to-r from-sky-50 to-blue-50 px-8 py-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-xl font-bold text-sky-900">Daftar Lokasi Terdaftar</h2>
                            <p class="mt-1 text-sky-700">Semua lokasi yang tersedia untuk absensi karyawan</p>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="relative">
                                <input
                                    type="text"
                                    id="searchInput"
                                    placeholder="Cari lokasi..."
                                    class="w-64 rounded-lg border border-gray-200 bg-white py-2 pr-4 pl-10 text-sm transition-all duration-200 focus:border-sky-500 focus:ring-2 focus:ring-sky-500"
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
                        </div>
                    </div>
                </div>

                <!-- Tabs -->
                <div class="border-b border-gray-100 bg-white px-8 pt-6 pb-4">
                    <div class="flex items-center justify-between">
                        <div class="inline-flex overflow-hidden rounded-xl border border-sky-200 shadow-sm">
                            <button
                                type="button"
                                id="tab-wfo"
                                class="bg-sky-100 px-6 py-2.5 text-sm font-semibold text-sky-800 transition-all duration-200"
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
                                        class="mr-2"
                                    >
                                        <rect width="18" height="18" x="3" y="3" rx="2" />
                                        <path d="M3 9h18" />
                                        <path d="M9 21V9" />
                                    </svg>
                                    WFO
                                </div>
                            </button>
                            <button
                                type="button"
                                id="tab-wfa"
                                class="px-6 py-2.5 text-sm font-semibold text-gray-600 transition-all duration-200 hover:bg-gray-50"
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
                                        class="mr-2"
                                    >
                                        <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                                        <polyline points="9 22 9 12 15 12 15 22" />
                                    </svg>
                                    WFA
                                </div>
                            </button>
                        </div>
                        <div class="hidden items-center space-x-2 sm:flex">
                            <button
                                type="button"
                                id="btn-bulk-activate"
                                class="inline-flex items-center rounded-lg bg-green-100 px-4 py-2 text-sm font-semibold text-green-700 transition-all duration-200 hover:bg-green-200"
                            >
                                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M5 13l4 4L19 7"
                                    />
                                </svg>
                                Aktifkan
                            </button>
                            <button
                                type="button"
                                id="btn-bulk-deactivate"
                                class="inline-flex items-center rounded-lg bg-gray-100 px-4 py-2 text-sm font-semibold text-gray-700 transition-all duration-200 hover:bg-gray-200"
                            >
                                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M18 12H6"
                                    />
                                </svg>
                                Nonaktifkan
                            </button>
                            <button
                                type="button"
                                id="btn-bulk-delete"
                                class="inline-flex items-center rounded-lg bg-red-100 px-4 py-2 text-sm font-semibold text-red-700 transition-all duration-200 hover:bg-red-200"
                            >
                                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                    />
                                </svg>
                                Hapus
                            </button>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="border-b-2 border-gray-200 bg-gray-50">
                            <tr>
                                <th class="px-4 py-4 text-left">
                                    <input
                                        id="select-all"
                                        type="checkbox"
                                        class="h-4 w-4 rounded border-gray-300 text-sky-600 focus:ring-sky-500"
                                    />
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
                                            class="lucide lucide-map-pin mr-2 text-sky-600"
                                        >
                                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                            <circle cx="12" cy="10" r="3"></circle>
                                        </svg>
                                        Nama Lokasi
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
                                            class="lucide lucide-globe mr-2 text-sky-600"
                                        >
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"></path>
                                            <path d="M2 12h20"></path>
                                        </svg>
                                        Koordinat
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
                                            class="lucide lucide-target mr-2 text-sky-600"
                                        >
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <circle cx="12" cy="12" r="6"></circle>
                                            <circle cx="12" cy="12" r="2"></circle>
                                        </svg>
                                        Radius & Status
                                    </div>
                                </th>
                                <th
                                    class="px-8 py-4 text-left text-xs font-bold tracking-wider text-gray-700 uppercase"
                                >
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <!-- WFO body -->
                        <tbody id="tbody-wfo" class="divide-y divide-gray-100">
                            @include('admin.locations.partials.wfo', ['locations' => $locations])
                        </tbody>
                        <!-- WFA body -->
                        <tbody id="tbody-wfa" class="hidden divide-y divide-gray-100">
                            @include('admin.locations.partials.wfa', ['locations' => $locations])
                        </tbody>
                    </table>
                </div>
                <form id="bulk-form" method="POST" class="hidden">
                    @csrf
                </form>
            </div>
        </div>
    </div>
    <script>
        (function () {
            const tabWfo = document.getElementById('tab-wfo');
            const tabWfa = document.getElementById('tab-wfa');
            const bodyWfo = document.getElementById('tbody-wfo');
            const bodyWfa = document.getElementById('tbody-wfa');
            const selectAll = document.getElementById('select-all');
            const bulkForm = document.getElementById('bulk-form');
            const btnActivate = document.getElementById('btn-bulk-activate');
            const btnDeactivate = document.getElementById('btn-bulk-deactivate');
            const btnDelete = document.getElementById('btn-bulk-delete');

            function activate(tab) {
                if (tab === 'wfo') {
                    bodyWfo.classList.remove('hidden');
                    bodyWfa.classList.add('hidden');
                    tabWfo.classList.add('bg-sky-100', 'text-sky-800');
                    tabWfa.classList.remove('bg-sky-100', 'text-sky-800');
                    tabWfa.classList.add('text-gray-600');
                } else {
                    bodyWfo.classList.add('hidden');
                    bodyWfa.classList.remove('hidden');
                    tabWfa.classList.add('bg-sky-100', 'text-sky-800');
                    tabWfo.classList.remove('bg-sky-100', 'text-sky-800');
                }
                // reset select all when switching
                if (selectAll) selectAll.checked = false;
            }
            // bind tab click listeners
            tabWfo?.addEventListener('click', () => activate('wfo'));
            tabWfa?.addEventListener('click', () => activate('wfa'));
            // set default tab
            activate('wfo');
            // Return NodeList of checkboxes only from the visible tab body
            function getAllCheckboxes() {
                const visibleBody = bodyWfo.classList.contains('hidden') ? bodyWfa : bodyWfo;
                return visibleBody.querySelectorAll('input[name="ids[]"]');
            }
            selectAll?.addEventListener('change', (e) => {
                getAllCheckboxes().forEach((cb) => (cb.checked = e.target.checked));
            });

            function submitBulk(action) {
                const selected = Array.from(getAllCheckboxes()).some((cb) => cb.checked);
                if (!selected) {
                    alert('Pilih minimal satu lokasi.');
                    return;
                }
                // Clear previous hidden ids
                bulkForm.querySelectorAll('input[name="ids[]"]').forEach((el) => el.remove());
                // Append current selected ids into hidden form
                getAllCheckboxes().forEach((cb) => {
                    if (cb.checked) {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'ids[]';
                        input.value = cb.value;
                        bulkForm.appendChild(input);
                    }
                });
                bulkForm.action = action;
                bulkForm.submit();
            }

            btnActivate?.addEventListener('click', () => submitBulk('{{ route('admin.locations.bulk-activate') }}'));
            btnDeactivate?.addEventListener('click', () =>
                submitBulk('{{ route('admin.locations.bulk-deactivate') }}')
            );
            btnDelete?.addEventListener('click', () => {
                if (confirm('Yakin ingin menghapus lokasi terpilih?')) {
                    submitBulk('{{ route('admin.locations.bulk-delete') }}');
                }
            });

            // Realtime search functionality
            const searchInput = document.getElementById('searchInput');

            function filterLocations() {
                const searchTerm = searchInput.value.toLowerCase();
                const visibleBody = bodyWfo.classList.contains('hidden') ? bodyWfa : bodyWfo;
                const rows = visibleBody.querySelectorAll('tr');
                let visibleCount = 0;

                rows.forEach((row) => {
                    // Skip empty state row
                    if (row.querySelector('td[colspan]')) {
                        return;
                    }

                    const locationName = row.querySelector('td:nth-child(2)')?.textContent.toLowerCase() || '';
                    const coordinates = row.querySelector('td:nth-child(3)')?.textContent.toLowerCase() || '';
                    const radiusStatus = row.querySelector('td:nth-child(4)')?.textContent.toLowerCase() || '';

                    const matches =
                        locationName.includes(searchTerm) ||
                        coordinates.includes(searchTerm) ||
                        radiusStatus.includes(searchTerm);

                    if (matches) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                // Show/hide empty state
                const emptyRow = visibleBody.querySelector('tr td[colspan]')?.parentElement;
                if (emptyRow) {
                    emptyRow.style.display = visibleCount === 0 ? '' : 'none';
                }
            }

            searchInput?.addEventListener('input', filterLocations);

            // Re-filter when switching tabs
            tabWfo?.addEventListener('click', () => {
                setTimeout(filterLocations, 100);
            });
            tabWfa?.addEventListener('click', () => {
                setTimeout(filterLocations, 100);
            });
        })();
    </script>
@endsection
