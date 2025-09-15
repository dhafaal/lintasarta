<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Schedules;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class AttendancesController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today()->toDateString();

        $schedule = Schedules::with('shift')
            ->where('user_id', $user->id)
            ->whereDate('schedule_date', $today)
            ->first();

        $attendance = $schedule ? Attendance::where('schedule_id', $schedule->id)
            ->where('user_id', $user->id)
            ->first() : null;

        $schedules = Schedules::with(['shift', 'attendances', 'permissions'])
            ->where('user_id', $user->id)
            ->orderBy('schedule_date', 'asc')
            ->get();

        return view('users.attendances.index', compact('schedule', 'attendance', 'schedules'));
    }

    public function checkin(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
        ]);

        $schedule = Schedules::find($request->schedule_id);
        $user = Auth::user();

        $attendance = Attendance::firstOrCreate(
            ['schedule_id' => $schedule->id, 'user_id' => $user->id],
            ['status' => 'hadir', 'check_in_time' => now()]
        );

        if (!$attendance->wasRecentlyCreated && $attendance->check_in_time) {
            return back()->with('error', 'Anda sudah check-in sebelumnya.');
        }

        $attendance->update([
            'status' => 'hadir',
            'check_in_time' => now()
        ]);

        return back()->with('success', 'Check-in berhasil.');
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
        ]);

        $schedule = Schedules::find($request->schedule_id);
        $user = Auth::user();

        $attendance = Attendance::where('schedule_id', $schedule->id)
            ->where('user_id', $user->id)
            ->first();

        if (!$attendance || !$attendance->check_in_time) {
            return back()->with('error', 'Anda belum check-in.');
        }

        if ($attendance->check_out_time) {
            return back()->with('error', 'Anda sudah check-out.');
        }

        $attendance->update([
            'check_out_time' => now()
        ]);

        return back()->with('success', 'Check-out berhasil.');
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

    public function history()
    {
        $user = Auth::user();

        $attendances = Attendance::with('schedule.shift')
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->get();

        return view('users.attendances.history', compact('attendances'));
    }



    private function isWithinRadius($lat, $lng, $centerLat, $centerLng, $radius)
    {
        $earthRadius = 6371000; // meter
        $latFrom = deg2rad($lat);
        $lonFrom = deg2rad($lng);
        $latTo = deg2rad($centerLat);
        $lonTo = deg2rad($centerLng);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $a = sin($latDelta / 2) ** 2 +
            cos($latFrom) * cos($latTo) * sin($lonDelta / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        $distance = $earthRadius * $c;
        return $distance <= $radius;
    }
}
