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
            
            // Get attendances for this date
            // Hadir = semua attendance (hadir + telat + early_checkout) kecuali izin
            $hadirCount = Attendance::whereHas('schedule', function($q) use ($dateString) {
                $q->whereDate('schedule_date', $dateString);
            })->where('status', '!=', 'izin')->count();
            
            // Telat = has attendance AND is_late = 1 (termasuk early_checkout yang telat)
            $telatCount = Attendance::whereHas('schedule', function($q) use ($dateString) {
                $q->whereDate('schedule_date', $dateString);
            })->where('is_late', 1)
              ->where('status', '!=', 'izin')
              ->count();
            
            // Izin = attendance with status 'izin'
            $izinCount = Attendance::whereHas('schedule', function($q) use ($dateString) {
                $q->whereDate('schedule_date', $dateString);
            })->where('status', 'izin')->count();
            
            // Alpha = schedules - (hadir + izin)
            $alphaCount = max(0, $schedulesCount - ($hadirCount + $izinCount));
            
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
        
        // Get unique user IDs for each status (to match modal display)
        // Hadir = unique users yang punya attendance (hadir + telat + early_checkout) kecuali izin
        $hadirUserIds = Attendance::whereHas('schedule', function($q) use ($today) {
            $q->whereDate('schedule_date', $today);
        })->where('status', '!=', 'izin')
          ->distinct()
          ->pluck('user_id');
        $todayHadir = $hadirUserIds->count();

        // Telat = unique users yang punya attendance dengan is_late = 1
        $telatUserIds = Attendance::whereHas('schedule', function($q) use ($today) {
            $q->whereDate('schedule_date', $today);
        })->where('is_late', 1)
          ->where('status', '!=', 'izin')
          ->distinct()
          ->pluck('user_id');
        $todayTelat = $telatUserIds->count();

        // Izin = unique users yang punya attendance dengan status 'izin'
        $izinUserIds = Attendance::whereHas('schedule', function($q) use ($today) {
            $q->whereDate('schedule_date', $today);
        })->where('status', 'izin')
          ->distinct()
          ->pluck('user_id');
        $todayIzin = $izinUserIds->count();

        // Alpha = unique users yang punya schedule tapi tidak ada attendance
        $allScheduledUserIds = Schedules::whereDate('schedule_date', $today)
            ->distinct()
            ->pluck('user_id');
        $allAttendedUserIds = $hadirUserIds->merge($izinUserIds)->unique();
        $todayAlpha = $allScheduledUserIds->diff($allAttendedUserIds)->count();

        return view('admin.dashboard', [
            'totalUsers'       => User::where('role', '!=', 'Admin')->count(),
            'totalShifts'      => Shift::count(),
            'totalSchedules'   => Schedules::count(),
            'attendanceData'   => $attendanceData,
            'chartDates'       => $dates,
            'todaySchedules'   => $todaySchedules,
            'todayHadir'       => $todayHadir,
            'todayTelat'       => $todayTelat,
            'todayIzin'        => $todayIzin,
            'todayAlpha'       => $todayAlpha,
            'currentMonth'     => $monthDate->format('F Y'),
            'selectedMonth'    => $selectedMonth,
            'selectedYear'     => $selectedYear
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
            
            if ($schedule->attendance) {
                // Check attendance status field first (for early_checkout, forgot_checkout, izin)
                $attendanceStatus = $schedule->attendance->status;
                
                if ($attendanceStatus === 'early_checkout') {
                    // Early checkout: set as hadir with early checkout badge
                    $actualStatus = $schedule->attendance->is_late == 1 ? 'telat' : 'hadir';
                    $checkoutStatus = 'early';
                } elseif ($attendanceStatus === 'izin') {
                    // Izin from permission
                    $actualStatus = 'izin';
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
                }
            }
            
            // Filter by requested status
            if ($status === 'all' || $actualStatus === $status) {
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
                    'checkout_status' => $checkoutStatus,
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