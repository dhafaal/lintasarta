<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permissions;
use App\Models\AdminPermissionsLog;
use App\Services\LeaveService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PermissionController extends Controller
{
    protected $leaveService;

    public function __construct(LeaveService $leaveService)
    {
        $this->leaveService = $leaveService;
    }

    public function approve(Request $request, Permissions $permission)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'admin_note' => 'required|string|min:5|max:500',
        ], [
            'admin_note.required' => 'Catatan wajib diisi.',
            'admin_note.min'      => 'Catatan minimal 5 karakter.',
        ]);

        $oldStatus = $permission->status;
        $userName = $permission->user->name ?? 'Unknown';
        $permissionType = $permission->type;
        $permissionDate = $permission->schedule->schedule_date ?? null;
        
        if ($request->action === 'approve') {
            try {
                if ($permissionType === 'cuti') {
                    $this->leaveService->approveLeaveRequest($permission->id, Auth::id());
                } else {
                    $permission->update([
                        'status'      => 'approved',
                        'approved_by' => Auth::id(),
                        'approved_at' => now(),
                        'admin_note'  => $request->admin_note,
                    ]);
                }
            } catch (Exception $e) {
                return back()->with('error', $e->getMessage());
            }

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
            'admin_note'  => $request->admin_note,
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

    public function downloadAttachment(Permissions $permission)
    {
        if (!$permission->file) {
            abort(404);
        }

        $path = $permission->file;

        if (!Storage::disk('public')->exists($path)) {
            abort(404);
        }

        return Storage::disk('public')->response($path);
    }
}