@extends("layouts.admin")

@section("title", "Riwayat Jadwal")

@section("content")
    <div class="min-h-screen bg-white sm:p-6 lg:p-8">
        <div class="mx-auto space-y-8">
            <!-- Enhanced Header Section -->
            <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex items-center space-x-4">
                    <div
                        class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-sky-100 to-sky-200 shadow-lg"
                    >
                        <i data-lucide="calendar-days" class="h-7 w-7 text-sky-600"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight text-gray-900">Riwayat Jadwal</h1>
                        <p class="mt-1 text-sm text-gray-600">
                            {{ $user->name }} • Kelola dan lihat riwayat penjadwalan karyawan
                        </p>
                    </div>
                </div>
                <a
                    href="{{ route("admin.schedules.index") }}"
                    class="inline-flex items-center px-6 py-2.5 bg-sky-50 text-sky-700 border-2 border-sky-100 font-bold rounded-xl transition-all transform hover:bg-sky-100 hover:scale-105 active:scale-95 focus:outline-none focus:ring-4 focus:ring-sky-200 shadow-sm whitespace-nowrap"
                >
                    <i data-lucide="arrow-left" class="mr-2 h-4 w-4"></i>
                    Kembali
                </a>
            </div>

            <!-- Compact User Info -->
            <div class="rounded-lg border border-gray-200 bg-white p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-sky-100">
                            <span class="text-sm font-bold text-sky-600">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </span>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900">{{ $user->name }}</h3>
                            <p class="text-xs text-gray-500">{{ $user->email }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-500">Total</p>
                        <p class="text-xl font-bold text-gray-900">
                            {{ method_exists($schedules, "total") ? $schedules->total() : $schedules->count() }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Minimalist Filter Section -->
            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
                <div class="border-sky-gray-200 border-b bg-gradient-to-br from-sky-50 to-blue-50 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-gray-700">Filter Periode</h3>
                        @if (request("start_date") || request("end_date"))
                            <a
                                href="{{ route("admin.schedules.history", $user->id) }}"
                                class="text-xs font-medium text-sky-600 hover:text-sky-700"
                            >
                                Reset
                            </a>
                        @endif
                    </div>
                </div>

                <div class="p-6">
                    <form method="GET" action="{{ route("admin.schedules.history", $user->id) }}" class="space-y-4">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                            <div>
                                <label for="start_date" class="mb-2 block text-xs font-medium text-gray-600">
                                    Dari Tanggal
                                </label>
                                <input
                                    type="date"
                                    name="start_date"
                                    id="start_date"
                                    value="{{ request("start_date") }}"
                                    class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-500"
                                />
                            </div>

                            <div>
                                <label for="end_date" class="mb-2 block text-xs font-medium text-gray-600">
                                    Sampai Tanggal
                                </label>
                                <input
                                    type="date"
                                    name="end_date"
                                    id="end_date"
                                    value="{{ request("end_date") }}"
                                    class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-500"
                                />
                            </div>

                            <div class="flex items-end">
                                <button
                                    type="submit"
                                    class="inline-flex w-full items-center justify-center rounded-lg bg-sky-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-sky-700"
                                >
                                    <i data-lucide="filter" class="mr-2 h-4 w-4"></i>
                                    Filter
                                </button>
                            </div>
                        </div>

                        <!-- Realtime Search -->
                        <div>
                            <label for="realtime_search" class="mb-2 block text-xs font-medium text-gray-600">
                                Cari di Tabel (Realtime)
                            </label>
                            <div class="relative">
                                <input
                                    type="text"
                                    id="searchInput"
                                    class="block w-full rounded-lg border border-gray-300 py-2 pr-9 pl-9 text-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-500"
                                    placeholder="Ketik untuk mencari..."
                                    autocomplete="off"
                                />
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <i data-lucide="search" class="h-4 w-4 text-gray-400"></i>
                                </div>
                                <button
                                    type="button"
                                    id="clear_search"
                                    class="absolute inset-y-0 right-0 flex hidden items-center pr-3 text-gray-400 hover:text-gray-600"
                                >
                                    <i data-lucide="x" class="h-4 w-4"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Enhanced Table Section -->
            <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-xl">
                <div class="border-b border-gray-200 bg-gradient-to-r from-sky-50 to-blue-50 px-8 py-6">
                    <h3 class="flex items-center gap-3 text-lg font-semibold text-gray-800">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-md bg-gradient-to-br from-sky-100 to-sky-200"
                        >
                            <i data-lucide="history" class="h-6 w-6 text-sky-600"></i>
                        </div>
                        Data Riwayat Jadwal
                    </h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="border-b-2 border-gray-200 bg-white">
                            <tr>
                                <th
                                    class="px-8 py-4 text-left text-xs font-bold tracking-wider text-gray-700 uppercase"
                                >
                                    <div class="flex items-center">
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
                                            class="lucide lucide-clock mr-2 text-sky-600"
                                        >
                                            <circle cx="12" cy="12" r="10" />
                                            <polyline points="12 6 12 12 16 14" />
                                        </svg>
                                        Shift
                                    </div>
                                </th>
                                <th
                                    class="px-8 py-4 text-left text-xs font-bold tracking-wider text-gray-700 uppercase"
                                >
                                    <div class="flex items-center">
                                        <i data-lucide="map-pin" class="mr-2 h-4 w-4 text-sky-600"></i>
                                        Lokasi
                                    </div>
                                </th>
                                <th
                                    class="px-8 py-4 text-left text-xs font-bold tracking-wider text-gray-700 uppercase"
                                >
                                    <div class="flex items-center">
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
                                            class="lucide lucide-calendar mr-2 text-sky-600"
                                        >
                                            <path d="M8 2v4" />
                                            <path d="M16 2v4" />
                                            <rect width="18" height="18" x="3" y="4" rx="2" />
                                            <path d="M3 10h18" />
                                        </svg>
                                        Tanggal
                                    </div>
                                </th>
                                <th
                                    class="px-8 py-4 text-left text-xs font-bold tracking-wider text-gray-700 uppercase"
                                >
                                    <div class="flex items-center">
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
                                            class="lucide lucide-log-in mr-2 text-sky-600"
                                        >
                                            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" />
                                            <polyline points="10 17 15 12 10 7" />
                                            <line x1="15" x2="3" y1="12" y2="12" />
                                        </svg>
                                        Check In
                                    </div>
                                </th>
                                <th
                                    class="px-8 py-4 text-left text-xs font-bold tracking-wider text-gray-700 uppercase"
                                >
                                    <div class="flex items-center">
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
                                            class="lucide lucide-log-out mr-2 text-sky-600"
                                        >
                                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                                            <polyline points="16 17 21 12 16 7" />
                                            <line x1="21" x2="9" y1="12" y2="12" />
                                        </svg>
                                        Check Out
                                    </div>
                                </th>
                                </th>
                                <th
                                    class="px-8 py-4 text-left text-xs font-bold tracking-wider text-gray-700 uppercase"
                                >
                                    <div class="flex items-center">
                                        <i data-lucide="info" class="mr-2 h-4 w-4 text-sky-600"></i>
                                        Status
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @php
                                // Group schedules by user_id and schedule_date for double shift support
                                $groupedSchedules = $schedules->groupBy(function ($schedule) {
                                    return $schedule->user_id . "_" . $schedule->schedule_date;
                                });
                            @endphp

                            @forelse ($groupedSchedules as $groupKey => $userSchedules)
                                @php
                                    // Get first schedule for user info
                                    $firstSchedule = $userSchedules->first();

                                    // Sort schedules by shift category (Pagi -> Siang -> Malam)
                                    $order = ["Pagi" => 1, "Siang" => 2, "Malam" => 3];
                                    $sortedSchedules = $userSchedules->sortBy(function ($s) use ($order) {
                                        return $order[$s->shift->category ?? ""] ?? 99;
                                    });

                                    // Get schedule IDs for this group
                                    $scheduleIds = $sortedSchedules->pluck("id");

                                    // Get attendances and permissions for all schedules in this group
                                    $attGroup = $attendances->whereIn("schedule_id", $scheduleIds);
                                    $permGroup = $permissions->whereIn("schedule_id", $scheduleIds);

                                    // Get first schedule (Pagi if exists) for status calculation
                                    $firstShift = $sortedSchedules->first();
                                    $firstAttendance = $attendances->firstWhere("schedule_id", $firstShift->id);
                                    $firstPermission = $permissions->firstWhere("schedule_id", $firstShift->id);

                                    // Location: pick any available
                                    $firstWithLocation = $attGroup->first(function ($a) {
                                        return $a && $a->location;
                                    });
                                    $location = $firstWithLocation ? $firstWithLocation->location : null;

                                    // Times: earliest check-in, latest check-out
                                    $checkInTime = optional(
                                        $attGroup
                                            ->whereNotNull("check_in_time")
                                            ->sortBy("check_in_time")
                                            ->first(),
                                    )->check_in_time;
                                    $checkOutTime = optional(
                                        $attGroup
                                            ->whereNotNull("check_out_time")
                                            ->sortByDesc("check_out_time")
                                            ->first(),
                                    )->check_out_time;
                                @endphp

                                <tr class="transition-colors duration-200 hover:bg-sky-50">
                                    <td class="px-8 py-6 whitespace-nowrap">
                                        <div class="flex flex-col space-y-1">
                                            @foreach ($sortedSchedules as $us)
                                                <div class="flex items-center">
                                                    @if ($us->shift && $us->shift->category == "Pagi")
                                                        <div
                                                            class="mr-3 flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-yellow-100 to-orange-100 shadow-sm"
                                                        >
                                                            <svg
                                                                xmlns="http://www.w3.org/2000/svg"
                                                                width="14"
                                                                height="14"
                                                                viewBox="0 0 24 24"
                                                                fill="none"
                                                                stroke="currentColor"
                                                                stroke-width="2"
                                                                stroke-linecap="round"
                                                                stroke-linejoin="round"
                                                                class="lucide lucide-sun text-yellow-600"
                                                            >
                                                                <circle cx="12" cy="12" r="4" />
                                                                <path d="M12 2v2" />
                                                                <path d="M12 20v2" />
                                                                <path d="m4.93 4.93 1.41 1.41" />
                                                                <path d="m17.66 17.66 1.41 1.41" />
                                                                <path d="M2 12h2" />
                                                                <path d="M20 12h2" />
                                                                <path d="m6.34 17.66-1.41 1.41" />
                                                                <path d="m19.07 4.93-1.41 1.41" />
                                                            </svg>
                                                        </div>
                                                    @elseif ($us->shift && $us->shift->category == "Siang")
                                                        <div
                                                            class="mr-3 flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-orange-100 to-red-100 shadow-sm"
                                                        >
                                                            <svg
                                                                xmlns="http://www.w3.org/2000/svg"
                                                                width="14"
                                                                height="14"
                                                                viewBox="0 0 24 24"
                                                                fill="none"
                                                                stroke="currentColor"
                                                                stroke-width="2"
                                                                stroke-linecap="round"
                                                                stroke-linejoin="round"
                                                                class="lucide lucide-sun text-orange-600"
                                                            >
                                                                <circle cx="12" cy="12" r="4" />
                                                                <path d="M12 2v2" />
                                                                <path d="M12 20v2" />
                                                                <path d="m4.93 4.93 1.41 1.41" />
                                                                <path d="m17.66 17.66 1.41 1.41" />
                                                                <path d="M2 12h2" />
                                                                <path d="M20 12h2" />
                                                                <path d="m6.34 17.66-1.41 1.41" />
                                                                <path d="m19.07 4.93-1.41 1.41" />
                                                            </svg>
                                                        </div>
                                                    @elseif ($us->shift && $us->shift->category == "Malam")
                                                        <div
                                                            class="mr-3 flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-indigo-100 to-purple-100 shadow-sm"
                                                        >
                                                            <svg
                                                                xmlns="http://www.w3.org/2000/svg"
                                                                width="14"
                                                                height="14"
                                                                viewBox="0 0 24 24"
                                                                fill="none"
                                                                stroke="currentColor"
                                                                stroke-width="2"
                                                                stroke-linecap="round"
                                                                stroke-linejoin="round"
                                                                class="lucide lucide-moon text-indigo-600"
                                                            >
                                                                <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9" />
                                                            </svg>
                                                        </div>
                                                    @else
                                                        <div
                                                            class="mr-3 flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-gray-100 to-gray-200 shadow-sm"
                                                        >
                                                            <svg
                                                                xmlns="http://www.w3.org/2000/svg"
                                                                width="14"
                                                                height="14"
                                                                viewBox="0 0 24 24"
                                                                fill="none"
                                                                stroke="currentColor"
                                                                stroke-width="2"
                                                                stroke-linecap="round"
                                                                stroke-linejoin="round"
                                                                class="lucide lucide-help-circle text-gray-500"
                                                            >
                                                                <circle cx="12" cy="12" r="10" />
                                                                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3" />
                                                                <path d="M12 17h.01" />
                                                            </svg>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <div class="text-sm font-bold text-gray-900">
                                                            {{ $us->shift->shift_name ?? "-" }}
                                                        </div>
                                                        <div class="text-xs text-gray-500">
                                                            @if ($us->shift)
                                                                <span
                                                                    class="@if ($us->shift->category == "Pagi")
                                                                        bg-yellow-100
                                                                        text-yellow-800
                                                                    @elseif ($us->shift->category == "Siang")
                                                                        bg-orange-100
                                                                        text-orange-800
                                                                    @elseif ($us->shift->category == "Malam")
                                                                        bg-indigo-100
                                                                        text-indigo-800
                                                                    @else
                                                                        bg-gray-100
                                                                        text-gray-800
                                                                    @endif inline-flex items-center rounded-full px-2 py-1 text-[10px] font-medium"
                                                                >
                                                                    {{ $us->shift->category }}
                                                                </span>
                                                                <span class="ml-1 text-[10px]">
                                                                    {{ \Carbon\Carbon::parse($us->shift->start_time)->format("H:i") }}
                                                                    -
                                                                    {{ \Carbon\Carbon::parse($us->shift->end_time)->format("H:i") }}
                                                                </span>
                                                            @else
                                                                    -
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 whitespace-nowrap">
                                        @if ($location)
                                            <div class="flex items-center">
                                                <div
                                                    class="mr-3 flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-sky-100 to-sky-200"
                                                >
                                                    <i data-lucide="map-pin" class="h-4 w-4 text-sky-600"></i>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-semibold text-gray-900">
                                                        {{ $location->name }}
                                                    </div>
                                                    <div class="text-xs text-gray-500">
                                                        Radius: {{ $location->radius }}m
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-sm text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-8 py-6 whitespace-nowrap">
                                        <div class="text-base font-semibold text-gray-900">
                                            {{ \Carbon\Carbon::parse($firstSchedule->schedule_date)->format("d M Y") }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ \Carbon\Carbon::parse($firstSchedule->schedule_date)->translatedFormat("l") }}
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 whitespace-nowrap">
                                        @if ($checkInTime)
                                            <div class="flex items-start">
                                                <div
                                                    class="mr-3 flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-green-100"
                                                >
                                                    <i data-lucide="log-in" class="h-4 w-4 text-green-600"></i>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-semibold text-gray-900">
                                                        {{ \Carbon\Carbon::parse($checkInTime)->format("H:i") }}
                                                    </div>
                                                    <div class="text-xs text-gray-500">
                                                        {{ \Carbon\Carbon::parse($checkInTime)->format("d M Y") }}
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-sm text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-8 py-6 whitespace-nowrap">
                                        @if ($checkOutTime)
                                            <div class="flex items-start">
                                                <div
                                                    class="mr-3 flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-red-100"
                                                >
                                                    <i data-lucide="log-out" class="h-4 w-4 text-red-600"></i>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-semibold text-gray-900">
                                                        {{ \Carbon\Carbon::parse($checkOutTime)->format("H:i") }}
                                                    </div>
                                                    <div class="text-xs text-gray-500">
                                                        {{ \Carbon\Carbon::parse($checkOutTime)->format("d M Y") }}
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-sm text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-8 py-6 whitespace-nowrap">
                                        @php
                                            // Status priority: izin > early_checkout > telat > hadir > forgot_checkout > alpha
                                            // Status based on FIRST shift (Pagi if exists) for consistency with index.blade.php
                                            $statusText = "-";
                                            if ($attGroup->where("status", "izin")->isNotEmpty()) {
                                                $statusText = "izin";
                                            } elseif ($attGroup->where("status", "early_checkout")->isNotEmpty()) {
                                                $statusText = "early_checkout";
                                            } elseif ($attGroup->where("status", "telat")->isNotEmpty()) {
                                                $statusText = "telat";
                                            } elseif ($attGroup->where("status", "hadir")->isNotEmpty()) {
                                                $statusText = "hadir";
                                            } elseif ($attGroup->where("status", "forgot_checkout")->isNotEmpty()) {
                                                $statusText = "forgot_checkout";
                                            } elseif ($attGroup->isNotEmpty()) {
                                                $statusText = optional($attGroup->first())->status ?: "-";
                                            }

                                            // Fallback: if no attendance and no permission, mark as alpha
                                            if ($statusText === "-" && $sortedSchedules->isNotEmpty() && $attGroup->isEmpty() && $permGroup->isEmpty()) {
                                                $statusText = "alpha";
                                            }

                                            // Determine if we need stacked badges
                                            $hasForgot = $attGroup->where("status", "forgot_checkout")->isNotEmpty();
                                            $hasEarly = $attGroup->where("status", "early_checkout")->isNotEmpty();
                                            $wasLate =
                                                $attGroup
                                                    ->filter(function ($a) {
                                                        return $a && $a->is_late;
                                                    })
                                                    ->isNotEmpty() || $attGroup->where("status", "telat")->isNotEmpty();
                                            $wasPresent =
                                                $attGroup
                                                    ->filter(function ($a) {
                                                        return $a && ! is_null($a->check_in_time);
                                                    })
                                                    ->isNotEmpty() || $attGroup->where("status", "hadir")->isNotEmpty();
                                            $showStacked = ($hasForgot || $hasEarly) && ($wasLate || $wasPresent);

                                            $statusColor = "bg-gray-100 text-gray-700";
                                            if ($statusText === "hadir") {
                                                $statusColor = "bg-green-100 text-green-800";
                                            }
                                            if ($statusText === "telat") {
                                                $statusColor = "bg-orange-100 text-orange-800";
                                            }
                                            if ($statusText === "izin") {
                                                $statusColor = "bg-yellow-100 text-yellow-800";
                                            }
                                            if ($statusText === "early_checkout") {
                                                $statusColor = "bg-amber-100 text-amber-800";
                                            }
                                            if ($statusText === "forgot_checkout") {
                                                $statusColor = "bg-rose-100 text-rose-800";
                                            }
                                            if ($statusText === "alpha") {
                                                $statusColor = "bg-red-100 text-red-800";
                                            }

                                            $primaryText = $wasLate ? "telat" : "hadir";
                                            $primaryColor = $wasLate ? "bg-orange-100 text-orange-800" : "bg-green-100 text-green-800";
                                            $forgotColor = "bg-rose-100 text-rose-800";
                                            $earlyColor = "bg-amber-100 text-amber-800";
                                        @endphp

                                        <div class="flex flex-col space-y-2">
                                            @if ($showStacked)
                                                <div class="flex flex-col space-y-1">
                                                    <span
                                                        class="{{ $primaryColor }} inline-flex items-center rounded-full px-3 py-1 text-sm font-medium"
                                                    >
                                                        {{ ucwords($primaryText) }}
                                                    </span>
                                                    @if ($hasForgot)
                                                        <span
                                                            class="inline-flex items-center rounded-full bg-rose-100 px-3 py-1 text-sm font-medium text-rose-800"
                                                        >
                                                            Forgot Checkout
                                                        </span>
                                                    @endif

                                                    @if ($hasEarly)
                                                        <span
                                                            class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-sm font-medium text-amber-800"
                                                        >
                                                            Early Checkout
                                                        </span>
                                                    @endif
                                                </div>
                                            @else
                                                <span
                                                    class="{{ $statusColor }} inline-flex items-center rounded-full px-3 py-1 text-sm font-medium"
                                                >
                                                    {{ ucwords(str_replace("_", " ", $statusText)) }}
                                                </span>
                                            @endif
                                            @php
                                                $latestPerm = $permGroup->sortByDesc("created_at")->first();
                                            @endphp

                                            @if ($latestPerm && $latestPerm->reason)
                                                <div
                                                    class="max-w-xs truncate rounded bg-gray-50 px-2 py-1 text-xs text-gray-600"
                                                    title="{{ $latestPerm->reason }}"
                                                >
                                                    <svg
                                                        class="mr-1 inline h-3 w-3"
                                                        fill="currentColor"
                                                        viewBox="0 0 8 8"
                                                    >
                                                        <circle cx="4" cy="4" r="3" />
                                                    </svg>
                                                    {{ $latestPerm->reason }}
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-8 py-16 text-center">
                                        <div class="flex flex-col items-center">
                                            <div
                                                class="mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-gradient-to-br from-sky-100 to-sky-200"
                                            >
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    width="40"
                                                    height="40"
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    stroke-width="2"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    class="lucide lucide-calendar text-sky-400"
                                                >
                                                    <path d="M8 2v4" />
                                                    <path d="M16 2v4" />
                                                    <rect width="18" height="18" x="3" y="4" rx="2" />
                                                    <path d="M3 10h18" />
                                                </svg>
                                            </div>
                                            <h3 class="mb-2 text-xl font-bold text-gray-900">
                                                Belum ada riwayat jadwal
                                            </h3>
                                            <p class="mb-6 max-w-sm text-gray-600">
                                                Tidak ada riwayat jadwal untuk periode yang dipilih
                                            </p>
                                            <a
                                                href="{{ route("admin.schedules.create") }}"
                                                class="inline-flex transform items-center rounded-xl bg-gradient-to-r from-sky-500 to-sky-600 px-6 py-3 font-bold text-white shadow-lg transition-all duration-200 hover:from-sky-600 hover:to-sky-700"
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
                                                    class="lucide lucide-plus mr-2"
                                                >
                                                    <path d="M12 5v14" />
                                                    <path d="M5 12h14" />
                                                </svg>
                                                Tambah Jadwal
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Standardized Manual Pagination Footer -->
            <div id="pagination-footer" class="mt-8 flex flex-col items-center justify-between gap-6 px-4 pb-8 sm:flex-row sm:px-0">
                <div class="flex items-center gap-3">
                    <span class="text-sm font-semibold text-gray-600">Tampilkan</span>
                    <div class="relative">
                        <select id="pageSize" class="appearance-none rounded-xl border-2 border-sky-100 bg-white py-2.5 pr-10 pl-4 text-sm font-bold text-sky-700 transition-all hover:border-sky-300 focus:border-sky-500 focus:ring-0">
                            <option value="5">5</option>
                            <option value="10" selected>10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                            <i data-lucide="chevron-down" class="h-4 w-4 text-sky-500"></i>
                        </div>
                    </div>
                    <span class="text-sm font-semibold text-gray-600">data</span>
                </div>

                <div id="paginationInfo" class="text-sm font-bold text-gray-700 bg-sky-50 px-6 py-2.5 rounded-2xl border border-sky-100">
                    <!-- Info will be populated by JS -->
                </div>

                <div id="paginationButtons" class="flex items-center gap-2">
                    <!-- Buttons will be populated by JS -->
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const table = document.querySelector('table');
            const tbody = table ? table.querySelector('tbody') : null;
            if (!tbody) return;

            const allRows = Array.from(tbody.querySelectorAll('tr')).filter(row => !row.hasAttribute('colspan'));
            const searchInput = document.getElementById('searchInput');
            const clearSearchBtn = document.getElementById('clear_search');
            const pageSizeSelect = document.getElementById('pageSize');
            const infoText = document.getElementById('paginationInfo');
            const buttonsContainer = document.getElementById('paginationButtons');

            let currentPage = 1;
            let pageSize = parseInt(pageSizeSelect.value);

            function render() {
                const searchTerm = searchInput.value.toLowerCase().trim();
                
                // Filter rows based on search
                const filteredRows = allRows.filter(row => {
                    const text = row.textContent.toLowerCase();
                    return text.includes(searchTerm);
                });

                const total = filteredRows.length;
                const totalPages = Math.max(1, Math.ceil(total / pageSize));

                if (currentPage > totalPages) currentPage = totalPages;

                // Hide all rows initially
                allRows.forEach(row => row.style.display = 'none');

                // Show only current page rows
                const start = (currentPage - 1) * pageSize;
                const end = start + pageSize;
                
                filteredRows.forEach((row, idx) => {
                    if (idx >= start && idx < end) {
                        row.style.display = '';
                    }
                });

                // Update info text
                const infoStart = total === 0 ? 0 : start + 1;
                const infoEnd = Math.min(start + pageSize, total);
                infoText.textContent = total === 0 
                    ? 'Tidak ada data' 
                    : `Menampilkan ${infoStart} – ${infoEnd} dari ${total} jadwal`;

                // Render pagination buttons
                buttonsContainer.innerHTML = '';
                
                const btnClass = 'inline-flex items-center justify-center min-w-[2.25rem] h-[2.25rem] px-2 rounded-xl text-sm font-bold transition-all duration-200 border-none';
                const inactiveClass = 'bg-sky-100 text-sky-700 hover:bg-sky-200';
                const activeClass = 'bg-sky-500 text-white shadow-md shadow-sky-200';
                const disabledClass = 'opacity-30 cursor-not-allowed bg-gray-100 text-gray-400';

                function addBtn(label, page, disabled, active = false) {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.innerHTML = label;
                    btn.className = `${btnClass} ${disabled ? disabledClass : (active ? activeClass : inactiveClass)}`;
                    if (!disabled && !active) {
                        btn.onclick = () => {
                            currentPage = page;
                            render();
                            window.scrollTo({ top: table.offsetTop - 100, behavior: 'smooth' });
                        };
                    }
                    buttonsContainer.appendChild(btn);
                }

                // Prev
                addBtn('‹', currentPage - 1, currentPage === 1);

                // Page numbers
                let startPage = Math.max(1, currentPage - 1);
                let endPage = Math.min(totalPages, startPage + 2);
                if (endPage - startPage < 2) startPage = Math.max(1, endPage - 2);

                for (let i = startPage; i <= endPage; i++) {
                    addBtn(i.toString(), i, false, i === currentPage);
                }

                // Next
                addBtn('›', currentPage + 1, currentPage === totalPages);

                // Show/hide clear search button
                if (clearSearchBtn) {
                    clearSearchBtn.style.display = searchTerm ? 'flex' : 'none';
                    if (searchTerm) clearSearchBtn.classList.remove('hidden');
                }
            }

            // Listeners
            searchInput.addEventListener('input', () => {
                currentPage = 1;
                render();
            });

            if (clearSearchBtn) {
                clearSearchBtn.addEventListener('click', () => {
                    searchInput.value = '';
                    currentPage = 1;
                    render();
                    searchInput.focus();
                });
            }

            pageSizeSelect.addEventListener('change', () => {
                pageSize = parseInt(pageSizeSelect.value);
                currentPage = 1;
                render();
            });

            // Initial render
            render();
            if (typeof lucide !== 'undefined') lucide.createIcons();
        });
    </script>
@endsection
