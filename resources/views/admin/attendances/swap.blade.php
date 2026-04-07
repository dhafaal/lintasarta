@extends('layouts.admin')

@section('content')
<div class="h-full bg-gray-50 pb-20 sm:pb-0">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        {{-- Header Section --}}
        <div class="mb-6 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-gray-900">Validasi Swap Schedules</h1>
                    <p class="mt-1 text-sm text-gray-500">Kelola dan setujui pertukaran jadwal yang diajukan oleh karyawan.</p>
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

        <div class="rounded-2xl bg-white shadow-sm ring-1 ring-gray-100 p-6 overflow-x-auto">
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
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($requests as $req)
                        <tr class="hover:bg-gray-50 transition-colors">
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
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs uppercase font-bold text-gray-500">Milik Requester:</span>
                                        <span class="font-semibold text-indigo-700">{{ \Carbon\Carbon::parse($req->requestedSchedule->schedule_date)->format('d/m/Y') }}</span>
                                    </div>
                                    <div class="flex items-center justify-center my-1"><i data-lucide="arrow-down-up" class="h-4 w-4 text-gray-400"></i></div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs uppercase font-bold text-gray-500">Milik Target:</span>
                                        <span class="font-semibold text-purple-700">{{ \Carbon\Carbon::parse($req->targetSchedule->schedule_date)->format('d/m/Y') }}</span>
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
                                    <div class="flex space-x-2">
                                        <button type="button" onclick="openSwapAdminNoteModal('{{ route('admin.swaps.reject', $req->id) }}', 'reject')" class="flex items-center justify-center h-8 w-8 rounded-full bg-red-100 text-red-600 hover:bg-red-200" title="Tolak">
                                            <i data-lucide="x" class="h-4 w-4"></i>
                                        </button>
                                        @if(!$isExpired)
                                            <button type="button" onclick="openSwapAdminNoteModal('{{ route('admin.swaps.approve', $req->id) }}', 'approve')" class="flex items-center justify-center h-8 w-8 rounded-full bg-emerald-100 text-emerald-600 hover:bg-emerald-200" title="Terima">
                                                <i data-lucide="check" class="h-4 w-4"></i>
                                            </button>
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
            <div class="mt-4">
                {{ $requests->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Admin Note Modal -->
<div id="swapAdminNoteModal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-gray-900 bg-opacity-50 backdrop-blur-sm transition-opacity">
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
        
        if (actionType === 'approve') {
            title.textContent = 'Terima Swap';
            subtitle.textContent = '(Wajib) Berikan catatan admin (min 5 karakter)';
            noteInput.placeholder = 'Catatan persetujuan...';
        } else {
            title.textContent = 'Tolak Swap';
            subtitle.textContent = '(Wajib) Berikan alasan penolakan (min 5 karakter)';
            noteInput.placeholder = 'Alasan penolakan...';
        }
        
        modal.classList.remove('hidden');
    }

    function closeSwapAdminNoteModal() {
        const modal = document.getElementById('swapAdminNoteModal');
        modal.classList.add('hidden');
        document.getElementById('swapAdminNoteInput').value = '';
    }
</script>
@endsection
