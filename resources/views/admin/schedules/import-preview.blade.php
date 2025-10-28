@extends('layouts.admin')

@section('title', 'Preview Import Jadwal')

@section('content')
    <div class="min-h-screen bg-white">
        {{-- Header Section --}}
        <div class="bg-white px-6 py-4 border-b border-gray-200">
            <div class="mx-auto">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-emerald-100 to-emerald-200 rounded-xl flex items-center justify-center">
                            <i data-lucide="file-check" class="w-6 h-6 text-emerald-600"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-800">Preview Import Jadwal</h1>
                            <p class="text-sm text-gray-500">
                                Bulan: {{ \Carbon\Carbon::createFromDate((int)$year, (int)$month, 1)->translatedFormat('F') }} {{ (int)$year }}
                            </p>
                        </div>
                    </div>
                    
                    <form action="{{ route('admin.schedules.import-cancel') }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg transition-all">
                            <i data-lucide="x" class="w-4 h-4"></i>
                            <span>Batal</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Main Content --}}
        <div class="mx-auto px-6 py-6">
            @if (session('error'))
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
                    <div class="flex items-start gap-2">
                        <i data-lucide="alert-triangle" class="w-5 h-5 text-red-600 mt-0.5"></i>
                        <div>{{ session('error') }}</div>
                    </div>
                </div>
            @endif
            {{-- Summary Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                {{-- Total Schedules --}}
                <div class="bg-white rounded-xl border border-blue-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-blue-600">Total Jadwal</p>
                            <p class="text-3xl font-bold text-blue-700 mt-1">{{ $successCount + $skipCount }}</p>
                        </div>
                        <div class="p-3 bg-blue-100 rounded-lg">
                            <i data-lucide="calendar" class="w-8 h-8 text-blue-600"></i>
                        </div>
                    </div>
                </div>

                {{-- New Schedules --}}
                <div class="bg-white rounded-xl border border-green-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-green-600">Jadwal Baru</p>
                            <p class="text-3xl font-bold text-green-700 mt-1">{{ $successCount }}</p>
                            <p class="text-xs text-green-600 mt-1">Akan ditambahkan</p>
                        </div>
                        <div class="p-3 bg-green-100 rounded-lg">
                            <i data-lucide="plus-circle" class="w-8 h-8 text-green-600"></i>
                        </div>
                    </div>
                </div>

                {{-- Existing Schedules --}}
                <div class="bg-white rounded-xl border border-amber-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-amber-600">Sudah Ada</p>
                            <p class="text-3xl font-bold text-amber-700 mt-1">{{ $skipCount }}</p>
                            <p class="text-xs text-amber-600 mt-1">Akan dilewati</p>
                        </div>
                        <div class="p-3 bg-amber-100 rounded-lg">
                            <i data-lucide="alert-circle" class="w-8 h-8 text-amber-600"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Errors (if any) --}}
            @if(isset($importErrors) && count($importErrors) > 0)
                <div class="bg-red-50 border border-red-200 rounded-xl p-6 mb-6">
                    <div class="flex items-start gap-3">
                        <i data-lucide="alert-triangle" class="w-6 h-6 text-red-600 mt-0.5"></i>
                        <div class="flex-1">
                            <h3 class="text-lg font-bold text-red-800 mb-2">Error Ditemukan</h3>
                            <ul class="list-disc ml-5 text-sm text-red-700 space-y-1">
                                @foreach($importErrors as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Preview Table --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-emerald-50 to-teal-50">
                    <h2 class="text-lg font-bold text-gray-800">Preview Data Import</h2>
                    <p class="text-sm text-gray-600 mt-1">Periksa data di bawah ini sebelum menyimpan</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Shift 1</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Shift 2</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($previewData as $index => $data)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-gradient-to-br from-sky-100 to-sky-200 rounded-lg flex items-center justify-center mr-3">
                                                <span class="text-xs font-bold text-sky-700">{{ strtoupper(substr($data['user_name'], 0, 2)) }}</span>
                                            </div>
                                            <div>
                                                <div class="text-sm font-semibold text-gray-900">{{ $data['user_name'] }}</div>
                                                <div class="text-xs text-gray-500">ID: {{ $data['user_id'] }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($data['schedule_date'])->format('d M Y') }}</div>
                                        <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($data['schedule_date'])->translatedFormat('l') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <span class="px-3 py-1 bg-purple-100 text-purple-800 text-xs font-semibold rounded-full">
                                                {{ $data['shift_1_name'] }}
                                            </span>
                                            @if($data['shift_1_status'] == 'new')
                                                <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-medium rounded">Baru</span>
                                            @else
                                                <span class="px-2 py-1 bg-amber-100 text-amber-700 text-xs font-medium rounded">Ada</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($data['shift_2_id'])
                                            <div class="flex items-center gap-2">
                                                <span class="px-3 py-1 bg-purple-100 text-purple-800 text-xs font-semibold rounded-full">
                                                    {{ $data['shift_2_name'] }}
                                                </span>
                                                @if($data['shift_2_status'] == 'new')
                                                    <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-medium rounded">Baru</span>
                                                @else
                                                    <span class="px-2 py-1 bg-amber-100 text-amber-700 text-xs font-medium rounded">Ada</span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-gray-400 text-sm">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $hasNew = $data['shift_1_status'] == 'new' || $data['shift_2_status'] == 'new';
                                        @endphp
                                        @if($hasNew)
                                            <span class="inline-flex items-center gap-1 px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">
                                                <i data-lucide="check-circle" class="w-3 h-3"></i>
                                                Akan Disimpan
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-3 py-1 bg-gray-100 text-gray-600 text-xs font-semibold rounded-full">
                                                <i data-lucide="minus-circle" class="w-3 h-3"></i>
                                                Dilewati
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <i data-lucide="inbox" class="w-16 h-16 text-gray-300 mb-3"></i>
                                            <p class="text-gray-500 font-medium">Tidak ada data untuk diimport</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="mt-6 flex items-center justify-between bg-white rounded-xl border border-gray-200 p-6">
                <div class="flex items-start gap-3">
                    <i data-lucide="info" class="w-5 h-5 text-blue-600 mt-0.5"></i>
                    <div>
                        <p class="text-sm font-semibold text-gray-800">Konfirmasi Import</p>
                        <p class="text-xs text-gray-600 mt-1">
                            Klik tombol "Simpan Jadwal" untuk menyimpan <strong class="text-green-600">{{ $successCount }} jadwal baru</strong> ke database.
                            @if($skipCount > 0)
                                <strong class="text-amber-600">{{ $skipCount }} jadwal</strong> akan dilewati karena sudah ada.
                            @endif
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <form action="{{ route('admin.schedules.import-cancel') }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg transition-all">
                            <i data-lucide="x" class="w-4 h-4"></i>
                            <span>Batal</span>
                        </button>
                    </form>

                    <form action="{{ route('admin.schedules.import-confirm') }}" method="POST" id="confirmForm">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg transition-all shadow-sm hover:shadow-md">
                            <i data-lucide="check-circle" class="w-4 h-4"></i>
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
        document.getElementById('confirmForm').addEventListener('submit', function(e) {
            if (!confirm('Apakah Anda yakin ingin menyimpan {{ $successCount }} jadwal baru?')) {
                e.preventDefault();
            }
        });
    </script>
    @endpush
@endsection
