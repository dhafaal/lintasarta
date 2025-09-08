@extends('layouts.app')

@section('title', 'Buat Jadwal Baru')

@section('content')
<div class="min-h-screen bg-white sm:p-6 lg:p-8">
    <div class="mx-auto space-y-8">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-gradient-to-br from-sky-100 to-sky-200 rounded-xl shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar text-sky-700"><path d="M8 2v4"/><path d="M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/></svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-700">Buat Jadwal Baru</h1>
                    <p class="text-gray-500 text-sm">Kelola jadwal karyawan dengan berbagai metode</p>
                </div>
            </div>
            <a href="{{ route('admin.schedules.index') }}"
               class="px-4 py-2 rounded-xl bg-gray-200 hover:bg-gray-300 text-gray-700 transition">
                ← Kembali
            </a>
        </div>

        <!-- Tabs -->
        <div class="flex mt-4 pb-2 border-b">
            <div class="bg-gray-100 p-1 rounded-xl">
                <button id="tab-single"
                    class="tab-button px-4 py-2 font-semibold bg-white text-sky-700 border border-gray-300 rounded-lg shadow-sm">
                    Jadwal Tunggal
                </button>
                <button id="tab-bulk-monthly"
                    class="tab-button px-4 py-2 font-semibold text-gray-500 hover:text-sky-700 rounded-lg">
                    Jadwal Bulanan
                </button>
                <button id="tab-bulk-multiple"
                    class="tab-button px-4 py-2 font-semibold text-gray-500 hover:text-sky-700 rounded-lg">
                    Multi User/Tanggal
                </button>
                <button id="tab-bulk-range"
                    class="tab-button px-4 py-2 font-semibold text-gray-500 hover:text-sky-700 rounded-lg">
                    Rentang Tanggal
                </button> 
            </div>
        </div>

        <!-- Form Single -->
        <div id="form-single" class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-sky-500 to-sky-600 px-6 py-4">
                <h2 class="text-white text-lg font-semibold">Tambah Jadwal Individu</h2>
                <p class="text-sky-100 text-sm">Buat satu jadwal untuk satu user dan tanggal</p>
            </div>
            <div class="p-6">
                <form action="{{ route('admin.schedules.bulkStore') }}" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="single" value="1">

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                        <!-- User -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Pilih User</label>
                            <select name="single_user_id"
                                class="w-full border rounded-xl p-2 focus:ring-2 focus:ring-sky-400" required>
                                <option value="">-- Pilih User --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('single_user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('single_user_id')
                                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Shift -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Pilih Shift</label>
                            <select name="single_shift_id"
                                class="w-full border rounded-xl p-2 focus:ring-2 focus:ring-sky-400" required>
                                <option value="">-- Pilih Shift --</option>
                                @foreach($shifts as $shift)
                                    <option value="{{ $shift->id }}" {{ old('single_shift_id') == $shift->id ? 'selected' : '' }}>
                                        {{ $shift->name }} ({{ $shift->start_time }} - {{ $shift->end_time }})
                                    </option>
                                @endforeach
                            </select>
                            @error('single_shift_id')
                                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tanggal -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal</label>
                            <input type="date" name="single_schedule_date" value="{{ old('single_schedule_date') }}"
                                class="w-full border rounded-xl p-2 focus:ring-2 focus:ring-sky-400" required>
                            @error('single_schedule_date')
                                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="flex justify-end">
                        <button type="submit"
                            class="px-6 py-3 rounded-xl bg-sky-600 text-white font-semibold shadow hover:bg-sky-700 transition flex items-center gap-2">
                            <span>Simpan Jadwal</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Form Bulk Monthly -->
        <div id="form-bulk-monthly" class="bg-white rounded-2xl shadow-xl overflow-hidden hidden">
            <div class="bg-gradient-to-r from-sky-500 to-sky-600 px-6 py-4">
                <h2 class="text-white text-lg font-semibold">Tambah Jadwal Bulanan</h2>
                <p class="text-sky-100 text-sm">Atur jadwal untuk satu user dalam satu bulan penuh</p>
            </div>
            <div class="p-6">
                <form action="{{ route('admin.schedules.bulkStore') }}" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="bulk_monthly" value="1">

                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-6">
                        <!-- User -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Pilih User</label>
                            <select name="user_id" class="w-full border rounded-xl p-2 focus:ring-2 focus:ring-sky-400" required>
                                <option value="">-- Pilih User --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Bulan -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Bulan</label>
                            <select name="month" id="month-select"
                                class="w-full border rounded-xl p-2 focus:ring-2 focus:ring-sky-400" required>
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ ($m == (old('month') ?? date('m'))) ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                    </option>
                                @endfor
                            </select>
                            @error('month')
                                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tahun -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Tahun</label>
                            <input type="number" id="year-select" name="year" value="{{ old('year') ?? date('Y') }}"
                                class="w-full border rounded-xl p-2 focus:ring-2 focus:ring-sky-400" required />
                            @error('year')
                                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Mode tampilan -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Tampilan</label>
                            <select id="view-mode" class="w-full border rounded-xl p-2 focus:ring-2 focus:ring-sky-400">
                                <option value="grid">Kalender Grid</option>
                                <option value="table">Tabel</option>
                            </select>
                        </div>
                    </div>

                    <!-- Info -->
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-sm text-blue-800 font-medium">Tips:</p>
                        </div>
                        <ul class="text-sm text-blue-700 mt-2 space-y-1">
                            <li>• Kosongkan field untuk menghapus jadwal yang sudah ada</li>
                            <li>• Gunakan tombol bantuan untuk mengisi multiple tanggal sekaligus</li>
                        </ul>
                    </div>

                    <!-- Bulk Buttons -->
                    <div class="flex flex-wrap items-center gap-3 mt-6">
                        <button type="button" onclick="setAllShifts('')"
                            class="flex items-center gap-x-2 px-3 py-2 rounded-lg bg-red-50 hover:bg-red-100 border border-red-300 text-red-700 text-sm transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Hapus Semua
                        </button>
                        @foreach($shifts as $shift)
                            <button type="button" onclick="setAllShifts('{{ $shift->id }}')"
                                class="px-3 py-1 rounded-lg bg-sky-100 hover:bg-sky-200 text-sky-700 text-sm transition">
                                Isi Semua: {{ $shift->name }}
                            </button>
                            <button type="button" onclick="setEmptyShifts('{{ $shift->id }}')"
                                class="px-3 py-1 rounded-lg bg-green-100 hover:bg-green-200 text-green-700 text-sm transition">
                                Isi Kosong: {{ $shift->name }}
                            </button>
                        @endforeach
                    </div>

                    <!-- Calendar -->
                    <div id="calendar-container" class="mt-6"></div>

                    <!-- Table View -->
                    <div id="table-container" class="mt-6 hidden overflow-x-auto">
                        <table class="w-full border border-gray-300 rounded-lg overflow-hidden">
                            <thead>
                                <tr class="bg-gray-100 text-gray-700">
                                    <th class="p-3 border text-left font-semibold">Tanggal</th>
                                    <th class="p-3 border text-left font-semibold">Shift</th>
                                </tr>
                            </thead>
                            <tbody id="table-body" class="bg-white"></tbody>
                        </table>
                    </div>

                    <!-- Submit -->
                    <div class="flex justify-end mt-8">
                        <button type="submit"
                            class="px-6 py-3 rounded-xl bg-sky-600 text-white font-semibold shadow hover:bg-sky-700 transition flex items-center gap-2">
                            <span>Simpan Jadwal Bulanan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Form Bulk Multiple Users/Dates -->
        <div id="form-bulk-multiple" class="bg-white rounded-2xl shadow-xl overflow-hidden hidden">
            <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 px-6 py-4">
                <h2 class="text-white text-lg font-semibold">Multi User & Tanggal</h2>
                <p class="text-emerald-100 text-sm">Assign satu shift ke beberapa user dan tanggal sekaligus</p>
            </div>
            <div class="p-6">
                <form action="{{ route('admin.schedules.bulkStore') }}" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="bulk_multiple" value="1">

                    <!-- Shift Selection -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Shift</label>
                        <select name="shift_id" class="w-full border rounded-xl p-3 focus:ring-2 focus:ring-emerald-400" required>
                            <option value="">-- Pilih Shift --</option>
                            @foreach($shifts as $shift)
                                <option value="{{ $shift->id }}">
                                    {{ $shift->name }} ({{ $shift->start_time }} - {{ $shift->end_time }})
                                </option>
                            @endforeach
                        </select>
                        @error('shift_id')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Users Selection -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Pilih User (Multiple)
                                <span class="text-gray-500 font-normal text-xs">(Ctrl/Cmd + Click)</span>
                            </label>
                            <div class="border rounded-xl p-3 bg-gray-50 max-h-48 overflow-y-auto">
                                @foreach($users as $user)
                                    <label class="flex items-center gap-2 p-2 hover:bg-white rounded-lg cursor-pointer transition">
                                        <input type="checkbox" name="users[]" value="{{ $user->id }}" 
                                            class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                                        <span class="text-sm">{{ $user->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('users')
                                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Dates Selection -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal-tanggal</label>
                            <div id="dates-container" class="space-y-2">
                                <div class="flex gap-2">
                                    <input type="date" name="dates[]" 
                                        class="flex-1 border rounded-xl p-2 focus:ring-2 focus:ring-emerald-400">
                                    <button type="button" onclick="addDateField()" 
                                        class="px-3 py-2 bg-emerald-100 text-emerald-700 rounded-xl hover:bg-emerald-200 transition">
                                        +
                                    </button>
                                </div>
                            </div>
                            <button type="button" onclick="addDateRange()" 
                                class="mt-2 text-sm text-emerald-600 hover:text-emerald-800">
                                + Tambah Rentang Tanggal
                            </button>
                            @error('dates')
                                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="flex justify-end mt-8">
                        <button type="submit"
                            class="px-6 py-3 rounded-xl bg-emerald-600 text-white font-semibold shadow hover:bg-emerald-700 transition">
                            Buat Jadwal Multi
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Form Bulk Range -->
        <div id="form-bulk-range" class="bg-white rounded-2xl shadow-xl overflow-hidden hidden">
            <div class="bg-gradient-to-r from-purple-500 to-purple-600 px-6 py-4">
                <h2 class="text-white text-lg font-semibold">Rentang Tanggal</h2>
                <p class="text-purple-100 text-sm">Assign shift yang sama untuk rentang tanggal dengan filter hari</p>
            </div>
            <div class="p-6">
                <form action="{{ route('admin.schedules.bulkStore') }}" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="bulk_same_shift" value="1">

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <!-- User -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Pilih User</label>
                            <select name="user_id" class="w-full border rounded-xl p-2 focus:ring-2 focus:ring-purple-400" required>
                                <option value="">-- Pilih User --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Shift -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Pilih Shift</label>
                            <select name="shift_id" class="w-full border rounded-xl p-2 focus:ring-2 focus:ring-purple-400" required>
                                <option value="">-- Pilih Shift --</option>
                                @foreach($shifts as $shift)
                                    <option value="{{ $shift->id }}">
                                        {{ $shift->name }} ({{ $shift->start_time }} - {{ $shift->end_time }})
                                    </option>
                                @endforeach
                            </select>
                            @error('shift_id')
                                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <!-- Start Date -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Mulai</label>
                            <input type="date" name="start_date" 
                                class="w-full border rounded-xl p-2 focus:ring-2 focus:ring-purple-400" required>
                            @error('start_date')
                                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- End Date -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Selesai</label>
                            <input type="date" name="end_date" 
                                class="w-full border rounded-xl p-2 focus:ring-2 focus:ring-purple-400" required>
                            @error('end_date')
                                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Day Selection -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Pilih Hari (Kosongkan untuk semua hari)
                        </label>
                        <div class="grid grid-cols-4 sm:grid-cols-7 gap-2">
                            <label class="flex items-center gap-2 p-2 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 transition">
                                <input type="checkbox" name="selected_days[]" value="1" 
                                    class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                <span class="text-sm">Senin</span>
                            </label>
                            <label class="flex items-center gap-2 p-2 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 transition">
                                <input type="checkbox" name="selected_days[]" value="2" 
                                    class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                <span class="text-sm">Selasa</span>
                            </label>
                            <label class="flex items-center gap-2 p-2 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 transition">
                                <input type="checkbox" name="selected_days[]" value="3" 
                                    class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                <span class="text-sm">Rabu</span>
                            </label>
                            <label class="flex items-center gap-2 p-2 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 transition">
                                <input type="checkbox" name="selected_days[]" value="4" 
                                    class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                <span class="text-sm">Kamis</span>
                            </label>
                            <label class="flex items-center gap-2 p-2 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 transition">
                                <input type="checkbox" name="selected_days[]" value="5" 
                                    class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                <span class="text-sm">Jumat</span>
                            </label>
                            <label class="flex items-center gap-2 p-2 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 transition">
                                <input type="checkbox" name="selected_days[]" value="6" 
                                    class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                <span class="text-sm">Sabtu</span>
                            </label>
                            <label class="flex items-center gap-2 p-2 bg-red-50 rounded-lg cursor-pointer hover:bg-red-100 transition">
                                <input type="checkbox" name="selected_days[]" value="0" 
                                    class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                <span class="text-sm text-red-600">Minggu</span>
                            </label>
                        </div>
                        <div class="mt-2 flex gap-2">
                            <button type="button" onclick="selectWeekdays()" 
                                class="text-xs px-3 py-1 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition">
                                Hari Kerja
                            </button>
                            <button type="button" onclick="selectWeekends()" 
                                class="text-xs px-3 py-1 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition">
                                Weekend
                            </button>
                            <button type="button" onclick="selectAllDays()" 
                                class="text-xs px-3 py-1 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition">
                                Semua Hari
                            </button>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="flex justify-end mt-8">
                        <button type="submit"
                            class="px-6 py-3 rounded-xl bg-purple-600 text-white font-semibold shadow hover:bg-purple-700 transition">
                            Buat Jadwal Rentang
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

<script>
    const shifts = @json($shifts);

    // Tabs Management
    const tabs = {
        'single': document.getElementById('tab-single'),
        'bulk-monthly': document.getElementById('tab-bulk-monthly'),
        'bulk-multiple': document.getElementById('tab-bulk-multiple'),
        'bulk-range': document.getElementById('tab-bulk-range')
    };

    const forms = {
        'single': document.getElementById('form-single'),
        'bulk-monthly': document.getElementById('form-bulk-monthly'),
        'bulk-multiple': document.getElementById('form-bulk-multiple'),
        'bulk-range': document.getElementById('form-bulk-range')
    };

    function activateTab(activeTab) {
        Object.keys(tabs).forEach(tabKey => {
            if (tabKey === activeTab) {
                tabs[tabKey].className = "tab-button px-4 py-2 font-semibold bg-white text-sky-700 border border-gray-300 rounded-lg shadow-sm";
                forms[tabKey].classList.remove("hidden");
            } else {
                tabs[tabKey].className = "tab-button px-4 py-2 font-semibold text-gray-500 hover:text-sky-700 rounded-lg";
                forms[tabKey].classList.add("hidden");
            }
        });
    }

    Object.keys(tabs).forEach(tabKey => {
        tabs[tabKey].addEventListener("click", () => activateTab(tabKey));
    });

    // Calendar & Table render for monthly bulk
    document.addEventListener("DOMContentLoaded", () => {
        const monthSelect = document.getElementById("month-select");
        const yearSelect = document.getElementById("year-select");
        const viewMode = document.getElementById("view-mode");

        if (monthSelect && yearSelect && viewMode) {
            function renderCalendar() {
                const month = parseInt(monthSelect.value);
                const year = parseInt(yearSelect.value);

                const firstDay = new Date(year, month - 1, 1).getDay() || 7;
                const daysInMonth = new Date(year, month, 0).getDate();

                const calContainer = document.getElementById("calendar-container");
                calContainer.innerHTML = "";

                const header = ["Sen","Sel","Rab","Kam","Jum","Sab","Min"];
                const headerRow = document.createElement("div");
                headerRow.className = "grid grid-cols-7 gap-2 text-center font-semibold text-gray-600 mb-2";
                header.forEach(h => {
                    const div = document.createElement("div");
                    div.textContent = h;
                    headerRow.appendChild(div);
                });
                calContainer.appendChild(headerRow);

                const grid = document.createElement("div");
                grid.className = "grid grid-cols-7 gap-2 text-center";

                for (let i = 1; i < firstDay; i++) {
                    grid.appendChild(document.createElement("div"));
                }

                for (let d = 1; d <= daysInMonth; d++) {
                    const today = new Date();
                    const isToday = (d === today.getDate() && month === today.getMonth()+1 && year === today.getFullYear());
                    const date = new Date(year, month - 1, d);
                    const isSunday = date.getDay() === 0;

                    const cell = document.createElement("div");
                    cell.className = `border rounded-xl p-2 flex flex-col items-center 
                        ${isToday ? 'bg-yellow-50 border-yellow-400' : 'bg-white'} 
                        ${isSunday ? 'bg-red-50' : ''} hover:shadow transition`;

                    const label = document.createElement("div");
                    label.textContent = d;
                    label.className = `font-bold text-sm ${isSunday ? 'text-red-600' : 'text-gray-800'}`;
                    cell.appendChild(label);

                    const select = document.createElement("select");
                    select.name = `shifts[${d}]`;
                    select.className = "mt-1 w-full text-sm border rounded-lg p-1 focus:ring-2 focus:ring-sky-400";
                    select.innerHTML = `<option value="">--</option>` +
                        shifts.map(s => `<option value="${s.id}">${s.name}</option>`).join("");
                    cell.appendChild(select);

                    grid.appendChild(cell);
                }

                calContainer.appendChild(grid);

                const tableBody = document.getElementById("table-body");
                tableBody.innerHTML = "";
                for (let d = 1; d <= daysInMonth; d++) {
                    const date = new Date(year, month - 1, d);
                    const dateStr = date.toLocaleDateString('id-ID', { 
                        weekday: 'long', 
                        year: 'numeric', 
                        month: 'long', 
                        day: 'numeric' 
                    });

                    const tr = document.createElement("tr");
                    tr.className = "hover:bg-sky-50 transition";
                    tr.innerHTML = `
                        <td class="border p-3 text-left">
                            <div class="font-medium">${d}</div>
                            <div class="text-xs text-gray-500">${dateStr}</div>
                        </td>
                        <td class="border p-3">
                            <select name="shifts[${d}]" class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-sky-400">
                                <option value="">-- Kosongkan untuk menghapus --</option>
                                ${shifts.map(s => `<option value="${s.id}">${s.name} (${s.start_time} - ${s.end_time})</option>`).join("")}
                            </select>
                        </td>`;
                    tableBody.appendChild(tr);
                }
            }

            function toggleView() {
                const mode = viewMode.value;
                document.getElementById("calendar-container").classList.toggle("hidden", mode !== "grid");
                document.getElementById("table-container").classList.toggle("hidden", mode !== "table");
            }

            monthSelect.addEventListener("change", renderCalendar);
            yearSelect.addEventListener("change", renderCalendar);
            viewMode.addEventListener("change", toggleView);

            renderCalendar();
            toggleView();
        }
    });

    // Bulk assign functions for monthly
    function setAllShifts(shiftId) {
        document.querySelectorAll("select[name^='shifts']").forEach(select => {
            select.value = shiftId;
        });
    }

    function setEmptyShifts(shiftId) {
        document.querySelectorAll("select[name^='shifts']").forEach(select => {
            if (!select.value || select.value === '') {
                select.value = shiftId;
            }
        });
    }

    function clearAllShifts() {
        document.querySelectorAll("select[name^='shifts']").forEach(select => {
            select.value = '';
        });
    }

    // Handle individual shift changes
    function handleShiftChange(selectElement) {
        // This function can be used to add visual feedback or validation
        if (selectElement.value === 'DELETE_SCHEDULE') {
            selectElement.style.backgroundColor = '#fee2e2'; // red background
        } else if (selectElement.value === '') {
            selectElement.style.backgroundColor = ''; // default
        } else {
            selectElement.style.backgroundColor = '#f0f9ff'; // blue background
        }
    }

    // Prepare form before submission - only include changed fields
    function prepareFormSubmission(form) {
        // Remove empty shift fields before submission
        const allShifts = form.querySelectorAll("select[name^='shifts']");
        const activeShifts = [];
        
        allShifts.forEach(select => {
            if (select.value && select.value !== '') {
                // Keep only non-empty values
                activeShifts.push({
                    name: select.name,
                    value: select.value
                });
            }
            // Remove the original input
            select.removeAttribute('name');
        });

        // Add only the active shifts back
        activeShifts.forEach(shift => {
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = shift.name;
            hiddenInput.value = shift.value;
            form.appendChild(hiddenInput);
        });

        return true; // Allow form submission
    }

    // Multiple dates functions
    function addDateField() {
        const container = document.getElementById('dates-container');
        const div = document.createElement('div');
        div.className = 'flex gap-2';
        div.innerHTML = `
            <input type="date" name="dates[]" 
                class="flex-1 border rounded-xl p-2 focus:ring-2 focus:ring-emerald-400">
            <button type="button" onclick="removeDateField(this)" 
                class="px-3 py-2 bg-red-100 text-red-700 rounded-xl hover:bg-red-200 transition">
                −
            </button>
        `;
        container.appendChild(div);
    }

    function removeDateField(button) {
        button.parentElement.remove();
    }

    function addDateRange() {
        const container = document.getElementById('dates-container');
        const div = document.createElement('div');
        div.className = 'flex gap-2 items-center bg-gray-50 p-3 rounded-xl';
        div.innerHTML = `
            <span class="text-sm font-medium text-gray-700">Dari:</span>
            <input type="date" id="range-start" class="border rounded-lg p-2 focus:ring-2 focus:ring-emerald-400">
            <span class="text-sm font-medium text-gray-700">Sampai:</span>
            <input type="date" id="range-end" class="border rounded-lg p-2 focus:ring-2 focus:ring-emerald-400">
            <button type="button" onclick="generateDateRange()" 
                class="px-3 py-2 bg-emerald-100 text-emerald-700 rounded-lg hover:bg-emerald-200 transition text-sm">
                Generate
            </button>
            <button type="button" onclick="removeDateField(this.parentElement)" 
                class="px-3 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition">
                ×
            </button>
        `;
        container.appendChild(div);
    }

    function generateDateRange() {
        const startDate = document.getElementById('range-start').value;
        const endDate = document.getElementById('range-end').value;
        
        if (!startDate || !endDate) {
            alert('Silakan isi tanggal mulai dan selesai');
            return;
        }

        const start = new Date(startDate);
        const end = new Date(endDate);
        const container = document.getElementById('dates-container');
        
        // Remove existing single date inputs (keep only the range input)
        const existingInputs = container.querySelectorAll('div:not(.bg-gray-50)');
        existingInputs.forEach(input => input.remove());

        // Generate dates
        const current = new Date(start);
        while (current <= end) {
            const div = document.createElement('div');
            div.className = 'flex gap-2';
            div.innerHTML = `
                <input type="date" name="dates[]" value="${current.toISOString().split('T')[0]}" 
                    class="flex-1 border rounded-xl p-2 focus:ring-2 focus:ring-emerald-400" readonly>
                <button type="button" onclick="removeDateField(this)" 
                    class="px-3 py-2 bg-red-100 text-red-700 rounded-xl hover:bg-red-200 transition">
                    −
                </button>
            `;
            container.appendChild(div);
            current.setDate(current.getDate() + 1);
        }
    }

    // Day selection functions for range bulk
    function selectWeekdays() {
        const checkboxes = document.querySelectorAll('input[name="selected_days[]"]');
        checkboxes.forEach(cb => {
            cb.checked = ['1', '2', '3', '4', '5'].includes(cb.value);
        });
    }

    function selectWeekends() {
        const checkboxes = document.querySelectorAll('input[name="selected_days[]"]');
        checkboxes.forEach(cb => {
            cb.checked = ['6', '0'].includes(cb.value);
        });
    }

    function selectAllDays() {
        const checkboxes = document.querySelectorAll('input[name="selected_days[]"]');
        checkboxes.forEach(cb => {
            cb.checked = true;
        });
    }

    // Error handling - show relevant tab if there are errors
    @if($errors->any())
        document.addEventListener('DOMContentLoaded', function() {
            @if($errors->has('single_user_id') || $errors->has('single_shift_id') || $errors->has('single_schedule_date'))
                activateTab('single');
            @elseif($errors->has('user_id') || $errors->has('month') || $errors->has('year'))
                activateTab('bulk-monthly');
            @elseif($errors->has('users') || $errors->has('dates'))
                activateTab('bulk-multiple');
            @elseif($errors->has('start_date') || $errors->has('end_date'))
                activateTab('bulk-range');
            @endif
        });
    @endif
</script>
@endsection         