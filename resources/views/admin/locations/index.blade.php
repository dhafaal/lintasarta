@extends('layouts.admin')

@section('title', 'Manajemen Lokasi')

@section('content')
    <div class="min-h-screen bg-white sm:p-6 lg:p-8">
        <div class="mx-auto space-y-8">
            <!-- Enhanced Header Section -->
            <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex items-center space-x-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-sky-100 to-sky-200 shadow-sm">
                        <i data-lucide="map-pin" class="h-6 w-6 text-sky-700"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight text-gray-700">Manajemen Lokasi</h1>
                        <p class="mt-1 text-gray-500">Kelola lokasi untuk check-in dan check-out karyawan</p>
                    </div>
                </div>
                <a
                    href="{{ route('admin.locations.create') }}"
                    class="inline-flex transform items-center rounded-xl bg-gradient-to-r from-sky-500 to-indigo-600 px-6 py-2.5 font-bold text-white shadow-lg transition-all duration-200 hover:from-sky-600 hover:to-indigo-700 hover:scale-105 active:scale-95 focus:ring-4 focus:ring-sky-200 focus:outline-none whitespace-nowrap"
                >
                    <i data-lucide="plus" class="mr-2 h-5 w-5"></i>
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
                                    class="w-64 rounded-xl border-2 border-sky-100 bg-white py-2.5 pr-10 pl-10 text-sm font-semibold text-sky-900 transition-all duration-200 focus:border-sky-500 focus:ring-4 focus:ring-sky-100"
                                />
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                                    <i data-lucide="search" class="h-4 w-4 text-gray-400"></i>
                                </div>
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
                                    <i data-lucide="layout-grid" class="mr-2 h-4 w-4"></i>
                                    WFO
                                </div>
                            </button>
                            <button
                                type="button"
                                id="tab-wfa"
                                class="px-6 py-2.5 text-sm font-semibold text-gray-600 transition-all duration-200 hover:bg-gray-50"
                            >
                                <div class="flex items-center">
                                    <i data-lucide="home" class="mr-2 h-4 w-4"></i>
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
                                <i data-lucide="check" class="mr-2 h-4 w-4"></i>
                                Aktifkan
                            </button>
                            <button
                                type="button"
                                id="btn-bulk-deactivate"
                                class="inline-flex items-center rounded-lg bg-gray-100 px-4 py-2 text-sm font-semibold text-gray-700 transition-all duration-200 hover:bg-gray-200"
                            >
                                <i data-lucide="minus-circle" class="mr-2 h-4 w-4"></i>
                                Nonaktifkan
                            </button>
                            <button
                                type="button"
                                id="btn-bulk-delete"
                                class="inline-flex items-center rounded-lg bg-red-100 px-4 py-2 text-sm font-semibold text-red-700 transition-all duration-200 hover:bg-red-200"
                            >
                                <i data-lucide="trash-2" class="mr-2 h-4 w-4"></i>
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
                                        <i data-lucide="map-pin" class="mr-2 h-4 w-4 text-sky-600"></i>
                                        Nama Lokasi
                                    </div>
                                </th>
                                <th
                                    class="px-8 py-4 text-left text-xs font-bold tracking-wider text-gray-700 uppercase"
                                >
                                    <div class="flex items-center">
                                        <i data-lucide="globe" class="mr-2 h-4 w-4 text-sky-600"></i>
                                        Koordinat
                                    </div>
                                </th>
                                <th
                                    class="px-8 py-4 text-left text-xs font-bold tracking-wider text-gray-700 uppercase"
                                >
                                    <div class="flex items-center">
                                        <i data-lucide="focus" class="mr-2 h-4 w-4 text-sky-600"></i>
                                        Radius & Status
                                    </div>
                                </th>
                                <th class="px-8 py-4 text-center text-xs font-bold tracking-wider text-gray-700 uppercase">
                                    <div class="flex items-center justify-center">
                                        <i data-lucide="settings" class="mr-2 h-4 w-4 text-sky-600"></i>
                                        Aksi
                                    </div>
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

                <!-- Manual Pagination Footer (Standardized) -->
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
            const tableWrapper = document.querySelector('.overflow-x-auto');
            const btnActivate = document.getElementById('btn-bulk-activate');
            const btnDeactivate = document.getElementById('btn-bulk-deactivate');
            const btnDelete = document.getElementById('btn-bulk-delete');

            // --- Pagination State ---
            let currentPage = 1;
            let pageSize = 10;
            let currentTab = 'wfo';

            const pageSizeSelect = document.getElementById('pageSize');
            const infoText = document.getElementById('paginationInfo');
            const buttonsContainer = document.getElementById('paginationButtons');

            pageSizeSelect.addEventListener('change', function () {
                pageSize = parseInt(this.value);
                currentPage = 1;
                renderPagination();
            });

            function getVisibleRows(body) {
                return Array.from(body.querySelectorAll('tr')).filter(row => {
                    if (row.querySelector('td[colspan]')) return false;
                    return row.style.display !== 'none';
                });
            }

            function renderPagination() {
                const body = currentTab === 'wfo' ? bodyWfo : bodyWfa;
                const rows = getVisibleRows(body);
                const total = rows.length;
                const totalPages = Math.max(1, Math.ceil(total / pageSize));

                if (currentPage > totalPages) currentPage = totalPages;

                // Show/hide rows based on current page
                rows.forEach((row, idx) => {
                    const start = (currentPage - 1) * pageSize;
                    const end = start + pageSize;
                    row.style.display = (idx >= start && idx < end) ? '' : 'none';
                });

                // Build info
                const start = total === 0 ? 0 : (currentPage - 1) * pageSize + 1;
                const end = Math.min(currentPage * pageSize, total);
                infoText.textContent = total === 0
                    ? 'Tidak ada data'
                    : `Menampilkan ${start} – ${end} dari ${total} lokasi`;

                // Build page buttons
                buttonsContainer.innerHTML = '';
                const btnClass = 'inline-flex items-center justify-center min-w-[2.25rem] h-[2.25rem] px-2 rounded-xl text-sm font-bold transition-all duration-200 border-none';
                const inactiveClass = 'bg-sky-100 text-sky-700 hover:bg-sky-200';
                const activeClass = 'bg-sky-500 text-white shadow-md shadow-sky-200';
                const disabledClass = 'opacity-30 cursor-not-allowed bg-gray-100 text-gray-400';

                function addBtn(label, page, disabled, active = false) {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.innerHTML = label;
                    btn.className = `${btnClass} ${disabled ? disabledClass : (active ? activeClass : inactiveClass)}`;
                    if (!disabled && !active) {
                        btn.addEventListener('click', () => { 
                            currentPage = page; 
                            renderPagination();
                            window.scrollTo({ top: tableWrapper.offsetTop - 100, behavior: 'smooth' });
                        });
                    }
                    buttonsContainer.appendChild(btn);
                }

                addBtn('‹', currentPage - 1, currentPage === 1);
                for (let p = 1; p <= totalPages; p++) {
                    addBtn(p.toString(), p, false, p === currentPage);
                }
                addBtn('›', currentPage + 1, currentPage === totalPages);
            }

            function activate(tab) {
                currentTab = tab;
                currentPage = 1;
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
                if (selectAll) selectAll.checked = false;
                renderPagination();
            }

            tabWfo?.addEventListener('click', () => activate('wfo'));
            tabWfa?.addEventListener('click', () => activate('wfa'));
            activate('wfo');

            function getAllCheckboxes() {
                const visibleBody = currentTab === 'wfo' ? bodyWfo : bodyWfa;
                return visibleBody.querySelectorAll('input[name="ids[]"]');
            }
            selectAll?.addEventListener('change', (e) => {
                getAllCheckboxes().forEach((cb) => (cb.checked = e.target.checked));
            });

            function submitBulk(action) {
                const selected = Array.from(getAllCheckboxes()).some((cb) => cb.checked);
                if (!selected) { alert('Pilih minimal satu lokasi.'); return; }
                bulkForm.querySelectorAll('input[name="ids[]"]').forEach((el) => el.remove());
                getAllCheckboxes().forEach((cb) => {
                    if (cb.checked) {
                        const input = document.createElement('input');
                        input.type = 'hidden'; input.name = 'ids[]'; input.value = cb.value;
                        bulkForm.appendChild(input);
                    }
                });
                bulkForm.action = action;
                bulkForm.submit();
            }

            btnActivate?.addEventListener('click', () => submitBulk('{{ route('admin.locations.bulk-activate') }}'));
            btnDeactivate?.addEventListener('click', () => submitBulk('{{ route('admin.locations.bulk-deactivate') }}'));
            btnDelete?.addEventListener('click', () => {
                if (confirm('Yakin ingin menghapus lokasi terpilih?')) {
                    submitBulk('{{ route('admin.locations.bulk-delete') }}');
                }
            });

            // Realtime search functionality
            const searchInput = document.getElementById('searchInput');

            function filterLocations() {
                const searchTerm = searchInput.value.toLowerCase();
                const bodies = [bodyWfo, bodyWfa];
                bodies.forEach(body => {
                    const rows = body.querySelectorAll('tr');
                    rows.forEach((row) => {
                        if (row.querySelector('td[colspan]')) return;
                        const locationName = row.querySelector('td:nth-child(2)')?.textContent.toLowerCase() || '';
                        const coordinates = row.querySelector('td:nth-child(3)')?.textContent.toLowerCase() || '';
                        const radiusStatus = row.querySelector('td:nth-child(4)')?.textContent.toLowerCase() || '';
                        const matches = locationName.includes(searchTerm) || coordinates.includes(searchTerm) || radiusStatus.includes(searchTerm);
                        row.dataset.hidden = matches ? '' : '1';
                        row.style.display = matches ? '' : 'none';
                    });
                });
                currentPage = 1;
                renderPagination();
            }

            searchInput?.addEventListener('input', filterLocations);
        })();
    </script>
@endsection

