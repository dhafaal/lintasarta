<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\User;
use App\Models\Shift;
use App\Models\Schedules;
use App\Models\Attendance;
use App\Models\Permissions;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Get selected month and year or default to current
        $selectedMonth = $request->input('selected_month', Carbon::now()->month);
        $selectedYear = $request->input('selected_year', Carbon::now()->year);
        
        // Create date from selected month and year
        $monthDate = Carbon::create($selectedYear, $selectedMonth, 1);
        
        // Get month data for chart
        $startOfMonth = $monthDate->copy()->startOfMonth();
        $endOfMonth = $monthDate->copy()->endOfMonth();
        
        // Get daily attendance data for current month
        $attendanceData = [];
        $dates = [];
        
        for ($date = $startOfMonth->copy(); $date <= $endOfMonth; $date->addDay()) {
            $dateString = $date->format('Y-m-d');
            $dates[] = $date->format('d');

            $stats = $this->aggregateDailyByUser($dateString);

            $attendanceData[] = [
                'date'  => $dateString,
                'hadir' => $stats['hadir'],
                'telat' => $stats['telat'],
                'izin'  => $stats['izin'],
                'alpha' => $stats['alpha'],
            ];
        }
        
        // Get today's attendance summary
        $today = Carbon::today();
        $todayStats = $this->aggregateDailyByUser($today->format('Y-m-d'));
        $todaySchedules = $todayStats['total'];
        $todayHadir = $todayStats['hadir'];
        $todayTelat = $todayStats['telat'];
        $todayIzin = $todayStats['izin'];
        $todayAlpha = $todayStats['alpha'];
        // Keep EC/FC as zero here since cards not shown in this view
        $todayEarlyCheckout = 0;
        $todayForgotCheckout = 0;

        return view('admin.dashboard', [
            'totalUsers'          => User::where('role', '!=', 'Admin')->count(),
            'totalShifts'         => Shift::count(),
            'totalSchedules'      => Schedules::count(),
            'attendanceData'      => $attendanceData,
            'chartDates'          => $dates,
            'todaySchedules'      => $todaySchedules,
            'todayHadir'          => $todayHadir,
            'todayTelat'          => $todayTelat,
            'todayIzin'           => $todayIzin,
            'todayAlpha'          => $todayAlpha,
            'todayEarlyCheckout'  => $todayEarlyCheckout,
            'todayForgotCheckout' => $todayForgotCheckout,
            'currentMonth'        => $monthDate->format('F Y'),
            'selectedMonth'       => $selectedMonth,
            'selectedYear'        => $selectedYear
        ]);
    }

    /**
     * Aggregate daily stats by user so double/long shifts count as ONE schedule.
     * Status derived from earliest shift's attendance (is_late) unless has approved permission.
     * Returns: ['total','hadir','telat','izin','alpha']
     */
    private function aggregateDailyByUser(string $dateString): array
    {
        $schedulesByUser = Schedules::with('shift')
            ->whereDate('schedule_date', $dateString)
            ->get()
            ->groupBy('user_id');

        $total = 0; $hadir = 0; $telat = 0; $izin = 0; $alpha = 0;

        foreach ($schedulesByUser as $userId => $userSchedules) {
            $total++;
            $scheduleIds = $userSchedules->pluck('id');

            // Permission takes precedence
            $hasApprovedPermission = Permissions::whereIn('schedule_id', $scheduleIds)
                ->where('status', 'approved')
                ->exists();
            if ($hasApprovedPermission) {
                $izin++;
                continue;
            }

            $atts = Attendance::whereIn('schedule_id', $scheduleIds)->get();
            if ($atts->isEmpty()) {
                $alpha++;
                continue;
            }

            // Determine earliest shift for the day
            $earliestSchedule = $userSchedules->sortBy(function($s){
                return optional($s->shift)->shift_start ?? '23:59:59';
            })->first();

            $refAttendance = $atts->firstWhere('schedule_id', optional($earliestSchedule)->id);
            if (!$refAttendance) {
                $refAttendance = $atts->sortBy('check_in_time')->first();
            }

            if ($refAttendance) {
                if ((int)$refAttendance->is_late === 1) { $telat++; } else { $hadir++; }
            } else {
                $alpha++;
            }
        }

        return compact('total','hadir','telat','izin','alpha');
    }

    public function getTodayAttendanceDetails(Request $request)
    {
        $status = $request->input('status');
        $today = Carbon::today();

        // Load all schedules for today with relations
        $schedules = Schedules::with(['user', 'shift', 'attendance'])
            ->whereDate('schedule_date', $today)
            ->get()
            ->groupBy('user_id');

        // Build per-user summary (earliest category, combined times)
        $users = [];
        foreach ($schedules as $userId => $userSchedules) {
            $user = optional($userSchedules->first())->user;
            if (!$user) { continue; }

            // Determine earliest schedule by shift_start
            $sorted = $userSchedules->sortBy(function($s){ return optional($s->shift)->shift_start ?? '23:59:59'; });
            $earliest = $sorted->first();
            $primaryCategory = optional($earliest->shift)->category ?? 'Unknown';
            $primaryShiftName = optional($earliest->shift)->shift_name ?? '';
            $primaryStart = optional($earliest->shift)->shift_start ?? null;
            $primaryEnd = optional($earliest->shift)->shift_end ?? null;

            // Combine attendance across schedules for this user
            $attendances = $userSchedules->map(function($s){ return $s->attendance; })->filter();
            $checkIn = $attendances->pluck('check_in_time')->filter()->sort()->first();
            $checkOut = $attendances->pluck('check_out_time')->filter()->sort()->last();

            // Flags & permissions
            $permission = Permissions::whereIn('schedule_id', $userSchedules->pluck('id'))
                ->where('status', 'approved')
                ->first();
            $hasApprovedPermission = (bool) $permission;
            $permissionType = $permission ? $permission->type : null;
            $permissionFile = $permission ? $permission->file : null;
            $permissionId = $permission ? $permission->id : null;
            $hasEarly = $attendances->first(function($a){ return optional($a)->status === 'early_checkout'; }) ? true : false;
            $hasForgot = $attendances->first(function($a){ return optional($a)->status === 'forgot_checkout'; }) ? true : false;

            // Determine status based on requested rule (permission > forgot > early > hadir/telat > alpha)
            $actualStatus = 'alpha';
            if ($hasApprovedPermission) {
                $actualStatus = 'izin';
            } elseif ($hasForgot) {
                $actualStatus = 'forgot_checkout';
            } elseif ($hasEarly) {
                // Early checkout: still categorize as hadir/telat based on is_late
                $earliestAttendance = optional($earliest)->attendance;
                if ($earliestAttendance) {
                    $actualStatus = ((int)$earliestAttendance->is_late === 1) ? 'telat' : 'hadir';
                }
            } else {
                // Use earliest schedule's attendance is_late
                $earliestAttendance = optional($earliest)->attendance;
                if ($earliestAttendance) {
                    $actualStatus = ((int)$earliestAttendance->is_late === 1) ? 'telat' : 'hadir';
                }
            }

            // Filter by requested status
            if ($status === 'early_checkout') {
                if (!$hasEarly) { continue; }
            } elseif (!($status === 'all' || $actualStatus === $status)) {
                continue;
            }

            // Build all shifts info for this user
            $allShifts = $sorted->map(function($s) {
                $att = $s->attendance;
                // Normalize status: early_checkout/forgot_checkout -> hadir/telat based on is_late
                $shiftStatus = null;
                if ($att) {
                    if (in_array($att->status, ['early_checkout', 'forgot_checkout'])) {
                        $shiftStatus = ((int)$att->is_late === 1) ? 'telat' : 'hadir';
                    } else {
                        $shiftStatus = $att->status;
                    }
                }
                return [
                    'category' => optional($s->shift)->category,
                    'shift_name' => optional($s->shift)->shift_name,
                    'shift_start' => optional($s->shift)->shift_start,
                    'shift_end' => optional($s->shift)->shift_end,
                    'check_in' => optional($att)->check_in_time,
                    'check_out' => optional($att)->check_out_time,
                    'status' => $shiftStatus,
                    'is_early_checkout' => optional($att)->status === 'early_checkout',
                    'permission_type' => null,
                ];
            })->values()->all();

            // Store per-user entry keyed by earliest category
            $users[] = [
                'category' => $primaryCategory,
                'shift_start' => $primaryStart ?: null,
                'shift_end' => $primaryEnd ?: null,
                'name' => $user->name,
                'shift_name' => $primaryShiftName ?: '',
                'status' => $actualStatus,
                'check_in' => $checkIn ?: null,
                'check_out' => $checkOut ?: null,
                'is_early_checkout' => $hasEarly,
                'permission_type' => $permissionType,
                'permission_file' => $permissionFile,
                'permission_id' => $permissionId,
                'shifts' => $allShifts,
            ];
        }

        // Build grouped data by earliest category and normalize header times
        $groupedData = [];
        foreach ($users as $emp) {
            $cat = $emp['category'];
            if (!isset($groupedData[$cat])) {
                $groupedData[$cat] = [
                    'category' => $cat,
                    'shift_start' => $emp['shift_start'] ?: null,
                    'shift_end' => $emp['shift_end'] ?: null,
                    'employees' => []
                ];
            } else {
                if ($emp['shift_start'] && (!isset($groupedData[$cat]['shift_start']) || $emp['shift_start'] < $groupedData[$cat]['shift_start'])) {
                    $groupedData[$cat]['shift_start'] = $emp['shift_start'];
                }
                if ($emp['shift_end'] && (!isset($groupedData[$cat]['shift_end']) || $emp['shift_end'] > $groupedData[$cat]['shift_end'])) {
                    $groupedData[$cat]['shift_end'] = $emp['shift_end'];
                }
            }
            $groupedData[$cat]['employees'][] = [
                'name' => $emp['name'],
                'shift_name' => $emp['shift_name'] ?: '',
                'status' => $emp['status'],
                'check_in' => $emp['check_in'] ?: null,
                'check_out' => $emp['check_out'] ?: null,
                'is_early_checkout' => $emp['is_early_checkout'],
                'permission_type' => $emp['permission_type'],
                'permission_file' => $emp['permission_file'] ?? null,
                'permission_id' => $emp['permission_id'] ?? null,
                'shifts' => $emp['shifts'] ?? [],
            ];
        }

        // Sort groups by earliest shift start (null-safe)
        $groups = array_values($groupedData);
        usort($groups, function($a, $b) {
            $sa = $a['shift_start'] ?? '23:59';
            $sb = $b['shift_start'] ?? '23:59';
            return strcmp($sa, $sb);
        });

        return response()->json([
            'status' => $status,
            'data' => $groups,
        ]);
    }
}