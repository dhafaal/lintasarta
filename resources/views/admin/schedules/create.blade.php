@extends('layouts.app')

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
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Preset Shift Cepat</label>
                            <div class="flex flex-wrap gap-2">
                                <button type="button"
                                    class="px-3 py-1.5 bg-sky-50 text-sky-600 text-sm font-medium rounded-lg hover:bg-sky-100 transition-colors duration-200"
                                    onclick="applyQuickPreset('pagi')">
                                    Isi Pagi
                                </button>
                                <button type="button"
                                    class="px-3 py-1.5 bg-orange-50 text-orange-600 text-sm font-medium rounded-lg hover:bg-orange-100 transition-colors duration-200"
                                    onclick="applyQuickPreset('siang')">
                                    Isi Siang
                                </button>
                                <button type="button"
                                    class="px-3 py-1.5 bg-purple-50 text-purple-600 text-sm font-medium rounded-lg hover:bg-purple-100 transition-colors duration-200"
                                    onclick="applyQuickPreset('malam')">
                                    Isi Malam
                                </button>
                                <button type="button"
                                    class="px-3 py-1.5 bg-red-50 text-red-600 text-sm font-medium rounded-lg hover:bg-red-100 transition-colors duration-200"
                                    onclick="clearPreset()">
                                    Kosongkan
                                </button>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center gap-4 pt-8 border-t border-gray-200">
                            <button type="submit"
                                class="bg-sky-600 hover:bg-sky-700 text-white font-bold py-4 px-8 rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl disabled:opacity-50"
                                id="submitBtn">
                                <span id="submitText">Simpan Jadwal Bulanan</span>
                            </button>
                            <a href="{{ route('admin.schedules.index') }}"
                                class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-4 px-8 rounded-xl border-2 border-gray-200 hover:border-gray-300 shadow-md">
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
            const monthSelect = document.getElementById("calendarMonth");
            const yearSelect = document.getElementById("calendarYear");
            const calendarContainer = document.getElementById("calendarDays");

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
                    renderCalendar(data);
                } catch (err) {
                    calendarContainer.innerHTML =
                        `<div class="col-span-7 text-center py-8 text-red-500">Gagal memuat data kalender</div>`;
                    console.error(err);
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
                    html += `
                    <div class="p-2 bg-white border border-gray-100 rounded-lg flex flex-col items-center hover:shadow-sm transition-shadow duration-200">
                        <span class="text-sm font-semibold text-gray-700">${day}</span>
                        <select name="shifts[${day}]"
                            class="mt-1 w-full px-2 py-1 border border-gray-200 rounded-md text-xs focus:ring-1 focus:ring-sky-200 focus:border-sky-500 bg-white transition-colors duration-150">
                            <option value="">-- Pilih Shift --</option>
                            @foreach ($shifts as $shift)
                                <option value="{{ $shift->id }}">{{ $shift->name }}</option>
                            @endforeach
                        </select>
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

            monthSelect.addEventListener("change", loadCalendar);
            yearSelect.addEventListener("change", loadCalendar);
            loadCalendar();
        });

        // Atur ID shift sesuai dengan ID shift yang ada di database
        const SHIFT_IDS = {
            pagi: 1, // ganti sesuai id shift pagi
            siang: 2, // ganti sesuai id shift siang
            malam: 3 // ganti sesuai id shift malam
        };

        function applyQuickPreset(type) {
            const shiftId = SHIFT_IDS[type];
            if (!shiftId) return alert("ID shift untuk " + type + " belum diatur!");

            document.querySelectorAll('#calendarDays select').forEach(select => {
                select.value = shiftId;
            });
        }

        function clearPreset() {
            document.querySelectorAll('#calendarDays select').forEach(select => {
                select.value = "";
            });
        }
    </script>
@endsection
