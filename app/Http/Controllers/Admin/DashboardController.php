<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Shift;
use App\Models\Schedule;
use App\Models\Permission;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'totalUsers'       => User::where('role', '!=', 'Admin')->count(),
            'totalShifts'      => Shift::count(),
            'totalSchedules'   => Schedule::count(),
            'totalPermissions' => Permission::count(),
        ]);
    }
}
