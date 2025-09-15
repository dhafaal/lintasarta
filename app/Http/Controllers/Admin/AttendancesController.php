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


        // schedules hari ini
        $schedulesToday = Schedules::with(['user', 'shift'])
            ->whereDate('schedule_date', $today)
            ->get();

        // attendances terkait schedule hari ini
        $attendances = Attendance::with(['user', 'schedule.shift'])
            ->whereHas('schedule', fn($q) => $q->whereDate('schedule_date', $today))
            ->get();

        // permissions (izin) yang terkait schedule hari ini
        $permissions = Permissions::with(['user', 'schedule'])
            ->whereHas('schedule', fn($q) => $q->whereDate('schedule_date', $today))
            ->get();

        $totalSchedules = $schedulesToday->count();
        $totalHadir     = $attendances->where('status', 'hadir')->count();
        $totalIzin      = $attendances->where('status', 'izin')->count();
        $totalAlpha     = max(0, $totalSchedules - ($totalHadir + $totalIzin));

        return view('admin.attendances.index', compact(
            'today',
            'todayFormated',
            'schedulesToday',
            'attendances',
            'permissions',
            'totalSchedules',
            'totalHadir',
            'totalIzin',
            'totalAlpha'
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
        $date = $request->input('date', now()->toDateString());
        $search = $request->input('search');

        // Ambil jadwal sesuai tanggal
        $schedules = \App\Models\Schedules::with(['user', 'shift'])
            ->whereDate('schedule_date', $date)
            ->get();

        // Ambil absensi sesuai jadwal & filter nama
        $attendances = \App\Models\Attendance::with(['user', 'schedule.shift'])
            ->whereHas('schedule', function ($q) use ($date, $search) {
                $q->whereDate('schedule_date', $date);
                if ($search) {
                    $q->whereHas('user', fn($u) => $u->where('name', 'like', "%$search%"));
                }
            })
            ->get();

        // Ambil izin sesuai tanggal
        $permissions = \App\Models\Permissions::with(['user', 'schedule'])
            ->whereHas('schedule', function ($q) use ($date, $search) {
                $q->whereDate('schedule_date', $date);
                if ($search) {
                    $q->whereHas('user', fn($u) => $u->where('name', 'like', "%$search%"));
                }
            })
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
