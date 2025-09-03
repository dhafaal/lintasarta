<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Permissions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function index()
    {
        $users = User::with([
            'schedules.shift',
            'schedules.attendances',
        ])->get();

        return view('admin.attendances.index', compact('users'));
    }

    public function show(Request $request, User $user)
    {
        $filter = $request->get('filter', 'all');
        $today = now()->toDateString();
        $startOfMonth = now()->startOfMonth()->toDateString();
        $endOfMonth = now()->endOfMonth()->toDateString();

        $query = $user->schedules()->with(['attendances', 'shift', 'permissions']);

        if ($filter === 'today') {
            $query->whereDate('schedule_date', $today);
        } elseif ($filter === 'month') {
            $query->whereBetween('schedule_date', [$startOfMonth, $endOfMonth]);
        }

        // hanya sampai hari ini (hide mendatang)
        $query->whereDate('schedule_date', '<=', $today);

        $schedules = $query->orderBy('schedule_date', 'desc')->paginate(10);

        return view('admin.attendances.show', compact('user', 'schedules', 'filter'));
    }

    public function approve(Request $request, Permissions $permission)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
        ]);

        $attendance = Attendance::firstOrCreate(
            [
                'user_id' => $permission->user_id,
                'schedule_id' => $permission->schedule_id,
            ]
        );

        if ($request->action === 'approve') {
            $permission->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);
            $attendance->update([
                'status' => 'izin_approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);
            return back()->with('success', 'Izin disetujui ✅');
        } else {
            $permission->update([
                'status' => 'rejected',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);
            $attendance->update([
                'status' => 'alpha',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);
            return back()->with('error', 'Izin ditolak ❌, dianggap Alpha');
        }
    }
}
