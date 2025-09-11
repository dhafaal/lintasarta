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
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today()->toDateString();

        // Ambil schedule hari ini (untuk area "Jadwal Anda Hari Ini")
        $schedule = Schedules::with('shift')
            ->where('user_id', $user->id)
            ->whereDate('schedule_date', $today)
            ->first();

        // Ambil attendance hari ini
        $attendance = null;
        if ($schedule) {
            $attendance = Attendance::where('user_id', $user->id)
                ->where('schedule_id', $schedule->id)
                ->first();
        }

        // Ambil jadwal mendatang: hari ini dan ke depan
        $schedules = Schedules::with(['shift', 'attendances' => function ($q) use ($user) {
            $q->where('user_id', $user->id);
        }])
            ->where('user_id', $user->id)
            ->whereDate('schedule_date', '>=', $today) // <-- hanya jadwal mendatang (termasuk hari ini)
            ->orderBy('schedule_date', 'asc')
            ->get();

        return view('users.attendances.index', compact('schedule', 'attendance', 'schedules'));
    }

    public function history(Request $request)
    {
        $userId = Auth::id();
        $date = $request->input('date');
        $search = $request->input('search');

        $query = Schedules::with([
            'shift',
            'attendances' => function ($q) use ($userId) {
                $q->where('user_id', $userId);
            },
            'permissions' => function ($q) use ($userId) {
                $q->where('user_id', $userId);
            }
        ])
            ->where('user_id', $userId)
            ->whereDate('schedule_date', '<', Carbon::today()); // hanya hari yang sudah lewat

        // filter tanggal
        if ($date) {
            $query->whereDate('schedule_date', $date);
        }

        // filter shift name
        if ($search) {
            $query->whereHas('shift', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $schedules = $query->orderByDesc('schedule_date')->paginate(10);

        // tandai alpha otomatis
        foreach ($schedules as $schedule) {
            $attendance = $schedule->attendances->first();
            $permission = $schedule->permissions->first();

            if (!$attendance && !$permission) {
                // jika belum ada record, set status alpha (hanya di view, tidak tulis DB)
                $schedule->computed_status = 'alpha';
            } elseif ($attendance) {
                $schedule->computed_status = $attendance->status;
            } elseif ($permission) {
                $schedule->computed_status = 'izin';
            }
        }

        return view('users.attendances.history', [
            'schedules' => $schedules,
            'schedule_date' => $date,
            'search' => $search,
        ]);
    }

    public function checkin(Request $request)
    {
        $user = Auth::user();

        $schedule = Schedules::where('user_id', $user->id)
            ->whereDate('schedule_date', Carbon::today())
            ->first();

        if (!$schedule) {
            return back()->with('error', 'Anda tidak memiliki jadwal hari ini.');
        }

        $attendance = Attendance::where('user_id', $user->id)
            ->where('schedule_id', $schedule->id)
            ->first();

        if ($attendance && $attendance->check_in_time) {
            return back()->with('error', 'Anda sudah melakukan check in.');
        }

        Attendance::updateOrCreate(
            [
                'user_id' => $user->id,
                'schedule_id' => $schedule->id,
            ],
            [
                'status' => 'hadir',
                'check_in_time' => now(),
            ]
        );

        return back()->with('success', 'Berhasil check in.');
    }

    public function checkout(Request $request)
    {
        $user = Auth::user();

        $schedule = Schedules::where('user_id', $user->id)
            ->whereDate('schedule_date', Carbon::today())
            ->with('shift')
            ->first();

        if (!$schedule) {
            return back()->with('error', 'Anda tidak memiliki jadwal hari ini.');
        }

        $attendance = Attendance::where('user_id', $user->id)
            ->where('schedule_id', $schedule->id)
            ->first();

        if (!$attendance || !$attendance->check_in_time) {
            return back()->with('error', 'Anda belum melakukan check in.');
        }

        if ($attendance->check_out_time) {
            return back()->with('error', 'Anda sudah melakukan check out.');
        }

        $shiftEnd = Carbon::parse($schedule->shift->end_time);
        if (now()->lt($shiftEnd)) {
            return back()->with('error', 'Anda belum bisa check out sebelum shift selesai.');
        }

        $attendance->update([
            'check_out_time' => now()
        ]);

        return back()->with('success', 'Berhasil check out.');
    }
}
