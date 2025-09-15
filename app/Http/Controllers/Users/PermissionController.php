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
            'reason' => 'nullable|string|max:255',
        ]);

        Permissions::create([
            'user_id' => Auth::id(),
            'schedule_id' => $request->schedule_id,
            'type' => $request->type,
            'reason' => $request->reason,
            'status' => 'pending'
        ]);

        return back()->with('success', 'Izin berhasil diajukan dan menunggu persetujuan.');
    }
}
