<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Permissions;
use App\Models\Schedules;
use App\Models\UserActivityLog;
use App\Services\LeaveService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermissionController extends Controller
{
    protected $leaveService;

    public function __construct(LeaveService $leaveService)
    {
        $this->leaveService = $leaveService;
    }

    public function index()
    {
        $permissions = Permissions::with(['schedule.shift', 'user'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('users.attendances.permissions', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'type' => 'required|in:izin,sakit,cuti',
            'reason' => 'required|string|min:10|max:255',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
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

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('permissions', 'public');
        }

        $permission = Permissions::create([
            'user_id' => Auth::id(),
            'schedule_id' => $request->schedule_id,
            'type' => $request->type,
            'reason' => $request->reason,
            'file' => $filePath,
            'status' => 'pending'
        ]);

        // Log user activity
        UserActivityLog::log(
            'request_permission',
            'permissions',
            $permission->id,
            "Izin {$request->type} - {$schedule->schedule_date}",
            [
                'schedule_id' => $request->schedule_id,
                'type' => $request->type,
                'reason' => $request->reason,
                'schedule_date' => $schedule->schedule_date
            ],
            "Mengajukan izin {$request->type} untuk tanggal {$schedule->schedule_date}"
        );

        return back()->with('success', 'Pengajuan izin berhasil dikirim dan menunggu persetujuan.');
    }

    public function storeLeave(Request $request)
    {
        $request->validate([
            'schedule_ids' => 'required|array|min:1|max:12',
            'schedule_ids.*' => 'exists:schedules,id',
            'type' => 'required|in:cuti',
            'reason' => 'required|string|min:10|max:500',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ], [
            'schedule_ids.max' => 'Maksimal 12 hari cuti yang bisa diajukan dalam satu permintaan.',
        ]);

        $user = Auth::user();

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('permissions', 'public');
        }

        try {
            $createdPermissions = $this->leaveService->submitLeaveRequest([
                'user' => $user,
                'schedule_ids' => $request->schedule_ids,
                'reason' => $request->reason,
                'file_path' => $filePath,
            ]);

            $scheduleCount = count($createdPermissions);
            $schedules = Schedules::whereIn('id', $request->schedule_ids)->get();
            $dateRange = $schedules->min('schedule_date') === $schedules->max('schedule_date') 
                ? $schedules->first()->schedule_date
                : $schedules->min('schedule_date') . ' - ' . $schedules->max('schedule_date');

            return back()->with('success', "Pengajuan cuti untuk {$scheduleCount} jadwal ({$dateRange}) berhasil dikirim dan menunggu persetujuan.");
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}