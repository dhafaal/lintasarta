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

        $attendance = null;
        if ($schedule) {
            $attendance = Attendance::where('user_id', $user->id)
                ->where('schedule_id', $schedule->id)
                ->first();
        }

        $schedules = Schedules::with(['shift', 'attendances' => function ($q) use ($user) {
            $q->where('user_id', $user->id);
        }])
            ->where('user_id', $user->id)
            ->whereDate('schedule_date', '>=', $today)
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
            'attendances' => fn($q) => $q->where('user_id', $userId),
            'permissions' => fn($q) => $q->where('user_id', $userId),
        ])
            ->where('user_id', $userId)
            ->whereDate('schedule_date', '<', Carbon::today());

        if ($date) {
            $query->whereDate('schedule_date', $date);
        }

        if ($search) {
            $query->whereHas('shift', fn($q) => $q->where('name', 'like', "%{$search}%"));
        }

        $schedules = $query->orderByDesc('schedule_date')->paginate(10);

        foreach ($schedules as $schedule) {
            $attendance = $schedule->attendances->first();
            $permission = $schedule->permissions->first();

            if (!$attendance && !$permission) {
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
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return back()->with('error', 'Lokasi tidak valid.');
        }

        $user = Auth::user();
        $lat = $request->latitude;
        $lng = $request->longitude;

        $schedule = Schedules::with('shift')
            ->where('user_id', $user->id)
            ->whereDate('schedule_date', Carbon::today())
            ->first();

        if (!$schedule) {
            return back()->with('error', 'Anda tidak memiliki jadwal hari ini.');
        }

        // Cegah check-in sebelum jadwal mulai
        $shiftStart = Carbon::parse($schedule->shift->start_time);
        if (now()->lt($shiftStart)) {
            return back()->with('error', 'Belum waktunya check in.');
        }

        // Cegah check-in jika sudah lebih dari 5 jam dari shift start
        if (now()->gt($shiftStart->copy()->addHours(5))) {
            return back()->with('error', 'Waktu check in sudah lewat (Alpha).');
        }

        // Validasi lokasi kantor
        if (!$this->isWithinRadius($lat, $lng, -6.200000, 106.816666, 200)) {
            return back()->with('error', 'Anda berada di luar area kantor.');
        }

        $attendance = Attendance::where('user_id', $user->id)
            ->where('schedule_id', $schedule->id)
            ->first();

        if ($attendance && $attendance->check_in_time) {
            return back()->with('error', 'Anda sudah melakukan check in.');
        }

        Attendance::updateOrCreate(
            ['user_id' => $user->id, 'schedule_id' => $schedule->id],
            [
                'status' => 'hadir',
                'check_in_time' => now(),
                'latitude' => $lat,
                'longitude' => $lng,
            ]
        );

        return back()->with('success', 'Berhasil check in.');
    }

    public function checkout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return back()->with('error', 'Lokasi tidak valid.');
        }

        $user = Auth::user();
        $lat = $request->latitude;
        $lng = $request->longitude;

        $schedule = Schedules::with('shift')
            ->where('user_id', $user->id)
            ->whereDate('schedule_date', Carbon::today())
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

        if (!$this->isWithinRadius($lat, $lng, -6.200000, 106.816666, 200)) {
            return back()->with('error', 'Anda berada di luar area kantor.');
        }

        $attendance->update([
            'check_out_time' => now(),
            'latitude' => $lat,
            'longitude' => $lng,
        ]);

        return back()->with('success', 'Berhasil check out.');
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
