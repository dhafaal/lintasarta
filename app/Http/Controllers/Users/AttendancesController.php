<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Schedules;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendancesController extends Controller
{
    // ✅ Koordinat kantor kamu
    private $officeLat = -6.2903534643805115;
    private $officeLng = 106.7852134376512;
    private $officeRadius = 1000; // dalam meter (1 KM)

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

        $attendance = Attendance::firstOrCreate(
            ['schedule_id' => $request->schedule_id, 'user_id' => Auth::id()],
            ['status' => 'hadir', 'check_in_time' => now()]
        );

        if (!$attendance->wasRecentlyCreated && $attendance->check_in_time) {
            return back()->with('error', 'Anda sudah check-in sebelumnya.');
        }

        $attendance->update([
            'status' => 'hadir',
            'check_in_time' => now(),
            'latitude' => $request->latitude,
            'longitude' => $request->longitude
        ]);

        return back()->with('success', 'Check-in berhasil.');
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
            'check_out_time' => now(),
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

    public function history()
    {
        $user = Auth::user();

        $attendances = Attendance::with('schedule.shift')
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->get();

        return view('users.attendances.history', compact('attendances'));
    }
}
