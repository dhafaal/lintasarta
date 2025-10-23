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
            
            // Get schedules for this date
            $schedulesCount = Schedules::whereDate('schedule_date', $dateString)->count();
            
            // Hadir = attendance dengan is_late = 0 (tidak telat)
            $hadirCount = Attendance::whereHas('schedule', function($q) use ($dateString) {
                $q->whereDate('schedule_date', $dateString);
            })->where('is_late', 0)->count();
            
            // Telat = attendance dengan is_late = 1
            $telatCount = Attendance::whereHas('schedule', function($q) use ($dateString) {
                $q->whereDate('schedule_date', $dateString);
            })->where('is_late', 1)->count();
            
            // Izin = approved permissions untuk tanggal ini
            $izinCount = Permissions::whereHas('schedule', function($q) use ($dateString) {
                $q->whereDate('schedule_date', $dateString);
            })->where('status', 'approved')->count();
            
            // Alpha = schedules tanpa attendance dan tanpa approved permission
            $attendedScheduleIds = Attendance::whereHas('schedule', function($q) use ($dateString) {
                $q->whereDate('schedule_date', $dateString);
            })->pluck('schedule_id');
            
            $permissionScheduleIds = Permissions::whereHas('schedule', function($q) use ($dateString) {
                $q->whereDate('schedule_date', $dateString);
            })->where('status', 'approved')->pluck('schedule_id');
            
            $allScheduleIds = Schedules::whereDate('schedule_date', $dateString)->pluck('id');
            $alphaCount = $allScheduleIds->diff($attendedScheduleIds)->diff($permissionScheduleIds)->count();
            
            $attendanceData[] = [
                'date' => $dateString,
                'hadir' => $hadirCount,
                'telat' => $telatCount,
                'izin' => $izinCount,
                'alpha' => $alphaCount
            ];
        }
        
        // Get today's attendance summary
        $today = Carbon::today();
        $todaySchedules = Schedules::whereDate('schedule_date', $today)->count();
        
        // Hadir = attendance dengan is_late = 0 (tidak telat)
        $todayHadir = Attendance::whereHas('schedule', function($q) use ($today) {
            $q->whereDate('schedule_date', $today);
        })->where('is_late', 0)->count();

        // Telat = attendance dengan is_late = 1
        $todayTelat = Attendance::whereHas('schedule', function($q) use ($today) {
            $q->whereDate('schedule_date', $today);
        })->where('is_late', 1)->count();

        // Izin = approved permissions untuk hari ini
        $todayIzin = Permissions::whereHas('schedule', function($q) use ($today) {
            $q->whereDate('schedule_date', $today);
        })->where('status', 'approved')->count();

        // Alpha = schedules tanpa attendance dan tanpa approved permission
        $todayAttendedScheduleIds = Attendance::whereHas('schedule', function($q) use ($today) {
            $q->whereDate('schedule_date', $today);
        })->pluck('schedule_id');
        
        $todayPermissionScheduleIds = Permissions::whereHas('schedule', function($q) use ($today) {
            $q->whereDate('schedule_date', $today);
        })->where('status', 'approved')->pluck('schedule_id');
        
        $todayAllScheduleIds = Schedules::whereDate('schedule_date', $today)->pluck('id');
        $todayAlpha = $todayAllScheduleIds->diff($todayAttendedScheduleIds)->diff($todayPermissionScheduleIds)->count();
        
        // Early Checkout = attendance dengan status 'early_checkout'
        $todayEarlyCheckout = Attendance::whereHas('schedule', function($q) use ($today) {
            $q->whereDate('schedule_date', $today);
        })->where('status', 'early_checkout')->count();
        
        // Forgot Checkout = attendance dengan status 'forgot_checkout'
        $todayForgotCheckout = Attendance::whereHas('schedule', function($q) use ($today) {
            $q->whereDate('schedule_date', $today);
        })->where('status', 'forgot_checkout')->count();

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

    public function getTodayAttendanceDetails(Request $request)
    {
        $status = $request->input('status');
        $today = Carbon::today();
        
        // Get all schedules for today
        $schedules = Schedules::with(['user', 'shift', 'attendance'])
            ->whereDate('schedule_date', $today)
            ->get();
        
        // Group by shift category
        $groupedData = [];
        
        foreach ($schedules as $schedule) {
            $shiftCategory = $schedule->shift->category;
            $shiftName = $schedule->shift->shift_name;
            
            // Determine actual status using attendance status field
            $actualStatus = 'alpha'; // default
            $checkoutStatus = null;
            $isEarlyCheckout = false;
            $permissionType = null;
            
            if ($schedule->attendance) {
                // Check attendance status field first (for early_checkout, forgot_checkout, izin)
                $attendanceStatus = $schedule->attendance->status;
                
                if ($attendanceStatus === 'early_checkout') {
                    // Early checkout: categorize as hadir/telat based on is_late, but flag as early checkout
                    $isEarlyCheckout = true;
                    if ($schedule->attendance->is_late == 1) {
                        $actualStatus = 'telat';
                    } else {
                        $actualStatus = 'hadir';
                    }
                } elseif ($attendanceStatus === 'forgot_checkout') {
                    // Forgot checkout: treat as separate status
                    $actualStatus = 'forgot_checkout';
                } elseif ($attendanceStatus === 'izin') {
                    // Izin from permission - get permission type
                    $actualStatus = 'izin';
                    $permission = Permissions::where('schedule_id', $schedule->id)
                        ->where('status', 'approved')
                        ->first();
                    if ($permission) {
                        $permissionType = $permission->type; // izin or cuti
                    }
                } else {
                    // Normal attendance: use is_late field
                    if ($schedule->attendance->is_late == 1) {
                        $actualStatus = 'telat';
                    } else {
                        $actualStatus = 'hadir';
                    }
                }
            } else {
                // No attendance, check if has approved permission
                $permission = Permissions::where('schedule_id', $schedule->id)
                    ->where('status', 'approved')
                    ->first();
                if ($permission) {
                    $actualStatus = 'izin';
                    // Store permission type for display (izin or cuti)
                    $permissionType = $permission->type;
                }
            }
            
            // Filter by requested status
            // For early_checkout filter, show only those with early checkout flag
            if ($status === 'early_checkout') {
                if (!$isEarlyCheckout) {
                    continue; // Skip if not early checkout
                }
            } elseif ($status === 'all' || $actualStatus === $status) {
                // Normal filtering
            } else {
                continue; // Skip if doesn't match
            }
            
            // If we reach here, include this schedule
            if (true) {
                // Group by category, not shift name
                if (!isset($groupedData[$shiftCategory])) {
                    $groupedData[$shiftCategory] = [
                        'category' => $shiftCategory,
                        'shift_start' => $schedule->shift->shift_start,
                        'shift_end' => $schedule->shift->shift_end,
                        'employees' => []
                    ];
                } else {
                    // Update shift_start to earliest time
                    if ($schedule->shift->shift_start < $groupedData[$shiftCategory]['shift_start']) {
                        $groupedData[$shiftCategory]['shift_start'] = $schedule->shift->shift_start;
                    }
                    // Update shift_end to latest time
                    if ($schedule->shift->shift_end > $groupedData[$shiftCategory]['shift_end']) {
                        $groupedData[$shiftCategory]['shift_end'] = $schedule->shift->shift_end;
                    }
                }
                
                $groupedData[$shiftCategory]['employees'][] = [
                    'name' => $schedule->user->name,
                    'shift_name' => $shiftName,
                    'status' => $actualStatus,
                    'check_in' => $schedule->attendance ? $schedule->attendance->check_in_time : null,
                    'check_out' => $schedule->attendance ? $schedule->attendance->check_out_time : null,
                    'is_early_checkout' => $isEarlyCheckout,
                    'permission_type' => $permissionType, // izin or cuti (only for status = izin)
                ];
            }
        }
        
        // Sort shifts by start time
        usort($groupedData, function($a, $b) {
            return strcmp($a['shift_start'], $b['shift_start']);
        });
        
        return response()->json([
            'status' => $status,
            'data' => array_values($groupedData)
        ]);
    }
}