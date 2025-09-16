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
        $totalIzin = $attendances->where('status', 'izin')->count();
        $totalAlpha = max(0, $totalSchedules - ($totalHadir + $totalIzin));

        return view('admin.attendances.index', compact(
            'today',
            'todayFormated',
            'schedulesToday',
            'attendances',
            'permissions',
            'totalSchedules',
            'totalHadir',
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



    // optional: show() dan destroy() jika memang kamu pakai di routes
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
}
