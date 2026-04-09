@extends('layouts.admin')

@section('content')
<div class="min-h-screen p-3 sm:p-4 md:p-6 lg:p-8">
    <div class="space-y-4 sm:space-y-6 md:space-y-8">
        {{-- Enhanced Header Section --}}
        <div class="flex flex-col gap-4 sm:gap-6 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-center space-x-3 sm:space-x-4">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-sky-100 to-sky-200 shadow-sm sm:h-12 sm:w-12">
                    <i data-lucide="arrow-right-left" class="h-5 w-5 text-sky-700 sm:h-6 sm:w-6"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold tracking-tight text-gray-900 sm:text-2xl md:text-3xl">Validasi Swap Schedules</h1>
                    <p class="mt-1 text-xs text-gray-500 sm:text-sm md:text-base">{{ now()->format('l, d F Y') }} - Kelola dan setujui pertukaran jadwal karyawan.</p>
                </div>
            </div>
        </div>

        {{-- Alerts --}}
        @if (session('success'))
            <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4">
                <div class="flex items-center gap-3">
                    <i data-lucide="check-circle" class="h-5 w-5 text-emerald-600"></i>
                    <p class="text-sm font-medium text-emerald-800">{{ session('success') }}</p>
                </div>
            </div>
        @endif
        @if (session('error'))
            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4">
                <div class="flex items-center gap-3">
                    <i data-lucide="alert-circle" class="h-5 w-5 text-red-600"></i>
                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        {{-- Filter Section --}}
        <div class="mb-4 flex flex-col items-center justify-between gap-4 md:flex-row">
            <div class="flex w-full flex-col gap-3 sm:flex-row sm:items-center">
                <div class="relative w-full sm:w-64">
                    <i data-lucide="search" class="absolute left-3 top-2.5 h-4 w-4 text-gray-400"></i>
                    <input type="text" id="admin_search_input" placeholder="Cari nama karyawan..." class="w-full rounded-xl border border-gray-300 py-2 pl-9 pr-4 text-sm focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500">
                </div>
                <select id="statusFilter" class="w-full rounded-xl border border-gray-300 bg-white px-3 py-2 text-sm focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500 sm:w-48">
                    <option value="">Semua Status</option>
                    <option value="pending_admin">Pending Admin</option>
                    <option value="pending_target">Pending Target</option>
                    <option value="approved">Disetujui</option>
                    <option value="rejected_by_admin">Ditolak Admin</option>
                    <option value="rejected_by_target">Ditolak Target</option>
                    <option value="canceled">Dibatalkan</option>
                </select>
            </div>
        </div>

        <div class="rounded-2xl bg-white shadow-sm ring-1 ring-gray-100 p-0 overflow-hidden">
            <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tgl Pengajuan</th>
                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requester</th>
                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Target</th>
                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jadwal Ditukar</th>
                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="relative px-3 py-3 font-semibold w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody id="table-container" class="divide-y divide-gray-200 bg-white">
                    @forelse($requests as $req)
                        <tr class="admin-swap-row hover:bg-gray-50 transition-colors" data-names="{{ strtolower($req->requester->name . ' ' . $req->targetUser->name) }}" data-status="{{ $req->status }}">
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-900">
                                {{ $req->created_at->format('d M Y, H:i') }}
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm">
                                <div class="font-bold text-gray-900">{{ $req->requester->name }}</div>
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm">
                                <div class="font-bold text-gray-900">{{ $req->targetUser->name }}</div>
                            </td>
                            <td class="px-3 py-4 text-sm text-gray-900">
                                <div class="flex flex-col gap-2 p-3 bg-gray-50 rounded border border-gray-100 min-w-[280px]">
                                    <div class="flex flex-col gap-1">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs uppercase font-bold text-gray-500">Milik Requester:</span>
                                            <span class="font-semibold text-indigo-700">{{ \Carbon\Carbon::parse($req->requestedSchedule->schedule_date)->format('d/m/Y') }}</span>
                                        </div>
                                        <div class="text-right text-xs text-gray-600">
                                            {{ $req->requestedSchedule->shift->shift_name }} ({{ \Carbon\Carbon::parse($req->requestedSchedule->shift->start_time)->format('H:i') }}-{{ \Carbon\Carbon::parse($req->requestedSchedule->shift->end_time)->format('H:i') }})
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-center my-1"><i data-lucide="arrow-down-up" class="h-4 w-4 text-gray-400"></i></div>
                                    <div class="flex flex-col gap-1">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs uppercase font-bold text-gray-500">Milik Target:</span>
                                            <span class="font-semibold text-purple-700">{{ \Carbon\Carbon::parse($req->targetSchedule->schedule_date)->format('d/m/Y') }}</span>
                                        </div>
                                        <div class="text-right text-xs text-gray-600">
                                            {{ $req->targetSchedule->shift->shift_name }} ({{ \Carbon\Carbon::parse($req->targetSchedule->shift->start_time)->format('H:i') }}-{{ \Carbon\Carbon::parse($req->targetSchedule->shift->end_time)->format('H:i') }})
                                        </div>
                                    </div>
                                </div>
                                @if($req->reason)
                                    <div class="mt-2 text-xs text-gray-500 italic px-2">"{{ $req->reason }}"</div>
                                @endif
                                @if($req->admin_note)
                                    <div class="mt-2 text-xs text-sky-800 bg-sky-50 px-2 py-1 rounded border border-sky-100 whitespace-normal">
                                        <strong>Admin Note:</strong> {{ $req->admin_note }}
                                    </div>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm">
                                @if($req->status === 'pending_admin')
                                    <span class="inline-flex rounded-full bg-blue-100 px-2 leading-5 text-xs font-semibold text-blue-800 border border-blue-200">Pending Admin</span>
                                @elseif($req->status === 'approved')
                                    <span class="inline-flex rounded-full bg-emerald-100 px-2 leading-5 text-xs font-semibold text-emerald-800 border border-emerald-200">Disetujui</span>
                                @elseif(in_array($req->status, ['rejected_by_target', 'rejected_by_admin']))
                                    <span class="inline-flex rounded-full bg-red-100 px-2 leading-5 text-xs font-semibold text-red-800 border border-red-200">Ditolak</span>
                                @elseif($req->status === 'pending_target')
                                    <span class="inline-flex rounded-full bg-amber-100 px-2 leading-5 text-xs font-semibold text-amber-800 border border-amber-200">Pending Target</span>
                                @else
                                    <span class="inline-flex rounded-full bg-gray-100 px-2 leading-5 text-xs font-semibold text-gray-800 border border-gray-200">Dibatalkan</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-right text-sm font-medium">
                                @php
                                    $isExpired = \Carbon\Carbon::parse($req->requestedSchedule->schedule_date)->startOfDay()->lte(\Carbon\Carbon::today()) || \Carbon\Carbon::parse($req->targetSchedule->schedule_date)->startOfDay()->lte(\Carbon\Carbon::today());
                                @endphp
                                @if($req->status === 'pending_admin')
                                    <div class="flex flex-wrap gap-2 justify-end sm:justify-start">
                                        <button type="button" onclick="openSwapAdminNoteModal('{{ route('admin.swaps.reject', $req->id) }}', 'reject')" class="flex items-center justify-center h-8 w-8 rounded-full bg-red-100 text-red-600 hover:bg-red-200" title="Tolak">
                                            <i data-lucide="x" class="h-4 w-4"></i>
                                        </button>
                                        @if(!$isExpired)
                                            <form action="{{ route('admin.swaps.approve', $req->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="flex items-center justify-center h-8 w-8 rounded-full bg-emerald-100 text-emerald-600 hover:bg-emerald-200" title="Terima">
                                                    <i data-lucide="check" class="h-4 w-4"></i>
                                                </button>
                                            </form>
                                        @else
                                            <button type="button" class="flex items-center justify-center h-8 w-8 rounded-full bg-emerald-100 text-emerald-600 opacity-50 cursor-not-allowed" title="Tidak dapat disetujui (jadwal hari ini atau lewat)" disabled>
                                                <i data-lucide="check" class="h-4 w-4"></i>
                                            </button>
                                        @endif
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                                Tidak ada data request swap.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination Footer --}}
            <div class="flex flex-col border-t border-gray-200 bg-gray-50 px-6 py-4 md:flex-row md:items-center md:justify-between rounded-b-2xl">
                <div class="flex items-center justify-center space-x-3 text-sm font-medium text-gray-600 md:justify-start">
                    <span>Tampilkan</span>
                    <select id="pageSize" class="cursor-pointer rounded-xl border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 focus:border-sky-500 focus:ring-2 focus:ring-sky-500 focus:outline-none">
                        <option value="5">5</option>
                        <option value="10" selected>10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <span>data</span>
                </div>
                <div class="mt-4 flex flex-col items-center space-y-4 sm:flex-row sm:space-y-0 sm:space-x-6 md:mt-0 md:justify-end">
                    <span id="paginationInfo" class="text-sm font-medium text-gray-500">Menampilkan 0 – 0 dari 0 data</span>
                    <div id="paginationButtons" class="flex items-center space-x-1"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Admin Note Modal -->
<div id="swapAdminNoteModal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black/50 transition-opacity">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 overflow-hidden transform transition-all">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
            <div>
                <h3 id="swapAdminNoteModalTitle" class="text-lg font-bold text-gray-900">Konfirmasi</h3>
                <p id="swapAdminNoteModalSubtitle" class="text-xs text-gray-500 mt-1">Tambahkan catatan</p>
            </div>
            <button type="button" onclick="closeSwapAdminNoteModal()" class="text-gray-400 hover:text-gray-500 hover:bg-gray-200 p-2 rounded-lg transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
            </button>
        </div>
        
        <form id="swapAdminNoteForm" method="POST" class="p-6">
            @csrf
            <div class="mb-5">
                <label for="swapAdminNoteInput" class="block text-sm font-semibold text-gray-700 mb-2">Admin Note</label>
                <textarea 
                    id="swapAdminNoteInput" 
                    name="admin_note" 
                    rows="3" 
                    class="w-full rounded-xl border-gray-200 shadow-sm focus:border-sky-500 focus:ring-sky-500 sm:text-sm p-3 border resize-none"
                    placeholder="Catatan..."
                    required minlength="5"
                ></textarea>
            </div>
            
            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" onclick="closeSwapAdminNoteModal()" class="px-4 py-2 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 text-sm font-semibold text-white bg-sky-600 border border-transparent rounded-xl hover:bg-sky-700 transition-colors shadow-sm">
                    Submit
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openSwapAdminNoteModal(actionUrl, actionType) {
        const modal = document.getElementById('swapAdminNoteModal');
        const form = document.getElementById('swapAdminNoteForm');
        const noteInput = document.getElementById('swapAdminNoteInput');
        const title = document.getElementById('swapAdminNoteModalTitle');
        const subtitle = document.getElementById('swapAdminNoteModalSubtitle');
        
        form.action = actionUrl;
        
        title.textContent = 'Tolak Swap';
        subtitle.textContent = '(Wajib) Berikan alasan penolakan (min 5 karakter)';
        noteInput.placeholder = 'Alasan penolakan...';
        
        modal.classList.remove('hidden');
    }

    function closeSwapAdminNoteModal() {
        const modal = document.getElementById('swapAdminNoteModal');
        modal.classList.add('hidden');
        document.getElementById('swapAdminNoteInput').value = '';
    }

    // Client-side Search & Pagination for Admin
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('admin_search_input');
        const statusSelect = document.getElementById('statusFilter');
        const pageSizeSelect = document.getElementById('pageSize');
        
        const infoText = document.getElementById('paginationInfo');
        const buttonsContainer = document.getElementById('paginationButtons');
        
        const tableContainer = document.getElementById('table-container');
        if (!tableContainer) return;
        
        const rows = Array.from(tableContainer.querySelectorAll('.admin-swap-row'));
        
        let currentPage = 1;
        let pageSize = parseInt(pageSizeSelect.value);
        let filteredRows = [...rows];
        
        function render() {
            const searchTerm = searchInput.value.toLowerCase().trim();
            const statusFilter = statusSelect.value;
            
            filteredRows = rows.filter(row => {
                const names = row.getAttribute('data-names') || '';
                const status = row.getAttribute('data-status') || '';
                
                const matchesSearch = names.includes(searchTerm);
                const matchesStatus = statusFilter === '' || status === statusFilter;
                
                return matchesSearch && matchesStatus;
            });
            
            const total = filteredRows.length;
            const totalPages = Math.max(1, Math.ceil(total / pageSize));
            if (currentPage > totalPages) currentPage = totalPages;
            
            const start = total === 0 ? 0 : (currentPage - 1) * pageSize + 1;
            const end = Math.min(currentPage * pageSize, total);
            infoText.textContent = total === 0 ? 'Tidak ada data' : `Menampilkan ${start} – ${end} dari ${total} data`;
            
            // Hide all
            rows.forEach(row => row.style.display = 'none');
            // Show current page
            filteredRows.slice((currentPage - 1) * pageSize, currentPage * pageSize).forEach(row => {
                row.style.display = '';
            });
            
            // Render buttons
            buttonsContainer.innerHTML = '';
            const btnClass = 'inline-flex items-center justify-center min-w-[2.25rem] h-[2.25rem] px-2 rounded-xl text-sm font-bold transition-all duration-200 border-none';
            const inactiveClass = 'bg-gray-100 text-gray-700 hover:bg-gray-200';
            const activeClass = 'bg-sky-600 text-white shadow-md shadow-sky-200';
            const disabledClass = 'opacity-30 cursor-not-allowed bg-gray-50 text-gray-400';

            function createBtn(html, page, disabled = false, active = false) {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.innerHTML = html;
                btn.className = `${btnClass} ${disabled ? disabledClass : (active ? activeClass : inactiveClass)}`;
                if (!disabled && !active) {
                    btn.onclick = () => {
                        currentPage = page;
                        render();
                    };
                }
                buttonsContainer.appendChild(btn);
            }

            createBtn('<i data-lucide="chevron-left" class="h-4 w-4"></i>', currentPage - 1, currentPage === 1);
            
            for (let i = 1; i <= totalPages; i++) {
                if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
                    createBtn(i, i, false, i === currentPage);
                } else if (i === currentPage - 2 || i === currentPage + 2) {
                    createBtn('...', i, true);
                }
            }
            
            createBtn('<i data-lucide="chevron-right" class="h-4 w-4"></i>', currentPage + 1, currentPage === totalPages);
            lucide.createIcons();
        }

        searchInput.addEventListener('input', () => { currentPage = 1; render(); });
        statusSelect.addEventListener('change', () => { currentPage = 1; render(); });
        pageSizeSelect.addEventListener('change', (e) => {
            pageSize = parseInt(e.target.value);
            currentPage = 1;
            render();
        });
        
        render();
    });
</script>
@endsection
