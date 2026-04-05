<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Schedules;
use App\Models\ScheduleSwapRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SwapRequestController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Get incoming requests (Target is me) pending my action
        $incomingRequests = ScheduleSwapRequest::with(['requester', 'requestedSchedule.shift', 'targetSchedule.shift'])
            ->where('target_user_id', $user->id)
            ->where('status', 'pending_target')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get my outgoing requests
        $outgoingRequests = ScheduleSwapRequest::with(['targetUser', 'requestedSchedule.shift', 'targetSchedule.shift'])
            ->where('requester_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Prepare schedules for the "Create Swap" dropdown (future schedules only)
        $mySchedules = Schedules::with('shift')
            ->where('user_id', $user->id)
            ->whereDate('schedule_date', '>', Carbon::today())
            ->orderBy('schedule_date', 'asc')
            ->get();

        return view('users.attendances.swap', compact('incomingRequests', 'outgoingRequests', 'mySchedules'));
    }

    public function getTargetSchedules(Request $request)
    {
        $request->validate([
            'my_schedule_id' => 'required|exists:schedules,id',
            'target_user_id' => 'required|exists:users,id'
        ]);

        $mySchedule = Schedules::findOrFail($request->my_schedule_id);

        // Fetch target user's schedules
        $targetSchedules = Schedules::with('shift')
            ->where('user_id', $request->target_user_id)
            ->whereDate('schedule_date', '>', Carbon::today())
            ->orderBy('schedule_date', 'asc')
            ->get()
            ->map(function ($schedule) {
                return [
                    'id' => $schedule->id,
                    'shift_name' => $schedule->shift->shift_name ?? '-',
                    'formatted_date' => Carbon::parse($schedule->schedule_date)->format('d M Y'),
                    'time_range' => $schedule->shift ? 
                        Carbon::parse($schedule->shift->start_time)->format('H:i') . ' - ' . 
                        Carbon::parse($schedule->shift->end_time)->format('H:i') : '-'
                ];
            });

        return response()->json(['schedules' => $targetSchedules]);
    }

    public function searchUsers(Request $request)
    {
        $search = $request->query('q');
        $users = User::whereIn('role', ['user', 'operator'])
            ->where('id', '!=', Auth::id())
            ->when($search, function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })
            ->limit(10)
            ->get(['id', 'name']);
            
        return response()->json($users);
    }

    public function store(Request $request)
    {
        $request->validate([
            'requested_schedule_id' => 'required|exists:schedules,id',
            'target_user_id' => 'required|exists:users,id',
            'target_schedule_id' => 'required|exists:schedules,id',
            'reason' => 'nullable|string|max:500'
        ]);

        $mySchedule = Schedules::findOrFail($request->requested_schedule_id);
        $targetSchedule = Schedules::findOrFail($request->target_schedule_id);

        if ($mySchedule->user_id !== Auth::id()) {
            return back()->with('error', 'Akses ditolak: Jadwal ini bukan milik Anda.');
        }

        if ($targetSchedule->user_id != $request->target_user_id) {
            return back()->with('error', 'Jadwal target tidak sesuai dengan user target.');
        }

        // Rule 1: Cannot swap past schedules
        if (Carbon::parse($mySchedule->schedule_date)->isPast() && !Carbon::parse($mySchedule->schedule_date)->isToday()) {
            return back()->with('error', 'Tidak bisa melakukan swap untuk jadwal di masa lalu.');
        }

        // Avoid duplicating pending requests for same schedules
        $existing = ScheduleSwapRequest::where(function($q) use ($mySchedule, $targetSchedule) {
                $q->where('requested_schedule_id', $mySchedule->id)
                  ->whereIn('status', ['pending_target', 'pending_admin']);
            })->orWhere(function($q) use ($mySchedule, $targetSchedule) {
                $q->where('target_schedule_id', $targetSchedule->id)
                  ->whereIn('status', ['pending_target', 'pending_admin']);
            })->first();

        if ($existing) {
            return back()->with('error', 'Salah satu jadwal sedang dalam proses swap yang belum selesai.');
        }

        ScheduleSwapRequest::create([
            'requester_id' => Auth::id(),
            'requested_schedule_id' => $mySchedule->id,
            'target_user_id' => $request->target_user_id,
            'target_schedule_id' => $targetSchedule->id,
            'reason' => $request->reason,
            'status' => 'pending_target'
        ]);

        return back()->with('success', 'Permintaan swap berhasil dikirim ke karyawan.');
    }

    public function accept(ScheduleSwapRequest $swap)
    {
        if ($swap->target_user_id !== Auth::id()) {
            return back()->with('error', 'Akses ditolak.');
        }

        if ($swap->status !== 'pending_target') {
            return back()->with('error', 'Swap request ini tidak valid untuk disetujui.');
        }

        $swap->update(['status' => 'pending_admin']);

        return back()->with('success', 'Permintaan swap diterima. Menunggu persetujuan Admin.');
    }

    public function reject(ScheduleSwapRequest $swap)
    {
        if ($swap->target_user_id !== Auth::id() && $swap->requester_id !== Auth::id()) {
            return back()->with('error', 'Akses ditolak.');
        }

        // If requester cancels their own request
        if ($swap->requester_id === Auth::id()) {
            if (!in_array($swap->status, ['pending_target', 'pending_admin'])) {
                return back()->with('error', 'Tidak bisa dibatalkan.');
            }
            $swap->update(['status' => 'canceled']);
            return back()->with('success', 'Permintaan swap dibatalkan.');
        }

        if ($swap->status !== 'pending_target') {
            return back()->with('error', 'Swap request ini tidak valid untuk ditolak.');
        }

        $swap->update(['status' => 'rejected_by_target']);

        return back()->with('success', 'Permintaan swap ditolak.');
    }
}
