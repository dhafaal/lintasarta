<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Permissions;
use App\Models\Schedules;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendancesController extends Controller
{
    public function index(Request $request)
{
        $today = $request->input('date', Carbon::today()->toDateString());
        $todayFormated = Carbon::parse($today)->locale('id')->translatedFormat('l, d F Y');
        $search = $request->input('search', '');
        $statusFilter = $request->input('status', '');

        // Query builder untuk schedules dengan filter search
        $schedulesQuery = Schedules::with(['user', 'shift'])
            ->whereDate('schedule_date', $today);

        // Filter berdasarkan nama karyawan jika ada search
        if (!empty($search)) {
            $schedulesQuery->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        }

        $schedulesToday = $schedulesQuery->get();
        $scheduleIds = $schedulesToday->pluck('id');

        // Query builder untuk attendances dengan filter status
        $attendancesQuery = Attendance::with(['user', 'schedule.shift'])
            ->whereIn('schedule_id', $scheduleIds);

        // Filter berdasarkan status jika dipilih
        if (!empty($statusFilter)) {
            $attendancesQuery->where('status', $statusFilter);
        }

        $attendances = $attendancesQuery->get();

        // permissions (izin) yang terkait schedule hari ini
        $permissions = Permissions::with(['user', 'schedule'])
            ->whereIn('schedule_id', $scheduleIds)
            ->get();

        // Hitung statistik berdasarkan data yang sudah difilter
        $totalSchedules = $schedulesToday->count();
        $totalHadir = $attendances->where('status', 'hadir')->count();
        $totalTelat = $attendances->where('status', 'telat')->count();
        $totalIzin = $attendances->where('status', 'izin')->count();
        $totalAlpha = max(0, $totalSchedules - ($totalHadir + $totalTelat + $totalIzin));

        return view('admin.attendances.index', compact(
            'today',
            'todayFormated',
            'schedulesToday',
            'attendances',
            'permissions',
            'totalSchedules',
            'totalHadir',
            'totalTelat',
            'totalIzin',
            'totalAlpha',
            'search',
            'statusFilter'
        ));
    }

    /**
     * Approve permission -> set permission status dan pastikan attendance.status = 'izin'
     */
    public function approvePermission(Permissions $permission)
    {
        $permission->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        Attendance::updateOrCreate(
            [
                'user_id' => $permission->user_id,
                'schedule_id' => $permission->schedule_id,
            ],
            [
                'status' => 'izin',
            ]
        );

        return back()->with('success', 'Permission approved');
    }

    /**
     * Reject permission -> set permission status, dan kembalikan attendance jadi 'alpha'
     * (jika belum ada check in)
     */
    public function rejectPermission(Permissions $permission)
    {
        $permission->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        $attendance = Attendance::where('user_id', $permission->user_id)
            ->where('schedule_id', $permission->schedule_id)
            ->first();

        if ($attendance) {
            // jika belum checkin, set menjadi alpha (atau hapus sesuai preferensi)
            if (!$attendance->check_in_time) {
                $attendance->update(['status' => 'alpha']);
            }
        }

        return back()->with('success', 'Permission rejected');
    }

    public function history(Request $request)
    {
        // Ambil tanggal dari request, default hari ini
        $date = $request->input('date', now()->toDateString());
        
        // Ambil search parameter
        $search = $request->input('search', '');

        // Query builder untuk jadwal dengan relasi user dan shift
        $schedulesQuery = \App\Models\Schedules::with(['user', 'shift'])
            ->whereDate('schedule_date', $date);

        // Jika ada search, filter berdasarkan nama user
        if (!empty($search)) {
            $schedulesQuery->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        }

        $schedules = $schedulesQuery->get();

        // Ambil absensi berdasarkan schedule_id yang sudah difilter
        $scheduleIds = $schedules->pluck('id');
        $attendances = \App\Models\Attendance::with(['schedule.shift'])
            ->whereIn('schedule_id', $scheduleIds)
            ->get();

        // Ambil izin berdasarkan schedule_id yang sudah difilter
        $permissions = \App\Models\Permissions::with(['schedule'])
            ->whereIn('schedule_id', $scheduleIds)
            ->get();

        return view('admin.attendances.history', compact('attendances', 'permissions', 'schedules', 'date', 'search'));
    }

    public function show($userId)
    {
        // implementasi show per user bila perlu
        $userAttendances = Attendance::with('schedule.shift')->where('user_id', $userId)->get();
        return view('admin.attendances.show', compact('userAttendances'));
    }

    public function destroy(Attendance $attendance)
    {
        $attendance->delete();
        return back()->with('success', 'Attendance deleted');
    }

    /**
     * Check if check-in time is valid (not too early, within tolerance for late)
     */
    public function validateCheckInTime($scheduleId, $checkInTime = null)
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
            'message' => 'Check-in berhasil'
        ];
    }

    /**
     * Process check-in with late validation
     */
    public function processCheckIn(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'user_id' => 'required|exists:users,id'
        ]);

        $validation = $this->validateCheckInTime($request->schedule_id);
        
        if (!$validation['valid']) {
            return back()->with('error', $validation['message']);
        }

        // Create or update attendance
        $attendance = Attendance::updateOrCreate(
            [
                'user_id' => $request->user_id,
                'schedule_id' => $request->schedule_id,
            ],
            [
                'status' => $validation['status'],
                'is_late' => $validation['is_late'],
                'late_minutes' => $validation['late_minutes'],
                'check_in_time' => now(),
            ]
        );

        return back()->with('success', $validation['message']);
    }
}
