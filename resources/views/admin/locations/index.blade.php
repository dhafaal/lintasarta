@extends('layouts.admin')

@section('title', 'Manajemen Lokasi')

@section('content')
    <div class="min-h-screen bg-white sm:p-6 lg:p-8">
        <div class="mx-auto space-y-8">
            <!-- Enhanced Header Section -->
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-sky-100 to-sky-200 rounded-xl flex items-center justify-center shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="lucide lucide-map-pin text-sky-700">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                            <circle cx="12" cy="10" r="3"></circle>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-700 tracking-tight">Manajemen Lokasi</h1>
                        <p class="text-gray-500 mt-1">Kelola lokasi untuk check-in dan check-out karyawan</p>
                    </div>
                </div>
                <a href="{{ route('admin.locations.create') }}"
                   class="inline-flex items-center px-6 py-3 bg-sky-500  text-white font-bold rounded-xl hover:bg-sky-600 transition-all transform focus:outline-none focus:ring-4 focus:ring-sky-200 shadow-sm hover:shadow-md whitespace-normal">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah Lokasi Baru
                </a>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-gradient-to-br from-sky-500 to-sky-600 rounded-2xl p-6 text-white shadow-xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sky-100 text-sm font-medium uppercase tracking-wide">Total Lokasi</p>
                            <p class="text-3xl font-bold mt-2">{{ $locations->count() }}</p>
                            <p class="text-sky-200 text-xs mt-1">Lokasi terdaftar</p>
                        </div>
                        <div class="w-14 h-14 bg-sky-400 bg-opacity-30 rounded-xl flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pin">
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
                <x-stats-card
                    title="Radius Rata-rata"
                    :count="$locations->avg('radius') ? number_format($locations->avg('radius'), 0) . 'm' : '0m'"
                    subtitle="Jangkauan lokasi"
                    bgColor="bg-gradient-to-br from-purple-100 to-purple-200"
                    icon='<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-7 h-7 text-purple-600 lucide lucide-target"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></svg>'
                />
                <x-stats-card
                    title="Jangkauan Terluas"
                    :count="$locations->max('radius') ? $locations->max('radius') . 'm' : '0m'"
                    subtitle="Radius maksimal"
                    bgColor="bg-gradient-to-br from-orange-100 to-orange-200"
                    icon='<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-7 h-7 text-orange-600 lucide lucide-maximize"><path d="M8 3H5a2 2 0 0 0-2 2v3"/><path d="M21 8V5a2 2 0 0 0-2-2h-3"/><path d="M3 16v3a2 2 0 0 0 2 2h3"/><path d="M16 21h3a2 2 0 0 0 2-2v-3"/></svg>'
                />
            </div>

            <!-- Enhanced Table Card -->
            <div class="bg-white rounded-2xl border-2 border-sky-100 overflow-hidden shadow-xl">
                <div class="px-8 py-6 border-b border-sky-100 bg-gradient-to-r from-sky-50 to-blue-50">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-xl font-bold text-sky-900">Daftar Lokasi Terdaftar</h2>
                            <p class="text-sky-700 mt-1">Semua lokasi yang tersedia untuk absensi karyawan</p>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="relative">
                                <input type="text" id="searchInput" placeholder="Cari lokasi..."
                                    class="pl-10 pr-4 py-2 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm transition-all duration-200 w-64">
                                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-3" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabs -->
                <div class="px-8 pt-6 pb-4 bg-white border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <div class="inline-flex rounded-xl border border-sky-200 overflow-hidden shadow-sm">
                            <button type="button" id="tab-wfo" class="px-6 py-2.5 text-sm font-semibold bg-sky-100 text-sky-800 transition-all duration-200">
                                <div class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                                        <rect width="18" height="18" x="3" y="3" rx="2"/>
                                        <path d="M3 9h18"/>
                                        <path d="M9 21V9"/>
                                    </svg>
                                    WFO
                                </div>
                            </button>
                            <button type="button" id="tab-wfa" class="px-6 py-2.5 text-sm font-semibold text-gray-600 hover:bg-gray-50 transition-all duration-200">
                                <div class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                                        <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                                        <polyline points="9 22 9 12 15 12 15 22"/>
                                    </svg>
                                    WFA
                                </div>
                            </button>
                        </div>
                        <div class="hidden sm:flex items-center space-x-2">
                            <button type="button" id="btn-bulk-activate" class="inline-flex items-center px-4 py-2 bg-green-100 text-green-700 rounded-lg text-sm font-semibold hover:bg-green-200 transition-all duration-200  ">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Aktifkan
                            </button>
                            <button type="button" id="btn-bulk-deactivate" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-semibold hover:bg-gray-200 transition-all duration-200  ">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 12H6"/>
                                </svg>
                                Nonaktifkan
                            </button>
                            <button type="button" id="btn-bulk-delete" class="inline-flex items-center px-4 py-2 bg-red-100 text-red-700 rounded-lg text-sm font-semibold hover:bg-red-200 transition-all duration-200  ">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Hapus
                            </button>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b-2 border-gray-200">
                            <tr>
                                <th class="px-4 py-4 text-left">
                                    <input id="select-all" type="checkbox" class="w-4 h-4 text-sky-600 border-gray-300 rounded focus:ring-sky-500">
                                </th>
                                <th class="px-8 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="lucide lucide-map-pin text-sky-600 mr-2">
                                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                            <circle cx="12" cy="10" r="3"></circle>
                                        </svg>
                                        Nama Lokasi
                                    </div>
                                </th>
                                <th class="px-8 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="lucide lucide-globe text-sky-600 mr-2">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"></path>
                                            <path d="M2 12h20"></path>
                                        </svg>
                                        Koordinat
                                    </div>
                                </th>
                                <th class="px-8 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="lucide lucide-target text-sky-600 mr-2">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <circle cx="12" cy="12" r="6"></circle>
                                            <circle cx="12" cy="12" r="2"></circle>
                                        </svg>
                                        Radius & Status
                                    </div>
                                </th>
                                <th class="px-8 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <!-- WFO body -->
                        <tbody id="tbody-wfo" class="divide-y divide-gray-100">
                            @include('admin.locations.partials.wfo', ['locations' => $locations])
                        </tbody>
                        <!-- WFA body -->
                        <tbody id="tbody-wfa" class="divide-y divide-gray-100 hidden">
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
    (function(){
        const tabWfo = document.getElementById('tab-wfo');
        const tabWfa = document.getElementById('tab-wfa');
        const bodyWfo = document.getElementById('tbody-wfo');
        const bodyWfa = document.getElementById('tbody-wfa');
        const selectAll = document.getElementById('select-all');
        const bulkForm = document.getElementById('bulk-form');
        const btnActivate = document.getElementById('btn-bulk-activate');
        const btnDeactivate = document.getElementById('btn-bulk-deactivate');
        const btnDelete = document.getElementById('btn-bulk-delete');

        function activate(tab){
            if(tab === 'wfo'){
                bodyWfo.classList.remove('hidden');
                bodyWfa.classList.add('hidden');
                tabWfo.classList.add('bg-sky-100','text-sky-800');
                tabWfa.classList.remove('bg-sky-100','text-sky-800');
                tabWfa.classList.add('text-gray-600');
            } else {
                bodyWfo.classList.add('hidden');
                bodyWfa.classList.remove('hidden');
                tabWfa.classList.add('bg-sky-100','text-sky-800');
                tabWfo.classList.remove('bg-sky-100','text-sky-800');
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
        function getAllCheckboxes(){
            const visibleBody = bodyWfo.classList.contains('hidden') ? bodyWfa : bodyWfo;
            return visibleBody.querySelectorAll('input[name="ids[]"]');
        }
        selectAll?.addEventListener('change', (e) => {
            getAllCheckboxes().forEach(cb => cb.checked = e.target.checked);
        });

        function submitBulk(action){
            const selected = Array.from(getAllCheckboxes()).some(cb => cb.checked);
            if(!selected){
                alert('Pilih minimal satu lokasi.');
                return;
            }
            // Clear previous hidden ids
            bulkForm.querySelectorAll('input[name="ids[]"]').forEach(el => el.remove());
            // Append current selected ids into hidden form
            getAllCheckboxes().forEach(cb => {
                if(cb.checked){
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

        btnActivate?.addEventListener('click', () => submitBulk("{{ route('admin.locations.bulk-activate') }}"));
        btnDeactivate?.addEventListener('click', () => submitBulk("{{ route('admin.locations.bulk-deactivate') }}"));
        btnDelete?.addEventListener('click', () => {
            if(confirm('Yakin ingin menghapus lokasi terpilih?')){
                submitBulk("{{ route('admin.locations.bulk-delete') }}");
            }
        });

        // Realtime search functionality
        const searchInput = document.getElementById('searchInput');
        
        function filterLocations() {
            const searchTerm = searchInput.value.toLowerCase();
            const visibleBody = bodyWfo.classList.contains('hidden') ? bodyWfa : bodyWfo;
            const rows = visibleBody.querySelectorAll('tr');
            let visibleCount = 0;

            rows.forEach(row => {
                // Skip empty state row
                if (row.querySelector('td[colspan]')) {
                    return;
                }

                const locationName = row.querySelector('td:nth-child(2)')?.textContent.toLowerCase() || '';
                const coordinates = row.querySelector('td:nth-child(3)')?.textContent.toLowerCase() || '';
                const radiusStatus = row.querySelector('td:nth-child(4)')?.textContent.toLowerCase() || '';

                const matches = locationName.includes(searchTerm) || 
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