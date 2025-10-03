@extends('layouts.admin')

@section('title', 'Tambah Jadwal')

@section('content')
    <div class="min-h-screen bg-white">
        {{-- Header Section --}}
        <div class="bg-white px-6 py-4">
            <div class="mx-auto">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-sky-100 to-sky-200 rounded-xl flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="lucide lucide-calendar text-sky-600">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                            <line x1="16" x2="16" y1="2" y2="6"/>
                            <line x1="8" x2="8" y1="2" y2="6"/>
                            <line x1="3" x2="21" y1="10" y2="10"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Buat Jadwal Baru</h1>
                        <p class="text-sm text-gray-500">Buat jadwal bulanan baru untuk pengguna dengan mengisi informasi di bawah ini</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Content --}}
        <div class="mx-auto px-6 py-6">
            {{-- Form Card --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-sky-50 to-blue-50">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-sky-100 to-sky-200 rounded-lg flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="lucide lucide-file-text text-sky-600">
                                <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/>
                                <path d="M14 2v4a2 2 0 0 0 2 2h4"/>
                                <path d="M10 9H8"/>
                                <path d="M16 13H8"/>
                                <path d="M16 17H8"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-800">Informasi Jadwal</h2>
                            <p class="text-sm text-gray-500">Lengkapi semua field yang diperlukan untuk jadwal bulanan</p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <form action="{{ route('admin.schedules.store') }}" method="POST" class="space-y-6" id="scheduleForm">
                        @csrf
                        <input type="hidden" name="form_type" value="bulk_monthly">

                        {{-- Month and Year --}}
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">
                                <div class="flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                        class="lucide lucide-calendar text-sky-600">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                        <line x1="16" x2="16" y1="2" y2="6"/>
                                        <line x1="8" x2="8" y1="2" y2="6"/>
                                        <line x1="3" x2="21" y1="10" y2="10"/>
                                    </svg>
                                    <span>Pilih Bulan dan Tahun <span class="text-red-500">*</span></span>
                                </div>
                            </label>
                            <div class="flex items-center gap-4">
                                <select id="calendarMonth" name="month"
                                    class="block w-48 px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 bg-white transition-all duration-200"
                                    required>
                                    <option value="" disabled selected>Pilih bulan</option>
                                    @for ($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                        </option>
                                    @endfor
                                </select>
                                <select id="calendarYear" name="year"
                                    class="block w-32 px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 bg-white transition-all duration-200"
                                    required>
                                    <option value="" disabled selected>Pilih tahun</option>
                                    @for ($y = now()->year - 2; $y <= now()->year + 5; $y++)
                                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                                            {{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        {{-- User Selection with Search --}}
                        <div class="space-y-2">
                            <label for="user_search" class="block text-sm font-semibold text-gray-700">
                                <div class="flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                        class="lucide lucide-user text-sky-600">
                                        <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
                                        <circle cx="12" cy="7" r="4"/>
                                    </svg>
                                    <span>Pilih Pengguna <span class="text-red-500">*</span></span>
                                </div>
                            </label>

                            {{-- Search Input --}}
                            <div class="relative">
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                            class="lucide lucide-search text-gray-400">
                                            <circle cx="11" cy="11" r="8"/>
                                            <path d="m21 21-4.3-4.3"/>
                                        </svg>
                                    </div>
                                    <input type="text"
                                           id="user_search"
                                           class="block w-full pl-12 pr-10 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 bg-white transition-all duration-200"
                                           placeholder="Ketik untuk mencari pengguna..."
                                           autocomplete="off">
                                    <input type="hidden" name="user_id" id="selected_user_id" required>
                                </div>

                                {{-- Search Results Dropdown --}}
                                <div id="user_search_results"
                                     class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-y-auto hidden">
                                    <div id="user_search_loading" class="px-4 py-3 text-sm text-gray-500 text-center hidden">
                                        <svg class="animate-spin h-4 w-4 mx-auto mb-1" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Mencari pengguna...
                                    </div>
                                    <div id="user_search_no_results" class="px-4 py-3 text-sm text-gray-500 text-center hidden">
                                        Tidak ada pengguna ditemukan
                                    </div>
                                    <div id="user_search_results_list" class="divide-y divide-gray-100">
                                        {{-- Results will be populated here --}}
                                    </div>
                                </div>

                                {{-- Selected User Display --}}
                                <div id="selected_user_display" class="mt-3 hidden">
                                    <div class="flex items-center justify-between p-3 bg-sky-50 border border-sky-200 rounded-lg">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-gradient-to-br from-sky-100 to-sky-200 rounded-full flex items-center justify-center mr-3">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="lucide lucide-user text-sky-600">
                                                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
                                                    <circle cx="12" cy="7" r="4"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900" id="selected_user_name"></div>
                                                <div class="text-xs text-gray-500" id="selected_user_email"></div>
                                            </div>
                                        </div>
                                        <button type="button" onclick="clearUserSelection()"
                                                class="text-gray-400 hover:text-gray-600 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                class="lucide lucide-x">
                                                <path d="M18 6 6 18"/>
                                                <path d="m6 6 12 12"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                {{-- Validation Error --}}
                                <div id="user_validation_error" class="mt-2 text-sm text-red-600 hidden">
                                    Silakan pilih pengguna terlebih dahulu
                                </div>
                            </div>
                        </div>

                        {{-- Calendar Grid --}}
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">
                                <div class="flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                        class="lucide lucide-calendar-days text-sky-600">
                                        <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
                                        <line x1="16" x2="16" y1="2" y2="6"/>
                                        <line x1="8" x2="8" y1="2" y2="6"/>
                                        <line x1="3" x2="21" y1="10" y2="10"/>
                                        <path d="M8 14h.01"/>
                                        <path d="M12 14h.01"/>
                                        <path d="M16 14h.01"/>
                                        <path d="M8 18h.01"/>
                                        <path d="M12 18h.01"/>
                                        <path d="M16 18h.01"/>
                                    </svg>
                                    <span>Jadwal Per Tanggal <span class="text-red-500">*</span></span>
                                </div>
                            </label>
                            <div class="bg-sky-50 border border-sky-200 rounded-lg p-4 mb-3">
                                <div class="flex items-start space-x-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                        class="lucide lucide-info text-sky-600 mt-0.5 flex-shrink-0">
                                        <circle cx="12" cy="12" r="10"/>
                                        <path d="M12 16v-4"/>
                                        <path d="M12 8h.01"/>
                                    </svg>
                                    <div class="text-sm text-sky-700">
                                        <p class="font-medium">Jadwal Existing akan ditampilkan otomatis</p>
                                        <p class="text-xs mt-1">Saat Anda memilih user yang sudah memiliki jadwal, shift existing akan muncul otomatis di tanggal yang sesuai. Anda dapat menambahkan shift kedua atau mengubah shift yang ada.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                                <div class="grid grid-cols-7 gap-1 mb-3">
                                    <div class="text-center text-xs font-semibold text-gray-600">Ming</div>
                                    <div class="text-center text-xs font-semibold text-gray-600">Sen</div>
                                    <div class="text-center text-xs font-semibold text-gray-600">Sel</div>
                                    <div class="text-center text-xs font-semibold text-gray-600">Rab</div>
                                    <div class="text-center text-xs font-semibold text-gray-600">Kam</div>
                                    <div class="text-center text-xs font-semibold text-gray-600">Jum</div>
                                    <div class="text-center text-xs font-semibold text-gray-600">Sab</div>
                                </div>
                                <div id="calendarDays" class="grid grid-cols-7 gap-1 text-center text-gray-600">
                                    <div class="col-span-7 text-center py-6 text-gray-400 text-sm">Loading...</div>
                                </div>
                                <p class="text-xs text-gray-500 mt-2" id="daysInfo"></p>
                            </div>
                            {{-- Loading Indicator --}}
                            <div id="loadingIndicator" class="hidden">
                                <div class="flex items-center justify-center py-4">
                                    <svg class="w-6 h-6 animate-spin text-sky-500 mr-2" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span class="text-sky-600 text-sm">Memuat jadwal yang sudah ada...</span>
                                </div>
                            </div>
                        </div>

                        {{-- Preset Shift Cepat --}}
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">
                                <div class="flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                        class="lucide lucide-zap text-sky-600">
                                        <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
                                    </svg>
                                    <span>Preset Shift Cepat</span>
                                </div>
                            </label>
                            <div class="space-y-3 bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <div>
                                    <div class="text-xs text-gray-600 font-medium mb-2">Shift 1 (Dropdown Atas):</div>
                                    <div class="flex flex-wrap gap-2">
                                        <button type="button"
                                            class="px-4 py-2 bg-blue-50 text-blue-700 text-sm font-medium rounded-lg hover:bg-blue-100 transition-colors duration-200 border border-blue-200"
                                            onclick="applyQuickPreset('pagi', 1)">
                                            Shift 1: Pagi
                                        </button>
                                        <button type="button"
                                            class="px-4 py-2 bg-yellow-50 text-yellow-700 text-sm font-medium rounded-lg hover:bg-yellow-100 transition-colors duration-200 border border-yellow-200"
                                            onclick="applyQuickPreset('siang', 1)">
                                            Shift 1: Siang
                                        </button>
                                        <button type="button"
                                            class="px-4 py-2 bg-purple-50 text-purple-700 text-sm font-medium rounded-lg hover:bg-purple-100 transition-colors duration-200 border border-purple-200"
                                            onclick="applyQuickPreset('malam', 1)">
                                            Shift 1: Malam
                                        </button>
                                    </div>
                                </div>
                                <div>
                                    <div class="text-xs text-gray-600 font-medium mb-2">Shift 2 (Dropdown Bawah):</div>
                                    <div class="flex flex-wrap gap-2">
                                        <button type="button"
                                            class="px-4 py-2 bg-blue-50 text-blue-700 text-sm font-medium rounded-lg hover:bg-blue-100 transition-colors duration-200 border border-blue-200"
                                            onclick="applyQuickPreset('pagi', 2)">
                                            Shift 2: Pagi
                                        </button>
                                        <button type="button"
                                            class="px-4 py-2 bg-yellow-50 text-yellow-700 text-sm font-medium rounded-lg hover:bg-yellow-100 transition-colors duration-200 border border-yellow-200"
                                            onclick="applyQuickPreset('siang', 2)">
                                            Shift 2: Siang
                                        </button>
                                        <button type="button"
                                            class="px-4 py-2 bg-purple-50 text-purple-700 text-sm font-medium rounded-lg hover:bg-purple-100 transition-colors duration-200 border border-purple-200"
                                            onclick="applyQuickPreset('malam', 2)">
                                            Shift 2: Malam
                                        </button>
                                    </div>
                                </div>
                                <div>
                                    <div class="text-xs text-gray-600 font-medium mb-2">Kontrol:</div>
                                    <button type="button"
                                        class="px-4 py-2 bg-red-50 text-red-700 text-sm font-medium rounded-lg hover:bg-red-100 transition-colors duration-200 border border-red-200"
                                        onclick="clearPreset()">
                                        Kosongkan Semua
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex flex-col sm:flex-row gap-3 pt-6 border-t border-gray-200">
                            <button type="submit"
                                class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-sky-500 hover:bg-sky-600 text-white font-semibold rounded-lg transition-colors duration-200 focus:outline-none focus:ring-4 focus:ring-sky-200 shadow-md disabled:opacity-50 disabled:cursor-not-allowed"
                                id="submitBtn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                    class="lucide lucide-save">
                                    <path d="M15.2 3a2 2 0 0 1 1.4.6l3.8 3.8a2 2 0 0 1 .6 1.4V19a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"/>
                                    <path d="M17 21v-7a1 1 0 0 0-1-1H8a1 1 0 0 0-1 1v7"/>
                                    <path d="M7 3v4a1 1 0 0 0 1 1h7"/>
                                </svg>
                                <span id="submitText">Simpan Jadwal Bulanan</span>
                            </button>
                            <a href="{{ route('admin.schedules.index') }}"
                               class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg transition-colors duration-200 focus:outline-none focus:ring-4 focus:ring-gray-200 border border-gray-300">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                    class="lucide lucide-arrow-left">
                                    <path d="m12 19-7-7 7-7"/>
                                    <path d="M19 12H5"/>
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
            
            let currentCalendarData = null;
            let currentExistingSchedules = {};

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

            function renderCalendar(data) {
                let html = "";
                let day = 1;
                let currentDayOfWeek = 0;

                for (let i = 0; i < data.firstDayOfMonth; i++) {
                    html += `<div></div>`;
                    currentDayOfWeek++;
                }

                while (day <= data.daysInMonth) {
                    const existingSchedulesForDay = currentExistingSchedules[day] || [];
                    const shift1Selected = existingSchedulesForDay[0] ? existingSchedulesForDay[0].shift_id : '';
                    const shift2Selected = existingSchedulesForDay[1] ? existingSchedulesForDay[1].shift_id : '';
                    
                    html += `
                    <div class="p-2 bg-gray-50 border border-gray-200 rounded-lg flex flex-col items-center hover:shadow-sm transition-shadow duration-200">
                        <span class="text-sm font-semibold text-gray-700 mb-1">${day}</span>
                        <div class="w-full space-y-1">
                            <select name="shifts[${day}][]" data-day="${day}" data-shift-position="1" onchange="updateSecondDropdown(${day})"
                                class="shift-dropdown-1 w-full px-2 py-1 border border-gray-300 rounded-md text-xs focus:ring-1 focus:ring-sky-500 focus:border-sky-500 bg-white transition-colors duration-150">
                                <option value="">-- Shift 1 --</option>
                                @foreach ($shifts as $shift)
                                    <option value="{{ $shift->id }}" data-shift-name="{{ $shift->shift_name }}" ${shift1Selected == '{{ $shift->id }}' ? 'selected' : ''}>{{ $shift->shift_name }}</option>
                                @endforeach
                            </select>
                            <select name="shifts[${day}][]" data-day="${day}" data-shift-position="2" id="shift2-${day}"
                                class="shift-dropdown-2 w-full px-2 py-1 border border-gray-300 rounded-md text-xs focus:ring-1 focus:ring-green-500 focus:border-green-500 bg-white transition-colors duration-150">
                                <option value="">-- Shift 2 --</option>
                                @foreach ($shifts as $shift)
                                    <option value="{{ $shift->id }}" data-shift-name="{{ $shift->shift_name }}" ${shift2Selected == '{{ $shift->id }}' ? 'selected' : ''}>{{ $shift->shift_name }}</option>
                                @endforeach
                            </select>
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

            // User Search Functionality
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
        });

        const SHIFT_IDS = {
            pagi: 1,
            siang: 2,
            malam: 3
        };

        function applyQuickPreset(type, shiftPosition = 1) {
            const shiftId = SHIFT_IDS[type];
            if (!shiftId) return alert("ID shift untuk " + type + " belum diatur!");

            const selector = shiftPosition === 1 
                ? '.shift-dropdown-1'
                : '.shift-dropdown-2';
            
            document.querySelectorAll(selector).forEach(select => {
                select.value = shiftId;
                
                if (shiftPosition === 1) {
                    const day = select.getAttribute('data-day');
                    updateSecondDropdown(day);
                }
            });
        }

        function clearPreset() {
            document.querySelectorAll('#calendarDays select').forEach(select => {
                select.value = "";
            });
            document.querySelectorAll('.shift-dropdown-1').forEach(firstDropdown => {
                const day = firstDropdown.getAttribute('data-day');
                updateSecondDropdown(day);
            });
        }

        function updateSecondDropdown(day) {
            const firstDropdown = document.querySelector(`select[data-day="${day}"][data-shift-position="1"]`);
            const secondDropdown = document.querySelector(`select[data-day="${day}"][data-shift-position="2"]`);
            
            if (!firstDropdown || !secondDropdown) return;
            
            const selectedShiftId = firstDropdown.value;
            const currentSecondValue = secondDropdown.value;  
            
            const allShiftOptions = [
                @foreach ($shifts as $shift)
                    { id: "{{ $shift->id }}", name: "{{ $shift->shift_name }}" },
                @endforeach
            ];
            
            secondDropdown.innerHTML = '<option value="">-- Shift 2 --</option>';
            
            allShiftOptions.forEach(shift => {
                if (shift.id !== selectedShiftId) {
                    const option = document.createElement('option');
                    option.value = shift.id;
                    option.textContent = shift.name;
                    option.setAttribute('data-shift-name', shift.name);
                    
                    if (shift.id === currentSecondValue) {
                        option.selected = true;
                    }
                    
                    secondDropdown.appendChild(option);
                }
            });
            
            if (selectedShiftId === currentSecondValue) {
                secondDropdown.value = "";
            }
        }

        document.getElementById('scheduleForm')?.addEventListener('submit', function() {
            const submitBtn = document.getElementById('submitBtn');
            const submitText = document.getElementById('submitText');
            
            submitBtn.disabled = true;
            submitText.innerHTML = `
                <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Menyimpan...
            `;
        });
    </script>
@endsection