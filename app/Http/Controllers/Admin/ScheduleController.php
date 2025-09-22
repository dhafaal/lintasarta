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
use Maatwebsite\Excel\Facades\Excel;

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

        $exists = Schedules::where('user_id', $request->single_user_id)
            ->whereDate('schedule_date', $request->single_schedule_date)
            ->where('shift_id', $request->single_shift_id)
            ->exists();

        if (!$exists) {
            $schedule = Schedules::create([
                'user_id'       => $request->single_user_id,
                'schedule_date' => $request->single_schedule_date,
                'shift_id'      => $request->single_shift_id,
            ]);

            $user = User::find($request->single_user_id);
            $shift = Shift::find($request->single_shift_id);

            // Log admin schedule activity
            AdminSchedulesLog::log(
                'create',
                $schedule->id,
                $user->id,
                $user->name,
                $shift->id,
                $shift->shift_name,
                $request->single_schedule_date,
                null,
                $schedule->toArray(),
                "Membuat jadwal untuk {$user->name} pada {$request->single_schedule_date}"
            );
        }

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

        $user = User::find($userId);
        $createdSchedules = [];
        
        DB::transaction(function () use ($shifts, $userId, $month, $year, $user, &$createdSchedules) {
            $daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;

            for ($day = 1; $day <= $daysInMonth; $day++) {
                $date = Carbon::createFromDate($year, $month, $day)->format('Y-m-d');
                
                // Handle multiple shifts per day
                $dayShifts = $shifts[$day] ?? [];
                
                // If dayShifts is not an array, convert it to array for backward compatibility
                if (!is_array($dayShifts)) {
                    $dayShifts = [$dayShifts];
                }

                // Remove existing schedules for this user and date first
                $existingSchedules = Schedules::where('user_id', $userId)
                    ->whereDate('schedule_date', $date)
                    ->get();
                    
                // Log deletion of existing schedules
                foreach ($existingSchedules as $existingSchedule) {
                    $shift = $existingSchedule->shift;
                    AdminSchedulesLog::log(
                        'delete',
                        $existingSchedule->id,
                        $user->id,
                        $user->name,
                        $shift->id ?? null,
                        $shift->shift_name ?? 'Unknown',
                        $date,
                        $existingSchedule->toArray(),
                        null,
                        "Menghapus jadwal lama untuk {$user->name} pada {$date} (bulk monthly update)"
                    );
                }
                
                Schedules::where('user_id', $userId)
                    ->whereDate('schedule_date', $date)
                    ->delete();

                // Create new schedules for each shift
                foreach ($dayShifts as $shiftId) {
                    if (!empty($shiftId)) {
                        $schedule = Schedules::create([
                            'user_id'       => $userId,
                            'schedule_date' => $date,
                            'shift_id'      => $shiftId,
                        ]);
                        
                        $createdSchedules[] = $schedule;
                    }
                }
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
                    $exists = Schedules::where('user_id', $userId)
                        ->whereDate('schedule_date', $date)
                        ->where('shift_id', $request->shift_id)
                        ->exists();

                    if (!$exists) {
                        $schedule = Schedules::create([
                            'user_id'       => $userId,
                            'schedule_date' => $date,
                            'shift_id'      => $request->shift_id,
                        ]);
                        
                        $createdSchedules[] = [
                            'schedule' => $schedule,
                            'user' => $user,
                            'shift' => $shift
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
                    $exists = Schedules::where('user_id', $request->user_id)
                        ->whereDate('schedule_date', $currentDate->format('Y-m-d'))
                        ->where('shift_id', $request->shift_id)
                        ->exists();

                    if (!$exists) {
                        $schedule = Schedules::create([
                            'user_id'       => $request->user_id,
                            'schedule_date' => $currentDate->format('Y-m-d'),
                            'shift_id'      => $request->shift_id,
                        ]);
                        
                        $createdSchedules[] = $schedule;
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

    public function edit(Schedules $schedule)
    {
        $users = User::orderBy('name')->get();
        $shifts = Shift::orderBy('shift_name')->get();

        return view('admin.schedules.edit', compact('schedule', 'users', 'shifts'));
    }

    public function update(Request $request, Schedules $schedule)
    {
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

    public function destroy(Schedules $schedule)
    {
        $scheduleData = $schedule->toArray();
        $user = $schedule->user;
        $shift = $schedule->shift;
        $scheduleName = "Jadwal {$user->name} - {$shift->shift_name}";
        
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

                $schedules = Schedules::with(['shift', 'attendance'])
                    ->where('user_id', $user->id)
                    ->whereDate('schedule_date', $date)
                    ->get();

                if ($schedules->isNotEmpty()) {
                    $shiftLetters = [];
                    $hoursList = [];
                    $attendanceStatuses = [];

                    foreach ($schedules as $schedule) {
                        if (!$schedule->shift) continue;

                        $start = Carbon::parse($schedule->shift->start_time);
                        $end = Carbon::parse($schedule->shift->end_time);
                        if ($end->lt($start)) $end->addDay();

                        $minutes = $start->diffInMinutes($end);
                        $totalMinutes += $minutes;

                        $shiftLetters[] = strtoupper(substr($schedule->shift->shift_name, 0, 1));
                        $hoursList[] = round($minutes / 60, 1) . 'j';
                        
                        // Get attendance status for this schedule
                        $attendanceStatus = null;
                        if ($schedule->attendance) {
                            $attendanceStatus = $schedule->attendance->status;
                        }
                        $attendanceStatuses[] = $attendanceStatus;
                    }

                    $row['shifts'][$day] = [
                        'shift' => implode(',', $shiftLetters), // contoh: "P,M"
                        'hours' => implode(' + ', $hoursList), // contoh: "8j + 8j"
                        'attendance_statuses' => $attendanceStatuses, // array of attendance statuses for each shift
                        'primary_attendance' => $attendanceStatuses[0] ?? null, // primary attendance status for coloring
                    ];
                } else {
                    $row['shifts'][$day] = [
                        'shift' => '',
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
        $attendances = \App\Models\Attendance::whereIn('schedule_id', $scheduleIds)->get();
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
}
