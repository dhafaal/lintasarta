@extends('layouts.admin')

@section('title', 'Edit Jadwal')

@section('content')
    <div class="min-h-screen bg-white">
        {{-- Header Section --}}
        <div class="bg-white border-gray-200 px-6 py-4">
            <div class="mx-auto">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-sky-100 to-sky-200 rounded-xl flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="lucide lucide-calendar text-sky-600">
                            <path d="M8 2v4"/>
                            <path d="M16 2v4"/>
                            <rect width="18" height="18" x="3" y="4" rx="2"/>
                            <path d="M3 10h18"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">
                            @if(isset($isBulkEdit) && $isBulkEdit)
                                Edit Jadwal Bulanan
                            @else
                                Edit Jadwal Bulanan
                            @endif
                        </h1>
                        <p class="text-sm text-gray-500">
                            @if(isset($isBulkEdit) && $isBulkEdit)
                                @if(isset($selectedUser))
                                    Edit jadwal bulanan untuk {{ $selectedUser->name }} dengan mengubah informasi di bawah ini
                                @else
                                    Edit jadwal bulanan dengan mengubah informasi di bawah ini
                                @endif
                            @else
                                Edit jadwal bulanan untuk {{ $schedule->user->name }} dengan mengubah informasi di bawah ini
                            @endif
                        </p>
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
                                class="lucide lucide-edit text-sky-600">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4Z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-800">Edit Informasi Jadwal</h2>
                            <p class="text-sm text-gray-500">Lengkapi semua field yang diperlukan untuk jadwal bulanan</p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
                            <ul class="list-disc ml-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if ($errors->has('attendance_conflict'))
                        <div class="mb-4 p-4 bg-amber-50 border border-amber-200 rounded-lg text-sm text-amber-800">
                            <div class="font-semibold mb-2">Konfirmasi Diperlukan</div>
                            <div class="mb-3">{{ $errors->first('attendance_conflict') }}</div>
                            <div class="flex gap-2">
                                <button type="button" id="confirmRemapBtn"
                                    class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-md">
                                    Pindahkan attendance & simpan
                                </button>
                                <a href="{{ url()->current() }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md border border-gray-300">Batalkan</a>
                            </div>
                        </div>
                    @endif

                    <form action="{{ isset($isBulkEdit) && $isBulkEdit ? route('admin.schedules.update', 'bulk') : route('admin.schedules.update', $schedule) }}" method="POST" class="space-y-6" id="scheduleForm">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="form_type" value="bulk_monthly">
                        <input type="hidden" name="on_attendance_conflict" id="on_attendance_conflict" value="">

                        {{-- Bulan dan Tahun --}}
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">
                                <div class="flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                        class="lucide lucide-calendar text-sky-600">
                                        <path d="M8 2v4"/>
                                        <path d="M16 2v4"/>
                                        <rect width="18" height="18" x="3" y="4" rx="2"/>
                                        <path d="M3 10h18"/>
                                    </svg>
                                    <span>Pilih Bulan dan Tahun <span class="text-red-500">*</span></span>
                                </div>
                            </label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="relative">
                                    <select id="calendarMonth" name="month"
                                        class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 bg-white cursor-pointer"
                                        required>
                                        <option value="" disabled>Pilih bulan</option>
                                        @php
                                            $currentMonth = isset($isBulkEdit) && $isBulkEdit ? now()->month : \Carbon\Carbon::parse($schedule->schedule_date)->month;
                                        @endphp
                                        @for ($m = 1; $m <= 12; $m++)
                                            <option value="{{ $m }}" {{ $currentMonth == $m ? 'selected' : '' }}>
                                                {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                            </option>
                                        @endfor
                                    </select>
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                            class="lucide lucide-chevron-down text-gray-400">
                                            <path d="m6 9 6 6 6-6"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="relative">
                                    <select id="calendarYear" name="year"
                                        class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 bg-white cursor-pointer"
                                        required>
                                        <option value="" disabled>Pilih tahun</option>
                                        @php
                                            $currentYear = isset($isBulkEdit) && $isBulkEdit ? now()->year : \Carbon\Carbon::parse($schedule->schedule_date)->year;
                                        @endphp
                                        @for ($y = now()->year - 2; $y <= now()->year + 5; $y++)
                                            <option value="{{ $y }}" {{ $currentYear == $y ? 'selected' : '' }}>
                                                {{ $y }}</option>
                                        @endfor
                                    </select>
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                            class="lucide lucide-chevron-down text-gray-400">
                                            <path d="m6 9 6 6 6-6"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                    class="lucide lucide-info">
                                    <circle cx="12" cy="12" r="10"/>
                                    <path d="M12 16v-4"/>
                                    <path d="M12 8h.01"/>
                                </svg>
                                Mengubah bulan/tahun akan memuat ulang kalender dan menampilkan jadwal yang sudah ada
                            </p>
                        </div>

                        {{-- Karyawan --}}
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">
                                <div class="flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                        class="lucide lucide-user text-sky-600">
                                        <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
                                        <circle cx="12" cy="7" r="4"/>
                                    </svg>
                                    <span>Karyawan</span>
                                </div>
                            </label>
                            <div class="relative">
                                @if(isset($isBulkEdit) && $isBulkEdit)
                                    @if(isset($selectedUser))
                                        <div class="block w-full px-4 py-2.5 border border-sky-300 rounded-lg bg-sky-50 text-gray-900 font-medium">
                                            {{ $selectedUser->name }}
                                        </div>
                                        <input type="hidden" name="user_id" value="{{ $selectedUser->id }}">
                                        <input type="hidden" id="user_id" value="{{ $selectedUser->id }}">
                                    @else
                                        <select name="user_id" id="user_id" class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 bg-white cursor-pointer" required>
                                            <option value="">Pilih Karyawan</option>
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                class="lucide lucide-chevron-down text-gray-400">
                                                <path d="m6 9 6 6 6-6"/>
                                            </svg>
                                        </div>
                                    @endif
                                @else
                                    <div class="block w-full px-4 py-2.5 border border-sky-300 rounded-lg bg-sky-50 text-gray-900 font-medium">
                                        {{ $schedule->user->name }}
                                    </div>
                                    <input type="hidden" name="user_id" value="{{ $schedule->user_id }}">
                                    <input type="hidden" id="user_id" value="{{ $schedule->user_id }}">
                                @endif
                                @if(!isset($selectedUser) || !$selectedUser)
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                            class="lucide lucide-user text-gray-400">
                                            <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
                                            <circle cx="12" cy="7" r="4"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <p class="text-xs text-gray-500">Karyawan tidak dapat diubah saat mengedit jadwal</p>
                        </div>


                        <!-- Calendar Grid -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">
                                Jadwal Per Tanggal <span class="text-red-500">*</span>
                            </label>
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 shadow-sm">
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
                            <!-- Loading Indicator -->
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

                        <div class="space-y-2">
                            <div class="text-xs text-gray-500 font-medium">Kontrol:</div>
                                <div class="flex flex-wrap gap-2">
                                    <button type="button"
                                        class="px-3 py-1.5 bg-red-50 text-red-600 text-sm font-medium rounded-lg hover:bg-red-100 transition-colors duration-200"
                                        onclick="clearPreset()">
                                        Kosongkan Semua
                                    </button>
                                </div>      
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex flex-col sm:flex-row gap-3 pt-6 border-t border-gray-200">
                            <button type="submit"
                                class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-sky-500 hover:bg-sky-600 text-white font-semibold rounded-lg transition-colors duration-200 focus:outline-none focus:ring-4 focus:ring-sky-200 disabled:opacity-50 disabled:cursor-not-allowed"
                                id="submitBtn">
                                <span class="flex items-center justify-center gap-2" id="submitText">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                        class="lucide lucide-save">
                                        <path d="M15.2 3a2 2 0 0 1 1.4.6l3.8 3.8a2 2 0 0 1 .6 1.4V19a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"/>
                                        <path d="M17 21v-7a1 1 0 0 0-1-1H8a1 1 0 0 0-1 1v7"/>
                                        <path d="M7 3v4a1 1 0 0 0 1 1h7"/>
                                    </svg>
                                    Update Jadwal Bulanan
                                </span>
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
                        
                        <!-- Validation Alert -->
                        <div id="validationAlert" class="hidden mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-red-700 text-sm" id="validationMessage"></span>
                            </div>
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
            const userSelect = document.getElementById("user_id");
            const calendarContainer = document.getElementById("calendarDays");
            const conflictInput = document.getElementById('on_attendance_conflict');
            const confirmRemapBtn = document.getElementById('confirmRemapBtn');
            
            let currentCalendarData = null;
            let currentExistingSchedules = {};
            // Preserve previous user selection after validation error
            const OLD_SHIFTS = @json(old('shifts', []));

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
                    await loadExistingSchedules(); // Auto-load existing schedules after calendar loads
                    return data; // Return data untuk promise
                } catch (err) {
                    calendarContainer.innerHTML =
                        `<div class="col-span-7 text-center py-8 text-red-500">Gagal memuat data kalender</div>`;
                    console.error(err);
                    throw err; // Re-throw error untuk promise rejection
                }
            }

            async function loadExistingSchedules() {
                const userId = userSelect.value;
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

                // kosongkan awal bulan
                for (let i = 0; i < data.firstDayOfMonth; i++) {
                    html += `<div></div>`;
                    currentDayOfWeek++;
                }

                while (day <= data.daysInMonth) {
                    // Determine selected shifts: prefer OLD_SHIFTS (from previous submit) then fallback to existing
                    let shift1Selected = '';
                    let shift2Selected = '';
                    const oldForDay = OLD_SHIFTS && OLD_SHIFTS[day] ? OLD_SHIFTS[day] : null;
                    if (oldForDay && Array.isArray(oldForDay) && (oldForDay[0] || oldForDay[1])) {
                        shift1Selected = oldForDay[0] || '';
                        shift2Selected = oldForDay[1] || '';
                    } else {
                        const existingSchedulesForDay = currentExistingSchedules[day] || [];
                        shift1Selected = existingSchedulesForDay[0] ? existingSchedulesForDay[0].shift_id : '';
                        shift2Selected = existingSchedulesForDay[1] ? existingSchedulesForDay[1].shift_id : '';
                    }
                    
                    // Check if this day has any shifts assigned
                    const hasShifts = shift1Selected || shift2Selected;
                    const dayClass = hasShifts 
                        ? "p-2 bg-white border-2 border-blue-400 rounded-lg flex flex-col items-center hover:shadow-sm transition-all duration-200 ring-2 ring-blue-100" 
                        : "p-2 bg-white border border-gray-100 rounded-lg flex flex-col items-center hover:shadow-sm transition-shadow duration-200";
                    
                    html += `
                    <div class="${dayClass}">
                        <span class="text-sm font-semibold ${hasShifts ? 'text-blue-700' : 'text-gray-700'} mb-1">${day}</span>
                        <div class="w-full space-y-1">
                            <!-- Shift 1 with Search -->
                            <div class="relative shift-search-container" data-day="${day}" data-position="1">
                                <input type="text" 
                                    class="shift-search-input w-full px-2 py-1 border border-gray-200 rounded-md text-xs focus:ring-1 focus:ring-sky-200 focus:border-sky-500 bg-white"
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
                                    class="shift-search-input w-full px-2 py-1 border border-gray-200 rounded-md text-xs focus:ring-1 focus:ring-green-200 focus:border-green-500 bg-white"
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

            monthSelect.addEventListener("change", async function() {
                await loadCalendar();
            });
            yearSelect.addEventListener("change", async function() {
                await loadCalendar();
            });
            if (userSelect) userSelect.addEventListener("change", loadExistingSchedules);
            
            // Initial load
            loadCalendar();
            
            // Global click outside handler - only register once
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.shift-search-container')) {
                    document.querySelectorAll('.shift-dropdown').forEach(dropdown => {
                        dropdown.classList.add('hidden');
                    });
                }
            });

            // Form submission validation and loading states
            document.getElementById('scheduleForm')?.addEventListener('submit', function(e) {
                const submitBtn = document.getElementById('submitBtn');
                const submitText = document.getElementById('submitText');
                const validationAlert = document.getElementById('validationAlert');
                const validationMessage = document.getElementById('validationMessage');
                
                // Hide previous validation messages
                validationAlert.classList.add('hidden');
                
                // Basic validation
                const month = document.getElementById('calendarMonth').value;
                const year = document.getElementById('calendarYear').value;
                
                if (!month || !year) {
                    e.preventDefault();
                    validationMessage.textContent = 'Mohon pilih bulan dan tahun terlebih dahulu.';
                    validationAlert.classList.remove('hidden');
                    return false;
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
                    validationMessage.textContent = 'Mohon pilih minimal satu shift untuk jadwal.';
                    validationAlert.classList.remove('hidden');
                    return false;
                }
                
                // Show loading state
                submitBtn.disabled = true;
                submitText.innerHTML = `
                    <svg class="w-5 h-5 animate-spin mr-2" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Mengupdate...
                `;
            });
        }); // End of DOMContentLoaded

        // Global functions - OUTSIDE DOMContentLoaded
        const SHIFT_IDS = {
            pagi: 1, // ganti sesuai id shift pagi
            siang: 2, // ganti sesuai id shift siang
            malam: 3 // ganti sesuai id shift malam
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
                console.log('confirmRemapBtn clicked!'); // Debug
                const conflictInput = document.getElementById('on_attendance_conflict');
                const scheduleForm = document.getElementById('scheduleForm');
                
                console.log('conflictInput:', conflictInput); // Debug
                console.log('scheduleForm:', scheduleForm); // Debug
                
                if (conflictInput) {
                    conflictInput.value = 'remap';
                    console.log('Set on_attendance_conflict to: remap'); // Debug
                }
                
                if (scheduleForm) {
                    console.log('Submitting form...'); // Debug
                    scheduleForm.submit();
                }
            }
        });
    </script>
@endsection 