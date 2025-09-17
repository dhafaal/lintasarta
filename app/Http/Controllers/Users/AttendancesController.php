<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Schedules;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendancesController extends Controller
{
    // ✅ Koordinat kantor kamu
    private $officeLat = -6.2903534643805115;
    private $officeLng = 106.7852134376512;
    private $officeRadius = 500; // Minimal berjarak 500m

    public function index()
    {
        $user = Auth::user();
        $today = now()->toDateString();

        $schedule = Schedules::with(['shift', 'permissions', 'attendances'])
            ->where('user_id', $user->id)
            ->whereDate('schedule_date', $today)
            ->first();

        $attendance = $schedule?->attendances->where('user_id', $user->id)->first();

        $schedules = Schedules::with(['shift', 'permissions', 'attendances'])
            ->where('user_id', $user->id)
            ->orderBy('schedule_date')
            ->get();

        return view('users.attendances.index', compact('schedule', 'attendance', 'schedules'));
    }

    public function checkin(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        if (!$this->isWithinRadius($request->latitude, $request->longitude)) {
            return back()->with('error', 'Anda berada di luar radius 1 KM dari kantor.');
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

        return back()->with('success', $validation['message']);
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        if (!$this->isWithinRadius($request->latitude, $request->longitude)) {
            return back()->with('error', 'Anda berada di luar radius 1 KM dari kantor.');
        }

        // Ambil schedule + shift untuk mendapatkan jam selesai shift
        $schedule = \App\Models\Schedules::with('shift')->find($request->schedule_id);

        if (!$schedule || !$schedule->shift) {
            return back()->with('error', 'Data jadwal tidak ditemukan.');
        }

        // Ambil jam selesai shift dan bandingkan dengan waktu sekarang
        $shiftEnd = \Carbon\Carbon::parse($schedule->shift->end_time);
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

        return back()->with('success', 'Check-out berhasil.');
    }


    private function isWithinRadius($lat, $lng)
    {
        $earthRadius = 6371000; // meter
        $latFrom = deg2rad($lat);
        $lonFrom = deg2rad($lng);
        $latTo = deg2rad($this->officeLat);
        $lonTo = deg2rad($this->officeLng);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $a = sin($latDelta / 2) ** 2 +
            cos($latFrom) * cos($latTo) * sin($lonDelta / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        $distance = $earthRadius * $c;
        session()->flash('debug_distance', round($distance, 2) . ' meter'); // ✅ debug jarak

        return $distance <= $this->officeRadius;
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
        $attendanceQuery->whereHas('schedule', function($q) use ($selectedMonth, $selectedYear) {
            $q->whereMonth('schedule_date', $selectedMonth)
              ->whereYear('schedule_date', $selectedYear);
        });
        $permissionQuery->whereHas('schedule', function($q) use ($selectedMonth, $selectedYear) {
            $q->whereMonth('schedule_date', $selectedMonth)
              ->whereYear('schedule_date', $selectedYear);
        });
    }
    // Apply date range filter
    else if ($startDate && $endDate) {
        $scheduleQuery->whereBetween('schedule_date', [$startDate, $endDate]);
        $attendanceQuery->whereHas('schedule', function($q) use ($startDate, $endDate) {
            $q->whereBetween('schedule_date', [$startDate, $endDate]);
        });
        $permissionQuery->whereHas('schedule', function($q) use ($startDate, $endDate) {
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
     * Check if check-in time is valid (not too early, within tolerance for late)
     */
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
        
        // Batas check-in paling awal (90 menit sebelum shift)
        $earliestCheckIn = $shiftStart->copy()->subMinutes(90);
        
        // Batas toleransi terlambat (5 menit setelah shift dimulai)
        $lateToleranceLimit = $shiftStart->copy()->addMinutes(5);
        
        $checkIn = Carbon::parse($checkInTime);
        
        // Cek apakah terlalu awal
        if ($checkIn->lt($earliestCheckIn)) {
            return [
                'valid' => false, 
                'message' => 'Belum bisa check-in. Waktu check-in paling awal adalah ' . $earliestCheckIn->format('H:i')
            ];
        }
        
        // Tentukan status berdasarkan waktu check-in
        $status = 'hadir';
        $isLate = false;
        $lateMinutes = 0;
        
        if ($checkIn->gt($shiftStart)) {
            $lateMinutes = $shiftStart->diffInMinutes($checkIn);
            if ($lateMinutes <= 5) {
                // Masih dalam toleransi 5 menit
                $status = 'telat';
                $isLate = true;
            } else {
                // Lebih dari 5 menit = alpha (tidak bisa check-in)
                return [
                    'valid' => false,
                    'message' => 'Terlambat lebih dari 5 menit. Tidak dapat melakukan check-in.'
                ];
            }
        }
        
        return [
            'valid' => true,
            'status' => $status,
            'is_late' => $isLate,
            'late_minutes' => $lateMinutes,
            'message' => $status === 'telat' ? "Check-in berhasil (Terlambat {$lateMinutes} menit)" : 'Check-in berhasil'
        ];
    }

}
