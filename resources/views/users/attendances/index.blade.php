@extends('layouts.user')

@section('title', 'Attendance')

@section('content')
    <div class="min-h-screen bg-white">
        <div class="px-3 py-6 sm:px-4 sm:py-8 md:px-6 lg:px-8">
            {{-- Header --}}
            <div class="mb-8 sm:mb-10">
                <div class="mb-3 flex flex-col gap-3 sm:flex-row sm:items-center sm:gap-4">
                    <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-sky-500">
                        <i data-lucide="calendar" class="h-5 w-5 text-white"></i>
                    </div>
                    <div class="min-w-0">
                        <h1 class="mb-1 text-2xl font-bold break-words text-gray-900 sm:text-3xl">
                            Attendance Dashboard
                        </h1>
                        <p class="text-sm text-gray-500">Manage your daily attendance and schedule</p>
                    </div>
                </div>
            </div>

            {{-- Notifications --}}
            <div class="mb-8 space-y-3">
                @if (session('success'))
                    <div class="rounded-lg border border-emerald-200 bg-white p-4">
                        <div class="flex items-start gap-3">
                            <div
                                class="mt-0.5 flex h-5 w-5 flex-shrink-0 items-center justify-center rounded-full bg-emerald-500"
                            >
                                <i data-lucide="check-circle" class="h-3 w-3 text-white"></i>
                            </div>
                            <p class="text-sm break-words text-emerald-800">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                @if (session('warning'))
                    <div class="rounded-lg border border-amber-200 bg-white p-4">
                        <div class="flex items-start gap-3">
                            <div
                                class="mt-0.5 flex h-5 w-5 flex-shrink-0 items-center justify-center rounded-full bg-amber-500"
                            >
                                <i data-lucide="alert-triangle" class="h-3 w-3 text-white"></i>
                            </div>
                            <p class="text-sm break-words text-amber-800">{{ session('warning') }}</p>
                        </div>
                    </div>
                @endif

                @if (session('error'))
                    <div class="rounded-lg border border-rose-200 bg-white p-4">
                        <div class="flex items-start gap-3">
                            <div
                                class="mt-0.5 flex h-5 w-5 flex-shrink-0 items-center justify-center rounded-full bg-rose-500"
                            >
                                <i data-lucide="x-circle" class="h-3 w-3 text-white"></i>
                            </div>
                            <p class="text-sm break-words text-rose-800">{{ session('error') }}</p>
                        </div>
                    </div>
                @endif

                @if (session('debug_distance'))
                    <div class="rounded-lg border border-blue-200 bg-white p-4">
                        <div class="flex items-start gap-3">
                            <div
                                class="mt-0.5 flex h-5 w-5 flex-shrink-0 items-center justify-center rounded-full bg-blue-500"
                            >
                                <i data-lucide="map-pin" class="h-3 w-3 text-white"></i>
                            </div>
                            <p class="text-sm break-words text-blue-800">
                                Debug: Distance from office: {{ session('debug_distance') }}
                            </p>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Main Content --}}
            @if ($schedule)
                @php
                    // Load all schedules for the active schedule date (could be yesterday for night shifts)
                    $todaySchedules = \App\Models\Schedules::with('shift')
                        ->where('user_id', Auth::id())
                        ->whereDate('schedule_date', $schedule->schedule_date)
                        ->orderBy('id')
                        ->get();

                    $shiftCount = $todaySchedules->count();

                    // Compute combined work window and total planned minutes across shifts
                    $firstStartDT = null; // earliest start
                    $lastEndDT = null; // latest end
                    $plannedMinutes = 0; // sum of durations per shift

                    foreach ($todaySchedules as $sch) {
                        if (! $sch->shift) {
                            continue;
                        }
                        $date = \Carbon\Carbon::parse($sch->schedule_date);
                        $startT = \Carbon\Carbon::parse($sch->shift->start_time);
                        $endT = \Carbon\Carbon::parse($sch->shift->end_time);
                        $startDT = $date->copy()->setTimeFrom($startT);
                        $endDT = $date->copy()->setTimeFrom($endT);
                        if ($endDT->lt($startDT)) {
                            $endDT->addDay();
                        }

                        if (! $firstStartDT || $startDT->lt($firstStartDT)) {
                            $firstStartDT = $startDT->copy();
                        }
                        if (! $lastEndDT || $endDT->gt($lastEndDT)) {
                            $lastEndDT = $endDT->copy();
                        }

                        $plannedMinutes += $startDT->diffInMinutes($endDT);
                    }

                    $plannedHoursText = $plannedMinutes ? sprintf('%02d:%02d', intdiv($plannedMinutes, 60), $plannedMinutes % 60) : null;
                @endphp

                {{-- Today's Schedule Card --}}
                <div class="mb-6 rounded-lg border border-gray-200 bg-white p-5 sm:mb-8 sm:p-6">
                    <div class="mb-6 flex flex-col gap-4">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex min-w-0 items-center gap-3">
                                <div
                                    class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-sky-500 shadow"
                                >
                                    <i data-lucide="calendar" class="h-5 w-5 text-white"></i>
                                </div>
                                <div class="min-w-0">
                                    <h2 class="text-xl font-bold break-words text-gray-900 sm:text-2xl">
                                        Today's Schedule
                                    </h2>
                                    <p class="text-xs text-gray-500 sm:text-sm">
                                        {{ \Carbon\Carbon::now()->format('l, d F Y') }}
                                    </p>
                                </div>
                            </div>
                            @if ($attendance)
                                <div
                                    class="{{ $attendance->check_out_time ? 'border-emerald-200' : 'border-amber-200' }} flex w-fit items-center gap-2 rounded-lg border bg-white px-3 py-2"
                                >
                                    <div
                                        class="{{ $attendance->check_out_time ? 'bg-emerald-500' : 'animate-pulse bg-amber-500' }} h-2 w-2 rounded-full"
                                    ></div>
                                    <span
                                        class="{{ $attendance->check_out_time ? 'text-emerald-700' : 'text-amber-700' }} text-xs font-medium"
                                    >
                                        {{ $attendance->check_out_time ? 'Completed' : 'In Progress' }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Simplified card layout with solid colors and minimal styling --}}
                    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                            <div class="flex items-start gap-3">
                                <div
                                    class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg bg-sky-500"
                                >
                                    <i data-lucide="clock" class="h-4 w-4 text-white"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="mb-1 text-xs font-semibold text-gray-600">Shift</p>

                                    @if ($shiftCount > 1)
                                        <p class="text-base font-bold break-words text-gray-900">
                                            {{ $todaySchedules[0]->shift->shift_name ?? '-' }} &
                                            {{ $todaySchedules[1]->shift->shift_name ?? '-' }}
                                        </p>
                                        <span
                                            class="mt-1 inline-block rounded bg-sky-100 px-2 py-1 text-xs font-medium text-sky-700"
                                        >
                                            {{ $shiftCount }} Shifts
                                        </span>
                                    @else
                                        <p class="text-base font-bold break-words text-gray-900">
                                            {{ $schedule->shift->shift_name }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                            <div class="flex items-start gap-3">
                                <div
                                    class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg bg-sky-500"
                                >
                                    <i data-lucide="calendar-clock" class="h-4 w-4 text-white"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="mb-1 text-xs font-semibold text-gray-600">Working Hours</p>
                                    @php
                                        $forgotGraceHours = (int) env('FORGOT_CHECKOUT_GRACE_HOURS', 6);
                                    @endphp

                                    @if ($shiftCount > 1 && $firstStartDT && $lastEndDT)
                                        <p class="text-base font-bold text-gray-900">
                                            {{ $firstStartDT->format('H:i') }} - {{ $lastEndDT->format('H:i') }}
                                        </p>
                                        <p class="mt-1 text-xs text-gray-500">Planned: {{ $plannedHoursText }}</p>
                                        @if ($attendance && $attendance->check_in_time && ! $attendance->check_out_time)
                                            <div class="mt-2 space-y-1">
                                                <p class="text-xs text-gray-600">
                                                    Checkout by:
                                                    <span id="final-end-time" class="font-medium">
                                                        {{ $lastEndDT->format('H:i') }}
                                                    </span>
                                                    ·
                                                    <span id="final-end-countdown" class="text-xs font-bold"></span>
                                                </p>
                                                @php
                                                    $deadlineDT = $lastEndDT->copy()->addHours($forgotGraceHours);
                                                @endphp

                                                <p class="text-xs text-rose-600">
                                                    Forgot checkout deadline:
                                                    <span id="forgot-deadline-time" class="font-medium">
                                                        {{ $deadlineDT->format('d M Y H:i') }}
                                                    </span>
                                                    ·
                                                    <span
                                                        id="forgot-deadline-countdown"
                                                        class="text-xs font-bold"
                                                    ></span>
                                                </p>
                                            </div>
                                            <div
                                                id="final-end-dataset"
                                                data-final-end="{{ $lastEndDT->toIso8601String() }}"
                                                class="hidden"
                                            ></div>
                                            <div
                                                id="forgot-deadline-dataset"
                                                data-forgot-deadline="{{ $deadlineDT->toIso8601String() }}"
                                                class="hidden"
                                            ></div>
                                        @endif
                                    @else
                                        @php
                                            $singleStart = \Carbon\Carbon::parse($schedule->schedule_date . ' ' . $schedule->shift->start_time);
                                            $singleEnd = \Carbon\Carbon::parse($schedule->schedule_date . ' ' . $schedule->shift->end_time);
                                            if ($singleEnd->lt($singleStart)) {
                                                $singleEnd->addDay();
                                            }
                                            $singleDeadline = $singleEnd->copy()->addHours($forgotGraceHours);
                                        @endphp

                                        <p class="text-base font-bold text-gray-900">
                                            {{ $schedule->shift->start_time }} - {{ $schedule->shift->end_time }}
                                        </p>
                                        @if ($attendance && $attendance->check_in_time && ! $attendance->check_out_time)
                                            <div class="mt-2 space-y-1">
                                                <p class="text-xs text-gray-600">
                                                    Checkout by:
                                                    <span id="final-end-time" class="font-medium">
                                                        {{ $singleEnd->format('H:i') }}
                                                    </span>
                                                    ·
                                                    <span id="final-end-countdown" class="text-xs font-bold"></span>
                                                </p>
                                                <p class="text-xs text-rose-600">
                                                    Forgot checkout deadline:
                                                    <span id="forgot-deadline-time" class="font-medium">
                                                        {{ $singleDeadline->format('d M Y H:i') }}
                                                    </span>
                                                    ·
                                                    <span
                                                        id="forgot-deadline-countdown"
                                                        class="text-xs font-bold"
                                                    ></span>
                                                </p>
                                            </div>
                                            <div
                                                id="final-end-dataset"
                                                data-final-end="{{ $singleEnd->toIso8601String() }}"
                                                class="hidden"
                                            ></div>
                                            <div
                                                id="forgot-deadline-dataset"
                                                data-forgot-deadline="{{ $singleDeadline->toIso8601String() }}"
                                                class="hidden"
                                            ></div>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Attendance Status --}}
                    @if ($attendance)
                        @php
                            $workedMinutes = null;
                            if ($attendance->check_in_time) {
                                $start = \Carbon\Carbon::parse($attendance->check_in_time);
                                $end = $attendance->check_out_time ? \Carbon\Carbon::parse($attendance->check_out_time) : now();
                                $workedMinutes = $start->diffInMinutes($end);
                            }
                            $workedText = $workedMinutes !== null ? sprintf('%02d:%02d', intdiv($workedMinutes, 60), $workedMinutes % 60) : null;
                        @endphp

                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="mb-4 text-base font-semibold text-gray-900">Attendance Status</h3>
                            {{-- Simplified status cards with minimal styling --}}
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                                <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                                    <div class="flex items-start gap-3">
                                        <div
                                            class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-emerald-500"
                                        >
                                            <i data-lucide="user-check" class="h-4 w-4 text-white"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-xs font-semibold text-gray-600">Status</p>
                                            <p class="mt-1 text-sm font-bold break-words text-gray-900">
                                                {{ ucwords(str_replace('_', ' ', $attendance->status)) }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                                    <div class="flex items-start gap-3">
                                        <div
                                            class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-blue-500"
                                        >
                                            <i data-lucide="log-in" class="h-4 w-4 text-white"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-xs font-semibold text-gray-600">Check In</p>
                                            <p class="mt-1 text-sm font-bold text-gray-900">
                                                {{ $attendance->check_in_time ? \Carbon\Carbon::parse($attendance->check_in_time)->format('H:i') : '-' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                                    <div class="flex items-start gap-3">
                                        <div
                                            class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-orange-500"
                                        >
                                            <i data-lucide="log-out" class="h-4 w-4 text-white"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-xs font-semibold text-gray-600">Check Out</p>
                                            <p class="mt-1 text-sm font-bold text-gray-900">
                                                {{ $attendance->check_out_time ? \Carbon\Carbon::parse($attendance->check_out_time)->format('H:i') : '-' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Worked vs Planned --}}
                            <div class="mt-4">
                                <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                                    <div class="mb-3 flex flex-col justify-between gap-2 sm:flex-row sm:items-center">
                                        <div class="text-sm font-semibold text-gray-900">Work Hours</div>
                                        <div class="text-sm font-bold text-gray-800">
                                            @if ($workedText)
                                                <span>{{ $workedText }}</span>
                                            @else
                                                -
                                            @endif
                                            @if ($plannedHoursText)
                                                <span class="text-xs font-normal text-gray-500">
                                                    / Planned {{ $plannedHoursText }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    @if ($plannedMinutes && $workedMinutes !== null)
                                        @php
                                            $progress = min(100, (int) round(($workedMinutes / max(1, $plannedMinutes)) * 100));
                                        @endphp

                                        <div class="mt-3">
                                            <div class="mb-2 flex justify-between text-xs text-gray-600">
                                                <span>Progress</span>
                                                <span>{{ $progress }}%</span>
                                            </div>
                                            <div class="h-2 w-full overflow-hidden rounded-full bg-gray-200">
                                                <div
                                                    class="h-2 bg-sky-500 transition-all duration-500"
                                                    style="width: {{ $progress }}%"
                                                ></div>
                                            </div>
                                        </div>
                                    @endif

                                    @if ($shiftCount > 1 && $lastEndDT)
                                        <div class="mt-3 text-xs text-gray-500">
                                            Normal checkout time: {{ $lastEndDT->format('H:i') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if ($attendance && $attendance->status === 'forgot_checkout')
                            <div class="mt-4">
                                <div class="rounded-lg border border-rose-200 bg-white p-4">
                                    <div class="flex items-start gap-3">
                                        <div
                                            class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-rose-500"
                                        >
                                            <i data-lucide="alert-circle" class="h-4 w-4 text-white"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <h4 class="mb-1 text-sm font-semibold text-rose-900">Forgot Checkout</h4>
                                            @php
                                                $forgotGraceHoursBanner = (int) env('FORGOT_CHECKOUT_GRACE_HOURS', 6);
                                            @endphp

                                            <p class="text-xs text-rose-800">
                                                The system automatically closed your attendance because you didn't check
                                                out on time. The automatic closure limit is: end of last shift +
                                                {{ $forgotGraceHoursBanner }} hours.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>

                {{-- Status Permission Hari Ini --}}
                @if ($todayPermission)
                    @if ($todayPermission->status === 'pending')
                        <div class="mb-6 rounded-lg border border-amber-200 bg-white p-4 sm:mb-8 sm:p-5">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-start">
                                <div
                                    class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg bg-amber-500"
                                >
                                    <i data-lucide="clock" class="h-4 w-4 text-white"></i>
                                </div>
                                <div class="min-w-0">
                                    <h3 class="mb-1 text-base font-bold text-amber-900">
                                        {{ $todayPermission->type === 'cuti' ? 'Leave Request' : 'Permission Request' }}
                                        Pending Approval
                                    </h3>
                                    <p class="mb-1 text-xs text-amber-800">
                                        Status:
                                        <span class="font-semibold">Waiting for Approval</span>
                                    </p>
                                    <p class="text-xs break-words text-amber-700">
                                        Reason: {{ $todayPermission->reason }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @elseif ($todayPermission->status === 'approved')
                        @if ($todayPermission->type === 'cuti')
                            <div class="mb-6 rounded-lg border border-purple-200 bg-white p-4 sm:mb-8 sm:p-5">
                                <div class="flex flex-col gap-4 sm:flex-row sm:items-start">
                                    <div
                                        class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg bg-purple-500"
                                    >
                                        <i data-lucide="calendar-x" class="h-4 w-4 text-white"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <h3 class="mb-1 text-base font-bold text-purple-900">Leave Approved</h3>
                                        <p class="mb-1 text-xs text-purple-800">
                                            You are currently on
                                            <span class="font-semibold">Leave</span>
                                        </p>
                                        <p class="text-xs break-words text-purple-700">
                                            Reason: {{ $todayPermission->reason }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="mb-6 rounded-lg border border-emerald-200 bg-white p-4 sm:mb-8 sm:p-5">
                                <div class="flex flex-col gap-4 sm:flex-row sm:items-start">
                                    <div
                                        class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg bg-emerald-500"
                                    >
                                        <i data-lucide="check-circle" class="h-4 w-4 text-white"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <h3 class="mb-1 text-base font-bold text-emerald-900">
                                            {{ ucfirst($todayPermission->type) }} Approved
                                        </h3>
                                        <p class="mb-1 text-xs text-emerald-800">
                                            You are currently on
                                            <span class="font-semibold">{{ ucfirst($todayPermission->type) }}</span>
                                        </p>
                                        <p class="text-xs break-words text-emerald-700">
                                            Reason: {{ $todayPermission->reason }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif
                @endif

                {{-- Check for rejected permissions to show info --}}
                @php
                    $rejectedPermission = \App\Models\Permissions::where('user_id', Auth::id())
                        ->whereHas('schedule', function ($q) use ($schedule) {
                            $q->whereDate('schedule_date', $schedule->schedule_date);
                        })
                        ->where('status', 'rejected')
                        ->first();

                    $earlyCheckoutPermission = \App\Models\Permissions::where('user_id', Auth::id())
                        ->whereHas('schedule', function ($q) use ($schedule) {
                            $q->whereDate('schedule_date', $schedule->schedule_date);
                        })
                        ->where('status', 'pending')
                        ->where('type', 'izin')
                        ->where('reason', 'like', '[EARLY_CHECKOUT]%')
                        ->first();
                @endphp

                @if ($rejectedPermission)
                    @if ($earlyCheckoutPermission)
                        <div class="mb-6 rounded-lg border border-blue-200 bg-white p-4 sm:mb-8 sm:p-5">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                <div class="flex min-w-0 items-start gap-3">
                                    <div
                                        class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg bg-blue-500"
                                    >
                                        <i data-lucide="info" class="h-4 w-4 text-white"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <h3 class="mb-1 text-base font-bold text-blue-900">Permission Rejected</h3>
                                        <p class="mb-1 text-xs text-blue-800">
                                            Your permission has been rejected. You requested early checkout.
                                        </p>
                                        <p class="text-xs break-words text-blue-700">
                                            Permission reason (rejected): {{ $rejectedPermission->reason }}
                                        </p>
                                    </div>
                                </div>
                                <button
                                    type="button"
                                    onclick="document.getElementById('ec-review-modal').classList.remove('hidden')"
                                    class="inline-flex items-center gap-2 rounded-lg bg-blue-500 px-4 py-2 text-xs font-medium whitespace-nowrap text-white transition-colors hover:bg-blue-600 sm:text-sm"
                                >
                                    <i data-lucide="eye" class="h-4 w-4"></i>
                                    <span>Review</span>
                                </button>
                            </div>
                        </div>
                    @else
                        <div class="mb-6 rounded-lg border border-blue-200 bg-white p-4 sm:mb-8 sm:p-5">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                <div class="flex min-w-0 items-start gap-3">
                                    <div
                                        class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg bg-blue-500"
                                    >
                                        <i data-lucide="info" class="h-4 w-4 text-white"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <h3 class="mb-1 text-base font-bold text-blue-900">Permission Rejected</h3>
                                        <p class="text-xs break-words text-blue-700">
                                            Permission reason: {{ $rejectedPermission->reason }}
                                        </p>
                                    </div>
                                </div>
                                @if ($attendance && $attendance->check_in_time && ! $attendance->check_out_time)
                                    <button
                                        type="button"
                                        onclick="document.getElementById('early-checkout-modal').classList.remove('hidden')"
                                        class="inline-flex items-center gap-2 rounded-lg bg-amber-500 px-4 py-2 text-xs font-medium whitespace-nowrap text-white transition-colors hover:bg-amber-600 sm:text-sm"
                                    >
                                        <i data-lucide="clock" class="h-4 w-4"></i>
                                        <span>Request Early Checkout</span>
                                    </button>
                                @else
                                    <span class="text-xs text-blue-700">
                                        Please check-in to request early checkout.
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endif
                @endif

                {{-- Action Buttons --}}
                <div class="rounded-2xl border border-gray-200 bg-white p-4 transition-all duration-300 sm:p-6">
                    <h3 class="mb-4 text-lg font-bold text-gray-900 sm:mb-6 sm:text-xl">Quick Actions</h3>

                    @if (! $todayPermission)
                        {{-- Improved responsive grid for action buttons --}}
                        <div class="grid grid-cols-2 gap-3 sm:grid-cols-2 sm:gap-5 lg:grid-cols-4">
                            {{-- Check In Button --}}
                            @if (! $attendance || ! $attendance->check_in_time)
                                <form id="checkin-form" action="{{ route('user.attendances.checkin') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="schedule_id" value="{{ $schedule?->id }}" />
                                    <input type="hidden" name="latitude" id="latitude" />
                                    <input type="hidden" name="longitude" id="longitude" />
                                    <button
                                        type="submit"
                                        class="flex h-full w-full transform flex-col items-center justify-center gap-1 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 px-3 py-3 font-semibold text-white shadow-md transition-all duration-300 hover:from-emerald-600 hover:to-emerald-700 hover:shadow-lg sm:gap-2 sm:px-4 sm:py-4"
                                    >
                                        <div
                                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/20 sm:h-12 sm:w-12"
                                        >
                                            <i data-lucide="log-in" class="h-5 w-5 text-white sm:h-6 sm:w-6"></i>
                                        </div>
                                        <span class="text-xs sm:text-lg">Check In</span>
                                    </button>
                                </form>
                            @endif

                            {{-- Check Out Button --}}
                            @if ($attendance && $attendance->check_in_time && ! $attendance->check_out_time)
                                @php
                                    $graceUIHours = (int) env('FORGOT_CHECKOUT_GRACE_HOURS', 6);
                                    $sameDay = \App\Models\Schedules::with('shift')
                                        ->where('user_id', auth()->id())
                                        ->whereDate('schedule_date', optional($schedule)->schedule_date)
                                        ->get();
                                    $finalEndUI = null;
                                    $firstStartUI = null;
                                    foreach ($sameDay as $schUI) {
                                        if (! $schUI->shift) {
                                            continue;
                                        }
                                        $dUI = \Carbon\Carbon::parse($schUI->schedule_date);
                                        $stUI = \Carbon\Carbon::parse($schUI->shift->start_time);
                                        $etUI = \Carbon\Carbon::parse($schUI->shift->end_time);
                                        $startDTUI = $dUI->copy()->setTimeFrom($stUI);
                                        $endDTUI = $dUI->copy()->setTimeFrom($etUI);
                                        if ($endDTUI->lt($startDTUI)) {
                                            $endDTUI->addDay();
                                        }
                                        if (! $firstStartUI || $startDTUI->lt($firstStartUI)) {
                                            $firstStartUI = $startDTUI->copy();
                                        }
                                        if (! $finalEndUI || $endDTUI->gt($finalEndUI)) {
                                            $finalEndUI = $endDTUI->copy();
                                        }
                                    }
                                    $pastDeadlineUI = $finalEndUI ? now()->gte($finalEndUI->copy()->addHours($graceUIHours)) : false;
                                @endphp

                                @if ($pastDeadlineUI)
                                    <button
                                        type="button"
                                        disabled
                                        class="flex h-full w-full cursor-not-allowed flex-col items-center justify-center gap-1 rounded-xl bg-gray-200 px-3 py-3 font-semibold text-gray-500 sm:gap-2 sm:px-4 sm:py-4"
                                    >
                                        <div
                                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-gray-300 sm:h-12 sm:w-12"
                                        >
                                            <i data-lucide="lock" class="h-5 w-5 text-gray-500 sm:h-6 sm:w-6"></i>
                                        </div>
                                        <span class="text-xs sm:text-lg">Check Out Closed</span>
                                        <span class="text-xs">(Past Deadline)</span>
                                    </button>
                                @else
                                    <form
                                        id="checkout-form"
                                        action="{{ route('user.attendances.checkout') }}"
                                        method="POST"
                                    >
                                        @csrf
                                        <input type="hidden" name="schedule_id" value="{{ $schedule?->id }}" />
                                        <input type="hidden" name="latitude" id="checkout-latitude" />
                                        <input type="hidden" name="longitude" id="checkout-longitude" />
                                        <button
                                            type="submit"
                                            class="flex h-full w-full transform flex-col items-center justify-center gap-1 rounded-xl bg-gradient-to-br from-sky-500 to-sky-600 px-3 py-3 font-semibold text-white shadow-md transition-all duration-300 hover:from-sky-600 hover:to-sky-700 hover:shadow-lg sm:gap-2 sm:px-4 sm:py-4"
                                        >
                                            <div
                                                class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/20 sm:h-12 sm:w-12"
                                            >
                                                <i data-lucide="log-out" class="h-5 w-5 text-white sm:h-6 sm:w-6"></i>
                                            </div>
                                            <span class="text-xs sm:text-lg">Check Out</span>
                                        </button>
                                    </form>
                                @endif
                            @endif

                            {{-- Request Early Checkout Button --}}
                            @if ($attendance && $attendance->check_in_time && ! $attendance->check_out_time)
                                <button
                                    type="button"
                                    onclick="document.getElementById('early-checkout-modal').classList.remove('hidden')"
                                    class="flex h-full w-full transform flex-col items-center justify-center gap-1 rounded-xl bg-gradient-to-br from-amber-500 to-amber-600 px-3 py-3 font-semibold text-white shadow-md transition-all duration-300 hover:from-amber-600 hover:to-amber-700 hover:shadow-lg sm:gap-2 sm:px-4 sm:py-4"
                                >
                                    <div
                                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/20 sm:h-12 sm:w-12"
                                    >
                                        <i data-lucide="clock" class="h-5 w-5 text-white sm:h-6 sm:w-6"></i>
                                    </div>
                                    <span class="text-xs sm:text-lg">Request Early Checkout</span>
                                </button>
                            @endif

                            {{-- Request Permission Button --}}
                            @if (! $attendance || ! $attendance->check_in_time)
                                <button
                                    type="button"
                                    onclick="document.getElementById('izin-modal').classList.remove('hidden')"
                                    class="flex h-full w-full transform flex-col items-center justify-center gap-1 rounded-xl bg-gradient-to-br from-yellow-500 to-yellow-600 px-3 py-3 font-semibold text-white shadow-md transition-all duration-300 hover:from-yellow-600 hover:to-yellow-700 hover:shadow-lg sm:gap-2 sm:px-4 sm:py-4"
                                >
                                    <div
                                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/20 sm:h-12 sm:w-12"
                                    >
                                        <i data-lucide="file-text" class="h-5 w-5 text-white sm:h-6 sm:w-6"></i>
                                    </div>
                                    <span class="text-xs sm:text-lg">Request Permission</span>
                                </button>
                            @endif

                            {{-- Request Leave Button --}}
                            <button
                                type="button"
                                onclick="document.getElementById('cuti-modal').classList.remove('hidden'); loadUserSchedules()"
                                class="flex h-full w-full transform flex-col items-center justify-center gap-1 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 px-3 py-3 font-semibold text-white shadow-md transition-all duration-300 hover:from-purple-600 hover:to-purple-700 hover:shadow-lg sm:gap-2 sm:px-4 sm:py-4"
                            >
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/20 sm:h-12 sm:w-12"
                                >
                                    <i data-lucide="calendar-x" class="h-5 w-5 text-white sm:h-6 sm:w-6"></i>
                                </div>
                                <span class="text-xs sm:text-lg">Request Leave</span>
                            </button>

                            {{-- View History Button --}}
                            <a
                                href="{{ route('user.attendances.history') }}"
                                class="flex h-full w-full transform flex-col items-center justify-center gap-1 rounded-xl bg-gradient-to-br from-gray-500 to-gray-600 px-3 py-3 font-semibold text-white shadow-md transition-all duration-300 hover:from-gray-600 hover:to-gray-700 hover:shadow-lg sm:gap-2 sm:px-4 sm:py-4"
                            >
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/20 sm:h-12 sm:w-12"
                                >
                                    <i data-lucide="history" class="h-5 w-5 text-white sm:h-6 sm:w-6"></i>
                                </div>
                                <span class="text-xs sm:text-lg">View History</span>
                            </a>
                        </div>
                    @else
                        {{-- Pesan saat aksi dinonaktifkan --}}
                        <div
                            class="rounded-2xl border border-gray-200 bg-gradient-to-br from-gray-50 to-gray-100 p-6 text-center sm:p-8"
                        >
                            @if ($todayPermission->status === 'pending')
                                <div
                                    class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-amber-300 shadow-md sm:mb-5 sm:h-20 sm:w-20"
                                >
                                    <i data-lucide="clock" class="h-7 w-7 text-amber-700 sm:h-8 sm:w-8"></i>
                                </div>
                                <h4 class="mb-2 text-lg font-bold text-gray-900 sm:mb-3 sm:text-xl">
                                    Waiting for Approval
                                </h4>
                                <p class="mx-auto mb-4 max-w-md text-xs text-gray-600 sm:mb-6 sm:text-sm">
                                    Check-in and check-out are disabled because your
                                    {{ $todayPermission->type === 'cuti' ? 'leave request' : 'permission request' }} is
                                    pending approval.
                                </p>
                            @elseif ($todayPermission->status === 'approved')
                                @if ($todayPermission->type === 'cuti')
                                    <div
                                        class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-purple-300 shadow-md sm:mb-5 sm:h-20 sm:w-20"
                                    >
                                        <i data-lucide="calendar-x" class="h-7 w-7 text-purple-700 sm:h-8 sm:w-8"></i>
                                    </div>
                                    <h4 class="mb-2 text-lg font-bold text-gray-900 sm:mb-3 sm:text-xl">On Leave</h4>
                                    <p class="mx-auto mb-4 max-w-md text-xs text-gray-600 sm:mb-6 sm:text-sm">
                                        You are currently on leave. Check-in and check-out are not required.
                                    </p>
                                @else
                                    <div
                                        class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-emerald-300 shadow-md sm:mb-5 sm:h-20 sm:w-20"
                                    >
                                        <i
                                            data-lucide="check-circle"
                                            class="h-7 w-7 text-emerald-700 sm:h-8 sm:w-8"
                                        ></i>
                                    </div>
                                    <h4 class="mb-2 text-lg font-bold text-gray-900 sm:mb-3 sm:text-xl">
                                        On {{ ucfirst($todayPermission->type) }}
                                    </h4>
                                    <p class="mx-auto mb-4 max-w-md text-xs text-gray-600 sm:mb-6 sm:text-sm">
                                        You are currently on {{ $todayPermission->type }}. Check-in and check-out are
                                        not required.
                                    </p>
                                @endif
                            @endif

                            <div class="mt-4 sm:mt-6">
                                <a
                                    href="{{ route('user.attendances.history') }}"
                                    class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-sky-500 to-sky-600 px-4 py-2.5 font-semibold text-white shadow-md transition-all duration-300 hover:from-sky-600 hover:to-sky-700 hover:shadow-lg sm:gap-3 sm:px-6 sm:py-3"
                                >
                                    <i data-lucide="history" class="h-4 w-4 sm:h-5 sm:w-5"></i>
                                    <span class="text-sm sm:text-base">View History</span>
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            @else
                {{-- No Schedule State --}}
                <div class="rounded-2xl border border-gray-200 bg-white p-6 text-center shadow-lg sm:p-12">
                    <div
                        class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-gradient-to-br from-sky-100 to-sky-200 shadow-lg sm:mb-8 sm:h-24 sm:w-24"
                    >
                        <i data-lucide="calendar" class="h-8 w-8 text-sky-600 sm:h-10 sm:w-10"></i>
                    </div>
                    <h3 class="mb-3 text-xl font-bold text-gray-900 sm:mb-4 sm:text-2xl">No Schedule Today</h3>
                    <p class="mx-auto mb-6 max-w-md text-xs text-gray-600 sm:mb-8 sm:text-base">
                        You don't have any scheduled shifts for today. Please contact your administrator for schedule
                        information.
                    </p>

                    <a
                        href="{{ route('user.attendances.history') }}"
                        class="inline-flex transform items-center gap-2 rounded-xl bg-gradient-to-r from-sky-500 to-sky-600 px-4 py-2.5 font-semibold text-white shadow-md transition-all duration-300 hover:from-sky-600 hover:to-sky-700 hover:shadow-lg sm:gap-3 sm:px-8 sm:py-3"
                    >
                        <i data-lucide="history" class="h-4 w-4 sm:h-5 sm:w-5"></i>
                        <span class="text-sm sm:text-base">View History</span>
                    </a>
                </div>
            @endif
        </div>
    </div>

    {{-- Modal: Early Checkout Request --}}
    @if ($schedule)
        <div
            id="early-checkout-modal"
            class="fixed inset-0 z-50 flex hidden items-center justify-center bg-black/50 p-3 sm:p-4"
        >
            <div
                class="relative max-h-[90vh] w-full max-w-lg overflow-y-auto rounded-lg border border-gray-200 bg-white"
            >
                <div
                    class="sticky top-0 z-10 flex items-center justify-between border-b border-gray-200 bg-white p-4 sm:p-5"
                >
                    <div class="flex min-w-0 items-center gap-3">
                        <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-amber-500">
                            <i data-lucide="clock" class="h-4 w-4 text-white"></i>
                        </div>
                        <div class="min-w-0">
                            <h2 class="text-base font-bold break-words text-gray-900">Early Checkout Request</h2>
                            <p class="text-xs text-gray-500">Explain why you need to checkout early</p>
                        </div>
                    </div>
                    <button
                        type="button"
                        onclick="document.getElementById('early-checkout-modal').classList.add('hidden')"
                        class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg text-gray-500 transition-colors hover:bg-gray-100 hover:text-gray-700"
                    >
                        <i data-lucide="x" class="h-4 w-4"></i>
                    </button>
                </div>
                <form
                    method="POST"
                    action="{{ route('user.attendances.request-early-checkout') }}"
                    class="space-y-4 p-4 sm:p-5"
                >
                    @csrf
                    <input type="hidden" name="schedule_id" value="{{ $schedule->id }}" />
                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-3">
                        <div class="text-xs break-words text-gray-700">
                            <strong>Shift:</strong>
                            {{ $schedule->shift->shift_name ?? '-' }} • {{ $schedule->shift->start_time ?? '' }} -
                            {{ $schedule->shift->end_time ?? '' }}
                        </div>
                        <div class="mt-1 text-xs text-gray-500">
                            Date: {{ \Carbon\Carbon::parse($schedule->schedule_date)->format('d M Y') }}
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-xs font-semibold text-gray-900">
                            Reason
                            <span class="text-red-500">*</span>
                        </label>
                        <textarea
                            name="reason"
                            class="w-full resize-none rounded-lg border border-gray-300 px-3 py-2 text-xs placeholder-gray-400 transition-colors focus:border-amber-500 focus:ring-2 focus:ring-amber-500"
                            rows="4"
                            placeholder="Explain your reason..."
                            required
                        ></textarea>
                        <p class="text-xs text-gray-500">Minimum 5 characters</p>
                    </div>
                    <div class="flex justify-end gap-2 border-t border-gray-200 pt-3">
                        <button
                            type="button"
                            onclick="document.getElementById('early-checkout-modal').classList.add('hidden')"
                            class="rounded-lg bg-gray-100 px-4 py-2 text-xs font-medium text-gray-700 transition-colors hover:bg-gray-200"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="rounded-lg bg-amber-500 px-4 py-2 text-xs font-medium text-white transition-colors hover:bg-amber-600"
                        >
                            Submit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function() {
          @if(session('warning') && strpos(session('warning'), 'checkout sebelum') !== false)
            document.getElementById('early-checkout-modal')?.classList.remove('hidden');
          @endif

          function pad(n){ return n < 10 ? '0'+n : n; }
          function formatDuration(ms) {
            const sign = ms < 0 ? '-' : '';
            ms = Math.abs(ms);
            const totalSec = Math.floor(ms / 1000);
            const days = Math.floor(totalSec / 86400);
            const hrs = Math.floor((totalSec % 86400) / 3600);
            const mins = Math.floor((totalSec % 3600) / 60);
            const secs = totalSec % 60;
            if (days > 0) return `${sign}${days}d ${pad(hrs)}h ${pad(mins)}m ${pad(secs)}s`;
            if (hrs > 0) return `${sign}${hrs}h ${pad(mins)}m ${pad(secs)}s`;
            return `${sign}${pad(mins)}m ${pad(secs)}s`;
          }

          const finalEndDs = document.getElementById('final-end-dataset');
          const forgotDs   = document.getElementById('forgot-deadline-dataset');
          const finalLbl   = document.getElementById('final-end-countdown');
          const forgotLbl  = document.getElementById('forgot-deadline-countdown');

          if (finalEndDs && finalLbl) {
            const target = new Date(finalEndDs.getAttribute('data-final-end'));
            const tick = () => {
              const diff = target - new Date();
              finalLbl.textContent = diff >= 0 ? `${formatDuration(diff)} left` : `${formatDuration(diff)} overdue`;
            };
            tick();
            setInterval(tick, 1000);
          }

          if (forgotDs && forgotLbl) {
            const target = new Date(forgotDs.getAttribute('data-forgot-deadline'));
            const tick2 = () => {
              const diff = target - new Date();
              forgotLbl.textContent = diff >= 0 ? `${formatDuration(diff)} left` : `${formatDuration(diff)} overdue`;
            };
            tick2();
            setInterval(tick2, 1000);
          }
        });
    </script>

    @if (isset($earlyCheckoutPermission) && $earlyCheckoutPermission)
        {{-- Modal: Review Early Checkout --}}
        <div
            id="ec-review-modal"
            class="fixed inset-0 z-50 flex hidden items-center justify-center bg-black/50 p-3 sm:p-4"
        >
            <div
                class="relative max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-lg border border-gray-200 bg-white"
            >
                <div
                    class="sticky top-0 z-10 flex items-center justify-between border-b border-gray-200 bg-white p-4 sm:p-5"
                >
                    <div class="flex min-w-0 items-center gap-3">
                        <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-blue-500">
                            <i data-lucide="clipboard-list" class="h-4 w-4 text-white"></i>
                        </div>
                        <div class="min-w-0">
                            <h2 class="text-base font-bold break-words text-gray-900">Review Early Checkout</h2>
                            <p class="text-xs text-gray-500">
                                View rejected permission and early checkout request details
                            </p>
                        </div>
                    </div>
                    <button
                        type="button"
                        onclick="document.getElementById('ec-review-modal').classList.add('hidden')"
                        class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg text-gray-500 transition-colors hover:bg-gray-100 hover:text-gray-700"
                    >
                        <i data-lucide="x" class="h-4 w-4"></i>
                    </button>
                </div>
                <div class="space-y-4 p-4 sm:p-5">
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div class="rounded-lg border border-gray-200 bg-gray-50 p-3">
                            <div class="mb-2 text-xs font-semibold text-gray-900">Rejected Permission</div>
                            <div class="text-xs break-words text-gray-700">
                                Reason: {{ $rejectedPermission->reason ?? '-' }}
                            </div>
                            <div class="mt-2 text-xs text-gray-500">Status: Rejected</div>
                        </div>
                        <div class="rounded-lg border border-amber-200 bg-amber-50 p-3">
                            <div class="mb-2 text-xs font-semibold text-amber-900">Early Checkout Request</div>
                            @php
                                $ecReason = preg_replace('/^\[EARLY_CHECKOUT\]\s*/', '', $earlyCheckoutPermission->reason ?? '');
                            @endphp

                            <div class="text-xs break-words text-amber-800">Reason: {{ $ecReason ?: '-' }}</div>
                            <div class="mt-2 text-xs text-amber-700">
                                Requested: {{ optional($earlyCheckoutPermission->created_at)->format('d M Y H:i') }}
                            </div>
                            <div class="text-xs text-amber-700">
                                Status: {{ ucfirst($earlyCheckoutPermission->status) }}
                            </div>
                        </div>
                    </div>

                    <div
                        class="flex flex-col justify-between gap-3 border-t border-gray-200 pt-3 sm:flex-row sm:items-center"
                    >
                        <div class="text-xs break-words text-gray-500">
                            Schedule: {{ optional($schedule?->shift)->shift_name }} •
                            {{ $schedule->shift->start_time ?? '' }} - {{ $schedule->shift->end_time ?? '' }}
                        </div>
                        <div class="flex items-center gap-2">
                            @if (auth()->user() && method_exists(auth()->user(), 'role') ? auth()->user()->role === 'Admin' : (auth()->user()->role ?? '') === 'Admin')
                                <form
                                    method="POST"
                                    action="{{ route('admin.attendances.permission.reject', ['permission' => $earlyCheckoutPermission->id]) }}"
                                    class="inline"
                                >
                                    @csrf
                                    <button
                                        type="submit"
                                        class="rounded-lg bg-red-500 px-3 py-2 text-xs font-medium text-white transition-colors hover:bg-red-600"
                                    >
                                        Reject
                                    </button>
                                </form>
                                <form
                                    method="POST"
                                    action="{{ route('admin.attendances.permission.approve', ['permission' => $earlyCheckoutPermission->id]) }}"
                                    class="inline"
                                >
                                    @csrf
                                    <button
                                        type="submit"
                                        class="rounded-lg bg-emerald-500 px-3 py-2 text-xs font-medium text-white transition-colors hover:bg-emerald-600"
                                    >
                                        Approve
                                    </button>
                                </form>
                            @else
                                <div class="text-xs text-gray-500">Waiting for Admin action</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Form Request Permission --}}
    <div id="izin-modal" class="fixed inset-0 z-50 flex hidden items-center justify-center bg-black/50 p-3 sm:p-4">
        <div class="relative max-h-[90vh] w-full max-w-lg overflow-y-auto rounded-lg border border-gray-200 bg-white">
            <div
                class="sticky top-0 z-10 flex items-center justify-between border-b border-gray-200 bg-white p-4 sm:p-5"
            >
                <div class="flex min-w-0 items-center gap-3">
                    <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-sky-500">
                        <i data-lucide="file-text" class="h-4 w-4 text-white"></i>
                    </div>
                    <div class="min-w-0">
                        <h2 class="text-base font-bold break-words text-gray-900">Permission Request</h2>
                        <p class="text-xs text-gray-500">Submit your permission request</p>
                    </div>
                </div>
                <button
                    type="button"
                    onclick="document.getElementById('izin-modal').classList.add('hidden')"
                    class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg text-gray-500 transition-colors hover:bg-gray-100 hover:text-gray-700"
                >
                    <i data-lucide="x" class="h-4 w-4"></i>
                </button>
            </div>

            <form action="{{ route('user.permissions.store') }}" method="POST" class="space-y-4 p-4 sm:p-5">
                @csrf
                <input type="hidden" name="schedule_id" value="{{ $schedule?->id }}" />
                <input type="hidden" name="type" value="izin" />

                @if ($schedule)
                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-3">
                        <div class="flex items-start gap-3">
                            <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-sky-500">
                                <i data-lucide="clock" class="h-4 w-4 text-white"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-semibold break-words text-gray-900">
                                    {{ \Carbon\Carbon::parse($schedule->schedule_date)->format('l, d F Y') }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    {{ $schedule->shift->shift_name ?? 'No Shift' }} •
                                    {{ $schedule->shift->start_time ?? '' }} - {{ $schedule->shift->end_time ?? '' }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="space-y-2">
                    <label class="block text-xs font-semibold text-gray-900">
                        Permission Reason
                        <span class="text-red-500">*</span>
                    </label>
                    <textarea
                        name="reason"
                        class="w-full resize-none rounded-lg border border-gray-300 px-3 py-2 text-xs placeholder-gray-400 transition-colors focus:border-amber-500 focus:ring-2 focus:ring-amber-500"
                        rows="4"
                        placeholder="Clearly explain your permission reason..."
                        required
                    ></textarea>
                    <p class="text-xs text-gray-500">Minimum 10 characters</p>
                </div>

                <div class="flex justify-end gap-2 border-t border-gray-200 pt-3">
                    <button
                        type="button"
                        onclick="document.getElementById('izin-modal').classList.add('hidden')"
                        class="rounded-lg bg-gray-100 px-4 py-2 text-xs font-medium text-gray-700 transition-colors hover:bg-gray-200"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        class="rounded-lg bg-amber-500 px-4 py-2 text-xs font-medium text-white transition-colors hover:bg-amber-600"
                    >
                        <span class="flex items-center gap-2">
                            <i data-lucide="send" class="h-3 w-3"></i>
                            <span>Submit</span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Form Request Leave (Cuti) --}}
    <div id="cuti-modal" class="fixed inset-0 z-50 flex hidden items-center justify-center bg-black/50 p-3 sm:p-4">
        <div class="relative max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-lg border border-gray-200 bg-white">
            <div
                class="sticky top-0 z-10 flex items-center justify-between border-b border-gray-200 bg-white p-4 sm:p-5"
            >
                <div class="flex min-w-0 items-center gap-3">
                    <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-purple-500">
                        <i data-lucide="calendar-x" class="h-4 w-4 text-white"></i>
                    </div>
                    <div class="min-w-0">
                        <h2 class="text-base font-bold break-words text-gray-900">Leave Request</h2>
                        <p class="text-xs text-gray-500">Select schedules for your leave</p>
                    </div>
                </div>
                <button
                    type="button"
                    onclick="document.getElementById('cuti-modal').classList.add('hidden')"
                    class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg text-gray-500 transition-colors hover:bg-gray-100 hover:text-gray-700"
                >
                    <i data-lucide="x" class="h-4 w-4"></i>
                </button>
            </div>

            <form action="{{ route('user.permissions.store-leave') }}" method="POST" class="space-y-4 p-4 sm:p-5">
                @csrf
                <input type="hidden" name="type" value="cuti" />

                <div class="space-y-3">
                    <div class="flex flex-col justify-between gap-2 sm:flex-row sm:items-center">
                        <label class="block text-xs font-semibold text-gray-900">
                            Select Schedules for Leave
                            <span class="text-red-500">*</span>
                        </label>
                        <div class="flex gap-2">
                            <button
                                type="button"
                                onclick="selectAllSchedules()"
                                class="rounded-lg bg-purple-100 px-3 py-1 text-xs font-medium text-purple-700 transition-colors hover:bg-purple-200"
                            >
                                Select All
                            </button>
                            <button
                                type="button"
                                onclick="clearAllSchedules()"
                                class="rounded-lg bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700 transition-colors hover:bg-gray-200"
                            >
                                Clear All
                            </button>
                        </div>
                    </div>

                    <div id="schedules-loading" class="py-6 text-center">
                        <div class="inline-flex items-center gap-2 text-gray-500">
                            <div class="h-4 w-4 animate-spin rounded-full border-b-2 border-purple-600"></div>
                            <span class="text-xs">Loading schedules...</span>
                        </div>
                    </div>

                    <div
                        id="schedules-container"
                        class="hidden max-h-60 space-y-2 overflow-y-auto rounded-lg border border-gray-200 bg-gray-50 p-3"
                    >
                        Schedules will be loaded here
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="block text-xs font-semibold text-gray-900">
                        Leave Reason
                        <span class="text-red-500">*</span>
                    </label>
                    <textarea
                        name="reason"
                        class="w-full resize-none rounded-lg border border-gray-300 px-3 py-2 text-xs placeholder-gray-400 transition-colors focus:border-purple-500 focus:ring-2 focus:ring-purple-500"
                        rows="4"
                        placeholder="Clearly explain your leave reason..."
                        required
                    ></textarea>
                    <p class="text-xs text-gray-500">Minimum 10 characters</p>
                </div>

                <div id="selected-summary" class="hidden rounded-lg border border-purple-200 bg-purple-50 p-3">
                    <h4 class="mb-2 text-xs font-semibold text-purple-900">Selected Schedules:</h4>
                    <div id="selected-list" class="text-xs break-words text-purple-700"></div>
                </div>

                <div class="flex justify-end gap-2 border-t border-gray-200 pt-3">
                    <button
                        type="button"
                        onclick="document.getElementById('cuti-modal').classList.add('hidden')"
                        class="rounded-lg bg-gray-100 px-4 py-2 text-xs font-medium text-gray-700 transition-colors hover:bg-gray-200"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        class="rounded-lg bg-purple-500 px-4 py-2 text-xs font-medium text-white transition-colors hover:bg-purple-600"
                    >
                        <span class="flex items-center gap-2">
                            <i data-lucide="send" class="h-3 w-3"></i>
                            <span>Submit</span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const geoOptions = {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 60000,
        };

        function handleLocationAndSubmit(form, latId, lngId) {
            const button = form.querySelector('button[type="submit"]');
            const originalText = button.innerHTML;
            button.disabled = true;
            button.innerHTML =
                '<div class="flex items-center gap-2"><div class="animate-spin rounded-full h-3 w-3 border-b-2 border-white"></div><span class="text-xs">Getting location...</span></div>';

            if (!navigator.geolocation) {
                alert('Browser does not support geolocation');
                button.disabled = false;
                button.innerHTML = originalText;
                return;
            }

            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    document.getElementById(latId).value = pos.coords.latitude;
                    document.getElementById(lngId).value = pos.coords.longitude;
                    form.submit();
                },
                (err) => {
                    alert('Failed to get location: ' + err.message);
                    button.disabled = false;
                    button.innerHTML = originalText;
                },
                geoOptions
            );
        }

        document.getElementById('checkin-form')?.addEventListener('submit', function (e) {
            e.preventDefault();
            handleLocationAndSubmit(this, 'latitude', 'longitude');
        });

        document.getElementById('checkout-form')?.addEventListener('submit', function (e) {
            e.preventDefault();
            handleLocationAndSubmit(this, 'checkout-latitude', 'checkout-longitude');
        });

        // Leave Modal Functions
        let userSchedules = [];
        let selectedSchedules = [];

        async function loadUserSchedules() {
            const loadingDiv = document.getElementById('schedules-loading');
            const containerDiv = document.getElementById('schedules-container');

            try {
                loadingDiv.classList.remove('hidden');
                containerDiv.classList.add('hidden');

                const response = await fetch('{{ route('user.schedules.upcoming') }}', {
                    method: 'GET',
                    headers: {
                        Accept: 'application/json',
                    },
                });

                if (!response.ok) {
                    const errorText = await response.text();
                    throw new Error(`HTTP ${response.status}: ${errorText}`);
                }

                const data = await response.json();
                userSchedules = data.schedules || [];
                renderSchedules();
            } catch (error) {
                console.error('Error loading schedules:', error);
                containerDiv.innerHTML = `<div class="text-center text-red-500 py-4 text-xs">Failed to load schedules: ${error.message}</div>`;
            } finally {
                loadingDiv.classList.add('hidden');
                containerDiv.classList.remove('hidden');
            }
        }

        function renderSchedules() {
            const container = document.getElementById('schedules-container');

            if (userSchedules.length === 0) {
                container.innerHTML =
                    '<div class="text-center text-gray-500 py-4 text-xs">No schedules available for leave.</div>';
                return;
            }

            const schedulesHtml = userSchedules
                .map((schedule) => {
                    const date = new Date(schedule.schedule_date);
                    const formattedDate = date.toLocaleDateString('id-ID', {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric',
                    });

                    const shiftName = schedule.shift ? schedule.shift.shift_name : 'No Shift';
                    const startTime = schedule.shift ? schedule.shift.start_time : '--:--';
                    const endTime = schedule.shift ? schedule.shift.end_time : '--:--';

                    return `
                <div class="flex items-center gap-3 p-3 border border-gray-200 rounded-lg hover:bg-white transition-colors bg-white">
                    <input type="checkbox"
                           name="schedule_ids[]"
                           value="${schedule.id}"
                           id="schedule_${schedule.id}"
                           onchange="updateSelectedSchedules()"
                           class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500 flex-shrink-0">
                    <label for="schedule_${schedule.id}" class="flex-1 cursor-pointer min-w-0">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i data-lucide="calendar" class="w-4 h-4 text-purple-600"></i>
                            </div>
                            <div class="min-w-0">
                                <div class="text-xs font-semibold text-gray-900 break-words">${formattedDate}</div>
                                <div class="text-xs text-gray-500">${shiftName} • ${startTime} - ${endTime}</div>
                            </div>
                        </div>
                    </label>
                </div>
            `;
                })
                .join('');

            container.innerHTML = schedulesHtml;

            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        }

        function selectAllSchedules() {
            const checkboxes = document.querySelectorAll('input[name="schedule_ids[]"]');
            checkboxes.forEach((checkbox) => {
                checkbox.checked = true;
            });
            updateSelectedSchedules();
        }

        function clearAllSchedules() {
            const checkboxes = document.querySelectorAll('input[name="schedule_ids[]"]');
            checkboxes.forEach((checkbox) => {
                checkbox.checked = false;
            });
            updateSelectedSchedules();
        }

        function updateSelectedSchedules() {
            const checkboxes = document.querySelectorAll('input[name="schedule_ids[]"]:checked');
            const summaryDiv = document.getElementById('selected-summary');
            const listDiv = document.getElementById('selected-list');

            selectedSchedules = Array.from(checkboxes)
                .map((cb) => {
                    const scheduleId = parseInt(cb.value);
                    return userSchedules.find((s) => s.id === scheduleId);
                })
                .filter(Boolean);

            if (selectedSchedules.length > 0) {
                summaryDiv.classList.remove('hidden');
                const summaryText = selectedSchedules
                    .map((schedule) => {
                        const date = new Date(schedule.schedule_date);
                        const formattedDate = date.toLocaleDateString('id-ID', {
                            weekday: 'short',
                            day: 'numeric',
                            month: 'short',
                        });
                        return `${formattedDate} (${schedule.shift.shift_name})`;
                    })
                    .join(', ');
                listDiv.textContent = summaryText;
            } else {
                summaryDiv.classList.add('hidden');
            }
        }
    </script>

    <style>
        @keyframes fade-in {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fade-in 0.3s ease-out;
        }
    </style>
@endsection
