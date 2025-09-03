<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Permissions;
use App\Models\Schedules;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    // Tampilkan jadwal + absensi
    public function index()
    {
        $userId = Auth::id();

        $schedules = Schedules::with(['shift', 'attendances' => function ($q) use ($userId) {
            $q->where('user_id', $userId);
        }])
            ->where('user_id', $userId) // hanya ambil jadwal milik user
            ->orderBy('schedule_date', 'asc')
            ->paginate(9);

        $totalHadir = Attendance::where('user_id', $userId)->where('status', 'hadir')->count();
        $totalIzin = Attendance::where('user_id', $userId)->where('status', 'izin_pending')
            ->orWhere('status', 'izin_approved')->count();
        $totalAlpha = Attendance::where('user_id', $userId)->where('status', 'alpha')->count();
        $totalSchedules = $schedules->total();

        return view('users.attendances.index', compact(
            'schedules',
            'totalHadir',
            'totalIzin',
            'totalAlpha',
            'totalSchedules'
        ));
    }

    // Check-in
    public function store(Request $request, $scheduleId)
    {
        $userId = Auth::id();
        $schedule = Schedules::findOrFail($scheduleId);

        $attendance = Attendance::firstOrCreate(
            [
                'user_id' => $userId,
                'schedule_id' => $scheduleId,
            ],
            [
                'status' => 'hadir',
                'check_in_time' => Carbon::now(),
            ]
        );

        return back()->with('success', 'Check-in berhasil!');
    }

    public function checkout($scheduleId)
    {
        $userId = Auth::id();
        $attendance = Attendance::where('user_id', $userId)
            ->where('schedule_id', $scheduleId)
            ->first();

        if (!$attendance) {
            return back()->with('error', 'Anda belum check-in.');
        }

        if ($attendance->check_out_time) {
            return back()->with('error', 'Anda sudah checkout.');
        }

        $schedule = $attendance->schedule; // relasi schedule
        $shift = $schedule->shift;

        // Pastikan shift punya jam selesai (misal column end_time di tabel shifts)
        $endTime = \Carbon\Carbon::parse($schedule->schedule_date . ' ' . $shift->end_time);
        $now = now();

        // Cek apakah sekarang sudah melewati jam selesai shift
        if ($now->lt($endTime)) {
            return back()->with('error', 'Checkout hanya bisa dilakukan setelah jam selesai shift (' . $endTime->format('H:i') . ').');
        }

        // Simpan waktu checkout
        $attendance->update([
            'check_out_time' => $now,
        ]);

        return back()->with('success', 'Berhasil checkout.');
    }


    // Ajukan izin
    public function izin(Request $request, $scheduleId)
    {
        $request->validate([
            'keterangan' => 'required|string|max:255',
        ]);

        // Simpan ke tabel permissions
        Permissions::create([
            'user_id'    => Auth::id(),
            'schedule_id' => $scheduleId,
            'alasan'     => $request->keterangan,
            'status'     => 'pending',
        ]);

        // Update atau buat attendance agar sinkron
        Attendance::updateOrCreate(
            [
                'user_id'    => Auth::id(),
                'schedule_id' => $scheduleId,
            ],
            [
                'status'     => 'izin_pending',
                'keterangan' => $request->keterangan,
            ]
        );

        return back()->with('success', 'Izin berhasil diajukan, menunggu persetujuan.');
    }
}
