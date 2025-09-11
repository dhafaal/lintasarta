<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Permissions;
use App\Models\Schedules;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermissionController extends Controller
{
    // Form pengajuan izin + riwayat
    public function create()
    {
        $permissions = Permissions::with('schedule')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('users.attendances.permission', compact('permissions'));
    }

    // Simpan izin baru
    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'reason' => 'required|string|max:500',
        ]);

        $schedule = Schedules::where('schedule_date', $request->date)
            ->where('user_id', Auth::id())
            ->first();

        if (!$schedule) {
            return back()->with('error', 'Tidak ada jadwal pada tanggal tersebut.');
        }

        // Cek apakah sudah ada izin pending/approved untuk tanggal itu
        $exists = Permissions::where('schedule_id', $schedule->id)
            ->where('user_id', Auth::id())
            ->whereIn('status', ['pending', 'approved'])
            ->exists();

        if ($exists) {
            return back()->with('error', 'Anda sudah mengajukan izin untuk tanggal ini.');
        }

        Permissions::create([
            'user_id' => Auth::id(),
            'schedule_id' => $schedule->id,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        return redirect()->route('user.permissions.index')->with('success', 'Izin berhasil diajukan.');
    }

    // Batalkan izin (hanya jika masih pending)
    public function cancel(Schedules $schedule)
    {
        $permission = Permissions::where('schedule_id', $schedule->id)
            ->where('user_id', Auth::id())
            ->where('status', 'pending')
            ->first();

        if (!$permission) {
            return back()->with('error', 'Tidak ada izin pending untuk dibatalkan.');
        }

        $permission->delete();

        return back()->with('success', 'Pengajuan izin berhasil dibatalkan.');
    }
}
