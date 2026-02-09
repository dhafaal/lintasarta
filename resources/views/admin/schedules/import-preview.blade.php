@extends('layouts.admin')

@section('title', 'Preview Import Jadwal')

@section('content')
    <div class="min-h-screen bg-white">
        {{-- Header Section --}}
        <div class="border-b border-gray-200 bg-white px-6 py-4">
            <div class="mx-auto">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-100 to-emerald-200"
                        >
                            <i data-lucide="file-check" class="h-6 w-6 text-emerald-600"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-800">Preview Import Jadwal</h1>
                            <p class="text-sm text-gray-500">
                                Bulan:
                                {{ \Carbon\Carbon::createFromDate((int) $year, (int) $month, 1)->translatedFormat('F') }}
                                {{ (int) $year }}
                            </p>
                        </div>
                    </div>

                    <form action="{{ route('admin.schedules.import-cancel') }}" method="POST">
                        @csrf
                        <button
                            type="submit"
                            class="inline-flex items-center gap-2 rounded-lg bg-gray-500 px-4 py-2 font-semibold text-white transition-all hover:bg-gray-600"
                        >
                            <i data-lucide="x" class="h-4 w-4"></i>
                            <span>Batal</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Main Content --}}
        <div class="mx-auto px-6 py-6">
            @if (session('error'))
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                    <div class="flex items-start gap-2">
                        <i data-lucide="alert-triangle" class="mt-0.5 h-5 w-5 text-red-600"></i>
                        <div>{{ session('error') }}</div>
                    </div>
                </div>
            @endif

            {{-- Summary Cards --}}
            <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-3">
                {{-- Total Schedules --}}
                <div class="rounded-xl border border-blue-200 bg-white p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-blue-600">Total Jadwal</p>
                            <p class="mt-1 text-3xl font-bold text-blue-700">{{ $successCount + $skipCount }}</p>
                        </div>
                        <div class="rounded-lg bg-blue-100 p-3">
                            <i data-lucide="calendar" class="h-8 w-8 text-blue-600"></i>
                        </div>
                    </div>
                </div>

                {{-- New Schedules --}}
                <div class="rounded-xl border border-green-200 bg-white p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-green-600">Jadwal Baru</p>
                            <p class="mt-1 text-3xl font-bold text-green-700">{{ $successCount }}</p>
                            <p class="mt-1 text-xs text-green-600">Akan ditambahkan</p>
                        </div>
                        <div class="rounded-lg bg-green-100 p-3">
                            <i data-lucide="plus-circle" class="h-8 w-8 text-green-600"></i>
                        </div>
                    </div>
                </div>

                {{-- Existing Schedules --}}
                <div class="rounded-xl border border-amber-200 bg-white p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-amber-600">Sudah Ada</p>
                            <p class="mt-1 text-3xl font-bold text-amber-700">{{ $skipCount }}</p>
                            <p class="mt-1 text-xs text-amber-600">Akan dilewati</p>
                        </div>
                        <div class="rounded-lg bg-amber-100 p-3">
                            <i data-lucide="alert-circle" class="h-8 w-8 text-amber-600"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Errors (if any) --}}
            @if (isset($importErrors) && count($importErrors) > 0)
                <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-6">
                    <div class="flex items-start gap-3">
                        <i data-lucide="alert-triangle" class="mt-0.5 h-6 w-6 text-red-600"></i>
                        <div class="flex-1">
                            <h3 class="mb-2 text-lg font-bold text-red-800">Error Ditemukan</h3>
                            <ul class="ml-5 list-disc space-y-1 text-sm text-red-700">
                                @foreach ($importErrors as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Preview Table --}}
            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 bg-gradient-to-r from-emerald-50 to-teal-50 px-6 py-4">
                    <h2 class="text-lg font-bold text-gray-800">Preview Data Import</h2>
                    <p class="mt-1 text-sm text-gray-600">Periksa data di bawah ini sebelum menyimpan</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                                >
                                    No
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                                >
                                    User
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                                >
                                    Tanggal
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                                >
                                    Shift 1
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                                >
                                    Shift 2
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                                >
                                    Status
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse ($previewData as $index => $data)
                                <tr class="transition-colors hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm whitespace-nowrap text-gray-900">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div
                                                class="mr-3 flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-sky-100 to-sky-200"
                                            >
                                                <span class="text-xs font-bold text-sky-700">
                                                    {{ strtoupper(substr($data['user_name'], 0, 2)) }}
                                                </span>
                                            </div>
                                            <div>
                                                <div class="text-sm font-semibold text-gray-900">
                                                    {{ $data['user_name'] }}
                                                </div>
                                                <div class="text-xs text-gray-500">ID: {{ $data['user_id'] }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ \Carbon\Carbon::parse($data['schedule_date'])->format('d M Y') }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ \Carbon\Carbon::parse($data['schedule_date'])->translatedFormat('l') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <span
                                                class="rounded-full bg-purple-100 px-3 py-1 text-xs font-semibold text-purple-800"
                                            >
                                                {{ $data['shift_1_name'] }}
                                            </span>
                                            @if ($data['shift_1_status'] == 'new')
                                                <span
                                                    class="rounded bg-green-100 px-2 py-1 text-xs font-medium text-green-700"
                                                >
                                                    Baru
                                                </span>
                                            @else
                                                <span
                                                    class="rounded bg-amber-100 px-2 py-1 text-xs font-medium text-amber-700"
                                                >
                                                    Ada
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($data['shift_2_id'])
                                            <div class="flex items-center gap-2">
                                                <span
                                                    class="rounded-full bg-purple-100 px-3 py-1 text-xs font-semibold text-purple-800"
                                                >
                                                    {{ $data['shift_2_name'] }}
                                                </span>
                                                @if ($data['shift_2_status'] == 'new')
                                                    <span
                                                        class="rounded bg-green-100 px-2 py-1 text-xs font-medium text-green-700"
                                                    >
                                                        Baru
                                                    </span>
                                                @else
                                                    <span
                                                        class="rounded bg-amber-100 px-2 py-1 text-xs font-medium text-amber-700"
                                                    >
                                                        Ada
                                                    </span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-sm text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $hasNew = $data['shift_1_status'] == 'new' || $data['shift_2_status'] == 'new';
                                        @endphp

                                        @if ($hasNew)
                                            <span
                                                class="inline-flex items-center gap-1 rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-800"
                                            >
                                                <i data-lucide="check-circle" class="h-3 w-3"></i>
                                                Akan Disimpan
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600"
                                            >
                                                <i data-lucide="minus-circle" class="h-3 w-3"></i>
                                                Dilewati
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <i data-lucide="inbox" class="mb-3 h-16 w-16 text-gray-300"></i>
                                            <p class="font-medium text-gray-500">Tidak ada data untuk diimport</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="mt-6 flex items-center justify-between rounded-xl border border-gray-200 bg-white p-6">
                <div class="flex items-start gap-3">
                    <i data-lucide="info" class="mt-0.5 h-5 w-5 text-blue-600"></i>
                    <div>
                        <p class="text-sm font-semibold text-gray-800">Konfirmasi Import</p>
                        <p class="mt-1 text-xs text-gray-600">
                            Klik tombol "Simpan Jadwal" untuk menyimpan
                            <strong class="text-green-600">{{ $successCount }} jadwal baru</strong>
                            ke database.
                            @if ($skipCount > 0)
                                <strong class="text-amber-600">{{ $skipCount }} jadwal</strong>
                                akan dilewati karena sudah ada.
                            @endif
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <form action="{{ route('admin.schedules.import-cancel') }}" method="POST">
                        @csrf
                        <button
                            type="submit"
                            class="inline-flex items-center gap-2 rounded-lg bg-gray-500 px-6 py-3 font-semibold text-white transition-all hover:bg-gray-600"
                        >
                            <i data-lucide="x" class="h-4 w-4"></i>
                            <span>Batal</span>
                        </button>
                    </form>

                    <form action="{{ route('admin.schedules.import-confirm') }}" method="POST" id="confirmForm">
                        @csrf
                        <button
                            type="submit"
                            class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-6 py-3 font-semibold text-white shadow-sm transition-all hover:bg-emerald-700 hover:shadow-md"
                        >
                            <i data-lucide="check-circle" class="h-4 w-4"></i>
                            <span>Simpan Jadwal</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Initialize Lucide icons
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }

            // Confirm before submit
            document.getElementById('confirmForm').addEventListener('submit', function (e) {
                if (!confirm('Apakah Anda yakin ingin menyimpan {{ $successCount }} jadwal baru?')) {
                    e.preventDefault();
                }
            });
        </script>
    @endpush
@endsection
