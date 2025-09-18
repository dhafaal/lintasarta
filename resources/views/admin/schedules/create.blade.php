@extends('layouts.admin')

@section('title', 'Tambah Jadwal')

@section('content')
    <div class="min-h-screen bg-white sm:p-6 lg:p-8">
        <div class="mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center gap-4 mb-3">
                    <div class="p-3 bg-gradient-to-br from-sky-100 to-sky-200 rounded-xl shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="lucide lucide-calendar text-sky-700">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-700 tracking-tight">Buat Jadwal Baru</h1>
                        <p class="text-gray-500 mt-1">
                            Buat jadwal bulanan baru untuk pengguna dengan mengisi informasi di bawah ini
                        </p>
                    </div>
                </div>
            </div>

            <!-- Form Card -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                <!-- Form Header -->
                <div class="bg-gradient-to-r from-sky-500 to-sky-600 px-8 py-6">
                    <h2 class="text-xl font-semibold text-white">Informasi Jadwal</h2>
                    <p class="text-sky-100 mt-1">Lengkapi semua field yang diperlukan untuk jadwal bulanan</p>
                </div>

                <div class="p-8">
                    <form action="{{ route('admin.schedules.store') }}" method="POST" class="space-y-8" id="scheduleForm">
                        @csrf
                        <input type="hidden" name="form_type" value="bulk_monthly">

                        <!-- Month and Year -->
                        <div class="space-y-3">
                            <label class="block text-sm font-bold text-gray-800">
                                Pilih Bulan dan Tahun <span class="text-red-500">*</span>
                            </label>
                            <div class="flex items-center gap-4">
                                <div class="relative group">
                                    <select id="calendarMonth" name="month"
                                        class="block w-48 py-4 px-3 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-sky-100 focus:border-sky-500 bg-gray-50 focus:bg-white cursor-pointer"
                                        required>
                                        <option value="" disabled selected>Pilih bulan</option>
                                        @for ($m = 1; $m <= 12; $m++)
                                            <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                                {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="relative group">
                                    <select id="calendarYear" name="year"
                                        class="block w-32 py-4 px-3 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-sky-100 focus:border-sky-500 bg-gray-50 focus:bg-white cursor-pointer"
                                        required>
                                        <option value="" disabled selected>Pilih tahun</option>
                                        @for ($y = now()->year - 2; $y <= now()->year + 5; $y++)
                                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                                                {{ $y }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- User Selection -->
                        <div class="space-y-3">
                            <label for="user_id" class="block text-sm font-bold text-gray-800">
                                Pilih Pengguna <span class="text-red-500">*</span>
                            </label>
                            <select id="user_id" name="user_id"
                                class="block w-full pl-3 pr-10 py-4 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-sky-100 focus:border-sky-500 bg-gray-50 focus:bg-white cursor-pointer"
                                required>
                                <option value="" disabled selected>Pilih pengguna</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
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
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Preset Shift Cepat</label>
                            <div class="space-y-2">
                                <div class="text-xs text-gray-500 font-medium">Shift 1 (Dropdown Atas):</div>
                                <div class="flex flex-wrap gap-2">
                                    <button type="button"
                                        class="px-3 py-1.5 bg-sky-50 text-sky-600 text-sm font-medium rounded-lg hover:bg-sky-100 transition-colors duration-200"
                                        onclick="applyQuickPreset('pagi', 1)">
                                        Shift 1: Pagi
                                    </button>
                                    <button type="button"
                                        class="px-3 py-1.5 bg-orange-50 text-orange-600 text-sm font-medium rounded-lg hover:bg-orange-100 transition-colors duration-200"
                                        onclick="applyQuickPreset('siang', 1)">
                                        Shift 1: Siang
                                    </button>
                                    <button type="button"
                                        class="px-3 py-1.5 bg-purple-50 text-purple-600 text-sm font-medium rounded-lg hover:bg-purple-100 transition-colors duration-200"
                                        onclick="applyQuickPreset('malam', 1)">
                                        Shift 1: Malam
                                    </button>
                                </div>
                                <div class="text-xs text-gray-500 font-medium">Shift 2 (Dropdown Bawah):</div>
                                <div class="flex flex-wrap gap-2">
                                    <button type="button"
                                        class="px-3 py-1.5 bg-sky-50 text-sky-600 text-sm font-medium rounded-lg hover:bg-sky-100 transition-colors duration-200"
                                        onclick="applyQuickPreset('pagi', 2)">
                                        Shift 2: Pagi
                                    </button>
                                    <button type="button"
                                        class="px-3 py-1.5 bg-orange-50 text-orange-600 text-sm font-medium rounded-lg hover:bg-orange-100 transition-colors duration-200"
                                        onclick="applyQuickPreset('siang', 2)">
                                        Shift 2: Siang
                                    </button>
                                    <button type="button"
                                        class="px-3 py-1.5 bg-purple-50 text-purple-600 text-sm font-medium rounded-lg hover:bg-purple-100 transition-colors duration-200"
                                        onclick="applyQuickPreset('malam', 2)">
                                        Shift 2: Malam
                                    </button>
                                </div>
                                <div class="text-xs text-gray-500 font-medium">Kontrol:</div>
                                <div class="flex flex-wrap gap-2">
                                    <button type="button"
                                        class="px-3 py-1.5 bg-red-50 text-red-600 text-sm font-medium rounded-lg hover:bg-red-100 transition-colors duration-200"
                                        onclick="clearPreset()">
                                        Kosongkan Semua
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-col sm:flex-row gap-4 pt-8 border-t border-gray-200 dark:border-gray-700">
                            <button type="submit"
                                class="flex-1 bg-gradient-to-r from-sky-500 to-sky-600 hover:from-sky-600 hover:to-sky-700 text-white font-bold py-4 px-8 rounded-xl transition-all duration-200 transform hover:scale-[1.02] focus:outline-none focus:ring-4 focus:ring-sky-200 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed"
                                id="submitBtn">
                                <span class="flex items-center justify-center gap-3" id="submitText">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 4v10m6-10v10m-6-4h6"></path>
                                    </svg>
                                    Simpan Jadwal Bulanan
                                </span>
                            </button>
                            <a href="{{ route('admin.schedules.index') }}"
                                class="flex-1 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-bold py-4 px-8 rounded-xl transition-all duration-200 text-center focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-600 border-2 border-gray-200 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500">
                                <span class="flex items-center justify-center gap-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                    </svg>
                                    Kembali ke Daftar
                                </span>
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
            const userSelect = document.getElementById("user_id");
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
                    // Check if there are existing schedules for this day
                    const existingSchedulesForDay = currentExistingSchedules[day] || [];
                    const shift1Selected = existingSchedulesForDay[0] ? existingSchedulesForDay[0].shift_id : '';
                    const shift2Selected = existingSchedulesForDay[1] ? existingSchedulesForDay[1].shift_id : '';
                    
                    html += `
                    <div class="p-2 bg-white border border-gray-100 rounded-lg flex flex-col items-center hover:shadow-sm transition-shadow duration-200">
                        <span class="text-sm font-semibold text-gray-700 mb-1">${day}</span>
                        <div class="w-full space-y-1">
                            <select name="shifts[${day}][]" data-day="${day}" data-shift-position="1" onchange="updateSecondDropdown(${day})"
                                class="shift-dropdown-1 w-full px-2 py-1 border border-gray-200 rounded-md text-xs focus:ring-1 focus:ring-sky-200 focus:border-sky-500 bg-white transition-colors duration-150">
                                <option value="">-- Shift 1 --</option>
                                @foreach ($shifts as $shift)
                                    <option value="{{ $shift->id }}" data-shift-name="{{ $shift->shift_name }}" ${shift1Selected == '{{ $shift->id }}' ? 'selected' : ''}>{{ $shift->shift_name }}</option>
                                @endforeach
                            </select>
                            <select name="shifts[${day}][]" data-day="${day}" data-shift-position="2" id="shift2-${day}"
                                class="shift-dropdown-2 w-full px-2 py-1 border border-gray-200 rounded-md text-xs focus:ring-1 focus:ring-green-200 focus:border-green-500 bg-white transition-colors duration-150">
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
            if (userSelect) userSelect.addEventListener("change", loadExistingSchedules);
            
            // Initial loads
            loadCalendar();
            loadExistingSchedules();
        });

        // Atur ID shift sesuai dengan ID shift yang ada di database
        const SHIFT_IDS = {
            pagi: 1, // ganti sesuai id shift pagi
            siang: 2, // ganti sesuai id shift siang
            malam: 3 // ganti sesuai id shift malam
        };

        function applyQuickPreset(type, shiftPosition = 1) {
            const shiftId = SHIFT_IDS[type];
            if (!shiftId) return alert("ID shift untuk " + type + " belum diatur!");

            // Apply to specific shift position (1 or 2)
            const selector = shiftPosition === 1 
                ? '.shift-dropdown-1'
                : '.shift-dropdown-2';
            
            document.querySelectorAll(selector).forEach(select => {
                select.value = shiftId;
                
                // If this is a first dropdown, update the corresponding second dropdown
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
            // Reset all second dropdowns to show all options
            document.querySelectorAll('.shift-dropdown-1').forEach(firstDropdown => {
                const day = firstDropdown.getAttribute('data-day');
                updateSecondDropdown(day);
            });
        }

        // Function to update second dropdown based on first dropdown selection
        function updateSecondDropdown(day) {
            const firstDropdown = document.querySelector(`select[data-day="${day}"][data-shift-position="1"]`);
            const secondDropdown = document.querySelector(`select[data-day="${day}"][data-shift-position="2"]`);
            
            if (!firstDropdown || !secondDropdown) return;
            
            const selectedShiftId = firstDropdown.value;
            const currentSecondValue = secondDropdown.value;
            
            // Get all original options from the template
            const allShiftOptions = [
                @foreach ($shifts as $shift)
                    { id: "{{ $shift->id }}", name: "{{ $shift->shift_name }}" },
                @endforeach
            ];
            
            // Clear second dropdown
            secondDropdown.innerHTML = '<option value="">-- Shift 2 --</option>';
            
            // Add options that are not selected in first dropdown
            allShiftOptions.forEach(shift => {
                if (shift.id !== selectedShiftId) {
                    const option = document.createElement('option');
                    option.value = shift.id;
                    option.textContent = shift.name;
                    option.setAttribute('data-shift-name', shift.name);
                    
                    // Restore previous selection if it's still valid
                    if (shift.id === currentSecondValue) {
                        option.selected = true;
                    }
                    
                    secondDropdown.appendChild(option);
                }
            });
            
            // If the current second dropdown value is now invalid, clear it
            if (selectedShiftId === currentSecondValue) {
                secondDropdown.value = "";
            }
        }

        // Form submission loading states
        document.getElementById('scheduleForm')?.addEventListener('submit', function() {
            const submitBtn = document.getElementById('submitBtn');
            const submitText = document.getElementById('submitText');
            
            submitBtn.disabled = true;
            submitText.innerHTML = `
                <svg class="w-5 h-5 animate-spin mr-2" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Menyimpan...
            `;
        });
    </script>
@endsection