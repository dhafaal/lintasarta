@extends('layouts.app')

@section('title', 'Buat Jadwal Baru')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-sky-50 via-white to-blue-50 p-6">
    <div class="max-w-7xl mx-auto space-y-8">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <h1 class="text-3xl font-bold text-sky-700">📅 Buat Jadwal Baru</h1>
            <a href="{{ route('admin.schedules.index') }}"
               class="px-4 py-2 rounded-xl bg-gray-200 hover:bg-gray-300 text-gray-700 transition">
                ← Kembali
            </a>
        </div>

        <!-- Tabs -->
        <div class="flex gap-4 border-b pb-2">
            <button id="tab-single" class="tab-button font-semibold text-sky-700 border-b-2 border-sky-500">Tambah Satu Jadwal</button>
            <button id="tab-bulk" class="tab-button font-semibold text-gray-500 hover:text-sky-700">Tambah Jadwal Bulanan</button>
        </div>

        <!-- Form Single -->
        <div id="form-single" class="bg-white rounded-2xl shadow-lg p-6 space-y-6">
            <form action="{{ route('admin.schedules.bulkStore') }}" method="POST">
                @csrf
                <input type="hidden" name="single" value="1">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <!-- User -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">👤 Pilih User</label>
                        <select name="single_user_id" class="w-full border rounded-xl p-2 focus:ring-2 focus:ring-sky-400">
                            <option value="">-- Pilih User --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                        @error('single_user_id')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <!-- Shift -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">🕒 Pilih Shift</label>
                        <select name="single_shift_id" class="w-full border rounded-xl p-2 focus:ring-2 focus:ring-sky-400">
                            <option value="">-- Pilih Shift --</option>
                            @foreach($shifts as $shift)
                                <option value="{{ $shift->id }}">{{ $shift->name }}</option>
                            @endforeach
                        </select>
                        @error('single_shift_id')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <!-- Tanggal -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">📆 Tanggal</label>
                        <input type="date" name="single_schedule_date"
                               class="w-full border rounded-xl p-2 focus:ring-2 focus:ring-sky-400">
                        @error('single_schedule_date')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="flex justify-end mt-8">
                    <button type="submit"
                        class="px-6 py-3 rounded-xl bg-sky-600 text-white font-semibold shadow hover:bg-sky-700 transition">
                        💾 Simpan Jadwal
                    </button>
                </div>
            </form>
        </div>

        <!-- Form Bulk -->
        <div id="form-bulk" class="bg-white rounded-2xl shadow-lg p-6 space-y-6 hidden">
            <form action="{{ route('admin.schedules.bulkStore') }}" method="POST">
                @csrf

                <!-- Input utama -->
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-6">
                    <!-- User -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">👤 Pilih User</label>
                        <select name="user_id" class="w-full border rounded-xl p-2 focus:ring-2 focus:ring-sky-400">
                            <option value="">-- Pilih User --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Bulan -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">🗓️ Bulan</label>
                        <select name="month" id="month-select" class="w-full border rounded-xl p-2 focus:ring-2 focus:ring-sky-400">
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $m == date('m') ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <!-- Tahun -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">📆 Tahun</label>
                        <input type="number" id="year-select" name="year" value="{{ date('Y') }}"
                               class="w-full border rounded-xl p-2 focus:ring-2 focus:ring-sky-400" />
                    </div>

                    <!-- Mode tampilan -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">👁️ Tampilan</label>
                        <select id="view-mode" class="w-full border rounded-xl p-2 focus:ring-2 focus:ring-sky-400">
                            <option value="grid">Kalender Grid</option>
                            <option value="table">Tabel</option>
                        </select>
                    </div>
                </div>

                <!-- Bulk Buttons -->
                <div class="flex flex-wrap items-center gap-3 mt-6">
                    <button type="button" onclick="setAllShifts('')"
                        class="px-3 py-1 rounded-lg bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm transition">
                        🗑 Kosongkan Semua
                    </button>
                    @foreach($shifts as $shift)
                        <button type="button" onclick="setAllShifts('{{ $shift->id }}')"
                            class="px-3 py-1 rounded-lg bg-sky-100 hover:bg-sky-200 text-sky-700 text-sm transition">
                            Isi Semua: {{ $shift->name }}
                        </button>
                    @endforeach
                </div>

                <!-- Grid Calendar -->
                <div id="calendar-container" class="mt-6"></div>

                <!-- Table View -->
                <div id="table-container" class="mt-6 hidden overflow-x-auto">
                    <table class="w-full border border-gray-300 rounded-lg overflow-hidden">
                        <thead>
                            <tr class="bg-gray-100 text-gray-700">
                                <th class="p-2 border">Tanggal</th>
                                <th class="p-2 border">Shift</th>
                            </tr>
                        </thead>
                        <tbody id="table-body" class="bg-white"></tbody>
                    </table>
                </div>

                <!-- Submit -->
                <div class="flex justify-end mt-8">
                    <button type="submit"
                        class="px-6 py-3 rounded-xl bg-sky-600 text-white font-semibold shadow hover:bg-sky-700 transition">
                        💾 Simpan Jadwal
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

<script>
    const shifts = @json($shifts);

    // Tab switch
    const tabSingle = document.getElementById('tab-single');
    const tabBulk = document.getElementById('tab-bulk');
    const formSingle = document.getElementById('form-single');
    const formBulk = document.getElementById('form-bulk');

    tabSingle.addEventListener('click', () => {
        tabSingle.classList.add('text-sky-700', 'border-sky-500');
        tabBulk.classList.remove('text-sky-700', 'border-sky-500');
        tabBulk.classList.add('text-gray-500');
        formSingle.classList.remove('hidden');
        formBulk.classList.add('hidden');
    });

    tabBulk.addEventListener('click', () => {
        tabBulk.classList.add('text-sky-700', 'border-sky-500');
        tabSingle.classList.remove('text-sky-700', 'border-sky-500');
        tabSingle.classList.add('text-gray-500');
        formSingle.classList.add('hidden');
        formBulk.classList.remove('hidden');
    });

    // Bulk calendar/table rendering
    document.addEventListener("DOMContentLoaded", () => {
        const monthSelect = document.getElementById("month-select");
        const yearSelect = document.getElementById("year-select");
        const viewMode = document.getElementById("view-mode");

        function renderCalendar() {
            const month = parseInt(monthSelect.value);
            const year = parseInt(yearSelect.value);

            const firstDay = new Date(year, month - 1, 1).getDay() || 7;
            const daysInMonth = new Date(year, month, 0).getDate();

            const calContainer = document.getElementById("calendar-container");
            calContainer.innerHTML = "";

            // Header hari
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
                const tr = document.createElement("tr");
                tr.className = "hover:bg-sky-50 transition";
                tr.innerHTML = `
                    <td class="border p-2 text-center font-medium">${d}</td>
                    <td class="border p-2">
                        <select name="shifts[${d}]" class="w-full border rounded-lg p-1 focus:ring-2 focus:ring-sky-400">
                            <option value="">--</option>
                            ${shifts.map(s => `<option value="${s.id}">${s.name}</option>`).join("")}
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
    });

    // Bulk assign shifts
    function setAllShifts(shiftId) {
        document.querySelectorAll("select[name^='shifts']").forEach(select => {
            select.value = shiftId;
        });
    }
</script>
@endsection
