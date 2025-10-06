<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Location;
use App\Models\Schedules;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AttendancesController extends Controller
{

    public function index()
    {
        $user = Auth::user();
        $now = now();
        $today = $now->toDateString();

        // Resolve the active schedule considering cross-midnight night shifts
        $activeSchedule = $this->getActiveScheduleForNow($user->id);

        // If an active schedule is found, use its date as the reference for queries; otherwise fall back to today
        $refDate = $activeSchedule?->schedule_date ?? $today;

        $schedule = $activeSchedule ?: Schedules::with(['shift', 'permissions', 'attendances'])
            ->where('user_id', $user->id)
            ->whereDate('schedule_date', $refDate)
            ->first();

        $attendance = $schedule?->attendances->where('user_id', $user->id)->first();

        // Check if user has permission for the reference date (only pending/approved, not rejected)
        $todayPermission = \App\Models\Permissions::where('user_id', $user->id)
            ->whereHas('schedule', function ($q) use ($refDate) {
                $q->whereDate('schedule_date', $refDate);
            })
            ->whereIn('status', ['pending', 'approved']) // Exclude rejected permissions
            ->first();

        $schedules = Schedules::with(['shift', 'permissions', 'attendances'])
            ->where('user_id', $user->id)
            ->orderBy('schedule_date')
            ->get();

        return view('users.attendances.index', compact('schedule', 'attendance', 'schedules', 'todayPermission'));
    }

    /**
     * Request Early Checkout: create a pending permission of type 'early_checkout'
     */
    public function requestEarlyCheckout(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'reason' => 'required|string|min:5',
        ]);

        $user = Auth::user();
        $schedule = Schedules::with('shift')->findOrFail($request->schedule_id);

        // Ensure user owns schedule
        if ($schedule->user_id !== $user->id) {
            return back()->with('error', 'Tidak dapat mengajukan untuk jadwal milik user lain.');
        }

        // Ensure already checked-in and not checked-out
        $attendance = Attendance::where('schedule_id', $schedule->id)
            ->where('user_id', $user->id)
            ->first();

        if (!$attendance || !$attendance->check_in_time) {
            return back()->with('error', 'Anda belum check-in.');
        }
        if ($attendance->check_out_time) {
            return back()->with('error', 'Anda sudah check-out.');
        }

        // Compute LAST shift end for the same day (multi-shift aware)
        $sameDaySchedules = Schedules::with('shift')
            ->where('user_id', $user->id)
            ->whereDate('schedule_date', $schedule->schedule_date)
            ->get();
        $lastEnd = null;
        foreach ($sameDaySchedules as $sch) {
            if (!$sch->shift) continue;
            $date = \Carbon\Carbon::parse($sch->schedule_date);
            $startT = \Carbon\Carbon::parse($sch->shift->start_time);
            $endT   = \Carbon\Carbon::parse($sch->shift->end_time);
            $startDT = $date->copy()->setTimeFrom($startT);
            $endDT   = $date->copy()->setTimeFrom($endT);
            if ($endDT->lt($startDT)) { $endDT->addDay(); }
            if (!$lastEnd || $endDT->gt($lastEnd)) { $lastEnd = $endDT->copy(); }
        }

        $now = now();
        if ($lastEnd && $now->gte($lastEnd)) {  
            return back()->with('error', 'Waktu shift sudah selesai, lakukan check-out biasa.');
        }

        // Prevent duplicate pending request for the same day (multi-shift aware)
        $existing = \App\Models\Permissions::where('user_id', $user->id)
            ->where('type', 'izin')
            ->where('status', 'pending')
            ->where('reason', 'like', '[EARLY_CHECKOUT]%')
            ->whereHas('schedule', function($q) use ($schedule) {
                $q->whereDate('schedule_date', $schedule->schedule_date);
            })
            ->first();
        if ($existing) {
            return back()->with('warning', 'Pengajuan checkout lebih cepat sudah dibuat dan menunggu persetujuan.');
        }

        $permission = \App\Models\Permissions::create([
            'user_id' => $user->id,
            'schedule_id' => $schedule->id,
            'type' => 'izin',
            'reason' => '[EARLY_CHECKOUT] ' . $request->reason,
            'status' => 'pending',
        ]);

        // Log user activity
        UserActivityLog::log(
            'request_permission',
            'permissions',
            $permission->id,
            "Request Early Checkout - {$schedule->schedule_date}",
            [
                'schedule_id' => $schedule->id,
                'type' => 'izin',
                'reason' => $request->reason,
                'requested_checkout_time' => $now->toDateTimeString(),
            ],
            'Mengajukan checkout lebih cepat'
        );

        return back()->with('success', 'Pengajuan checkout lebih cepat telah dikirim dan menunggu persetujuan admin.');
    }
    public function checkin(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        // Before processing a new check-in, auto-close any missed checkouts from previous shifts
        $this->autoCheckoutMissedShifts(Auth::id());

        // Cek apakah user memiliki izin (pending/approved) untuk tanggal ini
        // Jika izin ditolak (rejected), user harus bisa check-in
        $schedule = Schedules::findOrFail($request->schedule_id);
        $existingPermission = \App\Models\Permissions::where('user_id', Auth::id())
            ->whereHas('schedule', function ($q) use ($schedule) {
                $q->whereDate('schedule_date', $schedule->schedule_date);
            })
            ->whereIn('status', ['pending', 'approved']) // Hanya cek pending dan approved, bukan rejected
            ->first();

        if ($existingPermission) {
            $statusText = $existingPermission->status === 'pending' ? 'menunggu persetujuan' : 'telah disetujui';
            return back()->with('error', "Tidak dapat check-in karena Anda memiliki izin yang {$statusText} untuk tanggal ini.");
        }

        // Find valid location using smart detection
        $validLocation = $this->findValidLocation($request->latitude, $request->longitude);
        
        if (!$validLocation) {
            return back()->with('error', 'Anda berada di luar radius dari semua lokasi yang tersedia. Pastikan Anda berada di salah satu lokasi kantor.');
        }

        // Validate check-in time with late tolerance and early restriction
        $validation = $this->validateCheckInTime($request->schedule_id);

        if (!$validation['valid']) {
            return back()->with('error', $validation['message']);
        }

        // Cek apakah sudah ada attendance record (utama)
        $attendance = Attendance::where('schedule_id', $request->schedule_id)
            ->where('user_id', Auth::id())
            ->first();

        if ($attendance) {
            // Jika sudah ada dan sudah check-in, tidak boleh check-in lagi
            if ($attendance->check_in_time) {
                return back()->with('error', 'Anda sudah check-in sebelumnya.');
            }
            
            // Jika ada tapi belum check-in (status alpha dari rejected permission), update
            $attendance->update([
                'location_id' => $validLocation->id,
                'status' => $validation['status'],
                'is_late' => $validation['is_late'],
                'late_minutes' => $validation['late_minutes'],
                'check_in_time' => now(),
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]);
        } else {
            // Buat attendance baru
            $attendance = Attendance::create([
                'schedule_id' => $request->schedule_id,
                'user_id' => Auth::id(),
                'location_id' => $validLocation->id,
                'status' => $validation['status'],
                'is_late' => $validation['is_late'],
                'late_minutes' => $validation['late_minutes'],
                'check_in_time' => now(),
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]);
        }

        // Multi-shift: apply check-in to all schedules on same date
        $sameDaySchedules = Schedules::where('user_id', Auth::id())
            ->whereDate('schedule_date', $schedule->schedule_date)
            ->pluck('id');
        foreach ($sameDaySchedules as $sid) {
            if ((int)$sid === (int)$request->schedule_id) { continue; }
            $att = Attendance::firstOrNew([
                'schedule_id' => $sid,
                'user_id' => Auth::id(),
            ]);
            if (!$att->check_in_time) {
                $att->fill([
                    'location_id' => $validLocation->id,
                    'status' => 'hadir', // untuk shift lain jangan tandai telat
                    'is_late' => false,
                    'late_minutes' => 0,
                    'check_in_time' => now(),
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                ])->save();
            }
        }

        // Log user activity
        UserActivityLog::log(
            'checkin',
            'attendances',
            $attendance->id,
            "Check In - {$schedule->shift->shift_name}",
            [
                'schedule_id' => $schedule->id,
                'status' => $validation['status'],
                'is_late' => $validation['is_late'],
                'late_minutes' => $validation['late_minutes'],
                'latitude' => $request->latitude,
                'longitude' => $request->longitude
            ],
            $validation['is_late'] ? "Check in terlambat {$validation['late_minutes']} menit" : "Check in tepat waktu"
        );

        return back()->with('success', $validation['message']);
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        // Use transaction to ensure atomic multi-shift updates
        return DB::transaction(function () use ($request) {
            // Cek apakah user memiliki izin (pending/approved) untuk tanggal ini
            $schedule = Schedules::findOrFail($request->schedule_id);
            $existingPermission = \App\Models\Permissions::where('user_id', Auth::id())
                ->whereHas('schedule', function ($q) use ($schedule) {
                    $q->whereDate('schedule_date', $schedule->schedule_date);
                })
                ->whereIn('status', ['pending', 'approved'])
                ->first();

            if ($existingPermission) {
                $statusText = $existingPermission->status === 'pending' ? 'menunggu persetujuan' : 'telah disetujui';
                return back()->with('error', "Tidak dapat check-out karena Anda memiliki izin yang {$statusText} untuk tanggal ini.");
            }

            // Find valid location using smart detection
            $validLocation = $this->findValidLocation($request->latitude, $request->longitude);
            if (!$validLocation) {
                return back()->with('error', 'Anda berada di luar radius dari semua lokasi yang tersedia. Pastikan Anda berada di salah satu lokasi kantor.');
            }

            // Ambil semua schedule hari yang sama untuk menentukan akhir shift terakhir (cross-midnight aware)
            $schedule = \App\Models\Schedules::with('shift')->find($request->schedule_id);
            if (!$schedule || !$schedule->shift) {
                return back()->with('error', 'Data jadwal tidak ditemukan.');
            }

            $sameDaySchedules = Schedules::with('shift')
                ->where('user_id', Auth::id())
                ->whereDate('schedule_date', $schedule->schedule_date)
                ->get();

            $finalEnd = null; $firstStart = null;
            foreach ($sameDaySchedules as $sch) {
                if (!$sch->shift) continue;
                $date = Carbon::parse($sch->schedule_date);
                $st = Carbon::parse($sch->shift->start_time);
                $et = Carbon::parse($sch->shift->end_time);
                $startDT = $date->copy()->setTimeFrom($st);
                $endDT = $date->copy()->setTimeFrom($et);
                if ($endDT->lt($startDT)) { $endDT->addDay(); } // cross-midnight
                if (!$firstStart || $startDT->lt($firstStart)) { $firstStart = $startDT->copy(); }
                if (!$finalEnd || $endDT->gt($finalEnd)) { $finalEnd = $endDT->copy(); }
            }

            $now = now();

            // Early checkout guard
            if ($finalEnd && $now->lt($finalEnd)) {
                return back()->with('warning', 'Anda mencoba checkout sebelum jam shift berakhir (akhir shift terakhir). Silakan isi alasan checkout lebih cepat.');
            }

            // Collect open attendances for this day
            $sameDayIds = $sameDaySchedules->pluck('id');
            $openAttendances = Attendance::whereIn('schedule_id', $sameDayIds)
                ->where('user_id', Auth::id())
                ->whereNotNull('check_in_time')
                ->whereNull('check_out_time')
                ->get();

            if ($openAttendances->isEmpty()) {
                return back()->with('error', 'Semua shift hari ini sudah di-checkout.');
            }

            // Update each open attendance safely (checkout time cannot precede check-in)
            $affected = 0; $firstAttendance = null;
            foreach ($openAttendances as $att) {
                $checkoutTime = $now;
                if ($att->check_in_time && $checkoutTime->lt(Carbon::parse($att->check_in_time))) {
                    $checkoutTime = Carbon::parse($att->check_in_time); // clamp
                }
                $att->update([
                    'location_id' => $validLocation->id,
                    'check_out_time' => $checkoutTime,
                    'latitude_checkout' => $request->latitude,
                    'longitude_checkout' => $request->longitude,
                ]);
                $affected++;
                if (!$firstAttendance) { $firstAttendance = $att; }
            }

            // Log user activity (once)
            if ($firstAttendance) {
                UserActivityLog::log(
                    'checkout',
                    'attendances',
                    $firstAttendance->id,
                    "Check Out - Multi-shift di {$validLocation->name}",
                    [
                        'schedule_ids' => $sameDayIds->values()->all(),
                        'affected_attendances' => $affected,
                        'first_shift_start' => optional($firstStart)->toDateTimeString(),
                        'final_shift_end' => optional($finalEnd)->toDateTimeString(),
                        'check_out_time' => $now->toDateTimeString(),
                        'location_id' => $validLocation->id,
                        'location_name' => $validLocation->name,
                        'latitude_checkout' => $request->latitude,
                        'longitude_checkout' => $request->longitude,
                    ],
                    $affected > 1
                        ? "Check out berhasil untuk {$affected} shift pada {$now->format('H:i')} di {$validLocation->name}"
                        : "Check out berhasil pada {$now->format('H:i')} di {$validLocation->name}"
                );
            }

            return back()->with('success', 'Check-out berhasil untuk semua shift hari ini.');
        });
    }


    public function absent(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
        ]);
        
        $schedule = Schedules::find($request->schedule_id);
        $user = Auth::user();

        $attendance = Attendance::firstOrCreate(
            ['schedule_id' => $schedule->id, 'user_id' => $user->id],
            ['status' => 'alpha']
        );

        if (!$attendance->wasRecentlyCreated && $attendance->status === 'alpha') {
            return back()->with('error', 'Anda sudah ditandai Alpha.');
        }

        $attendance->update(['status' => 'alpha']);

        // Log user activity
        UserActivityLog::log(
            'absent',
            'attendances',
            $attendance->id,
            "Alpha - {$schedule->shift->shift_name}",
            [
                'schedule_id' => $schedule->id,
                'status' => 'alpha'
            ],
            "Menandai diri sebagai Alpha pada {$schedule->schedule_date}"
        );

        return back()->with('success', 'Anda ditandai Alpha.');
    }

    public function history(Request $request)
    {
        $user = Auth::user();
        $date = $request->input('date');
        $selectedMonth = $request->input('month', now()->month);
        $selectedYear = $request->input('year', now()->year);
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $today = now()->toDateString();

        // Base query for schedules
        $scheduleQuery = \App\Models\Schedules::with('shift')
            ->where('user_id', $user->id)
            ->whereDate('schedule_date', '<=', $today);

        $attendanceQuery = \App\Models\Attendance::with(['schedule.shift', 'location'])
            ->where('user_id', $user->id)
            ->whereHas('schedule', function ($q) use ($today) {
                $q->whereDate('schedule_date', '<=', $today);
            })
            ->whereIn('status', ['hadir', 'telat', 'alpha', 'izin', 'forgot_checkout', 'early_checkout']);

        $permissionQuery = \App\Models\Permissions::with('schedule')
            ->where('user_id', $user->id)
            ->whereHas('schedule', function ($q) use ($today) {
                $q->whereDate('schedule_date', '<=', $today);
            });

        // Apply date filters
        if (!empty($date)) {
            $scheduleQuery->whereDate('schedule_date', $date);
            $attendanceQuery->whereHas('schedule', fn($q) => $q->whereDate('schedule_date', $date));
            $permissionQuery->whereHas('schedule', fn($q) => $q->whereDate('schedule_date', $date));
        }
        // Apply month and year filter
        else if ($request->has('month') || $request->has('year')) {
            $scheduleQuery->whereMonth('schedule_date', $selectedMonth)
                ->whereYear('schedule_date', $selectedYear);
            $attendanceQuery->whereHas('schedule', function ($q) use ($selectedMonth, $selectedYear) {
                $q->whereMonth('schedule_date', $selectedMonth)
                    ->whereYear('schedule_date', $selectedYear);
            });
            $permissionQuery->whereHas('schedule', function ($q) use ($selectedMonth, $selectedYear) {
                $q->whereMonth('schedule_date', $selectedMonth)
                    ->whereYear('schedule_date', $selectedYear);
            });
        }
        // Apply date range filter
        else if ($startDate && $endDate) {
            $scheduleQuery->whereBetween('schedule_date', [$startDate, $endDate]);
            $attendanceQuery->whereHas('schedule', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('schedule_date', [$startDate, $endDate]);
            });
            $permissionQuery->whereHas('schedule', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('schedule_date', [$startDate, $endDate]);
            });
        }

        $schedules = $scheduleQuery->orderBy('schedule_date', 'desc')->get();
        $attendances = $attendanceQuery->get();
        $permissions = $permissionQuery->get();

        return view('users.attendances.history', compact(
            'attendances',
            'permissions',
            'schedules',
            'date',
            'selectedMonth',
            'selectedYear',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Find valid location based on user coordinates
     */
    private function findValidLocation($userLat, $userLng)
    {
        // Only consider active locations
        $locations = Location::where('is_active', true)->get();
        $validLocations = [];
        $debugInfo = [];

        foreach ($locations as $location) {
            $distance = $this->calculateDistance($userLat, $userLng, $location->latitude, $location->longitude);
            $debugInfo[] = "{$location->name}: {$distance}m (radius: {$location->radius}m)";
            
            if ($distance <= $location->radius) {
                $validLocations[] = [
                    'location' => $location,
                    'distance' => $distance
                ];
            }
        }

        // Sort by distance (closest first)
        usort($validLocations, function($a, $b) {
            return $a['distance'] <=> $b['distance'];
        });

        if (count($validLocations) > 0) {
            $closestLocation = $validLocations[0]['location'];
            $distance = $validLocations[0]['distance'];
            
            // Store debug info in session
            session()->flash('location_debug', "Check-in di {$closestLocation->name}: {$distance}m dari lokasi");
            
            return $closestLocation;
        } else {
            // Store debug info for invalid location
            session()->flash('location_debug', 'Jarak ke lokasi: ' . implode(', ', $debugInfo));
            return null;
        }
    }

    /**
     * Calculate distance between two coordinates using Haversine formula
     */
    private function calculateDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371000; // Earth radius in meters

        $lat1Rad = deg2rad($lat1);
        $lat2Rad = deg2rad($lat2);
        $deltaLatRad = deg2rad($lat2 - $lat1);
        $deltaLngRad = deg2rad($lng2 - $lng1);

        $a = sin($deltaLatRad / 2) * sin($deltaLatRad / 2) +
             cos($lat1Rad) * cos($lat2Rad) *
             sin($deltaLngRad / 2) * sin($deltaLngRad / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c);
    }

    private function autoCheckoutMissedShifts(int $userId): int
    {
        $now = Carbon::now();
        $open = Attendance::with(['schedule.shift'])
            ->where('user_id', $userId)
            ->whereNotNull('check_in_time')
            ->whereNull('check_out_time')
            ->get();

        $count = 0;
        foreach ($open as $att) {
            $sch = $att->schedule;
            if (!$sch || !$sch->shift) { continue; }

            $date = Carbon::parse($sch->schedule_date);
            $st = Carbon::parse($sch->shift->start_time);
            $et = Carbon::parse($sch->shift->end_time);
            $startDT = $date->copy()->setTimeFrom($st);
            $endDT   = $date->copy()->setTimeFrom($et);
            if ($endDT->lt($startDT)) { $endDT->addDay(); }

            // If the shift should have ended already, auto set checkout at scheduled end
            if ($now->gt($endDT)) {
                // Ensure we don't set checkout earlier than check-in
                $checkoutAt = Carbon::parse($att->check_in_time)->gt($endDT) ? Carbon::parse($att->check_in_time) : $endDT;

                $att->update([
                    // keep location_id from check-in; no GPS for auto checkout
                    'check_out_time' => $checkoutAt,
                    'status' => 'forgot_checkout',
                ]);
                $count++;

                // Log user activity for auto checkout
                UserActivityLog::log(
                    'auto_checkout',
                    'attendances',
                    $att->id,
                    'Auto Check Out (Missed Checkout)',
                    [
                        'schedule_id' => $sch->id,
                        'scheduled_end' => $endDT->toDateTimeString(),
                        'auto_checkout_time' => $checkoutAt->toDateTimeString(),
                    ],
                    'Sistem menutup otomatis attendance yang belum di-checkout'
                );
            }
        }

        return $count;
    }

    /**
     * Determine the active schedule for the current time, including cross-midnight (night) shifts.
     * Returns a Schedules model with relations loaded, or null if none applies.
     */
    private function getActiveScheduleForNow(int $userId)
    {
        $now = Carbon::now();
        $today = $now->toDateString();
        $yesterday = $now->copy()->subDay()->toDateString();

        // Load schedules for today and yesterday with shifts
        $candidates = Schedules::with(['shift', 'permissions', 'attendances'])
            ->where('user_id', $userId)
            ->whereDate('schedule_date', '>=', $yesterday)
            ->whereDate('schedule_date', '<=', $today)
            ->get();

        $active = null;
        $activeEnd = null;

        foreach ($candidates as $sch) {
            if (!$sch->shift) { continue; }
            $date = Carbon::parse($sch->schedule_date);
            $startT = Carbon::parse($sch->shift->start_time);
            $endT   = Carbon::parse($sch->shift->end_time);

            $startDT = $date->copy()->setTimeFrom($startT);
            $endDT   = $date->copy()->setTimeFrom($endT);

            // Cross-midnight handling: if end earlier than start, it ends next day
            if ($endDT->lt($startDT)) {
                $endDT->addDay();
            }

            if ($now->betweenIncluded($startDT, $endDT)) {
                // Choose the one that ends latest if multiple contain now
                if (!$activeEnd || $endDT->gt($activeEnd)) {
                    $active = $sch;
                    $activeEnd = $endDT->copy();
                }
            }
        }

        return $active;
    }

    private function validateCheckInTime($scheduleId, $checkInTime = null)
    {
        $checkInTime = $checkInTime ?: now();
        $schedule = Schedules::with('shift')->find($scheduleId);

        if (!$schedule || !$schedule->shift) {
            return ['valid' => false, 'message' => 'Schedule atau shift tidak ditemukan'];
        }

        // Gabungkan tanggal schedule dengan waktu shift
        $scheduleDate = Carbon::parse($schedule->schedule_date);
        $shiftStartTime = Carbon::parse($schedule->shift->start_time);

        // Buat datetime lengkap untuk shift start
        $shiftStart = $scheduleDate->copy()->setTimeFrom($shiftStartTime);

        $checkIn = Carbon::parse($checkInTime);

        // Tentukan status berdasarkan waktu check-in dengan kompensasi 5 menit
        $status = 'hadir';
        $isLate = false;
        $lateMinutes = 0;
        
        // Tambahkan kompensasi 5 menit ke waktu shift start
        $graceTime = 5; // menit kompensasi
        $shiftStartWithGrace = $shiftStart->copy()->addMinutes($graceTime);

        if ($checkIn->gt($shiftStartWithGrace)) {
            // Hitung berapa menit telat dari waktu shift asli (tanpa grace time)
            $lateMinutes = (int) $shiftStart->diffInMinutes($checkIn);
            $status = 'telat';
            $isLate = true;
        }

        return [
            'valid' => true,
            'status' => $status,
            'is_late' => $isLate,
            'late_minutes' => $lateMinutes,
            'message' => $isLate
                ? 'Anda terlambat ' . $lateMinutes . ' menit.'
                : ($checkIn->gt($shiftStart) 
                    ? 'Check-in berhasil dalam batas toleransi 5 menit.' 
                    : 'Check-in berhasil tepat waktu.')
        ];
    }

    public function getUpcomingSchedules()
    {
        $user = Auth::user();
        $today = now()->toDateString();

        try {
            // First, get all upcoming schedules for debugging
            $allSchedules = Schedules::with(['shift', 'permissions'])
                ->where('user_id', $user->id)
                ->whereDate('schedule_date', '>=', $today)
                ->orderBy('schedule_date')
                ->limit(30)
                ->get();

            // Filter out schedules that have pending or approved permissions
            $schedules = $allSchedules->filter(function ($schedule) {
                $hasBlockingPermission = $schedule->permissions()
                    ->whereIn('status', ['pending', 'approved'])
                    ->exists();
                return !$hasBlockingPermission;
            });

            \Log::info('Upcoming schedules query', [
                'user_id' => $user->id,
                'today' => $today,
                'all_schedules_count' => $allSchedules->count(),
                'filtered_schedules_count' => $schedules->count(),
                'all_schedules' => $allSchedules->toArray(),
                'filtered_schedules' => $schedules->values()->toArray()
            ]);

            return response()->json([
                'schedules' => $schedules->values(), // Reset array keys
                'debug' => [
                    'user_id' => $user->id,
                    'today' => $today,
                    'all_count' => $allSchedules->count(),
                    'filtered_count' => $schedules->count()
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error loading upcoming schedules', [
                'error' => $e->getMessage(),
                'user_id' => $user->id
            ]);

            return response()->json([
                'error' => 'Failed to load schedules',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
