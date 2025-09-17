<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\User;
use App\Models\Shift;
use App\Models\Schedules;
use App\Models\Attendance;
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
            $hadirCount = Attendance::whereHas('schedule', function($q) use ($dateString) {
                $q->whereDate('schedule_date', $dateString);
            })->where('status', 'hadir')->count();
            
            $telatCount = Attendance::whereHas('schedule', function($q) use ($dateString) {
                $q->whereDate('schedule_date', $dateString);
            })->where('status', 'telat')->count();
            
            $izinCount = Attendance::whereHas('schedule', function($q) use ($dateString) {
                $q->whereDate('schedule_date', $dateString);
            })->where('status', 'izin')->count();
            
            // Alpha = schedules - (hadir + telat + izin)
            $alphaCount = max(0, $schedulesCount - ($hadirCount + $telatCount + $izinCount));
            
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
        
        $todayHadir = Attendance::whereHas('schedule', function($q) use ($today) {
            $q->whereDate('schedule_date', $today);
        })->where('status', 'hadir')->count();

        $todayTelat = Attendance::whereHas('schedule', function($q) use ($today) {
            $q->whereDate('schedule_date', $today);
        })->where('status', 'telat')->count();

        $todayIzin = Attendance::whereHas('schedule', function($q) use ($today) {
            $q->whereDate('schedule_date', $today);
        })->where('status', 'izin')->count();

        $todayAlpha = max(0, $todaySchedules - ($todayHadir + $todayTelat + $todayIzin));
        
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
}
