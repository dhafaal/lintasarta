<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Permissions;
use App\Models\Schedules;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermissionController extends Controller
{
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
            'schedule_ids' => 'required|array|min:1',
            'schedule_ids.*' => 'exists:schedules,id',
            'type' => 'required|in:cuti',
            'reason' => 'required|string|min:10|max:500',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ], [
            'schedule_ids.max' => 'Maksimal 12 hari cuti yang bisa diajukan dalam satu permintaan.',
        ]);

        $user = Auth::user();
        $scheduleIds = $request->schedule_ids;
        $createdPermissions = [];

        // Validate that all schedules belong to the user and don't have existing permissions
        $schedules = Schedules::whereIn('id', $scheduleIds)
            ->where('user_id', $user->id)
            ->whereDate('schedule_date', '>=', now()->toDateString())
            ->get();

        if ($schedules->count() !== count($scheduleIds)) {
            return back()->with('error', 'Beberapa jadwal tidak valid atau sudah lewat.');
        }

        // Check for existing permissions
        foreach ($schedules as $schedule) {
            $existingPermission = Permissions::where('user_id', $user->id)
                ->whereHas('schedule', function($q) use ($schedule) {
                    $q->whereDate('schedule_date', $schedule->schedule_date);
                })
                ->first();

            if ($existingPermission) {
                return back()->with('error', "Anda sudah memiliki pengajuan untuk tanggal {$schedule->schedule_date}.");
            }
        }

        $schedulesByYear = $schedules->groupBy(function ($schedule) {
            return date('Y', strtotime($schedule->schedule_date));
        });

        foreach ($schedulesByYear as $year => $yearSchedules) {
            $usedDays = Permissions::where('user_id', $user->id)
                ->where('type', 'cuti')
                ->whereIn('status', ['pending', 'approved'])
                ->whereHas('schedule', function ($q) use ($year) {
                    $q->whereYear('schedule_date', $year);
                })
                ->count();

            $newDays = $yearSchedules->count();

            if ($usedDays + $newDays > 12) {
                $remaining = max(0, 12 - $usedDays);

                if ($remaining <= 0) {
                    return back()->with('error', "Jatah cuti Anda untuk tahun {$year} sudah habis (12 hari).");
                }

                return back()->with('error', "Pengajuan ini melebihi sisa jatah cuti tahun {$year}. Sisa jatah: {$remaining} hari.");
            }
        }

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('permissions', 'public');
        }

        // Create permissions for each schedule
        foreach ($schedules as $schedule) {
            $permission = Permissions::create([
                'user_id' => $user->id,
                'schedule_id' => $schedule->id,
                'type' => $request->type,
                'reason' => $request->reason,
                'file' => $filePath,
                'status' => 'pending'
            ]);

            $createdPermissions[] = $permission;

            // Log user activity
            UserActivityLog::log(
                'request_leave',
                'permissions',
                $permission->id,
                "Cuti - {$schedule->schedule_date}",
                [
                    'schedule_id' => $schedule->id,
                    'type' => $request->type,
                    'reason' => $request->reason,
                    'schedule_date' => $schedule->schedule_date
                ],
                "Mengajukan cuti untuk tanggal {$schedule->schedule_date}"
            );
        }

        $scheduleCount = count($createdPermissions);
        $dateRange = $schedules->min('schedule_date') === $schedules->max('schedule_date') 
            ? $schedules->first()->schedule_date
            : $schedules->min('schedule_date') . ' - ' . $schedules->max('schedule_date');

        return back()->with('success', "Pengajuan cuti untuk {$scheduleCount} jadwal ({$dateRange}) berhasil dikirim dan menunggu persetujuan.");
    }
}
