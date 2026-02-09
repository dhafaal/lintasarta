@extends('layouts.admin')

@section('title', 'Tambah Jadwal')

@section('content')
    {{-- Debug Session Data --}}
    @if (session('auto_load_month') && session('auto_load_year'))
        <div
            class="fixed top-4 right-4 z-50 rounded border border-green-400 bg-green-100 px-4 py-3 text-green-700 shadow-lg"
            role="alert"
        >
            <strong class="font-bold">Import Success!</strong>
            <span class="block sm:inline">
                Data untuk bulan {{ session('auto_load_month') }}/{{ session('auto_load_year') }} akan dimuat...
            </span>
        </div>
    @endif

    <div class="min-h-screen bg-white">
        {{-- Header Section --}}
        <div class="bg-white px-6 py-4">
            <div class="mx-auto">
                <div class="flex items-center space-x-3">
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-sky-100 to-sky-200"
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
                            class="lucide lucide-calendar text-sky-600"
                        >
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                            <line x1="16" x2="16" y1="2" y2="6" />
                            <line x1="8" x2="8" y1="2" y2="6" />
                            <line x1="3" x2="21" y1="10" y2="10" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Buat Jadwal Baru</h1>
                        <p class="text-sm text-gray-500">
                            Buat jadwal bulanan baru untuk pengguna dengan mengisi informasi di bawah ini
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Content --}}
        <div class="mx-auto px-6 py-6">
            {{-- Import Excel Section --}}
            <div
                class="mb-6 overflow-hidden rounded-xl border border-emerald-200 bg-gradient-to-r from-emerald-50 to-teal-50 shadow-sm"
            >
                <div class="border-b border-emerald-200 bg-white/50 px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-br from-emerald-100 to-emerald-200"
                        >
                            <i data-lucide="file-spreadsheet" class="h-5 w-5 text-emerald-600"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-800">Import dari Excel</h2>
                            <p class="text-sm text-gray-500">
                                Upload file Excel untuk mengisi jadwal bulanan secara otomatis
                            </p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    {{-- Success/Warning Messages for Import --}}
                    @if (session('import_errors'))
                        <div class="mb-4 rounded-lg border border-amber-200 bg-amber-50 p-4">
                            <div class="flex items-start gap-2">
                                <i data-lucide="alert-triangle" class="mt-0.5 h-5 w-5 text-amber-600"></i>
                                <div class="flex-1">
                                    <p class="mb-2 text-sm font-semibold text-amber-800">
                                        Beberapa baris memiliki error:
                                    </p>
                                    <ul class="ml-5 list-disc space-y-1 text-sm text-amber-700">
                                        @foreach (session('import_errors') as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Import Form Errors --}}
                    @if (session('error'))
                        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                            <div class="flex items-start gap-2">
                                <i data-lucide="alert-triangle" class="mt-0.5 h-5 w-5 text-red-600"></i>
                                <div>{{ session('error') }}</div>
                            </div>
                        </div>
                    @endif

                    @php
                        $importErrors = [];
                        if ($errors->has('excel_file')) {
                            $importErrors[] = $errors->first('excel_file');
                        }
                        if ($errors->has('month')) {
                            $importErrors[] = $errors->first('month');
                        }
                        if ($errors->has('year')) {
                            $importErrors[] = $errors->first('year');
                        }
                    @endphp

                    @if (! empty($importErrors))
                        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                            <div class="flex items-start gap-2">
                                <i data-lucide="x-circle" class="mt-0.5 h-5 w-5 text-red-600"></i>
                                <ul class="ml-5 list-disc">
                                    @foreach ($importErrors as $err)
                                        <li>{{ $err }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    <form
                        action="{{ route('admin.schedules.import-preview.submit') }}"
                        method="POST"
                        enctype="multipart/form-data"
                        id="importForm"
                    >
                        @csrf

                        {{-- Month and Year for Import --}}
                        <div class="mb-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-gray-700">
                                    <div class="flex items-center gap-2">
                                        <i data-lucide="calendar" class="h-4 w-4 text-emerald-600"></i>
                                        <span>
                                            Bulan
                                            <span class="text-red-500">*</span>
                                        </span>
                                    </div>
                                </label>
                                <select
                                    name="month"
                                    required
                                    class="block w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500"
                                >
                                    <option value="">Pilih Bulan</option>
                                    @for ($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}" {{ $m == date('n') ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                        </option>
                                    @endfor
                                </select>
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-semibold text-gray-700">
                                    <div class="flex items-center gap-2">
                                        <i data-lucide="calendar" class="h-4 w-4 text-emerald-600"></i>
                                        <span>
                                            Tahun
                                            <span class="text-red-500">*</span>
                                        </span>
                                    </div>
                                </label>
                                <select
                                    name="year"
                                    required
                                    class="block w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500"
                                >
                                    @for ($y = date('Y') - 1; $y <= date('Y') + 2; $y++)
                                        <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>
                                            {{ $y }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        {{-- File Upload --}}
                        <div class="mb-4">
                            <label class="mb-2 block text-sm font-semibold text-gray-700">
                                <div class="flex items-center gap-2">
                                    <i data-lucide="upload" class="h-4 w-4 text-emerald-600"></i>
                                    <span>
                                        File Excel
                                        <span class="text-red-500">*</span>
                                    </span>
                                </div>
                            </label>
                            <input
                                type="file"
                                name="excel_file"
                                accept=".xlsx,.xls"
                                required
                                class="block w-full cursor-pointer rounded-lg border border-gray-300 bg-white p-2.5 text-sm text-gray-900 focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                            />
                            <p class="mt-1 text-xs text-gray-500">Format: .xlsx atau .xls (Maksimal 2MB)</p>
                        </div>

                        {{-- Info Box --}}
                        <div class="mb-4 rounded-lg border border-blue-200 bg-blue-50 p-4">
                            <div class="flex items-start gap-2">
                                <i data-lucide="info" class="mt-0.5 h-5 w-5 text-blue-600"></i>
                                <div class="flex-1">
                                    <p class="mb-2 text-sm font-semibold text-blue-800">Format Excel:</p>
                                    <ul class="mb-3 ml-5 list-disc space-y-1 text-sm text-blue-700">
                                        <li>
                                            <strong>user_id</strong>
                                            : ID user (contoh: 3 untuk Mursidi)
                                        </li>
                                        <li>
                                            <strong>tanggal</strong>
                                            : Tanggal dalam bulan (1-31)
                                        </li>
                                        <li>
                                            <strong>shift_id</strong>
                                            : ID shift (lihat daftar shift di bawah)
                                        </li>
                                    </ul>

                                    <div class="mb-2 rounded border border-blue-300 bg-blue-100 p-3">
                                        <p class="mb-2 text-sm font-semibold text-blue-900">
                                            📋 Cara Mengisi 2 Shift dalam 1 Hari:
                                        </p>
                                        <p class="mb-2 text-xs text-blue-800">
                                            Untuk mengisi 2 shift di tanggal yang sama, buat
                                            <strong>2 baris</strong>
                                            dengan
                                            <strong>user_id</strong>
                                            dan
                                            <strong>tanggal</strong>
                                            yang sama:
                                        </p>
                                        <div class="rounded bg-white p-2 font-mono text-xs">
                                            <div class="mb-1 grid grid-cols-3 gap-2 font-bold text-blue-900">
                                                <span>user_id</span>
                                                <span>tanggal</span>
                                                <span>shift_id</span>
                                            </div>
                                            <div class="grid grid-cols-3 gap-2 text-gray-700">
                                                <span>3</span>
                                                <span>23</span>
                                                <span>1</span>
                                            </div>
                                            <div class="grid grid-cols-3 gap-2 text-gray-700">
                                                <span>3</span>
                                                <span>23</span>
                                                <span>2</span>
                                            </div>
                                        </div>
                                        <p class="mt-2 text-xs text-blue-700">
                                            ✅ Baris pertama = Shift 1 (Pagi)
                                            <br />
                                            ✅ Baris kedua = Shift 2 (Siang)
                                        </p>
                                    </div>

                                    <p class="mt-2 text-xs text-blue-600">
                                        💡 Tip: Download template untuk melihat contoh format yang benar
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Shift Reference --}}
                        <div class="mb-4 rounded-lg border border-purple-200 bg-purple-50 p-4">
                            <div class="flex items-start gap-2">
                                <i data-lucide="list" class="mt-0.5 h-5 w-5 text-purple-600"></i>
                                <div class="flex-1">
                                    <p class="mb-2 text-sm font-semibold text-purple-800">Daftar Shift ID:</p>
                                    <div class="grid grid-cols-1 gap-2 md:grid-cols-2 lg:grid-cols-3">
                                        @php
                                            $shifts = \App\Models\Shift::orderBy('id')->get();
                                        @endphp

                                        @foreach ($shifts as $shift)
                                            <div
                                                class="flex items-center gap-2 rounded border border-purple-100 bg-white px-3 py-2"
                                            >
                                                <span class="font-bold text-purple-700">ID {{ $shift->id }}:</span>
                                                <span class="text-sm text-gray-700">{{ $shift->shift_name }}</span>
                                                <span class="text-xs text-gray-500">({{ $shift->category }})</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex items-center gap-3">
                            <button
                                type="submit"
                                class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-6 py-2.5 font-semibold text-white shadow-sm transition-all duration-200 hover:bg-emerald-700 hover:shadow-md"
                            >
                                <i data-lucide="upload" class="h-4 w-4"></i>
                                <span>Import Excel</span>
                            </button>
                            <a
                                href="{{ route('admin.schedules.download-template') }}"
                                class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-6 py-2.5 font-semibold text-gray-700 shadow-sm transition-all duration-200 hover:bg-gray-50 hover:shadow-md"
                            >
                                <i data-lucide="download" class="h-4 w-4"></i>
                                <span>Download Template</span>
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Divider --}}
            <div class="relative my-8">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="bg-white px-4 font-medium text-gray-500">ATAU</span>
                </div>
            </div>

            {{-- Form Card --}}
            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 bg-gradient-to-r from-sky-50 to-blue-50 px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-br from-sky-100 to-sky-200"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="20"
                                height="20"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                class="lucide lucide-file-text text-sky-600"
                            >
                                <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z" />
                                <path d="M14 2v4a2 2 0 0 0 2 2h4" />
                                <path d="M10 9H8" />
                                <path d="M16 13H8" />
                                <path d="M16 17H8" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-800">Informasi Jadwal</h2>
                            <p class="text-sm text-gray-500">
                                Lengkapi semua field yang diperlukan untuk jadwal bulanan
                            </p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    @if ($errors->any())
                        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                            <ul class="ml-5 list-disc">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if ($errors->has('attendance_conflict'))
                        <div class="mb-4 rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                            <div class="mb-2 font-semibold">Konfirmasi Diperlukan</div>
                            <div class="mb-3">{{ $errors->first('attendance_conflict') }}</div>
                            <div class="flex gap-2">
                                <button
                                    type="button"
                                    id="confirmRemapBtn"
                                    class="rounded-md bg-amber-600 px-4 py-2 text-white hover:bg-amber-700"
                                >
                                    Pindahkan attendance & simpan
                                </button>
                                <a
                                    href="{{ url()->current() }}"
                                    class="rounded-md border border-gray-300 bg-gray-100 px-4 py-2 text-gray-700 hover:bg-gray-200"
                                >
                                    Batalkan
                                </a>
                            </div>
                        </div>
                    @endif

                    <form
                        action="{{ route('admin.schedules.store') }}"
                        method="POST"
                        class="space-y-6"
                        id="scheduleForm"
                    >
                        @csrf
                        <input type="hidden" name="form_type" value="bulk_monthly" />
                        <input type="hidden" name="on_attendance_conflict" id="on_attendance_conflict" value="" />

                        {{-- Month and Year --}}
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">
                                <div class="flex items-center gap-2">
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
                                        class="lucide lucide-calendar text-sky-600"
                                    >
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                                        <line x1="16" x2="16" y1="2" y2="6" />
                                        <line x1="8" x2="8" y1="2" y2="6" />
                                        <line x1="3" x2="21" y1="10" y2="10" />
                                    </svg>
                                    <span>
                                        Pilih Bulan dan Tahun
                                        <span class="text-red-500">*</span>
                                    </span>
                                </div>
                            </label>
                            <div class="flex items-center gap-4">
                                <select
                                    id="calendarMonth"
                                    name="month"
                                    class="block w-48 rounded-lg border border-gray-300 bg-white px-4 py-2.5 transition-all duration-200 focus:border-sky-500 focus:ring-2 focus:ring-sky-500"
                                    required
                                >
                                    <option value="" disabled selected>Pilih bulan</option>
                                    @for ($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                        </option>
                                    @endfor
                                </select>
                                <select
                                    id="calendarYear"
                                    name="year"
                                    class="block w-32 rounded-lg border border-gray-300 bg-white px-4 py-2.5 transition-all duration-200 focus:border-sky-500 focus:ring-2 focus:ring-sky-500"
                                    required
                                >
                                    <option value="" disabled selected>Pilih tahun</option>
                                    @for ($y = now()->year - 2; $y <= now()->year + 5; $y++)
                                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                                            {{ $y }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        {{-- User Selection with Search --}}
                        <div class="space-y-2">
                            <label for="user_search" class="block text-sm font-semibold text-gray-700">
                                <div class="flex items-center gap-2">
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
                                        class="lucide lucide-user text-sky-600"
                                    >
                                        <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" />
                                        <circle cx="12" cy="7" r="4" />
                                    </svg>
                                    <span>
                                        Pilih Pengguna
                                        <span class="text-red-500">*</span>
                                    </span>
                                </div>
                            </label>

                            {{-- Search Input --}}
                            <div class="relative">
                                <div class="relative">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            width="18"
                                            height="18"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            class="lucide lucide-search text-gray-400"
                                        >
                                            <circle cx="11" cy="11" r="8" />
                                            <path d="m21 21-4.3-4.3" />
                                        </svg>
                                    </div>
                                    <input
                                        type="text"
                                        id="user_search"
                                        class="block w-full rounded-lg border border-gray-300 bg-white py-2.5 pr-10 pl-12 transition-all duration-200 focus:border-sky-500 focus:ring-2 focus:ring-sky-500"
                                        placeholder="Ketik untuk mencari pengguna..."
                                        autocomplete="off"
                                        value="{{ old('user_search') }}"
                                    />
                                    <input
                                        type="hidden"
                                        name="user_id"
                                        id="selected_user_id"
                                        value="{{ old('user_id') }}"
                                        data-original-value="{{ old('user_id') }}"
                                    />
                                </div>

                                {{-- Search Results Dropdown --}}
                                <div
                                    id="user_search_results"
                                    class="absolute z-50 mt-1 hidden max-h-60 w-full overflow-y-auto rounded-lg border border-gray-200 bg-white shadow-lg"
                                >
                                    <div
                                        id="user_search_loading"
                                        class="hidden px-4 py-3 text-center text-sm text-gray-500"
                                    >
                                        <svg class="mx-auto mb-1 h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                            <circle
                                                class="opacity-25"
                                                cx="12"
                                                cy="12"
                                                r="10"
                                                stroke="currentColor"
                                                stroke-width="4"
                                            ></circle>
                                            <path
                                                class="opacity-75"
                                                fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                            ></path>
                                        </svg>
                                        Mencari pengguna...
                                    </div>
                                    <div
                                        id="user_search_no_results"
                                        class="hidden px-4 py-3 text-center text-sm text-gray-500"
                                    >
                                        Tidak ada pengguna ditemukan
                                    </div>
                                    <div id="user_search_results_list" class="divide-y divide-gray-100">
                                        {{-- Results will be populated here --}}
                                    </div>
                                </div>

                                {{-- Selected User Display --}}
                                <div id="selected_user_display" class="mt-3 hidden">
                                    <div
                                        class="flex items-center justify-between rounded-lg border-2 border-green-300 bg-green-50 p-3"
                                    >
                                        <div class="flex flex-1 items-center">
                                            <div
                                                class="mr-3 flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-green-100 to-green-200"
                                            >
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    width="18"
                                                    height="18"
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    stroke-width="2"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    class="lucide lucide-check text-green-600"
                                                >
                                                    <path d="M20 6 9 17l-5-5" />
                                                </svg>
                                            </div>
                                            <div class="flex-1">
                                                <div class="mb-0.5 text-xs font-semibold text-green-600">
                                                    ✓ Pengguna Terpilih
                                                </div>
                                                <div
                                                    class="text-sm font-medium text-gray-900"
                                                    id="selected_user_name"
                                                ></div>
                                                <div class="text-xs text-gray-500" id="selected_user_email"></div>
                                                <div class="mt-1 font-mono text-xs text-green-700">
                                                    User ID:
                                                    <span id="selected_user_id_display"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <button
                                            type="button"
                                            onclick="clearUserSelection()"
                                            class="text-gray-400 transition-colors hover:text-red-600"
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                width="18"
                                                height="18"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                class="lucide lucide-x"
                                            >
                                                <path d="M18 6 6 18" />
                                                <path d="m6 6 12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                {{-- Validation Error --}}
                                <div id="user_validation_error" class="mt-2 hidden text-sm text-red-600">
                                    Silakan pilih pengguna terlebih dahulu
                                </div>
                            </div>
                        </div>

                        {{-- Calendar Grid --}}
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">
                                <div class="flex items-center gap-2">
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
                                        class="lucide lucide-calendar-days text-sky-600"
                                    >
                                        <rect width="18" height="18" x="3" y="4" rx="2" ry="2" />
                                        <line x1="16" x2="16" y1="2" y2="6" />
                                        <line x1="8" x2="8" y1="2" y2="6" />
                                        <line x1="3" x2="21" y1="10" y2="10" />
                                        <path d="M8 14h.01" />
                                        <path d="M12 14h.01" />
                                        <path d="M16 14h.01" />
                                        <path d="M8 18h.01" />
                                        <path d="M12 18h.01" />
                                        <path d="M16 18h.01" />
                                    </svg>
                                    <span>
                                        Jadwal Per Tanggal
                                        <span class="text-red-500">*</span>
                                    </span>
                                </div>
                            </label>
                            <div class="mb-3 rounded-lg border border-sky-200 bg-sky-50 p-4">
                                <div class="flex items-start space-x-2">
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
                                        class="lucide lucide-info mt-0.5 flex-shrink-0 text-sky-600"
                                    >
                                        <circle cx="12" cy="12" r="10" />
                                        <path d="M12 16v-4" />
                                        <path d="M12 8h.01" />
                                    </svg>
                                    <div class="text-sm text-sky-700">
                                        <p class="font-medium">Jadwal Existing akan ditampilkan otomatis</p>
                                        <p class="mt-1 text-xs">
                                            Saat Anda memilih user yang sudah memiliki jadwal, shift existing akan
                                            muncul otomatis di tanggal yang sesuai. Anda dapat menambahkan shift kedua
                                            atau mengubah shift yang ada.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                                <div class="mb-3 grid grid-cols-7 gap-1">
                                    <div class="text-center text-xs font-semibold text-gray-600">Ming</div>
                                    <div class="text-center text-xs font-semibold text-gray-600">Sen</div>
                                    <div class="text-center text-xs font-semibold text-gray-600">Sel</div>
                                    <div class="text-center text-xs font-semibold text-gray-600">Rab</div>
                                    <div class="text-center text-xs font-semibold text-gray-600">Kam</div>
                                    <div class="text-center text-xs font-semibold text-gray-600">Jum</div>
                                    <div class="text-center text-xs font-semibold text-gray-600">Sab</div>
                                </div>
                                <div id="calendarDays" class="grid grid-cols-7 gap-1 text-center text-gray-600">
                                    <div class="col-span-7 py-6 text-center text-sm text-gray-400">Loading...</div>
                                </div>
                                <p class="mt-2 text-xs text-gray-500" id="daysInfo"></p>
                            </div>
                            {{-- Loading Indicator --}}
                            <div id="loadingIndicator" class="hidden">
                                <div class="flex items-center justify-center py-4">
                                    <svg class="mr-2 h-6 w-6 animate-spin text-sky-500" fill="none" viewBox="0 0 24 24">
                                        <circle
                                            class="opacity-25"
                                            cx="12"
                                            cy="12"
                                            r="10"
                                            stroke="currentColor"
                                            stroke-width="4"
                                        ></circle>
                                        <path
                                            class="opacity-75"
                                            fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                        ></path>
                                    </svg>
                                    <span class="text-sm text-sky-600">Memuat jadwal yang sudah ada...</span>
                                </div>
                            </div>
                        </div>

                        {{-- Preset Shift Cepat --}}
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">
                                <div class="flex items-center gap-2">
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
                                        class="lucide lucide-zap text-sky-600"
                                    >
                                        <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2" />
                                    </svg>
                                    <span>Preset Shift Cepat</span>
                                </div>
                            </label>
                            <div class="space-y-3 rounded-lg border border-gray-200 bg-gray-50 p-4">
                                <div>
                                    <div class="mb-2 text-xs font-medium text-gray-600">Shift 1 (Dropdown Atas):</div>
                                    <div class="flex flex-wrap gap-2">
                                        <button
                                            type="button"
                                            class="rounded-lg border border-blue-200 bg-blue-50 px-4 py-2 text-sm font-medium text-blue-700 transition-colors duration-200 hover:bg-blue-100"
                                            onclick="applyQuickPreset('pagi', 1)"
                                        >
                                            Shift 1: Pagi
                                        </button>
                                        <button
                                            type="button"
                                            class="rounded-lg border border-yellow-200 bg-yellow-50 px-4 py-2 text-sm font-medium text-yellow-700 transition-colors duration-200 hover:bg-yellow-100"
                                            onclick="applyQuickPreset('siang', 1)"
                                        >
                                            Shift 1: Siang
                                        </button>
                                        <button
                                            type="button"
                                            class="rounded-lg border border-purple-200 bg-purple-50 px-4 py-2 text-sm font-medium text-purple-700 transition-colors duration-200 hover:bg-purple-100"
                                            onclick="applyQuickPreset('malam', 1)"
                                        >
                                            Shift 1: Malam
                                        </button>
                                    </div>
                                </div>
                                <div>
                                    <div class="mb-2 text-xs font-medium text-gray-600">Shift 2 (Dropdown Bawah):</div>
                                    <div class="flex flex-wrap gap-2">
                                        <button
                                            type="button"
                                            class="rounded-lg border border-blue-200 bg-blue-50 px-4 py-2 text-sm font-medium text-blue-700 transition-colors duration-200 hover:bg-blue-100"
                                            onclick="applyQuickPreset('pagi', 2)"
                                        >
                                            Shift 2: Pagi
                                        </button>
                                        <button
                                            type="button"
                                            class="rounded-lg border border-yellow-200 bg-yellow-50 px-4 py-2 text-sm font-medium text-yellow-700 transition-colors duration-200 hover:bg-yellow-100"
                                            onclick="applyQuickPreset('siang', 2)"
                                        >
                                            Shift 2: Siang
                                        </button>
                                        <button
                                            type="button"
                                            class="rounded-lg border border-purple-200 bg-purple-50 px-4 py-2 text-sm font-medium text-purple-700 transition-colors duration-200 hover:bg-purple-100"
                                            onclick="applyQuickPreset('malam', 2)"
                                        >
                                            Shift 2: Malam
                                        </button>
                                    </div>
                                </div>
                                <div>
                                    <div class="mb-2 text-xs font-medium text-gray-600">Kontrol:</div>
                                    <button
                                        type="button"
                                        class="rounded-lg border border-red-200 bg-red-50 px-4 py-2 text-sm font-medium text-red-700 transition-colors duration-200 hover:bg-red-100"
                                        onclick="clearPreset()"
                                    >
                                        Kosongkan Semua
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex flex-col gap-3 border-t border-gray-200 pt-6 sm:flex-row">
                            <button
                                type="submit"
                                class="inline-flex flex-1 items-center justify-center gap-2 rounded-lg bg-sky-500 px-6 py-2.5 font-semibold text-white shadow-md transition-colors duration-200 hover:bg-sky-600 focus:ring-4 focus:ring-sky-200 focus:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                                id="submitBtn"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="18"
                                    height="18"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="lucide lucide-save"
                                >
                                    <path
                                        d="M15.2 3a2 2 0 0 1 1.4.6l3.8 3.8a2 2 0 0 1 .6 1.4V19a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"
                                    />
                                    <path d="M17 21v-7a1 1 0 0 0-1-1H8a1 1 0 0 0-1 1v7" />
                                    <path d="M7 3v4a1 1 0 0 0 1 1h7" />
                                </svg>
                                <span id="submitText">Simpan Jadwal Bulanan</span>
                            </button>
                            <a
                                href="{{ route('admin.schedules.index') }}"
                                class="inline-flex flex-1 items-center justify-center gap-2 rounded-lg border border-gray-300 bg-gray-100 px-6 py-2.5 font-semibold text-gray-700 transition-colors duration-200 hover:bg-gray-200 focus:ring-4 focus:ring-gray-200 focus:outline-none"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="18"
                                    height="18"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="lucide lucide-arrow-left"
                                >
                                    <path d="m12 19-7-7 7-7" />
                                    <path d="M19 12H5" />
                                </svg>
                                Kembali ke Daftar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
                    const calendarDataUrl = "{{ route('admin.schedules.calendar-grid-data') }}";
                    const userExistingSchedulesUrl = "{{ route('admin.schedules.user-existing-schedules') }}";
                    const monthSelect = document.getElementById("calendarMonth");
                    const yearSelect = document.getElementById("calendarYear");
                    const calendarContainer = document.getElementById("calendarDays");
                    const conflictInput = document.getElementById('on_attendance_conflict');
                    const confirmRemapBtn = document.getElementById('confirmRemapBtn');

                    let currentCalendarData = null;
                    let currentExistingSchedules = {};

                    // Auto-load calendar after import
                    @if(session('auto_load_month') && session('auto_load_year'))
                        console.log('=== AUTO-LOAD CALENDAR AFTER IMPORT ===');
                        console.log('Session month:', "{{ session('auto_load_month') }}");
                        console.log('Session year:', "{{ session('auto_load_year') }}");
                        console.log('Month select element:', monthSelect);
                        console.log('Year select element:', yearSelect);

                        monthSelect.value = "{{ session('auto_load_month') }}";
                        yearSelect.value = "{{ session('auto_load_year') }}";

                        console.log('Month select value set to:', monthSelect.value);
                        console.log('Year select value set to:', yearSelect.value);

                        // Trigger change event to reload calendar
                        monthSelect.dispatchEvent(new Event('change'));

                        // Scroll to "Informasi Jadwal" section after calendar loads
                        setTimeout(() => {
                            console.log('Scrolling to Informasi Jadwal section...');
                            const jadwalSection = document.querySelector('.bg-white.rounded-xl.border.border-gray-200');
                            console.log('Jadwal section found:', jadwalSection);

                            if (jadwalSection) {
                                jadwalSection.scrollIntoView({ behavior: 'smooth', block: 'start' });

                                // Add highlight effect
                                jadwalSection.style.transition = 'all 0.3s ease';
                                jadwalSection.style.boxShadow = '0 0 20px rgba(14, 165, 233, 0.3)';
                                setTimeout(() => {
                                    jadwalSection.style.boxShadow = '';
                                }, 2000);

                                console.log('Highlight effect applied!');
                            } else {
                                console.error('Jadwal section not found!');
                            }
                        }, 1500);

                        console.log('=== END AUTO-LOAD ===');
                    @else
                        console.log('No auto-load data in session');
                    @endif

                    async function loadCalendar() {
                        try {
                            calendarContainer.innerHTML =
                                `<div class="col-span-7 text-center py-8 text-gray-400">Loading...</div>`;
                            const month = monthSelect.value;
                            const year = yearSelect.value;

                            const res = await fetch(`${calendarDataUrl}?month=${month}&year=${year}`);
                            if (!res.ok) throw new Error('Gagal fetch data kalender');
                            const data = await res.json();

                            if (!data.success) throw new Error(data.message || 'Data tidak valid');
                            currentCalendarData = data;
                            renderCalendar(data);
                        } catch (err) {
                            calendarContainer.innerHTML =
                                `<div class="col-span-7 text-center py-8 text-red-500">Gagal memuat data kalender</div>`;
                            console.error(err);
                        }
                    }

                    async function loadExistingSchedules() {
                        const selectedUserId = document.getElementById('selected_user_id');
                        const userId = selectedUserId ? selectedUserId.value : null;
                        const month = monthSelect.value;
                        const year = yearSelect.value;
                        const loadingIndicator = document.getElementById('loadingIndicator');

                        if (!userId || !month || !year) {
                            currentExistingSchedules = {};
                            if (currentCalendarData) {
                                renderCalendar(currentCalendarData);
                            }
                            return;
                        }

                        try {
                            if (loadingIndicator) loadingIndicator.classList.remove('hidden');
                            const res = await fetch(`${userExistingSchedulesUrl}?user_id=${userId}&month=${month}&year=${year}`);
                            if (!res.ok) throw new Error('Gagal fetch existing schedules');
                            const data = await res.json();

                            if (data.success) {
                                currentExistingSchedules = data.schedules || {};
                                if (currentCalendarData) {
                                    renderCalendar(currentCalendarData);
                                }
                            }
                        } catch (err) {
                            console.error('Error loading existing schedules:', err);
                            currentExistingSchedules = {};
                        } finally {
                            if (loadingIndicator) loadingIndicator.classList.add('hidden');
                        }
                    }

                    // OLD_SHIFTS from Laravel old() helper - preserve user changes after validation error
                    const OLD_SHIFTS = @json(old('shifts', []));

                    function renderCalendar(data) {
                        let html = "";
                        let day = 1;
                        let currentDayOfWeek = 0;

                        for (let i = 0; i < data.firstDayOfMonth; i++) {
                            html += `<div></div>`;
                            currentDayOfWeek++;
                        }

                        while (day <= data.daysInMonth) {
                            // Priority: OLD_SHIFTS (user's last input) > existing schedules
                            let shift1Selected = '';
                            let shift2Selected = '';

                            if (OLD_SHIFTS && OLD_SHIFTS[day] && Array.isArray(OLD_SHIFTS[day])) {
                                // Use old values if available (after validation error)
                                shift1Selected = OLD_SHIFTS[day][0] || '';
                                shift2Selected = OLD_SHIFTS[day][1] || '';
                            } else {
                                // Fallback to existing schedules
                                const existingSchedulesForDay = currentExistingSchedules[day] || [];
                                shift1Selected = existingSchedulesForDay[0] ? existingSchedulesForDay[0].shift_id : '';
                                shift2Selected = existingSchedulesForDay[1] ? existingSchedulesForDay[1].shift_id : '';
                            }

                            html += `
                            <div class="p-2 bg-gray-50 border border-gray-200 rounded-lg flex flex-col items-center hover:shadow-sm transition-shadow duration-200">
                                <span class="text-sm font-semibold text-gray-700 mb-1">${day}</span>
                                <div class="w-full space-y-1">
                                    <!-- Shift 1 with Search -->
                                    <div class="relative shift-search-container" data-day="${day}" data-position="1">
                                        <input type="text" 
                                            class="shift-search-input w-full px-2 py-1 border border-gray-300 rounded-md text-xs focus:ring-1 focus:ring-sky-500 focus:border-sky-500 bg-white"
                                            placeholder="Shift 1..."
                                            data-day="${day}" 
                                            data-position="1"
                                            autocomplete="off">
                                        <input type="hidden" name="shifts[${day}][]" class="shift-value" data-day="${day}" data-position="1" value="${shift1Selected}">
                                        <div class="shift-dropdown absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-md shadow-lg max-h-32 overflow-y-auto hidden">
                                            <div class="shift-option px-2 py-1 hover:bg-sky-50 cursor-pointer text-xs" data-value="">-- Kosongkan --</div>
                                            @foreach ($shifts as $shift)
                                                <div class="shift-option px-2 py-1 hover:bg-sky-50 cursor-pointer text-xs" data-value="{{ $shift->id }}" data-name="{{ $shift->shift_name }}">{{ $shift->shift_name }}</div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <!-- Shift 2 with Search -->
                                    <div class="relative shift-search-container" data-day="${day}" data-position="2">
                                        <input type="text" 
                                            class="shift-search-input w-full px-2 py-1 border border-gray-300 rounded-md text-xs focus:ring-1 focus:ring-green-500 focus:border-green-500 bg-white"
                                            placeholder="Shift 2..."
                                            data-day="${day}" 
                                            data-position="2"
                                            autocomplete="off">
                                        <input type="hidden" name="shifts[${day}][]" class="shift-value" data-day="${day}" data-position="2" value="${shift2Selected}">
                                        <div class="shift-dropdown absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-md shadow-lg max-h-32 overflow-y-auto hidden">
                                            <div class="shift-option px-2 py-1 hover:bg-green-50 cursor-pointer text-xs" data-value="">-- Kosongkan --</div>
                                            @foreach ($shifts as $shift)
                                                <div class="shift-option px-2 py-1 hover:bg-green-50 cursor-pointer text-xs" data-value="{{ $shift->id }}" data-name="{{ $shift->shift_name }}">{{ $shift->shift_name }}</div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                            day++;
                            currentDayOfWeek++;
                            if (currentDayOfWeek === 7) {
                                currentDayOfWeek = 0;
                            }
                        }

                        calendarContainer.innerHTML = html;
                        document.getElementById("daysInfo").textContent =
                            `${data.monthName} ${data.year} memiliki ${data.daysInMonth} hari.`;

                        // Initialize shift search functionality
                        initializeShiftSearch();
                    }

                    monthSelect.addEventListener("change", function() {
                        loadCalendar();
                        loadExistingSchedules();
                    });
                    yearSelect.addEventListener("change", function() {
                        loadCalendar();
                        loadExistingSchedules();
                    });

                    loadCalendar();
                    loadExistingSchedules();

                    // Global click outside handler - only register once
                    document.addEventListener('click', function(e) {
                        if (!e.target.closest('.shift-search-container')) {
                            document.querySelectorAll('.shift-dropdown').forEach(dropdown => {
                                dropdown.classList.add('hidden');
                            });
                        }
                    });

                    // User Search Functionality - Declare variables first
                    const userSearchInput = document.getElementById('user_search');
                    const userSearchResults = document.getElementById('user_search_results');
                    const userSearchResultsList = document.getElementById('user_search_results_list');
                    const userSearchLoading = document.getElementById('user_search_loading');
                    const userSearchNoResults = document.getElementById('user_search_no_results');
                    const selectedUserId = document.getElementById('selected_user_id');
                    const selectedUserDisplay = document.getElementById('selected_user_display');
                    const selectedUserName = document.getElementById('selected_user_name');
                    const selectedUserEmail = document.getElementById('selected_user_email');
                    const userValidationError = document.getElementById('user_validation_error');

                    const usersData = [
                        @foreach ($users as $user)
                            {
                                id: {{ $user->id }},
                                name: "{{ $user->name }}",
                                email: "{{ $user->email ?? '' }}"
                            },
                        @endforeach
                    ];

                    let searchTimeout;

                    // Check if there's an old user_id value (after validation error)
                    const oldUserId = selectedUserId ? selectedUserId.value : null;
                    if (oldUserId) {
                        // Find user data from usersData
                        const oldUser = usersData.find(u => u.id == oldUserId);
                        if (oldUser) {
                            // Display the selected user
                            selectedUserName.textContent = oldUser.name;
                            selectedUserEmail.textContent = oldUser.email;
                            const userIdDisplay = document.getElementById('selected_user_id_display');
                            if (userIdDisplay) {
                                userIdDisplay.textContent = oldUser.id;
                            }
                            selectedUserDisplay.classList.remove('hidden');
                            userSearchInput.value = oldUser.name;
                        }
                    }

                    if (userSearchInput) {
                        userSearchInput.addEventListener('input', function() {
                            const query = this.value.trim();
                            clearTimeout(searchTimeout);

                            if (!query) {
                                hideSearchResults();
                                return;
                            }

                            showLoadingState();
                            searchTimeout = setTimeout(() => {
                                performUserSearch(query);
                            }, 300);
                        });

                        document.addEventListener('click', function(e) {
                            if (!userSearchInput.contains(e.target) && !userSearchResults.contains(e.target)) {
                                hideSearchResults();
                            }
                        });
                    }

                    function performUserSearch(query) {
                        const filteredUsers = usersData.filter(user =>
                            user.name.toLowerCase().includes(query.toLowerCase()) ||
                            user.email.toLowerCase().includes(query.toLowerCase())
                        );
                        showSearchResults(filteredUsers, query);
                    }

                    function showSearchResults(users, query) {
                        userSearchResultsList.innerHTML = '';

                        if (users.length === 0) {
                            hideLoadingState();
                            showNoResultsState();
                            return;
                        }

                        hideLoadingState();
                        hideNoResultsState();

                        users.forEach(user => {
                            const userItem = document.createElement('div');
                            userItem.className = 'px-4 py-3 hover:bg-gray-50 cursor-pointer transition-colors';
                            userItem.innerHTML = `
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-gradient-to-br from-sky-100 to-sky-200 rounded-full flex items-center justify-center mr-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                            class="lucide lucide-user text-sky-600">
                                            <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
                                            <circle cx="12" cy="7" r="4"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">${highlightText(user.name, query)}</div>
                                        <div class="text-xs text-gray-500">${highlightText(user.email, query)}</div>
                                    </div>
                                </div>
                            `;

                            userItem.addEventListener('click', () => selectUser(user));
                            userSearchResultsList.appendChild(userItem);
                        });

                        userSearchResults.classList.remove('hidden');
                    }

                    function highlightText(text, query) {
                        if (!query || !text) return text;
                        const regex = new RegExp(`(${query})`, 'gi');
                        return text.replace(regex, '<mark class="bg-yellow-200 text-yellow-800">$1</mark>');
                    }

                    function selectUser(user) {
                        selectedUserId.value = user.id;
                        selectedUserName.textContent = user.name;
                        selectedUserEmail.textContent = user.email;

                        // Display User ID for debugging
                        const userIdDisplay = document.getElementById('selected_user_id_display');
                        if (userIdDisplay) {
                            userIdDisplay.textContent = user.id;
                        }

                        selectedUserDisplay.classList.remove('hidden');
                        userSearchInput.value = user.name;
                        hideSearchResults();
                        userValidationError.classList.add('hidden');

                        if (monthSelect.value && yearSelect.value) {
                            loadExistingSchedules();
                        }
                    }

                    window.clearUserSelection = function() {
                        selectedUserId.value = '';
                        selectedUserName.textContent = '';
                        selectedUserEmail.textContent = '';
                        selectedUserDisplay.classList.add('hidden');
                        userSearchInput.value = '';
                        userValidationError.classList.add('hidden');

                        currentExistingSchedules = {};
                        if (currentCalendarData) {
                            renderCalendar(currentCalendarData);
                        }
                    }

                    function showLoadingState() {
                        userSearchResults.classList.remove('hidden');
                        userSearchLoading.classList.remove('hidden');
                        userSearchNoResults.classList.add('hidden');
                        userSearchResultsList.innerHTML = '';
                    }

                    function hideLoadingState() {
                        userSearchLoading.classList.add('hidden');
                    }

                    function showNoResultsState() {
                        userSearchResults.classList.remove('hidden');
                        userSearchNoResults.classList.remove('hidden');
                    }

                    function hideNoResultsState() {
                        userSearchNoResults.classList.add('hidden');
                    }

                    function hideSearchResults() {
                        userSearchResults.classList.add('hidden');
                        hideLoadingState();
                        hideNoResultsState();
                    }

                    // Form submission handler with validation
                    document.getElementById('scheduleForm')?.addEventListener('submit', function(e) {
                        const submitBtn = document.getElementById('submitBtn');
                        const submitText = document.getElementById('submitText');
                        const selectedUserId = document.getElementById('selected_user_id');
                        const userValidationError = document.getElementById('user_validation_error');

                        console.log('=== FORM SUBMIT DEBUG ===');
                        console.log('Hidden input element:', selectedUserId);
                        console.log('Hidden input value:', selectedUserId ? selectedUserId.value : 'NOT FOUND');
                        console.log('Hidden input name:', selectedUserId ? selectedUserId.name : 'NOT FOUND');

                        // Get FormData to see what will be sent
                        const formData = new FormData(this);
                        console.log('FormData user_id:', formData.get('user_id'));
                        console.log('All form data:');
                        for (let pair of formData.entries()) {
                            console.log(pair[0] + ': ' + pair[1]);
                        }
                        console.log('=== END DEBUG ===');

                        // Validate user selection
                        if (!selectedUserId || !selectedUserId.value || selectedUserId.value === '') {
                            e.preventDefault();

                            if (userValidationError) {
                                userValidationError.classList.remove('hidden');
                                userValidationError.textContent = 'Silakan pilih pengguna terlebih dahulu';
                            }

                            // Scroll to user selection field
                            document.getElementById('user_search')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            return false;
                        }

                        // Hide validation error
                        if (userValidationError) {
                            userValidationError.classList.add('hidden');
                        }

                        // Check if at least one shift is selected
                        const allShiftValues = document.querySelectorAll('#calendarDays .shift-value');
                        let hasSchedule = false;

                        allShiftValues.forEach(input => {
                            if (input.value && input.value !== '') {
                                hasSchedule = true;
                            }
                        });

                        if (!hasSchedule) {
                            e.preventDefault();
                            alert('Mohon pilih minimal satu shift untuk jadwal.');
                            return false;
                        }

                        // Show loading state
                        submitBtn.disabled = true;
                        submitText.innerHTML = `
                            <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Menyimpan...
                        `;
                    });
                }); // End of DOMContentLoaded

                // Global functions - OUTSIDE DOMContentLoaded
                const SHIFT_IDS = {
                    pagi: 1,
                    siang: 2,
                    malam: 3
                };

                function applyQuickPreset(type, shiftPosition = 1) {
                    const shiftId = SHIFT_IDS[type];
                    if (!shiftId) return alert("ID shift untuk " + type + " belum diatur!");

                    const shiftsData = [
                        @foreach ($shifts as $shift)
                            { id: "{{ $shift->id }}", name: "{{ $shift->shift_name }}" },
                        @endforeach
                    ];

                    const shift = shiftsData.find(s => s.id == shiftId);
                    if (!shift) return;

                    document.querySelectorAll(`.shift-search-container[data-position="${shiftPosition}"]`).forEach(container => {
                        const searchInput = container.querySelector('.shift-search-input');
                        const hiddenInput = container.querySelector('.shift-value');
                        const day = container.getAttribute('data-day');

                        hiddenInput.value = shiftId;
                        searchInput.value = shift.name;

                        if (shiftPosition === 1) {
                            updateSecondShiftOptions(day, shiftId);
                        }
                    });
                }

                function clearPreset() {
                    document.querySelectorAll('.shift-search-input').forEach(input => {
                        input.value = '';
                    });
                    document.querySelectorAll('.shift-value').forEach(input => {
                        input.value = '';
                    });

                    // Reset all shift 2 options
                    document.querySelectorAll('.shift-search-container[data-position="1"]').forEach(container => {
                        const day = container.getAttribute('data-day');
                        updateSecondShiftOptions(day, '');
                    });
                }

                // Initialize shift search functionality for all shift inputs
                function initializeShiftSearch() {
                    const shiftsData = [
                        @foreach ($shifts as $shift)
                            { id: "{{ $shift->id }}", name: "{{ $shift->shift_name }}", category: "{{ $shift->category }}" },
                        @endforeach
                    ];

                    // Set initial values for existing selections
                    document.querySelectorAll('.shift-search-container').forEach(container => {
                        const day = container.getAttribute('data-day');
                        const position = container.getAttribute('data-position');
                        const hiddenInput = container.querySelector('.shift-value');
                        const searchInput = container.querySelector('.shift-search-input');

                        if (hiddenInput.value) {
                            const shift = shiftsData.find(s => s.id == hiddenInput.value);
                            if (shift) {
                                searchInput.value = shift.name;
                            }
                        }
                    });

                    // Handle search input focus - use event delegation on calendar container
                    document.querySelectorAll('.shift-search-input').forEach(input => {
                        // Remove old listeners by cloning (if any)
                        const newInput = input.cloneNode(true);
                        input.parentNode.replaceChild(newInput, input);
                    });

                    // Re-attach event listeners
                    document.querySelectorAll('.shift-search-input').forEach(input => {
                        input.addEventListener('focus', handleShiftInputFocus);
                        input.addEventListener('input', handleShiftInputChange);
                        input.addEventListener('blur', handleShiftInputBlur);
                    });

                    // Handle option selection - use event delegation
                    document.querySelectorAll('.shift-option').forEach(option => {
                        // Remove old listeners by cloning
                        const newOption = option.cloneNode(true);
                        option.parentNode.replaceChild(newOption, option);
                    });

                    // Re-attach event listeners
                    document.querySelectorAll('.shift-option').forEach(option => {
                        option.addEventListener('click', handleShiftOptionClick);
                    });
                }

                // Event handler functions
                function handleShiftInputFocus() {
                    const container = this.closest('.shift-search-container');
                    const dropdown = container.querySelector('.shift-dropdown');

                    filterShiftOptions(container, '');
                    dropdown.classList.remove('hidden');
                }

                function handleShiftInputChange() {
                    const container = this.closest('.shift-search-container');
                    const dropdown = container.querySelector('.shift-dropdown');
                    const query = this.value.toLowerCase();

                    filterShiftOptions(container, query);
                    dropdown.classList.remove('hidden');
                }

                function handleShiftInputBlur() {
                    const container = this.closest('.shift-search-container');
                    const dropdown = container.querySelector('.shift-dropdown');

                    setTimeout(() => {
                        dropdown.classList.add('hidden');
                    }, 200);
                }

                function handleShiftOptionClick() {
                    const container = this.closest('.shift-search-container');
                    const day = container.getAttribute('data-day');
                    const position = container.getAttribute('data-position');
                    const searchInput = container.querySelector('.shift-search-input');
                    const hiddenInput = container.querySelector('.shift-value');
                    const dropdown = container.querySelector('.shift-dropdown');

                    const value = this.getAttribute('data-value');
                    const name = this.getAttribute('data-name') || '';

                    hiddenInput.value = value;
                    searchInput.value = name;
                    dropdown.classList.add('hidden');

                    if (position === '1') {
                        updateSecondShiftOptions(day, value);
                    }
                }

                function filterShiftOptions(container, query) {
                    const options = container.querySelectorAll('.shift-option');

                    options.forEach(option => {
                        const name = option.getAttribute('data-name') || '';
                        if (name.toLowerCase().includes(query) || option.getAttribute('data-value') === '') {
                            option.style.display = 'block';
                        } else {
                            option.style.display = 'none';
                        }
                    });
                }

                function updateSecondShiftOptions(day, firstShiftId) {
                    const shiftsData = [
                        @foreach ($shifts as $shift)
                            { id: "{{ $shift->id }}", name: "{{ $shift->shift_name }}", category: "{{ $shift->category }}" },
                        @endforeach
                    ];

                    const shift2Container = document.querySelector(`.shift-search-container[data-day="${day}"][data-position="2"]`);
                    if (!shift2Container) return;

                    const dropdown = shift2Container.querySelector('.shift-dropdown');
                    const searchInput = shift2Container.querySelector('.shift-search-input');
                    const hiddenInput = shift2Container.querySelector('.shift-value');
                    const currentValue = hiddenInput.value;

                    // If no first shift selected, reset shift 2 to normal state
                    if (!firstShiftId || firstShiftId === '') {
                        searchInput.disabled = false;
                        searchInput.placeholder = 'Shift 2...';
                        searchInput.classList.remove('bg-gray-100', 'cursor-not-allowed');

                        // Show all shifts
                        dropdown.innerHTML = '<div class="shift-option px-2 py-1 hover:bg-green-50 cursor-pointer text-xs" data-value="">-- Kosongkan --</div>';
                        shiftsData.forEach(shift => {
                            const optionDiv = document.createElement('div');
                            optionDiv.className = 'shift-option px-2 py-1 hover:bg-green-50 cursor-pointer text-xs';
                            optionDiv.setAttribute('data-value', shift.id);
                            optionDiv.setAttribute('data-name', shift.name);
                            optionDiv.textContent = shift.name;

                            optionDiv.addEventListener('click', function() {
                                hiddenInput.value = this.getAttribute('data-value');
                                searchInput.value = this.getAttribute('data-name') || '';
                                dropdown.classList.add('hidden');
                            });

                            dropdown.appendChild(optionDiv);
                        });
                        return;
                    }

                    // Find selected first shift
                    const selectedFirstShift = shiftsData.find(s => s.id == firstShiftId);

                    // Determine allowed category for shift 2 based on shift 1 category
                    let allowedCategory = null;
                    if (selectedFirstShift) {
                        if (selectedFirstShift.category === 'Pagi') {
                            allowedCategory = 'Siang';
                        } else if (selectedFirstShift.category === 'Siang') {
                            allowedCategory = 'Malam';
                        } else if (selectedFirstShift.category === 'Malam') {
                            allowedCategory = null; // No shift 2 allowed
                        }
                    }

                    // If Malam shift selected, disable shift 2
                    if (allowedCategory === null && selectedFirstShift) {
                        searchInput.disabled = true;
                        searchInput.placeholder = 'Tidak ada shift 2';
                        searchInput.value = '';
                        hiddenInput.value = '';
                        searchInput.classList.add('bg-gray-100', 'cursor-not-allowed');
                        dropdown.innerHTML = '';
                        return;
                    } else {
                        // Enable shift 2
                        searchInput.disabled = false;
                        searchInput.placeholder = 'Shift 2...';
                        searchInput.classList.remove('bg-gray-100', 'cursor-not-allowed');
                    }

                    // Rebuild options with category filter
                    dropdown.innerHTML = '<div class="shift-option px-2 py-1 hover:bg-green-50 cursor-pointer text-xs" data-value="">-- Kosongkan --</div>';

                    shiftsData.forEach(shift => {
                        // Only show shifts that match allowed category
                        if (shift.category === allowedCategory) {
                            const optionDiv = document.createElement('div');
                            optionDiv.className = 'shift-option px-2 py-1 hover:bg-green-50 cursor-pointer text-xs';
                            optionDiv.setAttribute('data-value', shift.id);
                            optionDiv.setAttribute('data-name', shift.name);
                            optionDiv.textContent = shift.name;

                            optionDiv.addEventListener('click', function() {
                                hiddenInput.value = this.getAttribute('data-value');
                                searchInput.value = this.getAttribute('data-name') || '';
                                dropdown.classList.add('hidden');
                            });

                            dropdown.appendChild(optionDiv);
                        }
                    });

                    // Clear shift 2 if current value is not in allowed category
                    const currentShift = shiftsData.find(s => s.id == currentValue);
                    if (currentShift && currentShift.category !== allowedCategory) {
                        hiddenInput.value = '';
                        searchInput.value = '';
                    }
                }

            // Handle "Pindahkan attendance & simpan" button click - OUTSIDE DOMContentLoaded
            // This ensures the handler works even when the button appears after page load (validation error)
            document.addEventListener('click', function(e) {
                if (e.target && e.target.id === 'confirmRemapBtn') {
                    const conflictInput = document.getElementById('on_attendance_conflict');
                    const scheduleForm = document.getElementById('scheduleForm');

                    if (conflictInput) {
                        conflictInput.value = 'remap';
                    }

                    if (scheduleForm) {
                        scheduleForm.submit();
                    }
                }
            });

            // Debug import form
            document.addEventListener('DOMContentLoaded', function() {
                const importForm = document.getElementById('importForm');

                if (importForm) {
                    console.log('Import form found:', importForm);

                    importForm.addEventListener('submit', function(e) {
                        console.log('=== IMPORT FORM SUBMIT ===');
                        console.log('Form action:', this.action);
                        console.log('Form method:', this.method);

                        const formData = new FormData(this);
                        console.log('Form data:');
                        for (let pair of formData.entries()) {
                            console.log(pair[0] + ':', pair[1]);
                        }

                        // Check if file is selected
                        const fileInput = this.querySelector('input[type="file"]');
                        if (fileInput && fileInput.files.length === 0) {
                            e.preventDefault();
                            alert('Silakan pilih file Excel terlebih dahulu!');
                            console.log('No file selected');
                            return false;
                        }

                        console.log('File selected:', fileInput.files[0].name);
                        console.log('Form will be submitted...');
                    });
                } else {
                    console.error('Import form NOT found!');
                }
            });
    </script>
@endsection
