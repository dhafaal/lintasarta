<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminSchedulesLog;
use App\Models\ScheduleSwapRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SwapRequestController extends Controller
{
    public function index()
    {
        $requests = ScheduleSwapRequest::with(['requester', 'targetUser', 'requestedSchedule.shift', 'targetSchedule.shift'])
            ->orderByRaw("FIELD(status, 'pending_admin') DESC")
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.attendances.swap', compact('requests'));
    }

    public function approve(Request $request, ScheduleSwapRequest $swap)
    {
        $request->validate([
            'admin_note' => 'nullable|string|max:500'
        ]);
        if ($swap->status !== 'pending_admin') {
            return back()->with('error', 'Status request tidak valid untuk di-approve.');
        }

        try {
            DB::transaction(function () use ($swap, $request) {
                $schedule1 = $swap->requestedSchedule;
                $schedule2 = $swap->targetSchedule;

                // Extra safety check in case schedule dates changed magically to past or it's currently today
                $today = Carbon::today();
                if (Carbon::parse($schedule1->schedule_date)->startOfDay()->lte($today) || 
                    Carbon::parse($schedule2->schedule_date)->startOfDay()->lte($today)) {
                    throw new \Exception('Tidak bisa menyetujui swap untuk jadwal hari ini atau masa lalu. Swap telah kadaluarsa.');
                }

                $originalUser1Id = $schedule1->user_id;
                $originalUser2Id = $schedule2->user_id;

                $oldValues1 = $schedule1->toArray();
                $oldValues2 = $schedule2->toArray();

                // Swap
                $schedule1->update(['user_id' => $originalUser2Id]);
                $schedule2->update(['user_id' => $originalUser1Id]);

                // Update Request status
                $swap->update([
                    'status' => 'approved',
                    'admin_id' => Auth::id(),
                    'admin_note' => null
                ]);

                // Log the swap for both schedules
                AdminSchedulesLog::log(
                    'update',
                    $schedule1->id,
                    $swap->targetUser->id,
                    $swap->targetUser->name,
                    $schedule1->shift->id,
                    $schedule1->shift->shift_name,
                    $schedule1->schedule_date,
                    $oldValues1,
                    $schedule1->fresh()->toArray(),
                    "Menukar jadwal via Swap Request: {$swap->requester->name} → {$swap->targetUser->name} pada {$schedule1->schedule_date}"
                );
                
                AdminSchedulesLog::log(
                    'update',
                    $schedule2->id,
                    $swap->requester->id,
                    $swap->requester->name,
                    $schedule2->shift->id,
                    $schedule2->shift->shift_name,
                    $schedule2->schedule_date,
                    $oldValues2,
                    $schedule2->fresh()->toArray(),
                    "Menukar jadwal via Swap Request: {$swap->targetUser->name} → {$swap->requester->name} pada {$schedule2->schedule_date}"
                );
            });

            return back()->with('success', 'Swap berhasil disetujui dan jadwal telah ditukar.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses swap: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, ScheduleSwapRequest $swap)
    {
        $request->validate([
            'admin_note' => 'required|string|max:500'
        ], [
            'admin_note.required' => 'Catatan penolakan (Admin Note) wajib diisi.'
        ]);

        if ($swap->status !== 'pending_admin') {
            return back()->with('error', 'Status request tidak valid untuk direject.');
        }

        $swap->update([
            'status' => 'rejected_by_admin',
            'admin_id' => Auth::id(),
            'admin_note' => $request->admin_note
        ]);

        return back()->with('success', 'Swap request berhasil ditolak.');
    }
}
