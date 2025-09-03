<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permissions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermissionController extends Controller
{
    public function approve(Request $request, Permissions $permission)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
        ]);

        if ($request->action === 'approve') {
            $permission->update([
                'status'      => 'approved',
                'approved_by' => Auth::id(),   // ✅ pakai Auth::id()
                'approved_at' => now(),
            ]);

            return back()->with('success', 'Izin berhasil disetujui ✅');
        }

        $permission->update([
            'status'      => 'rejected',
            'approved_by' => Auth::id(),   // ✅ pakai Auth::id()
            'approved_at' => now(),
        ]);

        return back()->with('error', 'Izin ditolak ❌');
    }
}
