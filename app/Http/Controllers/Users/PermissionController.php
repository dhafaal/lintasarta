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
    public function store(Request $request, $scheduleId)
    {
        $request->validate([
            'keterangan' => 'required|string|max:255',
        ]);

        Permissions::create([
            'user_id' => Auth::id(),
            'schedule_id' => $scheduleId,
            'keterangan' => $request->keterangan,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Izin berhasil diajukan dan menunggu persetujuan.');
    }

    public function izin(Request $request, $scheduleId)
    {
        $request->validate([
            'keterangan' => 'required|string|max:255',
        ]);

        $schedule = Schedules::findOrFail($scheduleId);

        Permissions::create([
            'user_id' => Auth::id(),
            'schedule_id' => $schedule->id,
            'alasan' => $request->keterangan,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Izin berhasil diajukan, menunggu persetujuan.');
    }


    // Approve izin (dipakai admin nantinya)
    public function approve($id)
    {
        $permission = Permissions::findOrFail($id);
        $permission->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);
        return back()->with('success', 'Izin disetujui.');
    }

    // Reject izin (dipakai admin nantinya)
    public function reject($id)
    {
        $permission = Permissions::findOrFail($id);
        $permission->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);
        return back()->with('error', 'Izin ditolak.');
    }
}
