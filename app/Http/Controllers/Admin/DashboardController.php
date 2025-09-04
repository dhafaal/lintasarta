<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\User;
use App\Models\Shift;
use App\Models\Schedules;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'totalUsers'       => User::where('role', '!=', 'Admin')->count(),
            'totalShifts'      => Shift::count(),
            'totalSchedules'   => Schedules::count(),
        ]);
    }
}
