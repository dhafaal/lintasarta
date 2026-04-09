@extends('layouts.user')

@section('content')
<div class="h-full pb-20 sm:pb-0">
    <div class="">
        {{-- Header Section --}}
        <div class="mb-6 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-gray-900">Request Swap Schedules</h1>
                    <p class="mt-1 text-sm text-gray-500">Ajukan atau kelola pertukaran jadwal dengan karyawan lain.</p>
                </div>
                <button onclick="document.getElementById('swap-request-modal').classList.remove('hidden')" class="inline-flex items-center justify-center gap-2 rounded-xl bg-purple-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-all hover:bg-purple-700 hover:shadow-md focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">
                    <i data-lucide="plus" class="h-4 w-4"></i>
                    Request Swap
                </button>
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

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            {{-- Incoming Requests --}}
            <div class="flex flex-col gap-4">
                <h2 class="text-lg font-bold text-gray-900">Incoming Requests (Menunggu Persetujuan Anda)</h2>
                @forelse($incomingRequests as $req)
                    <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition-all hover:border-purple-200 hover:shadow-md">
                        <div class="mb-4 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-purple-100 text-purple-700 font-bold">
                                    {{ substr($req->requester->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900">{{ $req->requester->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $req->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            <span class="inline-flex rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-800 border border-amber-200">Pending</span>
                        </div>
                        
                        <div class="mb-4 rounded-lg bg-gray-50 p-3 flex sm:flex-row flex-col sm:items-center justify-between border border-gray-100 gap-3">
                            <div class="flex-1">
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Jadwal Dia</p>
                                <p class="text-sm font-bold text-purple-700">{{ \Carbon\Carbon::parse($req->requestedSchedule->schedule_date)->format('d M Y') }}</p>
                                <p class="text-xs text-gray-700">{{ $req->requestedSchedule->shift->shift_name }} ({{ \Carbon\Carbon::parse($req->requestedSchedule->shift->start_time)->format('H:i') }}-{{ \Carbon\Carbon::parse($req->requestedSchedule->shift->end_time)->format('H:i') }})</p>
                            </div>
                            <div class="hidden sm:flex shrink-0 items-center justify-center">
                                <i data-lucide="arrow-right-left" class="h-5 w-5 text-gray-400"></i>
                            </div>
                            <div class="flex sm:hidden shrink-0 items-center justify-center">
                                <i data-lucide="arrow-down-up" class="h-4 w-4 text-gray-400"></i>
                            </div>
                            <div class="flex-1 sm:text-right">
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Jadwal Anda</p>
                                <p class="text-sm font-bold text-indigo-700">{{ \Carbon\Carbon::parse($req->targetSchedule->schedule_date)->format('d M Y') }}</p>
                                <p class="text-xs text-gray-700">{{ $req->targetSchedule->shift->shift_name }} ({{ \Carbon\Carbon::parse($req->targetSchedule->shift->start_time)->format('H:i') }}-{{ \Carbon\Carbon::parse($req->targetSchedule->shift->end_time)->format('H:i') }})</p>
                            </div>
                        </div>

                        @if($req->reason)
                            <div class="mb-4 text-sm text-gray-600 bg-gray-50 p-3 rounded border border-gray-100 italic">
                                "{{ $req->reason }}"
                            </div>
                        @endif

                        @if($req->admin_note)
                            <div class="mb-4 rounded-md bg-blue-50 p-3 text-sm text-blue-800 border border-blue-100">
                                <span class="font-bold">Admin Note:</span> {{ $req->admin_note }}
                            </div>
                        @endif

                        <div class="flex gap-2 justify-end">
                            <button type="button" onclick="showRejectModal({{ $req->id }})" class="rounded-lg bg-white px-4 py-2 text-sm font-medium text-red-600 ring-1 ring-inset ring-red-200 hover:bg-red-50 focus:outline-none">Tolak</button>
                            <form action="{{ route('user.swaps.accept', $req->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="rounded-lg bg-purple-600 px-4 py-2 text-sm font-medium text-white hover:bg-purple-700 focus:outline-none">Terima</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="rounded-xl border border-dashed border-gray-300 bg-white p-8 text-center text-sm text-gray-500">
                        Tidak ada request masuk.
                    </div>
                @endforelse
            </div>

            {{-- Outgoing Requests --}}
            <div class="flex flex-col gap-4">
                <h2 class="text-lg font-bold text-gray-900">Outgoing Requests (Pengajuan Anda)</h2>
                @forelse($outgoingRequests as $req)
                    <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition-all hover:border-indigo-200 hover:shadow-md">
                        <div class="mb-4 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div>
                                    <p class="text-sm font-bold text-gray-900">Ke: {{ $req->targetUser->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $req->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            @if($req->status === 'pending_target')
                                <span class="inline-flex rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-800 border border-amber-200">Menunggu User</span>
                            @elseif($req->status === 'pending_admin')
                                <span class="inline-flex rounded-full bg-blue-100 px-2.5 py-1 text-xs font-semibold text-blue-800 border border-blue-200">Menunggu Admin</span>
                            @elseif($req->status === 'approved')
                                <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-800 border border-emerald-200">Disetujui</span>
                            @elseif(in_array($req->status, ['rejected_by_target', 'rejected_by_admin']))
                                <span class="inline-flex rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-800 border border-red-200">Ditolak</span>
                            @else
                                <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-800 border border-gray-200">Dibatalkan</span>
                            @endif
                        </div>
                        
                        <div class="mb-3 rounded-lg bg-gray-50 p-3 flex sm:flex-row flex-col sm:items-center justify-between border border-gray-100 gap-3">
                            <div class="flex-1">
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Jadwal Anda</p>
                                <p class="text-sm font-bold text-indigo-700">{{ \Carbon\Carbon::parse($req->requestedSchedule->schedule_date)->format('d M Y') }}</p>
                                <p class="text-xs text-gray-700">{{ $req->requestedSchedule->shift->shift_name }} ({{ \Carbon\Carbon::parse($req->requestedSchedule->shift->start_time)->format('H:i') }}-{{ \Carbon\Carbon::parse($req->requestedSchedule->shift->end_time)->format('H:i') }})</p>
                            </div>
                            <div class="hidden sm:flex shrink-0 items-center justify-center">
                                <i data-lucide="arrow-right-left" class="h-4 w-4 text-gray-400"></i>
                            </div>
                            <div class="flex sm:hidden shrink-0 items-center justify-center">
                                <i data-lucide="arrow-down-up" class="h-4 w-4 text-gray-400"></i>
                            </div>
                            <div class="flex-1 sm:text-right">
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Jadwal Dia</p>
                                <p class="text-sm font-bold text-purple-700">{{ \Carbon\Carbon::parse($req->targetSchedule->schedule_date)->format('d M Y') }}</p>
                                <p class="text-xs text-gray-700">{{ $req->targetSchedule->shift->shift_name }} ({{ \Carbon\Carbon::parse($req->targetSchedule->shift->start_time)->format('H:i') }}-{{ \Carbon\Carbon::parse($req->targetSchedule->shift->end_time)->format('H:i') }})</p>
                            </div>
                        </div>

                        @if($req->reason)
                            <div class="mb-3 text-sm text-gray-600 bg-gray-50 p-3 rounded border border-gray-100 italic">
                                "{{ $req->reason }}"
                            </div>
                        @endif

                        @if($req->target_rejection_reason)
                            <div class="mb-3 rounded-md bg-red-50 p-3 text-sm text-red-800 border border-red-100 italic">
                                <span class="font-bold">Alasan Penolakan:</span> "{{ $req->target_rejection_reason }}"
                            </div>
                        @endif

                        @if($req->admin_note)
                            <div class="mb-3 rounded-md bg-blue-50 p-3 text-sm text-blue-800 border border-blue-100">
                                <span class="font-bold">Admin Note:</span> {{ $req->admin_note }}
                            </div>
                        @endif

                        @if(in_array($req->status, ['pending_target', 'pending_admin']))
                            <div class="flex justify-end mt-2">
                                <form action="{{ route('user.swaps.reject', $req->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-xs text-red-600 hover:text-red-700 font-semibold underline">Batalkan</button>
                                </form>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="rounded-xl border border-dashed border-gray-300 bg-white p-8 text-center text-sm text-gray-500">
                        Belum ada pengajuan.
                    </div>
                @endforelse
            </div>
        </div>

        {{-- History Section --}}
        <div class="mt-8 rounded-2xl bg-white shadow-sm ring-1 ring-gray-100">
            <div class="border-b border-gray-100 p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-bold text-gray-900">History (Riwayat Swap)</h2>
                    <p class="text-sm text-gray-500">Daftar riwayat pertukaran jadwal yang sudah selesai atau dibatalkan.</p>
                </div>
                
                {{-- Filter Input --}}
                <div class="flex w-full max-w-sm items-center gap-2">
                    <div class="relative w-full">
                        <i data-lucide="search" class="absolute left-3 top-2.5 h-4 w-4 text-gray-400"></i>
                        <input type="text" id="history_search_input" placeholder="Cari nama karyawan..." class="w-full rounded-lg border border-gray-300 py-2 pl-9 pr-4 text-sm focus:border-purple-500 focus:outline-none focus:ring-1 focus:ring-purple-500">
                    </div>
                </div>
            </div>

            <div class="p-6">
                @if($historyRequests->count() > 0)
                    <div id="history-grid" class="grid grid-cols-1 gap-4 lg:grid-cols-2 xl:grid-cols-3">
                        @foreach($historyRequests as $req)
                            @php
                                $isRequester = $req->requester_id === Auth::id();
                                $counterpart = $isRequester ? $req->targetUser : $req->requester;
                            @endphp
                            <div class="history-card rounded-xl border border-gray-200 bg-white p-5 shadow-sm" data-counterpart="{{ strtolower($counterpart->name) }}">
                                <div class="mb-4 flex items-center justify-between">
                                    @php
                                        $mySchedule = $isRequester ? $req->requestedSchedule : $req->targetSchedule;
                                        $theirSchedule = $isRequester ? $req->targetSchedule : $req->requestedSchedule;
                                    @endphp
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 text-gray-700 font-bold">
                                            {{ substr($counterpart->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-gray-900">{{ $counterpart->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $req->created_at->format('d M Y') }}</p>
                                        </div>
                                    </div>
                                    @if($req->status === 'approved')
                                        <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-800 border border-emerald-200">Disetujui</span>
                                    @elseif(in_array($req->status, ['rejected_by_target', 'rejected_by_admin']))
                                        <span class="inline-flex rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-800 border border-red-200">Ditolak</span>
                                    @else
                                        <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-800 border border-gray-200">Dibatalkan</span>
                                    @endif
                                </div>
                                
                                <div class="mb-3 rounded-lg bg-gray-50 p-3 flex flex-col gap-2 border border-gray-100">
                                    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-3">
                                        <div>
                                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-0.5">Jadwal Anda</p>
                                            <p class="text-xs font-bold text-indigo-700">{{ \Carbon\Carbon::parse($mySchedule->schedule_date)->format('d M Y') }}</p>
                                            <p class="text-xs text-gray-600 mt-0.5">{{ $mySchedule->shift->shift_name ?? '' }} ({{ \Carbon\Carbon::parse($mySchedule->shift->start_time)->format('H:i') }}-{{ \Carbon\Carbon::parse($mySchedule->shift->end_time)->format('H:i') }})</p>
                                        </div>
                                        <div class="hidden sm:flex items-center justify-center">
                                            <i data-lucide="arrow-right-left" class="h-4 w-4 text-gray-400"></i>
                                        </div>
                                        <div class="flex sm:hidden items-center justify-center my-1">
                                            <i data-lucide="arrow-down-up" class="h-4 w-4 text-gray-400"></i>
                                        </div>
                                        <div class="sm:text-right">
                                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-0.5">Jadwal {{ explode(' ', $counterpart->name)[0] }}</p>
                                            <p class="text-xs font-bold text-purple-700">{{ \Carbon\Carbon::parse($theirSchedule->schedule_date)->format('d M Y') }}</p>
                                            <p class="text-xs text-gray-600 mt-0.5">{{ $theirSchedule->shift->shift_name ?? '' }} ({{ \Carbon\Carbon::parse($theirSchedule->shift->start_time)->format('H:i') }}-{{ \Carbon\Carbon::parse($theirSchedule->shift->end_time)->format('H:i') }})</p>
                                        </div>
                                    </div>
                                </div>

                                @if($req->target_rejection_reason || $req->admin_note)
                                    <div class="mt-3 text-xs bg-gray-50 p-2 rounded text-gray-600">
                                        @if($req->target_rejection_reason)
                                            <span class="font-semibold text-red-600">Ditolak User:</span> {{ $req->target_rejection_reason }}<br>
                                        @endif
                                        @if($req->admin_note)
                                            <span class="font-semibold text-blue-600">Admin Note:</span> {{ $req->admin_note }}
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="rounded-xl border border-dashed border-gray-300 bg-white p-8 text-center text-sm text-gray-500">
                        Belum ada riwayat pertukaran jadwal.
                    </div>
                @endif
            </div>

            {{-- Pagination Footer --}}
            <div class="flex flex-col border-t border-gray-200 bg-white px-5 py-4 md:flex-row md:items-center md:justify-between rounded-b-2xl">
                <div class="flex items-center justify-center space-x-3 text-sm font-medium text-gray-600 md:justify-start">
                    <span>Tampilkan</span>
                    <select id="pageSize" class="cursor-pointer rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 focus:border-purple-500 focus:ring-2 focus:ring-purple-500 focus:outline-none">
                        <option value="5">5</option>
                        <option value="10" selected>10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <span>data</span>
                </div>
                <div class="mt-4 flex items-center justify-center space-x-4 md:mt-0 md:justify-end">
                    <span id="paginationInfo" class="text-sm font-medium text-gray-500">Menampilkan 0 – 0 dari 0 data</span>
                    <div id="paginationButtons" class="flex items-center space-x-1"></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Create Swap Request Modal --}}
<div id="swap-request-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex min-h-screen items-center justify-center px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-black/50 transition-opacity" aria-hidden="true" onclick="document.getElementById('swap-request-modal').classList.add('hidden')"></div>

        <span class="hidden sm:inline-block sm:h-screen sm:align-middle" aria-hidden="true">&#8203;</span>

        <div class="inline-block transform overflow-hidden rounded-2xl bg-white text-left align-bottom shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-xl sm:align-middle relative z-10">
            <div class="absolute right-0 top-0 pr-4 pt-4">
                <button type="button" onclick="document.getElementById('swap-request-modal').classList.add('hidden')" class="rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">
                    <span class="sr-only">Close</span>
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>

            <form action="{{ route('user.swaps.store') }}" method="POST" id="form-swap-request" class="flex flex-col">
                @csrf
                <div class="bg-purple-600 px-4 py-5 sm:px-6">
                    <h3 class="text-base font-semibold leading-6 text-white flex items-center gap-2" id="modal-title">
                        <i data-lucide="arrow-right-left" class="h-5 w-5"></i>
                        Buat Request Swap Jadwal
                    </h3>
                </div>

                <div class="p-6 space-y-5">
                    {{-- Select My Schedule --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jadwal Anda (Masa Depan)</label>
                        <select name="requested_schedule_id" id="my_schedule_id" class="mt-1 block w-full rounded-md border-gray-300 py-2 pl-3 pr-10 text-base focus:border-purple-500 focus:outline-none focus:ring-purple-500 sm:text-sm" required onchange="resetSearch()">
                            <option value="">-- Pilih Jadwal Anda --</option>
                            @foreach($mySchedules as $sch)
                                <option value="{{ $sch->id }}" data-date="{{ $sch->schedule_date }}">
                                    {{ \Carbon\Carbon::parse($sch->schedule_date)->format('d M Y') }} - {{ $sch->shift->shift_name ?? 'N/A' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Search Target Employee --}}
                    <div id="target-user-box" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cari Karyawan Pengganti</label>
                        <div class="relative">
                            <i data-lucide="search" class="absolute left-3 top-2.5 h-4 w-4 text-gray-400"></i>
                            <input type="text" id="user-search" class="pl-9 block w-full rounded-md border-gray-300 py-2 text-sm focus:border-purple-500 focus:ring-purple-500" placeholder="Ketik nama karyawan..." autocomplete="off">
                            <input type="hidden" name="target_user_id" id="target_user_id" required>
                            
                            {{-- Autocomplete Dropdown --}}
                            <div id="user-results" class="absolute z-10 mt-1 hidden w-full rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5"></div>
                        </div>
                    </div>

                    {{-- Select Target Schedule --}}
                    <div id="target-schedule-box" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Jadwal Karyawan Tersebut</label>
                        <select name="target_schedule_id" id="target_schedule_id" class="mt-1 block w-full rounded-md border-gray-300 py-2 pl-3 pr-10 text-base focus:border-purple-500 focus:outline-none focus:ring-purple-500 sm:text-sm" required>
                            <option value="">-- Pilih Jadwal --</option>
                        </select>
                        <p class="mt-1 text-xs text-red-500 hidden" id="same-day-warning">Tidak ada jadwal tersedia selain di hari yang sama dengan jadwal Anda.</p>
                    </div>

                    {{-- Reason --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alasan Swap <span class="text-purple-500">*</span></label>
                        <textarea name="reason" rows="2" class="mt-1 block w-full rounded-md border-gray-300 py-2 pl-3 focus:border-purple-500 focus:ring-purple-500 sm:text-sm" placeholder="Kenapa ingin bertukar jadwal?" required minlength="5"></textarea>
                    </div>
                </div>

                <div class="bg-gray-50 px-4 py-3 pb-6 sm:flex sm:flex-row-reverse sm:px-6 border-t border-gray-200">
                    <button type="submit" id="btn-submit-swap" class="inline-flex w-full justify-center rounded-lg bg-purple-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-purple-500 disabled:opacity-50 sm:ml-3 sm:w-auto" disabled>Kirim Request</button>
                    <button type="button" onclick="document.getElementById('swap-request-modal').classList.add('hidden')" class="mt-3 inline-flex w-full justify-center rounded-lg bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Reject Swap Request Modal --}}
<div id="reject-swap-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex min-h-screen items-center justify-center px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-black/50 transition-opacity" aria-hidden="true" onclick="hideRejectModal()"></div>

        <span class="hidden sm:inline-block sm:h-screen sm:align-middle" aria-hidden="true">&#8203;</span>

        <div class="inline-block transform overflow-hidden rounded-2xl bg-white text-left align-bottom shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:align-middle relative z-10">
            <form id="form-reject-swap" method="POST" class="flex flex-col">
                @csrf
                <div class="bg-red-600 px-4 py-5 sm:px-6">
                    <h3 class="text-base font-semibold leading-6 text-white flex items-center gap-2">
                        <i data-lucide="x-circle" class="h-5 w-5"></i>
                        Tolak Request Swap
                    </h3>
                </div>

                <div class="p-6 space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alasan Penolakan <span class="text-red-500">*</span></label>
                        <textarea name="target_rejection_reason" rows="3" class="mt-1 block w-full rounded-md border-gray-300 py-2 pl-3 focus:border-red-500 focus:ring-red-500 sm:text-sm" placeholder="Masukkan alasan kenapa menolak swap ini..." required minlength="5"></textarea>
                    </div>
                </div>

                <div class="bg-gray-50 px-4 py-3 pb-6 sm:flex sm:flex-row-reverse sm:px-6 border-t border-gray-200">
                    <button type="submit" class="inline-flex w-full justify-center rounded-lg bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto">Tolak Request</button>
                    <button type="button" onclick="hideRejectModal()" class="mt-3 inline-flex w-full justify-center rounded-lg bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const userSearch = document.getElementById('user-search');
    const userResults = document.getElementById('user-results');
    const targetUserIdInp = document.getElementById('target_user_id');
    const myScheduleSelect = document.getElementById('my_schedule_id');
    
    let searchTimeout;

    function resetSearch() {
        if (myScheduleSelect.value) {
            document.getElementById('target-user-box').classList.remove('hidden');
        } else {
            document.getElementById('target-user-box').classList.add('hidden');
            document.getElementById('target-schedule-box').classList.add('hidden');
        }
        userSearch.value = '';
        targetUserIdInp.value = '';
        document.getElementById('target_schedule_id').innerHTML = '<option value="">-- Pilih Jadwal --</option>';
        checkSubmitBox();
    }

    userSearch.addEventListener('input', function(e) {
        clearTimeout(searchTimeout);
        const query = e.target.value;
        targetUserIdInp.value = ''; // clear on type
        document.getElementById('target-schedule-box').classList.add('hidden');
        checkSubmitBox();

        if (query.length < 2) {
            userResults.classList.add('hidden');
            return;
        }

        searchTimeout = setTimeout(() => {
            fetch(`/user/swaps/search-users?q=${encodeURIComponent(query)}`)
                .then(res => res.json())
                .then(data => {
                    userResults.innerHTML = '';
                    if (data.length === 0) {
                        userResults.innerHTML = '<div class="px-4 py-2 text-sm text-gray-500">Karyawan tidak ditemukan</div>';
                    } else {
                        data.forEach(user => {
                            const div = document.createElement('div');
                            div.className = 'cursor-pointer px-4 py-2 text-sm text-gray-700 hover:bg-purple-50 hover:text-purple-900';
                            div.textContent = user.name;
                            div.onclick = () => {
                                selectUser(user);
                            };
                            userResults.appendChild(div);
                        });
                    }
                    userResults.classList.remove('hidden');
                });
        }, 300);
    });

    // Close dropdown on click outside
    document.addEventListener('click', function(e) {
        if (!userSearch.contains(e.target) && !userResults.contains(e.target)) {
            userResults.classList.add('hidden');
        }
    });

    function selectUser(user) {
        userSearch.value = user.name;
        targetUserIdInp.value = user.id;
        userResults.classList.add('hidden');
        loadTargetSchedules(user.id);
    }

    function loadTargetSchedules(userId) {
        const myScheduleId = myScheduleSelect.value;
        if (!myScheduleId) return;

        fetch(`/user/swaps/target-schedules`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                my_schedule_id: myScheduleId,
                target_user_id: userId
            })
        })
        .then(res => res.json())
        .then(data => {
            const targetSelect = document.getElementById('target_schedule_id');
            const warningBox = document.getElementById('same-day-warning');
            
            targetSelect.innerHTML = '<option value="">-- Pilih Jadwal --</option>';
            
            if (data.schedules && data.schedules.length > 0) {
                warningBox.classList.add('hidden');
                data.schedules.forEach(sch => {
                    const option = document.createElement('option');
                    option.value = sch.id;
                    option.textContent = `${sch.formatted_date} - ${sch.shift_name} (${sch.time_range})`;
                    targetSelect.appendChild(option);
                });
            } else {
                warningBox.classList.remove('hidden');
            }
            
            document.getElementById('target-schedule-box').classList.remove('hidden');
            checkSubmitBox();
        });
    }

    document.getElementById('target_schedule_id').addEventListener('change', checkSubmitBox);

    function checkSubmitBox() {
        const mySchedule = myScheduleSelect.value;
        const targetUser = targetUserIdInp.value;
        const targetSchedule = document.getElementById('target_schedule_id').value;

        document.getElementById('btn-submit-swap').disabled = !(mySchedule && targetUser && targetSchedule);
    }

    function showRejectModal(reqId) {
        const form = document.getElementById('form-reject-swap');
        form.action = `/user/swaps/${reqId}/reject`;
        document.getElementById('reject-swap-modal').classList.remove('hidden');
    }

    function hideRejectModal() {
        document.getElementById('reject-swap-modal').classList.add('hidden');
    }

    // Client-side Search & Pagination for History
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('history_search_input');
        const pageSizeSelect = document.getElementById('pageSize');
        const infoText = document.getElementById('paginationInfo');
        const buttonsContainer = document.getElementById('paginationButtons');
        
        const historyGrid = document.getElementById('history-grid');
        if (!historyGrid) return;
        
        const cards = Array.from(historyGrid.querySelectorAll('.history-card'));
        
        let currentPage = 1;
        let pageSize = parseInt(pageSizeSelect.value);
        let filteredCards = [...cards];
        
        function render() {
            const searchTerm = searchInput.value.toLowerCase().trim();
            
            filteredCards = cards.filter(card => {
                const counterpartName = card.getAttribute('data-counterpart') || '';
                return counterpartName.includes(searchTerm);
            });
            
            const total = filteredCards.length;
            const totalPages = Math.max(1, Math.ceil(total / pageSize));
            if (currentPage > totalPages) currentPage = totalPages;
            
            const start = total === 0 ? 0 : (currentPage - 1) * pageSize + 1;
            const end = Math.min(currentPage * pageSize, total);
            infoText.textContent = total === 0 ? 'Tidak ada data' : `Menampilkan ${start} – ${end} dari ${total} data`;
            
            // Hide all
            cards.forEach(card => card.style.display = 'none');
            // Show current page
            filteredCards.slice((currentPage - 1) * pageSize, currentPage * pageSize).forEach(card => {
                card.style.display = '';
            });
            
            // Render buttons
            buttonsContainer.innerHTML = '';
            const btnClass = 'inline-flex items-center justify-center min-w-[2.25rem] h-[2.25rem] px-2 rounded-xl text-sm font-bold transition-all duration-200 border-none';
            const inactiveClass = 'bg-gray-100 text-gray-700 hover:bg-gray-200';
            const activeClass = 'bg-purple-600 text-white shadow-md shadow-purple-200';
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
                        window.scrollTo({ top: historyGrid.offsetTop - 100, behavior: 'smooth' });
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
        pageSizeSelect.addEventListener('change', (e) => {
            pageSize = parseInt(e.target.value);
            currentPage = 1;
            render();
        });
        
        render();
    });
</script>
@endsection
