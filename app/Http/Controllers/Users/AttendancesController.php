<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Schedules;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendancesController extends Controller
{
    private $officeLat = -6.2903534643805115;
    private $officeLng = 106.7852134376512;
    private $officeRadius = 500; //berjarak 500m

    public function index()
    {
        $user = Auth::user();
        $today = now()->toDateString();

        $schedule = Schedules::with(['shift', 'permissions', 'attendances'])
            ->where('user_id', $user->id)
            ->whereDate('schedule_date', $today)
            ->first();

        $attendance = $schedule?->attendances->where('user_id', $user->id)->first();

        // Check if user has permission for today
        $todayPermission = \App\Models\Permissions::where('user_id', $user->id)
            ->whereHas('schedule', function ($q) use ($today) {
                $q->whereDate('schedule_date', $today);
            })
            ->first();

        $schedules = Schedules::with(['shift', 'permissions', 'attendances'])
            ->where('user_id', $user->id)
            ->orderBy('schedule_date')
            ->get();

        return view('users.attendances.index', compact('schedule', 'attendance', 'schedules', 'todayPermission'));
    }

    public function checkin(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

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
            return back()->with('error', "Tidak dapat check-in karena Anda memiliki izin yang {$statusText} untuk tanggal ini.");
        }

        if (!$this->isWithinRadius($request->latitude, $request->longitude)) {
            return back()->with('error', 'Anda berada di luar radius 500 meter dari kantor.');
        }

        // Validate check-in time with late tolerance and early restriction
        $validation = $this->validateCheckInTime($request->schedule_id);

        if (!$validation['valid']) {
            return back()->with('error', $validation['message']);
        }

        $attendance = Attendance::firstOrCreate(
            ['schedule_id' => $request->schedule_id, 'user_id' => Auth::id()],
            [
                'status' => $validation['status'],
                'is_late' => $validation['is_late'],
                'late_minutes' => $validation['late_minutes'],
                'check_in_time' => now()
            ]
        );

        if ($attendance->wasRecentlyCreated === false) {
            return back()->with('error', 'Anda sudah check-in sebelumnya.');
        }

        $attendance->update([
            'status' => $validation['status'],
            'is_late' => $validation['is_late'],
            'late_minutes' => $validation['late_minutes'],
            'check_in_time' => now(),
            'latitude' => $request->latitude,
            'longitude' => $request->longitude
        ]);

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

        if (!$this->isWithinRadius($request->latitude, $request->longitude)) {
            return back()->with('error', 'Anda berada di luar radius 500 meter dari kantor.');
        }

        // Ambil schedule + shift untuk mendapatkan jam selesai shift
        $schedule = \App\Models\Schedules::with('shift')->find($request->schedule_id);

        if (!$schedule || !$schedule->shift) {
            return back()->with('error', 'Data jadwal tidak ditemukan.');
        }

        // Buat DateTime lengkap untuk shift start & end berdasarkan schedule_date
        $scheduleDate = \Carbon\Carbon::parse($schedule->schedule_date);
        $shiftStartTime = \Carbon\Carbon::parse($schedule->shift->start_time);
        $shiftEndTime = \Carbon\Carbon::parse($schedule->shift->end_time);
        $shiftStart = $scheduleDate->copy()->setTimeFrom($shiftStartTime);
        $shiftEnd = $scheduleDate->copy()->setTimeFrom($shiftEndTime);
        // Tangani shift malam (end < start => selesai besok)
        if ($shiftEnd->lt($shiftStart)) {
            $shiftEnd->addDay();
        }

        $now = now();

        if ($now->lt($shiftEnd)) {
            return back()->with('error', 'Anda belum bisa check-out. Waktu shift belum selesai.');
        }

        $attendance = Attendance::where('schedule_id', $request->schedule_id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$attendance || !$attendance->check_in_time) {
            return back()->with('error', 'Anda belum check-in.');
        }

        if ($attendance->check_out_time) {
            return back()->with('error', 'Anda sudah check-out.');
        }

        $attendance->update([
            'check_out_time' => $now,
            'latitude_checkout' => $request->latitude,
            'longitude_checkout' => $request->longitude
        ]);

        // Log user activity
        UserActivityLog::log(
            'checkout',
            'attendances',
            $attendance->id,
            "Check Out - {$schedule->shift->shift_name}",
            [
                'schedule_id' => $schedule->id,
                'check_out_time' => $now->toDateTimeString(),
                'latitude_checkout' => $request->latitude,
                'longitude_checkout' => $request->longitude
            ],
            "Check out berhasil pada {$now->format('H:i')}"
        );

        return back()->with('success', 'Check-out berhasil.');
    }


    private function isWithinRadius($lat, $lng)
    {
        $location = \App\Models\Location::first();

        if (!$location) {
            // Handle jika belum ada lokasi
            return false; // Atau throw exception
        }

        $earthRadius = 6371000; // meter
        $latFrom = deg2rad($lat);
        $lonFrom = deg2rad($lng);
        $latTo = deg2rad($location->latitude);
        $lonTo = deg2rad($location->longitude);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $a = sin($latDelta / 2) ** 2 +
            cos($latFrom) * cos($latTo) * sin($lonDelta / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        $distance = $earthRadius * $c;
        session()->flash('debug_distance', round($distance, 2) . ' meter');

        return $distance <= $location->radius;
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

        $attendanceQuery = \App\Models\Attendance::with('schedule.shift')
            ->where('user_id', $user->id)
            ->whereHas('schedule', function ($q) use ($today) {
                $q->whereDate('schedule_date', '<=', $today);
            })
            ->whereIn('status', ['hadir', 'telat', 'alpha', 'izin']);

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

        // Tentukan status berdasarkan waktu check-in
        $status = 'hadir';
        $isLate = false;
        $lateMinutes = 0;

        if ($checkIn->gt($shiftStart)) {
            // Hitung berapa menit telat
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
                : 'Check-in berhasil tepat waktu.'
        ];
    }
}
