<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Permissions;
use App\Models\Schedules;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermissionController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'type' => 'required|in:izin,sakit,cuti',
            'reason' => 'required|string|min:10|max:255',
        ]);

        $schedule = Schedules::findOrFail($request->schedule_id);
        
        // Check if user already has permission for this date
        $existingPermission = Permissions::where('user_id', Auth::id())
            ->whereHas('schedule', function($q) use ($schedule) {
                $q->whereDate('schedule_date', $schedule->schedule_date);
            })
            ->first();

        if ($existingPermission) {
            return back()->with('error', 'Anda sudah memiliki pengajuan izin untuk tanggal ini.');
        }

        Permissions::create([
            'user_id' => Auth::id(),
            'schedule_id' => $request->schedule_id,
            'type' => $request->type,
            'reason' => $request->reason,
            'status' => 'pending'
        ]);

        return back()->with('success', 'Pengajuan izin berhasil dikirim dan menunggu persetujuan.');
    }
}
