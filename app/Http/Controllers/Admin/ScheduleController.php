<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Schedules;
use App\Models\Shift;
use App\Models\User;
use App\Models\AdminSchedulesLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\ScheduleReportExport;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Validation\ValidationException;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $query = Schedules::with(['user', 'shift']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('shift_filter')) {
            $query->whereHas('shift', function ($q) use ($request) {
                $q->where('category', $request->shift_filter);
            });
        }

        if ($request->filled('date_filter')) {
            $query->whereDate('schedule_date', $request->date_filter);
        }

        $schedules = $query->orderBy('schedule_date', 'asc')->get();

        // Ringkasan per user menggunakan koleksi Laravel
        $workHoursSummary = $schedules->groupBy('user_id')->map(function ($items, $userId) {
            $totalMinutes = $items->sum(function ($item) {
                if (!$item->shift) {
                    return 0; // Skip if shift data is missing
                }
                $start = Carbon::parse($item->shift->start_time);
                $end = Carbon::parse($item->shift->end_time);
                if ($end->lessThan($start)) {
                    $end->addDay();
                }
                return abs($end->diffInMinutes($start)); // pastikan selalu positif
            });

            $hours = floor($totalMinutes / 60);
            $mins = $totalMinutes % 60;

            return [
                'user_id' => $userId,
                'employee_name' => $items->first()->user->name ?? '-',
                'total_work_hours' => sprintf("%02dj %02dm", $hours, $mins),
                'total_work_days' => $items->count(),
            ];
        })->values();

        // Menghitung ringkasan untuk card-card
        $todaySchedules = $schedules->where('schedule_date', today()->toDateString())->count();
        $thisWeekSchedules = $schedules->whereBetween('schedule_date', [now()->startOfWeek(), now()->endOfWeek()])->count();
        $totalEmployeesWithSchedules = $workHoursSummary->count();

        // Kirim semua data ke view
        return view('admin.schedules.index', [
            'schedules' => $schedules,
            'workHoursSummary' => $workHoursSummary,
            'todaySchedules' => $todaySchedules,
            'thisWeekSchedules' => $thisWeekSchedules,
            'totalEmployeesWithSchedules' => $totalEmployeesWithSchedules,
        ]);
    }

    // Detail schedule per user
    public function userSchedules(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $query = Schedules::with('shift')
            ->where('user_id', $id);

        if ($request->filled('shift_filter')) {
            $query->whereHas('shift', function ($q) use ($request) {
                $q->where('category', $request->shift_filter);
            });
        }

        if ($request->filled('date_filter')) {
            $query->whereDate('schedule_date', $request->date_filter);
        }

        $schedules = $query->orderBy('schedule_date', 'asc')->get();

        return view('admin.schedules.users_schedules', [
            'user' => $user,
            'schedules' => $schedules,
        ]);
    }

    /**
     * Tampilkan satu halaman untuk semua opsi pembuatan jadwal.
     */
    public function create(Request $request)
    {
        $users = User::orderBy('name')->get();
        $shifts = Shift::orderBy('shift_name')->get();
        $month = (int) $request->query('month', now()->month);
        $year = (int) $request->query('year', now()->year);
        $daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;

        return view('admin.schedules.create', compact('users', 'shifts', 'daysInMonth', 'month', 'year'));
    }

    /**
     * Get available shifts for second shift based on first shift selection
     */
    public function getAvailableShifts(Request $request)
    {
        $firstShiftId = $request->input('first_shift_id');
        
        if (!$firstShiftId) {
            return response()->json(['shifts' => []]);
        }

        $firstShift = Shift::find($firstShiftId);
        
        if (!$firstShift) {
            return response()->json(['shifts' => []]);
        }

        $availableShifts = [];

        // Logic: Pagi -> Siang, Siang -> Malam, Malam -> tidak ada
        switch ($firstShift->category) {
            case 'Pagi':
                $availableShifts = Shift::where('category', 'Siang')->get();
                break;
            case 'Siang':
                $availableShifts = Shift::where('category', 'Malam')->get();
                break;
            case 'Malam':
                // Tidak ada shift kedua untuk shift malam
                $availableShifts = [];
                break;
        }

        return response()->json([
            'shifts' => $availableShifts->map(function($shift) {
                return [
                    'id' => $shift->id,
                    'shift_name' => $shift->shift_name,
                    'category' => $shift->category,
                    'start_time' => $shift->start_time,
                    'end_time' => $shift->end_time
                ];
            })
        ]);
    }

    /**
     * Metode terpadu untuk menyimpan semua jenis jadwal (tunggal, massal).
     */
    public function store(Request $request)
    {
        // Mendapatkan tipe form dari hidden input
        $formType = $request->input('form_type');

        switch ($formType) {
            case 'single':
                return $this->storeSingle($request);
            case 'bulk_monthly':
                return $this->storeMonthly($request);
            case 'bulk_multiple':
                return $this->storeMultiple($request);
            case 'bulk_same_shift':
                return $this->storeSameShift($request);
            default:
                return redirect()->back()->withErrors(['error' => 'Tipe form tidak valid.']);
        }
    }

    /**
     * Menyimpan jadwal tunggal.
     */
    private function storeSingle(Request $request)
    {
        $request->validate([
            'single_user_id'       => 'required|exists:users,id',
            'single_shift_id'      => 'required|exists:shifts,id',
            'single_schedule_date' => 'required|date',
        ]);

        // Non-destructive merge: on the same date, prefer updating an empty schedule over creating duplicates
        $user = User::find($request->single_user_id);
        $shift = Shift::find($request->single_shift_id);
        $date = Carbon::parse($request->single_schedule_date)->format('Y-m-d');

        DB::transaction(function () use ($request, $user, $shift, $date) {
            // Load all schedules for that user & date with attendances
            $existing = Schedules::with(['attendances' => function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                }])
                ->where('user_id', $user->id)
                ->whereDate('schedule_date', $date)
                ->get();

            // If exact schedule exists, do nothing
            if ($existing->firstWhere('shift_id', (int)$shift->id)) {
                return;
            }

            // Try to reuse a schedule without meaningful attendance by updating its shift_id
            $reusable = $existing->first(function ($s) use ($user) {
                return !$this->hasMeaningfulAttendance($s, $user->id);
            });

            if ($reusable) {
                $old = $reusable->toArray();
                $reusable->update(['shift_id' => $shift->id]);

                AdminSchedulesLog::log(
                    'update',
                    $reusable->id,
                    $user->id,
                    $user->name,
                    $shift->id,
                    $shift->shift_name,
                    $date,
                    $old,
                    $reusable->fresh()->toArray(),
                    "Mengubah shift jadwal (merge) untuk {$user->name} pada {$date}"
                );
                return;
            }

            // Otherwise, create a new schedule
            $schedule = Schedules::create([
                'user_id'       => $user->id,
                'schedule_date' => $date,
                'shift_id'      => $shift->id,
            ]);

            AdminSchedulesLog::log(
                'create',
                $schedule->id,
                $user->id,
                $user->name,
                $shift->id,
                $shift->shift_name,
                $date,
                null,
                $schedule->toArray(),
                "Membuat jadwal untuk {$user->name} pada {$date}"
            );
        });

        return redirect()->route('admin.schedules.index')
            ->with('success', 'Jadwal berhasil disimpan.');
    }

    /**
     * Menyimpan jadwal bulanan.
     */
    private function storeMonthly(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'month'   => 'required|integer|min:1|max:12',
            'year'    => 'required|integer|min:2000',
            'shifts'  => 'array',
        ]);

        $userId = $request->user_id;
        $month = $request->month;
        $year = $request->year;
        $shifts = $request->shifts ?? [];

        // Debug logging
        \Log::info('storeMonthly called', [
            'user_id' => $userId,
            'month' => $month,
            'year' => $year,
            'shifts_count' => count($shifts),
            'shifts_sample' => array_slice($shifts, 0, 5, true)
        ]);

        $user = User::find($userId);
        $createdSchedules = [];
        // Track dates that were processed to later recalculate attendance status
        $datesTouched = [];
        
        DB::transaction(function () use ($shifts, $userId, $month, $year, $user, &$createdSchedules, &$datesTouched) {
            $daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;

            for ($day = 1; $day <= $daysInMonth; $day++) {
                $date = Carbon::createFromDate($year, $month, $day)->format('Y-m-d');
                
                // Handle multiple shifts per day
                $dayShifts = $shifts[$day] ?? [];
                
                // If dayShifts is not an array, convert it to array for backward compatibility
                if (!is_array($dayShifts)) {
                    $dayShifts = [$dayShifts];
                }

                // Build desired list and perform safe merge for this date
                // Map to int first, then filter > 0 to drop 0, null, 'null', '' safely
                $desiredShiftIds = collect($dayShifts)
                    ->map(fn($id) => (int)$id)
                    ->filter(fn($id) => $id > 0)
                    ->values()
                    ->toArray();

                $result = $this->mergeSchedulesForDate($user, $date, $desiredShiftIds);
                // Collect created for summary logging
                foreach ($result['created'] as $created) {
                    $createdSchedules[] = $created;
                }
                // Mark this date for attendance status recalculation
                $datesTouched[$date] = true;
            }
        });
        
        // Log creation of new schedules
        foreach ($createdSchedules as $schedule) {
            $shift = Shift::find($schedule->shift_id);
            AdminSchedulesLog::log(
                'create',
                $schedule->id,
                $user->id,
                $user->name,
                $shift->id,
                $shift->shift_name,
                $schedule->schedule_date,
                null,
                $schedule->toArray(),
                "Membuat jadwal bulanan untuk {$user->name} pada {$schedule->schedule_date}"
            );
        }

        // Recalculate attendance statuses for all processed dates (handle shift changes/remaps)
        foreach (array_keys($datesTouched) as $date) {
            try {
                $this->recalcAttendancesForDate($user->id, $date, 5);
            } catch (\Throwable $e) {
                \Log::warning('Failed to recalc attendance on date after schedule update', [
                    'user_id' => $user->id,
                    'date' => $date,
                    'error' => $e->getMessage(),
                ]);
            }
        }
        
        $totalCreated = count($createdSchedules);
        
        return redirect()->route('admin.schedules.index')
            ->with('success', "Jadwal bulanan berhasil diperbarui. {$totalCreated} jadwal dibuat untuk {$user->name}.");
    }

    /**
     * Menyimpan jadwal massal untuk banyak user dan tanggal.
     */
    private function storeMultiple(Request $request)
    {
        $request->validate([
            'users'    => 'required|array|min:1',
            'users.*'  => 'exists:users,id',
            'dates'    => 'required|array|min:1',
            'dates.*'  => 'date',
            'shift_id' => 'required|exists:shifts,id',
        ]);

        $createdSchedules = [];
        $shift = Shift::find($request->shift_id);
        
        DB::transaction(function () use ($request, $shift, &$createdSchedules) {
            foreach ($request->users as $userId) {
                $user = User::find($userId);
                foreach ($request->dates as $date) {
                    $dateStr = Carbon::parse($date)->format('Y-m-d');
                    // Use merge logic for single desired shift on this date
                    $result = $this->mergeSchedulesForDate($user, $dateStr, [(int)$shift->id]);
                    foreach ($result['created'] as $created) {
                        $createdSchedules[] = [
                            'schedule' => $created,
                            'user' => $user,
                            'shift' => $shift,
                        ];
                    }
                }
            }
        });
        
        // Log creation of schedules
        foreach ($createdSchedules as $item) {
            AdminSchedulesLog::log(
                'create',
                $item['schedule']->id,
                $item['user']->id,
                $item['user']->name,
                $item['shift']->id,
                $item['shift']->shift_name,
                $item['schedule']->schedule_date,
                null,
                $item['schedule']->toArray(),
                "Membuat jadwal massal untuk {$item['user']->name} pada {$item['schedule']->schedule_date}"
            );
        }

        $userCount = count($request->users);
        $dateCount = count($request->dates);
        $createdCount = count($createdSchedules);

        return redirect()->route('admin.schedules.index')
            ->with('success', "Berhasil membuat {$createdCount} jadwal untuk {$userCount} user dengan {$dateCount} tanggal.");
    }

    /**
     * Menyimpan jadwal untuk periode tanggal dengan shift yang sama.
     */
    private function storeSameShift(Request $request)
    {
        $request->validate([
            'user_id'       => 'required|exists:users,id',
            'shift_id'      => 'required|exists:shifts,id',
            'start_date'    => 'required|date',
            'end_date'      => 'required|date|after_or_equal:start_date',
            'selected_days' => 'array',
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $selectedDays = $request->selected_days ?? [];
        $user = User::find($request->user_id);
        $shift = Shift::find($request->shift_id);
        $createdSchedules = [];

        DB::transaction(function () use ($request, $startDate, $endDate, $selectedDays, $user, $shift, &$createdSchedules) {
            $currentDate = $startDate->copy();

            while ($currentDate->lte($endDate)) {
                if (empty($selectedDays) || in_array($currentDate->dayOfWeek, $selectedDays)) {
                    $dateStr = $currentDate->format('Y-m-d');
                    $result = $this->mergeSchedulesForDate($user, $dateStr, [(int)$shift->id]);
                    foreach ($result['created'] as $created) {
                        $createdSchedules[] = $created;
                    }
                }
                $currentDate->addDay();
            }
        });
        
        // Log creation of schedules
        foreach ($createdSchedules as $schedule) {
            AdminSchedulesLog::log(
                'create',
                $schedule->id,
                $user->id,
                $user->name,
                $shift->id,
                $shift->shift_name,
                $schedule->schedule_date,
                null,
                $schedule->toArray(),
                "Membuat jadwal periode untuk {$user->name} pada {$schedule->schedule_date}"
            );
        }

        $createdCount = count($createdSchedules);
        
        return redirect()->route('admin.schedules.index')
            ->with('success', "Jadwal berhasil dibuat untuk periode yang dipilih. {$createdCount} jadwal dibuat untuk {$user->name}.");
    }

    public function edit($schedule)
    {
        $users = User::orderBy('name')->get();
        $shifts = Shift::orderBy('shift_name')->get();

        // Handle bulk edit case
        if ($schedule === 'bulk') {
            $selectedUserId = request('user_id');
            $selectedUser = $selectedUserId ? User::find($selectedUserId) : null;
            
            return view('admin.schedules.edit', compact('users', 'shifts', 'selectedUser'))
                ->with('schedule', null)
                ->with('isBulkEdit', true);
        }

        // Handle single schedule edit
        $schedule = Schedules::findOrFail($schedule);
        return view('admin.schedules.edit', compact('schedule', 'users', 'shifts'));
    }

    public function update(Request $request, $schedule)
    {
        // Check if this is a bulk monthly update or single schedule update
        $formType = $request->input('form_type');
        // Robust detection: if form_type missing but monthly fields exist, treat as bulk monthly
        $isMonthlyEdit = ($formType === 'bulk_monthly') || ($schedule === 'bulk') || $request->has('month') || $request->has('shifts');
        if ($isMonthlyEdit) {
            return $this->updateMonthly($request, null);
        }
        
        // For single schedule update, find the schedule
        $schedule = Schedules::findOrFail($schedule);
        
        // Handle single schedule update (original functionality)
        $request->validate([
            'user_id'       => 'required|exists:users,id',
            'shift_id'      => 'required|exists:shifts,id',
            'schedule_date' => 'required|date',
        ]);

        $oldValues = $schedule->toArray();
        $schedule->update($request->only(['user_id', 'shift_id', 'schedule_date']));

        $user = User::find($request->user_id);
        $shift = Shift::find($request->shift_id);

        // Log admin schedule activity
        AdminSchedulesLog::log(
            'update',
            $schedule->id,
            $user->id,
            $user->name,
            $shift->id,
            $shift->shift_name,
            $request->schedule_date,
            $oldValues,
            $schedule->fresh()->toArray(),
            "Mengubah jadwal untuk {$user->name} pada {$request->schedule_date}"
        );

        return redirect()->route('admin.schedules.index')
            ->with('success', 'Schedule berhasil diupdate.');
    }

    /**
     * Update monthly schedules for a user (used in edit mode)
     */
    private function updateMonthly(Request $request, $schedule = null)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'month'   => 'required|integer|min:1|max:12',
            'year'    => 'required|integer|min:2000',
            'shifts'  => 'array',
        ]);

        $userId = $request->user_id;
        $month = $request->month;
        $year = $request->year;
        $shifts = $request->shifts ?? [];

        $user = User::find($userId);
        $updatedSchedules = [];
        $datesTouched = [];
        
        DB::transaction(function () use ($shifts, $userId, $month, $year, $user, &$updatedSchedules, &$datesTouched) {
            $daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;

            for ($day = 1; $day <= $daysInMonth; $day++) {
                $date = Carbon::createFromDate($year, $month, $day)->format('Y-m-d');
                
                // Handle multiple shifts per day
                $dayShifts = $shifts[$day] ?? [];
                
                // If dayShifts is not an array, convert it to array for backward compatibility
                if (!is_array($dayShifts)) {
                    $dayShifts = [$dayShifts];
                }

                // Build desired list and perform safe merge for this date
                $desiredShiftIds = collect($dayShifts)
                    ->filter(fn($id) => !empty($id))
                    ->map(fn($id) => (int)$id)
                    ->values()
                    ->toArray();

                $result = $this->mergeSchedulesForDate($user, $date, $desiredShiftIds);
                foreach ($result['created'] as $created) {
                    $updatedSchedules[] = $created;
                }
                // track date for later attendance status recalculation
                $datesTouched[$date] = true;
            }
        });
        
        // Log creation of new schedules
        foreach ($updatedSchedules as $newSchedule) {
            $shift = Shift::find($newSchedule->shift_id);
            AdminSchedulesLog::log(
                'create',
                $newSchedule->id,
                $user->id,
                $user->name,
                $shift->id,
                $shift->shift_name,
                $newSchedule->schedule_date,
                null,
                $newSchedule->toArray(),
                "Mengupdate jadwal bulanan untuk {$user->name} pada {$newSchedule->schedule_date}"
            );
        }

        $totalUpdated = count($updatedSchedules);
        
        // Recalculate attendance statuses for all processed dates (handle shift changes/remaps)
        foreach (array_keys($datesTouched) as $date) {
            try {
                $this->recalcAttendancesForDate($user->id, $date, 5);
            } catch (\Throwable $e) {
                \Log::warning('Failed to recalc attendance on date after monthly update', [
                    'user_id' => $user->id,
                    'date' => $date,
                    'error' => $e->getMessage(),
                ]);
            }
        }
        
        return redirect()->route('admin.schedules.index')
            ->with('success', "Jadwal bulanan berhasil diperbarui. {$totalUpdated} jadwal diupdate untuk {$user->name}.");
    }

    public function destroy(Schedules $schedule)
    {
        $scheduleData = $schedule->toArray();
        $user = $schedule->user;
        $shift = $schedule->shift;

        // Guard: do not delete a schedule that has meaningful attendance
        $hasMeaningful = $this->hasMeaningfulAttendance($schedule, $user->id);
        if ($hasMeaningful) {
            return redirect()->route('admin.schedules.index')
                ->with('error', 'Tidak dapat menghapus jadwal yang memiliki attendance. Ubah shift saja jika diperlukan.');
        }

        $schedule->delete();

        // Log admin schedule activity
        AdminSchedulesLog::log(
            'delete',
            null,
            $user->id,
            $user->name,
            $shift->id,
            $shift->shift_name,
            $schedule->schedule_date,
            $scheduleData,
            null,
            "Menghapus jadwal untuk {$user->name} pada {$schedule->schedule_date}"
        );

        return redirect()->route('admin.schedules.index')
            ->with('success', 'Schedule berhasil dihapus.');
    }

    public function calendarView(Request $request)
    {
        $month = (int) $request->query('month', now()->month);
        $year = (int) $request->query('year', now()->year);

        [$data, $daysInMonth] = $this->buildMonthlyTableData($month, $year);

        return view('admin.schedules.calendar', compact('data', 'month', 'year', 'daysInMonth'));
    }

    /**
     * Provide calendar data for FullCalendar integration
     */
    public function calendarData()
    {
        $schedules = Schedules::with(['user', 'shift'])->get();

        $events = $schedules->map(function ($schedule) {
            $start = $schedule->schedule_date . 'T' . $schedule->shift->start_time;
            $end = Carbon::parse($schedule->schedule_date . ' ' . $schedule->shift->end_time);

            if ($end->lt(Carbon::parse($start))) {
                $end->addDay();
            }

            return [
                'id' => $schedule->id,
                'title' => "{$schedule->user->name} - {$schedule->shift->shift_name}",
                'start' => $start,
                'end' => $end->toDateTimeString(),
                'allDay' => false,
                'extendedProps' => [
                    'shift' => $schedule->shift->shift_name,
                    'category' => $schedule->shift->category,
                    'start_time' => $schedule->shift->start_time,
                    'end_time' => $schedule->shift->end_time,
                    'user' => $schedule->user->name,
                ],
            ];
        });

        return response()->json($events);
    }


    public function calendarGridData(Request $request)
    {
        try {
            $month = (int) $request->query('month', now()->month);
            $year = (int) $request->query('year', now()->year);

            if ($month < 1 || $month > 12) {
                return response()->json(['success' => false, 'message' => 'Bulan tidak valid'], 400);
            }

            $date = Carbon::createFromDate($year, $month, 1);

            // Pastikan selalu mulai dari Minggu (0 = Minggu, 6 = Sabtu)
            $firstDayOfMonth = $date->dayOfWeekIso; // 1 = Senin ... 7 = Minggu
            // Konversi supaya Minggu = 0
            $firstDayOfMonth = $firstDayOfMonth % 7;

            $shifts = Shift::select('id', 'shift_name')->orderBy('shift_name')->get();

            return response()->json([
                'success' => true,
                'month' => $month,
                'year' => $year,
                'daysInMonth' => $date->daysInMonth,
                'firstDayOfMonth' => $firstDayOfMonth,
                'monthName' => $date->translatedFormat('F'),
                'shifts' => $shifts,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server',
                'error' => $e->getMessage(),
            ], 500);
        }
    }




    /**
     * Determine if a schedule has meaningful attendance for the given user.
     * Meaningful = has check-in/out or status not 'alpha'.
     */
    private function hasMeaningfulAttendance(Schedules $schedule, int $userId): bool
    {
        // Ensure attendance relation exists; filter by user_id for safety
        $attendance = $schedule->relationLoaded('attendances')
            ? ($schedule->attendances->firstWhere('user_id', $userId) ?? $schedule->attendances->first())
            : Attendance::where('schedule_id', $schedule->id)->where('user_id', $userId)->first();

        if (!$attendance) {
            return false;
        }

        if (!is_null($attendance->check_in_time) || !is_null($attendance->check_out_time)) {
            return true;
        }

        if ($attendance->status && $attendance->status !== 'alpha') {
            return true;
        }

        return false;
    }

    /**
     * Merge desired shifts for one user and date into schedules non-destructively.
     * Rules:
     * - If desired shift exists: keep.
     * - If desired missing: reuse an existing schedule without attendance by updating its shift_id, else create new.
     * - Existing schedules not in desired and without attendance: delete.
     * - Existing schedules not in desired but WITH attendance: keep; if possible map it to an unmet desired by updating shift_id.
     * Returns arrays of created/updated/deleted for optional external logging.
     */
    private function mergeSchedulesForDate(User $user, string $date, array $desiredShiftIds): array
    {
        $created = [];
        $updated = [];
        $deleted = [];

        // Normalize desired list (unique, ints) and limit to maximum two shifts per day
        $desired = collect($desiredShiftIds)
            ->filter()
            ->map(fn($id) => (int)$id)
            ->unique()
            ->values()
            ->take(2);

        // Enforce shift sequence rules on desired: Pagi->Siang, Siang->Malam, Malam->(no second)
        if ($desired->isNotEmpty()) {
            $firstId = (int)$desired->first();
            $firstShift = Shift::find($firstId);
            if ($firstShift) {
                $allowedSecondCategory = null;
                switch ($firstShift->category) {
                    case 'Pagi':
                        $allowedSecondCategory = 'Siang';
                        break;
                    case 'Siang':
                        $allowedSecondCategory = 'Malam';
                        break;
                    case 'Malam':
                        $allowedSecondCategory = null; // no second shift allowed
                        break;
                }

                if ($allowedSecondCategory === null) {
                    // Only keep the first shift when first is Malam
                    $desired = collect([$firstId]);
                } else {
                    // If a second shift is provided, verify its category
                    $secondId = $desired->get(1);
                    if ($secondId) {
                        $secondShift = Shift::find((int)$secondId);
                        if (!$secondShift || $secondShift->category !== $allowedSecondCategory) {
                            // Drop invalid second shift
                            $desired = collect([$firstId]);
                        }
                    }
                }
            }
        }

        // Load all existing schedules for this user and date with shift and attendances
        $existing = Schedules::with(['shift', 'attendances' => function ($q) use ($user) {
                $q->where('user_id', $user->id);
            }])
            ->where('user_id', $user->id)
            ->whereDate('schedule_date', $date)
            ->get();


        // If any existing schedule on this date has attendance, handle conflicts (Option C)
        $hasAnyAttendance = $existing->contains(fn($s) => $this->hasMeaningfulAttendance($s, $user->id));
        if ($hasAnyAttendance) {
            $conflictAction = request()->input('on_attendance_conflict'); // expected: 'remap' or 'cancel'

            // If user confirmed remap, proceed even if desired has 0 or 2; pick a sensible target
            if ($conflictAction === 'remap') {
                // Determine target desired shift id
                $desiredId = $desired->isNotEmpty()
                    ? (int)$desired->first()
                    : (int)optional($existing->first(fn($s) => $this->hasMeaningfulAttendance($s, $user->id)))->shift_id;

                if (!$desiredId) {
                    // Fallback: keep first existing as target
                    $desiredId = (int)optional($existing->first())->shift_id;
                }

                // Ensure a target desired schedule exists
                $desiredSchedule = $existing->firstWhere('shift_id', $desiredId);
                if (!$desiredSchedule) {
                    $nonAttended = $existing->first(fn($s) => !$this->hasMeaningfulAttendance($s, $user->id));
                    if ($nonAttended) {
                        $old = $nonAttended->toArray();
                        $nonAttended->update(['shift_id' => $desiredId]);
                        $desiredSchedule = $nonAttended->fresh();
                        $shiftModel = Shift::find($desiredId);
                        AdminSchedulesLog::log(
                            'update',
                            $desiredSchedule->id,
                            $user->id,
                            $user->name,
                            $desiredId,
                            optional($shiftModel)->shift_name,
                            $date,
                            $old,
                            $desiredSchedule->toArray(),
                            "Menetapkan jadwal tujuan untuk remap attendance pada {$date}"
                        );
                        $updated[] = ['old' => $old, 'schedule' => $desiredSchedule];
                    } else {
                        $dup = Schedules::where('user_id', $user->id)
                            ->whereDate('schedule_date', $date)
                            ->where('shift_id', $desiredId)
                            ->exists();
                        if (!$dup) {
                            $desiredSchedule = Schedules::create([
                                'user_id' => $user->id,
                                'schedule_date' => $date,
                                'shift_id' => $desiredId,
                            ]);
                            $shiftModel = Shift::find($desiredId);
                            AdminSchedulesLog::log(
                                'create',
                                $desiredSchedule->id,
                                $user->id,
                                $user->name,
                                $desiredId,
                                optional($shiftModel)->shift_name,
                                $date,
                                null,
                                $desiredSchedule->toArray(),
                                "Membuat jadwal tujuan untuk remap attendance pada {$date}"
                            );
                            $created[] = $desiredSchedule;
                        } else {
                            $desiredSchedule = Schedules::where('user_id', $user->id)
                                ->whereDate('schedule_date', $date)
                                ->where('shift_id', $desiredId)
                                ->first();
                        }
                    }
                }

                // Remap attendance from other schedules to desired schedule, then delete others
                $others = $existing->filter(fn($s) => (int)$s->shift_id !== $desiredId);
                if ($others->isNotEmpty()) {
                    $otherIds = $others->pluck('id')->all();
                    Attendance::whereIn('schedule_id', $otherIds)
                        ->update(['schedule_id' => $desiredSchedule->id]);

                    // Recalculate statuses right after remap for this date
                    try { $this->recalcAttendancesForDate($user->id, $date, 5); } catch (\Throwable $e) { \Log::warning('recalc after remap failed', ['user_id'=>$user->id,'date'=>$date,'e'=>$e->getMessage()]); }

                    foreach ($others as $ex) {
                        $old = $ex->toArray();
                        $shift = $ex->shift;
                        $ex->delete();
                        AdminSchedulesLog::log(
                            'delete',
                            $old['id'] ?? null,
                            $user->id,
                            $user->name,
                            $shift->id ?? null,
                            $shift->shift_name ?? 'Unknown',
                            $date,
                            $old,
                            null,
                            "Menghapus jadwal lain setelah remap attendance pada {$date}"
                        );
                        $deleted[] = $old;
                    }
                }

                return [
                    'created' => $created,
                    'updated' => $updated,
                    'deleted' => $deleted,
                ];
            }

            // When editing down to ONE desired shift, we can optionally remap attendance from other shift(s)
            if ($desired->count() === 1) {
                $desiredId = (int)$desired->first();
                $desiredSchedule = $existing->firstWhere('shift_id', $desiredId);

                // Ensure target desired schedule exists (reuse a non-attended or create a new one)
                if (!$desiredSchedule) {
                    $nonAttended = $existing->first(fn($s) => !$this->hasMeaningfulAttendance($s, $user->id));
                    if ($nonAttended) {
                        $old = $nonAttended->toArray();
                        $nonAttended->update(['shift_id' => $desiredId]);
                        $desiredSchedule = $nonAttended->fresh();
                        $shiftModel = Shift::find($desiredId);
                        AdminSchedulesLog::log(
                            'update',
                            $desiredSchedule->id,
                            $user->id,
                            $user->name,
                            $desiredId,
                            optional($shiftModel)->shift_name,
                            $date,
                            $old,
                            $desiredSchedule->fresh()->toArray(),
                            "Menetapkan jadwal tujuan untuk remap attendance pada {$date}"
                        );
                        $updated[] = ['old' => $old, 'schedule' => $desiredSchedule];
                    } else {
                        // create new desired schedule as target if not duplicate
                        $dup = Schedules::where('user_id', $user->id)
                            ->whereDate('schedule_date', $date)
                            ->where('shift_id', $desiredId)
                            ->exists();
                        if (!$dup) {
                            $desiredSchedule = Schedules::create([
                                'user_id' => $user->id,
                                'schedule_date' => $date,
                                'shift_id' => $desiredId,
                            ]);
                            $shiftModel = Shift::find($desiredId);
                            AdminSchedulesLog::log(
                                'create',
                                $desiredSchedule->id,
                                $user->id,
                                $user->name,
                                $desiredId,
                                optional($shiftModel)->shift_name,
                                $date,
                                null,
                                $desiredSchedule->toArray(),
                                "Membuat jadwal tujuan untuk remap attendance pada {$date}"
                            );
                            $created[] = $desiredSchedule;
                        } else {
                            $desiredSchedule = Schedules::where('user_id', $user->id)
                                ->whereDate('schedule_date', $date)
                                ->where('shift_id', $desiredId)
                                ->first();
                        }
                    }
                }

                // Identify other schedules on that date (potentially with attendance)
                $others = $existing->filter(fn($s) => (int)$s->shift_id !== $desiredId);
                $othersWithAttendance = $others->filter(fn($s) => $this->hasMeaningfulAttendance($s, $user->id));

                if ($othersWithAttendance->isNotEmpty()) {
                    if ($conflictAction !== 'remap') {
                        // Ask UI to confirm via validation error
                        throw ValidationException::withMessages([
                            'attendance_conflict' => [
                                'Terdapat attendance pada shift yang akan dihapus. Konfirmasi diperlukan: kirim on_attendance_conflict=remap untuk memindahkan attendance.',
                            ],
                        ]);
                    }

                    // Remap attendance from others to desired schedule
                    $otherIds = $others->pluck('id')->all();
                    Attendance::whereIn('schedule_id', $otherIds)
                        ->update(['schedule_id' => $desiredSchedule->id]);

                    // Recalculate statuses right after remap for this date
                    try { $this->recalcAttendancesForDate($user->id, $date, 5); } catch (\Throwable $e) { \Log::warning('recalc after remap (single desired) failed', ['user_id'=>$user->id,'date'=>$date,'e'=>$e->getMessage()]); }

                    // Delete non-desired schedules after remap
                    foreach ($others as $ex) {
                        $old = $ex->toArray();
                        $shift = $ex->shift;
                        $ex->delete();
                        AdminSchedulesLog::log(
                            'delete',
                            $old['id'] ?? null,
                            $user->id,
                            $user->name,
                            $shift->id ?? null,
                            $shift->shift_name ?? 'Unknown',
                            $date,
                            $old,
                            null,
                            "Menghapus jadwal lain setelah remap attendance pada {$date}"
                        );
                        $deleted[] = $old;
                    }

                    return [
                        'created' => $created,
                        'updated' => $updated,
                        'deleted' => $deleted,
                    ];
                }

                // No others with attendance -> safe to delete non-desired
                foreach ($others as $ex) {
                    $old = $ex->toArray();
                    $shift = $ex->shift;
                    $ex->delete();
                    AdminSchedulesLog::log(
                        'delete',
                        $old['id'] ?? null,
                        $user->id,
                        $user->name,
                        $shift->id ?? null,
                        $shift->shift_name ?? 'Unknown',
                        $date,
                        $old,
                        null,
                        "Menghapus jadwal tanpa attendance (reduce to single) untuk {$user->name} pada {$date}"
                    );
                    $deleted[] = $old;
                }

                return [
                    'created' => $created,
                    'updated' => $updated,
                    'deleted' => $deleted,
                ];
            }

            // Case: allow adding a second shift when one attended shift already exists and desired has two shifts
            if ($desired->count() === 2) {
                // Find an attended schedule on this date
                $attended = $existing->first(fn($s) => $this->hasMeaningfulAttendance($s, $user->id));
                if ($attended) {
                    $attendedShiftId = (int)$attended->shift_id;
                    // Ensure desired includes the attended shift
                    if ($desired->contains($attendedShiftId)) {
                        // Determine the other desired shift (the one to add)
                        $otherDesired = (int)$desired->first(fn($sid) => (int)$sid !== $attendedShiftId);
                        // Check if it already exists
                        $existsSame = $existing->firstWhere('shift_id', $otherDesired);
                        if (!$existsSame) {
                            // Enforce max two: only add if current count < 2
                            if ($existing->count() < 2) {
                                // Prevent duplicate at DB level
                                $dup = Schedules::where('user_id', $user->id)
                                    ->whereDate('schedule_date', $date)
                                    ->where('shift_id', $otherDesired)
                                    ->exists();
                                if (!$dup) {
                                    $schedule = Schedules::create([
                                        'user_id'       => $user->id,
                                        'schedule_date' => $date,
                                        'shift_id'      => $otherDesired,
                                    ]);
                                    $shiftModel = Shift::find($otherDesired);
                                    AdminSchedulesLog::log(
                                        'create',
                                        $schedule->id,
                                        $user->id,
                                        $user->name,
                                        $otherDesired,
                                        optional($shiftModel)->shift_name,
                                        $date,
                                        null,
                                        $schedule->toArray(),
                                        "Menambah shift kedua (preserve attendance) untuk {$user->name} pada {$date}"
                                    );
                                    $created[] = $schedule;
                                }
                            }
                        }
                        // Return after possibly adding the second; do not delete or modify attended
                        return [
                            'created' => $created,
                            'updated' => $updated,
                            'deleted' => $deleted,
                        ];
                    }
                }
            }

            // For other cases (not reducing to single or adding allowed second), keep attended and remove only non-attended
            foreach ($existing as $ex) {
                if ($this->hasMeaningfulAttendance($ex, $user->id)) {
                    continue; // keep attended
                }
                $old = $ex->toArray();
                $shift = $ex->shift;
                $ex->delete();
                AdminSchedulesLog::log(
                    'delete',
                    $old['id'] ?? null,
                    $user->id,
                    $user->name,
                    $shift->id ?? null,
                    $shift->shift_name ?? 'Unknown',
                    $date,
                    $old,
                    null,
                    "Menghapus jadwal tanpa attendance (preserve attended) untuk {$user->name} pada {$date}"
                );
                $deleted[] = $old;
            }
            return [
                'created' => $created,
                'updated' => $updated,
                'deleted' => $deleted,
            ];
        }

        // No attendance exists -> align existing to desired (max 2)
        // 1) Remove non-desired schedules (safe to delete since no attendance exists on this date)
        foreach ($existing as $ex) {
            if (!$desired->contains((int)$ex->shift_id)) {
                $old = $ex->toArray();
                $shift = $ex->shift;
                $ex->delete();
                AdminSchedulesLog::log(
                    'delete',
                    $old['id'] ?? null,
                    $user->id,
                    $user->name,
                    $shift->id ?? null,
                    $shift->shift_name ?? 'Unknown',
                    $date,
                    $old,
                    null,
                    "Menghapus jadwal non-desired tanpa attendance untuk {$user->name} pada {$date} (merge)"
                );
                $deleted[] = $old;
            }
        }

        // Reload existing after deletions
        $existing = Schedules::with('shift')
            ->where('user_id', $user->id)
            ->whereDate('schedule_date', $date)
            ->get();

        // 2) Ensure each desired shift exists (reuse truly leftover schedule or create new up to 2 max)
        foreach ($desired as $shiftId) {
            // Skip if already exists (prevent duplicates per shift)
            $existsSameShift = $existing->firstWhere('shift_id', (int)$shiftId);
            if ($existsSameShift) {
                continue;
            }

            // Try to reuse a leftover schedule that is NOT already one of the desired shifts
            // After deletions above, existing may only contain desired ones; in that case, do not reuse (create new)
            $leftover = $existing->first(function ($s) use ($desired) {
                return !$desired->contains((int)$s->shift_id);
            });
            if ($leftover) {
                $old = $leftover->toArray();
                $leftover->update(['shift_id' => $shiftId]);
                $shiftModel = Shift::find($shiftId);
                AdminSchedulesLog::log(
                    'update',
                    $leftover->id,
                    $user->id,
                    $user->name,
                    $shiftId,
                    optional($shiftModel)->shift_name,
                    $date,
                    $old,
                    $leftover->fresh()->toArray(),
                    "Mengubah shift jadwal (merge align) untuk {$user->name} pada {$date}"
                );
                $updated[] = ['old' => $old, 'schedule' => $leftover];
            } else {
                // Create only if under 2 shifts and not duplicate
                $currentCount = Schedules::where('user_id', $user->id)
                    ->whereDate('schedule_date', $date)
                    ->count();
                if ($currentCount >= 2) {
                    break; // enforce max two shifts per date
                }
                $dup = Schedules::where('user_id', $user->id)
                    ->whereDate('schedule_date', $date)
                    ->where('shift_id', $shiftId)
                    ->exists();
                if ($dup) {
                    continue;
                }
                $schedule = Schedules::create([
                    'user_id'       => $user->id,
                    'schedule_date' => $date,
                    'shift_id'      => $shiftId,
                ]);
                $shiftModel = Shift::find($shiftId);
                AdminSchedulesLog::log(
                    'create',
                    $schedule->id,
                    $user->id,
                    $user->name,
                    $shiftId,
                    optional($shiftModel)->shift_name,
                    $date,
                    null,
                    $schedule->toArray(),
                    "Menambah jadwal (merge align) untuk {$user->name} pada {$date}"
                );
                $created[] = $schedule;
                // Update collection
                $existing->push($schedule);
            }
        }

        // 3a) Enforce per-shift uniqueness (delete duplicates of the same shift_id)
        $groups = $existing->groupBy('shift_id');
        foreach ($groups as $shiftId => $items) {
            if ($items->count() > 1) {
                // keep the earliest created (lowest id), delete the rest
                $sorted = $items->sortBy('id')->values();
                $toDelete = $sorted->slice(1);
                foreach ($toDelete as $ex) {
                    $old = $ex->toArray();
                    $shift = $ex->shift;
                    $ex->delete();
                    AdminSchedulesLog::log(
                        'delete',
                        $old['id'] ?? null,
                        $user->id,
                        $user->name,
                        $shift->id ?? null,
                        $shift->shift_name ?? 'Unknown',
                        $date,
                        $old,
                        null,
                        "Menghapus duplikasi jadwal untuk shift yang sama pada {$date}"
                    );
                    $deleted[] = $old;
                }
            }
        }

        // refresh existing after uniqueness enforcement
        $existing = Schedules::with('shift')
            ->where('user_id', $user->id)
            ->whereDate('schedule_date', $date)
            ->get();

        // 3b) Enforce max two schedules per date explicitly (delete extras without attendance)
        if ($existing->count() > 2) {
            // sort by id asc and keep first two, delete rest
            $toDelete = $existing->sortBy('id')->values()->slice(2);
            foreach ($toDelete as $ex) {
                $old = $ex->toArray();
                $shift = $ex->shift;
                $ex->delete();
                AdminSchedulesLog::log(
                    'delete',
                    $old['id'] ?? null,
                    $user->id,
                    $user->name,
                    $shift->id ?? null,
                    $shift->shift_name ?? 'Unknown',
                    $date,
                    $old,
                    null,
                    "Menghapus jadwal ekstra (max 2 per date) untuk {$user->name} pada {$date}"
                );
                $deleted[] = $old;
            }
        }

        // 3c) If desired has only one shift, ensure only one schedule remains
        if ($desired->count() === 1) {
            $desiredId = (int)$desired->first();
            $existing = Schedules::with('shift')
                ->where('user_id', $user->id)
                ->whereDate('schedule_date', $date)
                ->get();
            foreach ($existing as $ex) {
                if ((int)$ex->shift_id !== $desiredId) {
                    $old = $ex->toArray();
                    $shift = $ex->shift;
                    $ex->delete();
                    AdminSchedulesLog::log(
                        'delete',
                        $old['id'] ?? null,
                        $user->id,
                        $user->name,
                        $shift->id ?? null,
                        $shift->shift_name ?? 'Unknown',
                        $date,
                        $old,
                        null,
                        "Menghapus shift ke-2 sesuai permintaan edit pada {$date}"
                    );
                    $deleted[] = $old;
                }
            }
        }

        return [
            'created' => $created,
            'updated' => $updated,
            'deleted' => $deleted,
        ];
    }

    public function report()
    {
        return view('admin.schedules.report');
    }

    public function exportReport(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        $fileName = "Report_Jadwal_{$month}_{$year}.xlsx";
        return Excel::download(new ScheduleReportExport($month, $year), $fileName);
    }

    public function table(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        [$data, $daysInMonth] = $this->buildMonthlyTableData($month, $year);

        return view('admin.schedules.calendar', compact('data', 'month', 'year', 'daysInMonth'));
    }

    private function buildMonthlyTableData(int $month, int $year): array
    {
        $daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;

        $users = User::whereHas('schedules', function ($q) use ($year, $month) {
            $q->whereYear('schedule_date', $year)->whereMonth('schedule_date', $month);
        })
            ->whereIn('role', ['user', 'operator'])
            ->orderBy('name')
            ->get();

        $data = [];
        foreach ($users as $user) {
            $row = [
                'nama' => $user->name,
                'shifts' => [],
                'total_jam' => '0j'
            ];

            $totalMinutes = 0;

            for ($day = 1; $day <= $daysInMonth; $day++) {
                $date = Carbon::createFromDate($year, $month, $day)->format('Y-m-d');
                // Load shift + attendance(s) + permissions; hours use SHIFT DURATION (not actual checkin/checkout)
                $schedules = Schedules::with(['shift', 'attendances', 'permissions'])
                    ->where('user_id', $user->id)
                    ->whereDate('schedule_date', $date)
                    ->get();

                if ($schedules->isNotEmpty()) {
                    $shiftLetters = [];
                    $shiftNames = [];
                    $attendanceStatuses = [];
                    $dayMinutesAcc = 0; // accumulate minutes for the whole day

                    foreach ($schedules as $schedule) {
                        if (!$schedule->shift) continue;
                        // Determine minutes based on SHIFT DURATION by default
                        // Then apply business rules: alpha => 0, approved permission => 0
                        $attendance = null;
                        if (isset($schedule->attendances) && $schedule->attendances) {
                            $attendance = $schedule->attendances->firstWhere('user_id', $user->id) ?? $schedule->attendances->first();
                        } elseif (isset($schedule->attendance)) { // backward compatibility
                            $attendance = $schedule->attendance;
                        }

                        // Permissions presence for this schedule (to avoid auto-alpha for izin/cuti)
                        $permissionApproved = null;
                        $permissionPending = null;
                        if (isset($schedule->permissions) && $schedule->permissions) {
                            $permissionApproved = $schedule->permissions->firstWhere('status', 'approved');
                            $permissionPending = $schedule->permissions->firstWhere('status', 'pending');
                        }

                        // Compute shift duration in minutes (handle crossing midnight)
                        $start = Carbon::parse($schedule->shift->start_time);
                        $end = Carbon::parse($schedule->shift->end_time);
                        if ($end->lt($start)) { $end->addDay(); }
                        $shiftMinutes = $start->diffInMinutes($end);

                        // Apply rules:
                        // - If explicit alpha attendance: 0
                        // - Else if NO attendance AND NO pending/approved permission: auto-alpha => 0
                        // - Else: use shift duration MINUS 1 hour break per shift
                        if ($attendance && $attendance->status === 'alpha') {
                            $minutes = 0; // explicit alpha
                            $forcedAlpha = true;
                        } elseif (!$attendance && !$permissionApproved && !$permissionPending) {
                            $minutes = 0; // auto-alpha when truly absent without izin/cuti
                            $forcedAlpha = true;
                        } else {
                            // Deduct 1 hour (60 minutes) break per shift
                            $minutes = max(0, $shiftMinutes - 60);
                            $forcedAlpha = false;
                        }

                        // accumulate per-day with break already deducted per shift
                        $dayMinutesAcc += $minutes;

                        $shiftLetters[] = strtoupper(substr($schedule->shift->shift_name, 0, 1));
                        $shiftNames[] = $schedule->shift->shift_name; // full shift names
                        $hoursList[] = round($minutes / 60, 1) . 'j';

                        // Determine primary attendance status for coloring
                        $attendanceStatus = $attendance->status ?? (($permissionApproved || $permissionPending) ? 'izin' : null);
                        if ($forcedAlpha && !$attendanceStatus) { $attendanceStatus = 'alpha'; }
                        $attendanceStatuses[] = $attendanceStatus;
                    }

                    // Break already deducted per shift, so just use accumulated minutes
                    $dayMinutesAfterBreak = $dayMinutesAcc;
                    // Add to monthly total
                    $totalMinutes += $dayMinutesAfterBreak;

                    $row['shifts'][$day] = [
                        'shift' => implode(',', $shiftLetters), // contoh: "P,M"
                        'shift_name' => implode(' + ', $shiftNames), // contoh: "Pagi + Malam"
                        'hours' => (function($m){ $h = $m/60; return ($h==floor($h)) ? floor($h).'j' : number_format($h,1).'j'; })($dayMinutesAfterBreak),
                        'attendance_statuses' => $attendanceStatuses, // array of attendance statuses for each shift
                        'primary_attendance' => $attendanceStatuses[0] ?? null, // primary attendance status for coloring
                    ];
                } else {
                    $row['shifts'][$day] = [
                        'shift' => '',
                        'shift_name' => '',
                        'hours' => '',
                        'attendance_statuses' => [],
                        'primary_attendance' => null,
                    ];
                }
            }

            $row['total_jam'] = round($totalMinutes / 60, 1) . 'j';
            $data[] = $row;
        }

        return [$data, $daysInMonth];
    }

    public function history(Request $request, User $user)
    {
        $today = Carbon::today();
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = Schedules::with(['shift', 'user'])
            ->where('user_id', $user->id);

        // Filter berdasarkan tanggal jika ada input
        if ($startDate && $endDate) {
            $query->whereBetween('schedule_date', [$startDate, $endDate]);
        } elseif ($startDate) {
            $query->whereDate('schedule_date', '>=', $startDate);
        } elseif ($endDate) {
            $query->whereDate('schedule_date', '<=', $endDate);
        } else {
            // Default: tampilkan riwayat (tanggal sebelum hari ini)
            $query->whereDate('schedule_date', '<', $today);
        }

        $schedules = $query->orderBy('schedule_date', 'desc')->paginate(10);

        // Ambil attendance & permissions untuk schedule-schedule ini
        $scheduleIds = $schedules->pluck('id');
        $attendances = \App\Models\Attendance::with('location')->whereIn('schedule_id', $scheduleIds)->get();
        $permissions = \App\Models\Permissions::whereIn('schedule_id', $scheduleIds)->get();

        return view('admin.schedules.history', compact('user', 'schedules', 'attendances', 'permissions', 'startDate', 'endDate'));
    }

    /**
     * Get users that have schedules for swap functionality
     */
    public function getUsersWithSchedules()
    {
        $users = User::whereHas('schedules')
            ->whereIn('role', ['user', 'operator'])
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json(['users' => $users]);
    }

    /**
     * Get schedules for a specific user for swap functionality
     */
    public function getUserSchedulesForSwap($userId)
    {
        $schedules = Schedules::with('shift')
            ->where('user_id', $userId)
            ->whereDate('schedule_date', '>=', Carbon::today())
            ->orderBy('schedule_date', 'asc')
            ->get()
            ->map(function ($schedule) {
                return [
                    'id' => $schedule->id,
                    'shift_name' => $schedule->shift->shift_name ?? '-',
                    'formatted_date' => Carbon::parse($schedule->schedule_date)->format('d M Y'),
                    'time_range' => $schedule->shift ? 
                        Carbon::parse($schedule->shift->start_time)->format('H:i') . ' - ' . 
                        Carbon::parse($schedule->shift->end_time)->format('H:i') : '-'
                ];
            });

        return response()->json(['schedules' => $schedules]);
    }

    /**
     * Swap two schedules
     */
    public function swapSchedules(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'target_schedule_id' => 'required|exists:schedules,id',
        ]);

        try {
            $schedule1 = Schedules::with(['user', 'shift'])->findOrFail($request->schedule_id);
            $schedule2 = Schedules::with(['user', 'shift'])->findOrFail($request->target_schedule_id);
            
            // Store original values for logging
            $originalUser1 = $schedule1->user;
            $originalUser2 = $schedule2->user;
            $oldValues1 = $schedule1->toArray();
            $oldValues2 = $schedule2->toArray();
            
            DB::transaction(function () use ($request, $schedule1, $schedule2, $originalUser1, $originalUser2, $oldValues1, $oldValues2) {
                // Store original values
                $originalUserId1 = $schedule1->user_id;
                $originalUserId2 = $schedule2->user_id;

                // Swap user_id values
                $schedule1->update(['user_id' => $originalUserId2]);
                $schedule2->update(['user_id' => $originalUserId1]);
                
                // Log the swap for both schedules
                AdminSchedulesLog::log(
                    'update',
                    $schedule1->id,
                    $originalUser2->id,
                    $originalUser2->name,
                    $schedule1->shift->id,
                    $schedule1->shift->shift_name,
                    $schedule1->schedule_date,
                    $oldValues1,
                    $schedule1->fresh()->toArray(),
                    "Menukar jadwal: {$originalUser1->name} → {$originalUser2->name} pada {$schedule1->schedule_date}"
                );
                
                AdminSchedulesLog::log(
                    'update',
                    $schedule2->id,
                    $originalUser1->id,
                    $originalUser1->name,
                    $schedule2->shift->id,
                    $schedule2->shift->shift_name,
                    $schedule2->schedule_date,
                    $oldValues2,
                    $schedule2->fresh()->toArray(),
                    "Menukar jadwal: {$originalUser2->name} → {$originalUser1->name} pada {$schedule2->schedule_date}"
                );
            });

            return response()->json([
                'success' => true,
                'message' => 'Jadwal berhasil ditukar'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menukar jadwal: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get existing schedules for a user in specific month and year
     */
    public function getUserExistingSchedules(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000',
        ]);

        try {
            $userId = $request->user_id;
            $month = $request->month;
            $year = $request->year;

            // Get all schedules for the user in the specified month and year
            $schedules = Schedules::with('shift')
                ->where('user_id', $userId)
                ->whereYear('schedule_date', $year)
                ->whereMonth('schedule_date', $month)
                ->get();

            // Group schedules by day of month
            $schedulesByDay = [];
            foreach ($schedules as $schedule) {
                $day = Carbon::parse($schedule->schedule_date)->day;
                if (!isset($schedulesByDay[$day])) {
                    $schedulesByDay[$day] = [];
                }
                $schedulesByDay[$day][] = [
                    'shift_id' => $schedule->shift_id,
                    'shift_name' => $schedule->shift->shift_name ?? '',
                ];
            }

            return response()->json([
                'success' => true,
                'schedules' => $schedulesByDay
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil jadwal: ' . $e->getMessage()
            ], 500);
        }
    }

    private function recalcAttendancesForDate(int $userId, string $date, int $toleranceMinutes = 5): void
    {
        // Load attendances with their schedules and shifts for the given date
        $attendances = Attendance::with(['schedule.shift'])
            ->where('user_id', $userId)
            ->whereNotNull('check_in_time')
            ->whereHas('schedule', function($q) use ($date) {
                $q->whereDate('schedule_date', $date);
            })
            ->get();

        foreach ($attendances as $att) {
            $schedule = $att->schedule;
            if (!$schedule || !$schedule->shift) {
                continue;
            }

            // PRESERVE forgot_checkout and early_checkout statuses
            // Only recalculate hadir/telat for attendances that don't have these special statuses
            if (in_array($att->status, ['forgot_checkout', 'early_checkout'])) {
                // Still update is_late and late_minutes, but keep the status
                $scheduleDate = Carbon::parse($schedule->schedule_date);
                $shiftStartT  = Carbon::parse($schedule->shift->start_time);
                $shiftStart   = $scheduleDate->copy()->setTimeFrom($shiftStartT);
                $checkIn      = Carbon::parse($att->check_in_time);

                $isLate = false;
                $lateMinutes = 0;

                $grace = $toleranceMinutes;
                if ($checkIn->gt($shiftStart->copy()->addMinutes($grace))) {
                    $lateMinutes = (int) $shiftStart->diffInMinutes($checkIn);
                    $isLate = true;
                }

                // Update only is_late and late_minutes, preserve status
                if ((bool)$att->is_late !== $isLate || (int)$att->late_minutes !== $lateMinutes) {
                    \Log::info('Recalc attendance is_late/late_minutes (preserving special status)', [
                        'attendance_id' => $att->id,
                        'user_id' => $userId,
                        'date' => $date,
                        'preserved_status' => $att->status,
                        'old_is_late' => (bool)$att->is_late,
                        'old_late_minutes' => (int)$att->late_minutes,
                        'new_is_late' => $isLate,
                        'new_late_minutes' => $lateMinutes,
                    ]);
                    $att->update([
                        'is_late' => $isLate,
                        'late_minutes' => $lateMinutes,
                    ]);
                }
                continue;
            }

            // For normal statuses (hadir, telat, izin, alpha), recalculate as before
            $scheduleDate = Carbon::parse($schedule->schedule_date);
            $shiftStartT  = Carbon::parse($schedule->shift->start_time);
            $shiftStart   = $scheduleDate->copy()->setTimeFrom($shiftStartT);
            $checkIn      = Carbon::parse($att->check_in_time);

            $status = 'hadir';
            $isLate = false;
            $lateMinutes = 0;

            $grace = $toleranceMinutes; // minutes
            if ($checkIn->gt($shiftStart->copy()->addMinutes($grace))) {
                $lateMinutes = (int) $shiftStart->diffInMinutes($checkIn);
                $status = 'telat';
                $isLate = true;
            }

            // Update only if changed
            if ($att->status !== $status || (bool)$att->is_late !== $isLate || (int)$att->late_minutes !== $lateMinutes) {
                \Log::info('Recalc attendance status after schedule change', [
                    'attendance_id' => $att->id,
                    'user_id' => $userId,
                    'date' => $date,
                    'old_status' => $att->status,
                    'old_is_late' => (bool)$att->is_late,
                    'old_late_minutes' => (int)$att->late_minutes,
                    'new_status' => $status,
                    'new_is_late' => $isLate,
                    'new_late_minutes' => $lateMinutes,
                    'shift_start' => $shiftStart->toDateTimeString(),
                    'check_in' => $checkIn->toDateTimeString(),
                    'tolerance_min' => $grace,
                ]);
                $att->update([
                    'status' => $status,
                    'is_late' => $isLate,
                    'late_minutes' => $lateMinutes,
                ]);
            }
        }
    }

    /**
     * Bulk delete schedules
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'schedule_ids' => 'required|array',
            'schedule_ids.*' => 'exists:schedules,id'
        ]);

        try {
            DB::beginTransaction();

            $scheduleIds = $request->schedule_ids;
            
            // Get schedules for logging
            $schedules = Schedules::with(['user', 'shift'])->whereIn('id', $scheduleIds)->get();
            
            // Delete schedules
            $deletedCount = Schedules::whereIn('id', $scheduleIds)->delete();

            // Log activity for each deleted schedule
            foreach ($schedules as $schedule) {
                AdminSchedulesLog::create([
                    'user_id' => Auth::id(),
                    'action' => 'bulk_delete',
                    'resource_type' => 'Schedule',
                    'resource_id' => $schedule->id,
                    'description' => "Bulk delete jadwal: {$schedule->user->name} - {$schedule->shift->shift_name} pada " . 
                                   Carbon::parse($schedule->schedule_date)->format('d M Y'),
                    'old_values' => json_encode([
                        'user_name' => $schedule->user->name,
                        'shift_name' => $schedule->shift->shift_name,
                        'schedule_date' => $schedule->schedule_date,
                    ]),
                    'ip_address' => $request->ip    (),
                    'user_agent' => $request->userAgent(),
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Berhasil menghapus {$deletedCount} jadwal",
                'deleted_count' => $deletedCount
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus jadwal: ' . $e->getMessage()
            ], 500);
        }
    }
}
 