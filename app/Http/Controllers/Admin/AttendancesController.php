<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Permissions;
use App\Models\Schedules;
use App\Models\AdminPermissionsLog;
use App\Models\UserActivityLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AttendancesExport;

class AttendancesController extends Controller
{
    public function index(Request $request)
{
        $today = $request->input('date', Carbon::today()->toDateString());
        $todayFormated = Carbon::parse($today)->locale('id')->translatedFormat('l, d F Y');
        $search = $request->input('search', '');
        $statusFilter = $request->input('status', '');

        // Query builder untuk schedules dengan filter search
        $schedulesQuery = Schedules::with(['user', 'shift'])
            ->whereDate('schedule_date', $today);

        // Filter berdasarkan nama karyawan jika ada search
        if (!empty($search)) {
            $schedulesQuery->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        }

        $schedulesToday = $schedulesQuery->get();
        $scheduleIds = $schedulesToday->pluck('id');

        // Query builder untuk attendances dengan filter status
        $attendancesQuery = Attendance::with(['user', 'schedule.shift', 'location'])
            ->whereIn('schedule_id', $scheduleIds);

        // Filter berdasarkan status jika dipilih
        if (!empty($statusFilter)) {
            $attendancesQuery->where('status', $statusFilter);
        }

        $attendances = $attendancesQuery->get();

        // Overnight open attendances (yesterday check-in without checkout)
        $yesterday = Carbon::parse($today)->copy()->subDay()->toDateString();
        $yesterdayScheduleIds = Schedules::whereDate('schedule_date', $yesterday)->pluck('id');
        $overnightOpenAttendances = Attendance::with(['user', 'schedule.shift', 'location'])
            ->whereIn('schedule_id', $yesterdayScheduleIds)
            ->whereNotNull('check_in_time')
            ->whereNull('check_out_time')
            ->get();

        // permissions (izin) yang terkait schedule hari ini
        $permissions = Permissions::with(['user', 'schedule'])
            ->whereIn('schedule_id', $scheduleIds)
            ->get();

        // Hitung statistik berdasarkan data yang sudah difilter
        $totalSchedules = $schedulesToday->count();
        $totalHadir = $attendances->where('status', 'hadir')->count();
        $totalTelat = $attendances->where('status', 'telat')->count();
        $totalIzin = $attendances->where('status', 'izin')->count();
        $totalEarlyCheckout = $attendances->where('status', 'early_checkout')->count();
        $totalForgotCheckout = $attendances->where('status', 'forgot_checkout')->count();
        $totalAlpha = max(0, $totalSchedules - ($totalHadir + $totalTelat + $totalIzin + $totalEarlyCheckout + $totalForgotCheckout));

        // Users for per-user export selector
        $users = User::orderBy('name')->get(['id','name']);

        return view('admin.attendances.index', compact(
            'today',
            'todayFormated',
            'schedulesToday',
            'attendances',
            'permissions',
            'totalSchedules',
            'totalHadir',
            'totalTelat',
            'totalIzin',
            'totalAlpha',
            'search',
            'statusFilter',
            'users',
            // expose for future UI usage
            'totalEarlyCheckout',
            'totalForgotCheckout',
            'overnightOpenAttendances'
        ));
    }

    /**
     * Approve permission -> set permission status dan pastikan attendance.status = 'izin'
     */
    public function approvePermission(Permissions $permission)
    {
        // Validasi permission masih pending
        if ($permission->status !== 'pending') {
            return back()->with('error', 'Izin ini sudah diproses sebelumnya.');
        }

        $oldStatus = $permission->status;
        $userName = $permission->user ? $permission->user->name : 'Unknown';
        $permissionType = $permission->type;
        $permissionDate = $permission->schedule ? $permission->schedule->schedule_date : null;
        
        $permission->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        // Update attendance based on permission type
        $isEarlyCheckout = ($permission->type === 'izin') && (strpos((string)$permission->reason, '[EARLY_CHECKOUT]') === 0);
        if ($isEarlyCheckout) {
            // Multi-shift support: checkout all attendances on the same date
            $scheduleDate = optional($permission->schedule)?->schedule_date;
            $requested = Carbon::parse($permission->created_at);

            if ($scheduleDate) {
                // Get all same-day schedules for the user
                $sameDayScheduleIds = Schedules::where('user_id', $permission->user_id)
                    ->whereDate('schedule_date', $scheduleDate)
                    ->pluck('id');

                // Update all open attendances (checked-in, not checked-out)
                $openAttendances = Attendance::whereIn('schedule_id', $sameDayScheduleIds)
                    ->where('user_id', $permission->user_id)
                    ->whereNotNull('check_in_time')
                    ->whereNull('check_out_time')
                    ->get();

                $affected = 0;
                foreach ($openAttendances as $att) {
                    // Clamp to not precede check-in
                    $checkoutTime = $requested;
                    if ($att->check_in_time && $checkoutTime->lt(Carbon::parse($att->check_in_time))) {
                        $checkoutTime = Carbon::parse($att->check_in_time);
                    }
                    $att->update([
                        'check_out_time' => $checkoutTime,
                        'status' => 'early_checkout',
                    ]);
                    $affected++;
                }

                // Fallback: also update single attendance if needed
                $attendance = Attendance::where('user_id', $permission->user_id)
                    ->where('schedule_id', $permission->schedule_id)
                    ->first();
                if ($attendance && !$attendance->check_out_time) {
                    $checkoutTime = $requested;
                    if ($attendance->check_in_time && $checkoutTime->lt(Carbon::parse($attendance->check_in_time))) {
                        $checkoutTime = Carbon::parse($attendance->check_in_time);
                    }
                    $attendance->update([
                        'check_out_time' => $checkoutTime,
                        'status' => 'early_checkout',
                    ]);
                }
            }
        } else {
            // Default behavior for izin/cuti/sakit: set attendance to izin
            Attendance::updateOrCreate(
                [
                    'user_id' => $permission->user_id,
                    'schedule_id' => $permission->schedule_id,
                ],
                [
                    'status' => 'izin',
                    'is_late' => false,
                    'late_minutes' => 0,
                    'check_in_time' => null,
                    'check_out_time' => null,
                    'latitude' => null,
                    'longitude' => null,
                    'latitude_checkout' => null,
                    'longitude_checkout' => null,
                ]
            );
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
            [
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'attendance_updated' => true,
                'requested_checkout_time' => optional($permission->created_at)?->toDateTimeString(),
                // Optional: number of attendances affected for early checkout
                'affected_attendances_same_day' => isset($affected) ? $affected : null,
            ],
            "Menyetujui izin {$permissionType} dari {$userName} dan memperbarui kehadiran"
        );

        return back()->with('success', 'Izin telah disetujui dan status kehadiran diperbarui.');
    }

    /**
     * Reject permission -> set permission status, dan kembalikan attendance jadi 'alpha'
     * (jika belum ada check in)
     */
    public function rejectPermission(Permissions $permission)
    {
        // Validasi permission masih pending
        if ($permission->status !== 'pending') {
            return back()->with('error', 'Izin ini sudah diproses sebelumnya.');
        }

        $oldStatus = $permission->status;
        $userName = $permission->user ? $permission->user->name : 'Unknown';
        $permissionType = $permission->type;
        $permissionDate = $permission->schedule ? $permission->schedule->schedule_date : null;
        
        $permission->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        $isEarlyCheckout = ($permission->type === 'izin') && (strpos((string)$permission->reason, '[EARLY_CHECKOUT]') === 0);
        $attendance = Attendance::where('user_id', $permission->user_id)
            ->where('schedule_id', $permission->schedule_id)
            ->first();

        if ($isEarlyCheckout) {
            // Early checkout rejected: jangan reset attendance. User tetap check-in dan bisa lanjut kerja.
            $attendanceAction = 'kept_open_for_continue';
        } else {
            $attendanceAction = 'updated';
            if ($attendance) {
                // Permission selain early checkout: reset ke alpha agar user bisa check-in normal
                $attendance->update([
                    'status' => 'alpha',
                    'is_late' => false,
                    'late_minutes' => 0,
                    'check_in_time' => null,
                    'check_out_time' => null,
                    'latitude' => null,
                    'longitude' => null,
                    'latitude_checkout' => null,
                    'longitude_checkout' => null,
                ]);
            } else {
                $attendanceAction = 'cleared - user can now check-in';
            }
        }

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
            ['approved_by' => Auth::id(), 'approved_at' => now(), 'attendance_action' => $attendanceAction],
            "Menolak izin {$permissionType} dari {$userName} dan memperbarui kehadiran ke alpha"
        );

        return back()->with('success', 'Izin telah ditolak dan status kehadiran diperbarui.');
    }

    public function history(Request $request)
    {
        // Ambil tanggal dari request, default hari ini
        $date = $request->input('date', now()->toDateString());
        
        // Ambil search parameter
        $search = $request->input('search', '');

        // Query builder untuk jadwal dengan relasi user dan shift
        $schedulesQuery = \App\Models\Schedules::with(['user', 'shift'])
            ->whereDate('schedule_date', $date);

        // Jika ada search, filter berdasarkan nama user
        if (!empty($search)) {
            $schedulesQuery->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        }

        $schedules = $schedulesQuery->get();

        // Ambil absensi berdasarkan schedule_id yang sudah difilter
        $scheduleIds = $schedules->pluck('id');
        $attendances = \App\Models\Attendance::with(['schedule.shift', 'location'])
            ->whereIn('schedule_id', $scheduleIds)
            ->get();

        // Ambil izin berdasarkan schedule_id yang sudah difilter
        $permissions = \App\Models\Permissions::with(['schedule'])
            ->whereIn('schedule_id', $scheduleIds)
            ->get();

        return view('admin.attendances.history', compact('attendances', 'permissions', 'schedules', 'date', 'search'));
    }

    public function show($userId)
    {
        // implementasi show per user bila perlu
        $userAttendances = Attendance::with('schedule.shift')->where('user_id', $userId)->get();
        return view('admin.attendances.show', compact('userAttendances'));
    }

    public function destroy(Attendance $attendance)
    {
        $attendanceData = $attendance->toArray();
        $user = $attendance->user;
        $schedule = $attendance->schedule;
        $shift = $schedule->shift ? $schedule->shift : null;
        
        $attendance->delete();

        // Log admin activity for deleting attendance
        UserActivityLog::log(
            'delete_attendance',
            'attendances',
            null,
            "Kehadiran {$user->name} - " . ($shift ? $shift->shift_name : 'Unknown'),
            [
                'deleted_by_admin' => Auth::id(),
                'original_status' => $attendanceData['status'],
                'schedule_date' => $schedule->schedule_date,
                'deleted_data' => $attendanceData
            ],
            "Admin menghapus data kehadiran {$user->name} pada {$schedule->schedule_date}"
        );
        
        return back()->with('success', 'Attendance deleted');
    }

    /**
     * Export attendances for a given month (required) and year (required)
     */
    public function exportMonthly(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000|max:' . now()->year,
        ]);

        $month = (int) $request->input('month');
        $year = (int) $request->input('year');

        $filename = sprintf('absensi-bulanan-%04d-%02d.xlsx', $year, $month);
        return Excel::download(new AttendancesExport('monthly', $month, $year, null), $filename);
    }

    /**
     * Export attendances for a given year (required)
     */
    public function exportYearly(Request $request)
    {
        $request->validate([
            'year' => 'required|integer|min:2000|max:' . now()->year,
        ]);

        $year = (int) $request->input('year');
        $filename = sprintf('absensi-tahunan-%04d.xlsx', $year);
        return Excel::download(new AttendancesExport('yearly', null, $year, null), $filename);
    }

    /**
     * Export attendances per user with optional month/year filters
     */
    public function exportPerUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'month' => 'nullable|integer|min:1|max:12',
            'year' => 'nullable|integer|min:2000|max:' . now()->year,
        ]);

        $userId = (int) $request->input('user_id');
        $month = $request->filled('month') ? (int) $request->input('month') : null;
        $year = $request->filled('year') ? (int) $request->input('year') : null;

        $userName = optional(User::find($userId))->name ?? 'user';
        $suffix = [];
        if ($year) { $suffix[] = $year; }
        if ($month) { $suffix[] = sprintf('%02d', $month); }
        $suffixStr = $suffix ? ('-' . implode('-', $suffix)) : '';

        $safeUser = str_replace([' ', '/','\\',':'], '-', strtolower($userName));
        $filename = sprintf('absensi-%s%s.xlsx', $safeUser, $suffixStr);

        return Excel::download(new AttendancesExport('user', $month, $year, $userId), $filename);
    }

    /**
     * Export all attendances data (all users, all time)
     */
    public function exportAll(Request $request)
    {
        $filename = 'absensi-seluruh-data-' . now()->format('Y-m-d') . '.xlsx';
        return Excel::download(new AttendancesExport('all', null, null, null), $filename);
    }

    /**
     * Display leave requests management page
     */
    public function leaveRequests(Request $request)
    {
        $statusFilter = $request->input('status');
        
        // Group permissions by user and reason to get leave requests
        $query = DB::table('permissions')
            ->select([
                'user_id',
                'reason',
                'type',
                'status',
                'created_at',
                DB::raw('COUNT(*) as schedules_count'),
                DB::raw('MIN(id) as first_permission_id'),
                DB::raw('GROUP_CONCAT(id) as permission_ids')
            ])
            ->where('type', 'cuti')
            ->groupBy(['user_id', 'reason', 'type', 'status', 'created_at'])
            ->orderBy('created_at', 'desc');

        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }

        $leaveRequestsData = $query->paginate(15);

        // Transform the data to include user information and date ranges
        $leaveRequests = $leaveRequestsData->through(function ($item) {
            $user = User::find($item->user_id);
            $permissionIds = explode(',', $item->permission_ids);
            
            // Get date range for this leave request
            $permissions = Permissions::with('schedule')
                ->whereIn('id', $permissionIds)
                ->get();
            
            $dates = $permissions->pluck('schedule.schedule_date')->sort();
            $dateRange = $dates->count() > 1 
                ? $dates->first() . ' - ' . $dates->last()
                : $dates->first();

            return (object) [
                'id' => $item->first_permission_id,
                'user' => $user,
                'reason' => $item->reason,
                'status' => $item->status,
                'schedules_count' => $item->schedules_count,
                'date_range' => $dateRange,
                'created_at' => Carbon::parse($item->created_at),
                'permission_ids' => $permissionIds
            ];
        });

        return view('admin.attendances.leave-requests', compact('leaveRequests'));
    }

    /**
     * Show leave request details
     */
    public function showLeaveRequest($id)
    {
        $permission = Permissions::with(['user', 'schedule.shift'])->findOrFail($id);
        
        // Get all permissions with same user, reason, and created_at (same leave request)
        $permissions = Permissions::with(['schedule.shift'])
            ->where('user_id', $permission->user_id)
            ->where('reason', $permission->reason)
            ->where('type', 'cuti')
            ->whereDate('created_at', $permission->created_at->toDateString())
            ->orderBy('schedule_id')
            ->get();

        $leaveRequest = (object) [
            'id' => $id,
            'user' => $permission->user,
            'reason' => $permission->reason,
            'status' => $permission->status,
            'created_at' => $permission->created_at
        ];

        return view('admin.attendances.leave-request-detail', compact('leaveRequest', 'permissions'));
    }

    /**
     * Process leave request schedules (approve/reject selected schedules)
     */
    public function processLeaveRequestSchedules(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'approved_permissions' => 'nullable|array',
            'approved_permissions.*' => 'exists:permissions,id'
        ]);

        $permission = Permissions::findOrFail($id);
        $action = $request->input('action');
        
        // Get all permissions for this leave request
        $allPermissions = Permissions::where('user_id', $permission->user_id)
            ->where('reason', $permission->reason)
            ->where('type', 'cuti')
            ->whereDate('created_at', $permission->created_at->toDateString())
            ->get();

        DB::beginTransaction();
        
        try {
            if ($action === 'approve') {
                $approvedIds = $request->input('approved_permissions', []);
                
                // Approve selected permissions
                foreach ($allPermissions as $perm) {
                    $newStatus = in_array($perm->id, $approvedIds) ? 'approved' : 'rejected';
                    $oldStatus = $perm->status;
                $perm->update(['status' => $newStatus]);
                    
                    // Log admin action with detailed fields
                    AdminPermissionsLog::log(
                        action: $newStatus === 'approved' ? 'approve' : 'reject',
                        permissionId: $perm->id,
                        targetUserId: $perm->user_id,
                        targetUserName: optional($perm->user)->name,
                        permissionType: $perm->type,
                        permissionReason: $perm->reason,
                        permissionDate: optional($perm->schedule)->schedule_date,
                        oldStatus: $oldStatus,
                        newStatus: $newStatus,
                        additionalData: [
                            'schedule_id' => $perm->schedule_id,
                            'affected_attendance' => $newStatus === 'approved' ? 'set_izin' : 'reset_alpha',
                        ],
                        description: sprintf(
                            '%s %s for %s (%s) on %s',
                            $newStatus === 'approved' ? 'Approved' : 'Rejected',
                            strtoupper($perm->type),
                            optional($perm->user)->name,
                            $perm->reason,
                            (optional($perm->schedule)->schedule_date ? \Carbon\Carbon::parse(optional($perm->schedule)->schedule_date)->format('d M Y') : '-')
                        )
                    );

                    // Handle attendance based on status
                    if ($newStatus === 'approved') {
                        // Set attendance to izin
                        Attendance::updateOrCreate(
                            [
                                'user_id' => $perm->user_id,
                                'schedule_id' => $perm->schedule_id,
                            ],
                            [
                                'status' => 'izin',
                                'is_late' => false,
                                'late_minutes' => 0,
                                'check_in_time' => null,
                                'check_out_time' => null,
                                'latitude' => null,
                                'longitude' => null,
                                'latitude_checkout' => null,
                                'longitude_checkout' => null,
                            ]
                        );
                    } else {
                        // Reset attendance for rejected permission
                        $attendance = Attendance::where('user_id', $perm->user_id)
                            ->where('schedule_id', $perm->schedule_id)
                            ->first();

                        if ($attendance) {
                            $attendance->update([
                                'status' => 'alpha',
                                'check_in_time' => null,
                                'check_out_time' => null,
                                'latitude' => null,
                                'longitude' => null,
                                'latitude_checkout' => null,
                                'longitude_checkout' => null,
                            ]);
                        }
                    }
                }
                
                $approvedCount = count($approvedIds);
                $rejectedCount = $allPermissions->count() - $approvedCount;
                
                $message = "Leave request processed: {$approvedCount} schedules approved";
                if ($rejectedCount > 0) {
                    $message .= ", {$rejectedCount} schedules rejected";
                }
                
            } else { // reject all
                foreach ($allPermissions as $perm) {
                    $perm->update(['status' => 'rejected']);
                    
                    AdminPermissionsLog::log(
                        action: 'reject',
                        permissionId: $perm->id,
                        targetUserId: $perm->user_id,
                        targetUserName: optional($perm->user)->name,
                        permissionType: $perm->type,
                        permissionReason: $perm->reason,
                        permissionDate: optional($perm->schedule)->schedule_date,
                        oldStatus: $perm->getOriginal('status'),
                        newStatus: 'rejected',
                        additionalData: [
                            'schedule_id' => $perm->schedule_id,
                            'affected_attendance' => 'reset_alpha',
                            'scope' => 'reject_all_in_request'
                        ],
                        description: sprintf(
                            'Rejected %s for %s (%s) on %s',
                            strtoupper($perm->type),
                            optional($perm->user)->name,
                            $perm->reason,
                            (optional($perm->schedule)->schedule_date ? \Carbon\Carbon::parse(optional($perm->schedule)->schedule_date)->format('d M Y') : '-')
                        )
                    );

                    // Reset attendance for rejected permission
                    $attendance = Attendance::where('user_id', $perm->user_id)
                        ->where('schedule_id', $perm->schedule_id)
                        ->first();

                    if ($attendance) {
                        $attendance->update([
                            'status' => 'alpha',
                            'check_in_time' => null,
                            'check_out_time' => null,
                            'latitude' => null,
                            'longitude' => null,
                            'latitude_checkout' => null,
                            'longitude_checkout' => null,
                        ]);
                    }
                }
                
                $message = "Entire leave request rejected ({$allPermissions->count()} schedules)";
            }

            DB::commit();
            
            return redirect()->route('admin.attendances.leave-requests')
                ->with('success', $message);
                
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to process leave request: ' . $e->getMessage());
        }
    }

    /**
     * Process leave request with simple approve/reject all
     */
    public function processLeaveRequest(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:approve,reject'
        ]);

        $permission = Permissions::findOrFail($id);
        $action = $request->input('action');
        
        // Get all permissions for this leave request (same user, reason, date)
        $allPermissions = Permissions::where('user_id', $permission->user_id)
            ->where('reason', $permission->reason)
            ->where('type', 'cuti')
            ->whereDate('created_at', $permission->created_at->toDateString())
            ->get();

        DB::beginTransaction();
        
        try {
            $newStatus = $action === 'approve' ? 'approved' : 'rejected';
            
            foreach ($allPermissions as $perm) {
                $oldStatus = $perm->status;
                $perm->update(['status' => $newStatus]);
                
                // Log admin action with detailed fields
                AdminPermissionsLog::log(
                    action: $newStatus === 'approved' ? 'approve' : 'reject',
                    permissionId: $perm->id,
                    targetUserId: $perm->user_id,
                    targetUserName: optional($perm->user)->name,
                    permissionType: $perm->type,
                    permissionReason: $perm->reason,
                    permissionDate: optional($perm->schedule)->schedule_date,
                    oldStatus: $oldStatus,
                    newStatus: $newStatus,
                    additionalData: [
                        'schedule_id' => $perm->schedule_id,
                        'affected_attendance' => $newStatus === 'approved' ? 'set_izin' : 'reset_alpha'
                    ],
                    description: sprintf(
                        '%s %s for %s (%s) on %s',
                        $newStatus === 'approved' ? 'Approved' : 'Rejected',
                        strtoupper($perm->type),
                        optional($perm->user)->name,
                        $perm->reason,
                        (optional($perm->schedule)->schedule_date ? \Carbon\Carbon::parse(optional($perm->schedule)->schedule_date)->format('d M Y') : '-')
                    )
                );

                // Handle attendance based on status
                if ($newStatus === 'approved') {
                    // Set attendance to izin
                    Attendance::updateOrCreate(
                        [
                            'user_id' => $perm->user_id,
                            'schedule_id' => $perm->schedule_id,
                        ],
                        [
                            'status' => 'izin',
                            'is_late' => false,
                            'late_minutes' => 0,
                            'check_in_time' => null,
                            'check_out_time' => null,
                            'latitude' => null,
                            'longitude' => null,
                            'latitude_checkout' => null,
                            'longitude_checkout' => null,
                        ]
                    );
                } else {
                    // Reset attendance for rejected permission
                    $attendance = Attendance::where('user_id', $perm->user_id)
                        ->where('schedule_id', $perm->schedule_id)
                        ->first();

                    if ($attendance) {
                        $attendance->update([
                            'status' => 'alpha',
                            'check_in_time' => null,
                            'check_out_time' => null,
                            'latitude' => null,
                            'longitude' => null,
                            'latitude_checkout' => null,
                            'longitude_checkout' => null,
                        ]);
                    }
                }
            }

            DB::commit();
            
            $message = $action === 'approve' 
                ? "Leave request approved ({$allPermissions->count()} schedules)"
                : "Leave request rejected ({$allPermissions->count()} schedules)";

            return response()->json(['success' => true, 'message' => $message]);
                
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => 'Failed to process leave request: ' . $e->getMessage()], 500);
        }
    }

}
