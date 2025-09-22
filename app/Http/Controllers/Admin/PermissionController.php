<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permissions;
use App\Models\AdminPermissionsLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermissionController extends Controller
{
    public function approve(Request $request, Permissions $permission)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
        ]);

        $oldStatus = $permission->status;
        $userName = $permission->user->name ?? 'Unknown';
        $permissionType = $permission->type;
        $permissionDate = $permission->schedule->schedule_date ?? null;
        
        if ($request->action === 'approve') {
            $permission->update([
                'status'      => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            // Log admin permission activity
            AdminPermissionsLog::log(
                'approve',
                $permission->id,
                $permission->user_id,
                $userName,
                $permissionType,
                $permission->reason,
                $permissionDate,
                $oldStatus,
                'approved',
                ['approved_by' => Auth::id(), 'approved_at' => now()],
                "Menyetujui izin {$permissionType} dari {$userName}"
            );

            return back()->with('success', 'Izin berhasil disetujui ✅');
        }

        $permission->update([
            'status'      => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        // Log admin permission activity
        AdminPermissionsLog::log(
            'reject',
            $permission->id,
            $permission->user_id,
            $userName,
            $permissionType,
            $permission->reason,
            $permissionDate,
            $oldStatus,
            'rejected',
            ['approved_by' => Auth::id(), 'approved_at' => now()],
            "Menolak izin {$permissionType} dari {$userName}"
        );

        return back()->with('error', 'Izin ditolak ❌');
    }
}
