<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivityLog;
use App\Models\AdminShiftsLog;
use App\Models\AdminUsersLog;
use App\Models\AdminSchedulesLog;
use App\Models\AdminPermissionsLog;
use App\Models\UserActivityLog;
use App\Models\AuthActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type', 'all');
        $subType = $request->get('sub_type', 'all'); // shifts, users, schedules, permissions, locations
        $search = $request->get('search');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        $shiftsLogs = collect();
        $usersLogs = collect();
        $schedulesLogs = collect();
        $permissionsLogs = collect();
        $locationLogs = collect();
        $userLogs = collect();
        $attendanceLogs = collect();
        $authLogs = collect();

        if ($type === 'all' || $type === 'admin') {
            // Admin Shifts Logs
            if ($subType === 'all' || $subType === 'shifts') {
                $shiftsQuery = AdminShiftsLog::with('user')
                    ->when($search, function ($q) use ($search) {
                        $q->where('description', 'like', "%{$search}%")
                          ->orWhere('shift_name', 'like', "%{$search}%")
                          ->orWhereHas('user', function ($q) use ($search) {
                              $q->where('name', 'like', "%{$search}%");
                          });
                    })
                    ->when($dateFrom, function ($q) use ($dateFrom) {
                        $q->whereDate('created_at', '>=', $dateFrom);
                    })
                    ->when($dateTo, function ($q) use ($dateTo) {
                        $q->whereDate('created_at', '<=', $dateTo);
                    })
                    ->orderBy('created_at', 'desc');
                $shiftsLogs = $shiftsQuery->paginate(20, ['*'], 'shifts_page');
            }

            // Admin Users Logs
            if ($subType === 'all' || $subType === 'users') {
                $usersQuery = AdminUsersLog::with('user')
                    ->when($search, function ($q) use ($search) {
                        $q->where('description', 'like', "%{$search}%")
                          ->orWhere('target_user_name', 'like', "%{$search}%")
                          ->orWhereHas('user', function ($q) use ($search) {
                              $q->where('name', 'like', "%{$search}%");
                          });
                    })
                    ->when($dateFrom, function ($q) use ($dateFrom) {
                        $q->whereDate('created_at', '>=', $dateFrom);
                    })
                    ->when($dateTo, function ($q) use ($dateTo) {
                        $q->whereDate('created_at', '<=', $dateTo);
                    })
                    ->orderBy('created_at', 'desc');
                $usersLogs = $usersQuery->paginate(20, ['*'], 'users_page');
            }

            // Admin Schedules Logs
            if ($subType === 'all' || $subType === 'schedules') {
                $schedulesQuery = AdminSchedulesLog::with('user')
                    ->when($search, function ($q) use ($search) {
                        $q->where('description', 'like', "%{$search}%")
                          ->orWhere('target_user_name', 'like', "%{$search}%")
                          ->orWhere('shift_name', 'like', "%{$search}%")
                          ->orWhereHas('user', function ($q) use ($search) {
                              $q->where('name', 'like', "%{$search}%");
                          });
                    })
                    ->when($dateFrom, function ($q) use ($dateFrom) {
                        $q->whereDate('created_at', '>=', $dateFrom);
                    })
                    ->when($dateTo, function ($q) use ($dateTo) {
                        $q->whereDate('created_at', '<=', $dateTo);
                    })
                    ->orderBy('created_at', 'desc');
                $schedulesLogs = $schedulesQuery->paginate(20, ['*'], 'schedules_page');
            }

            // Admin Permissions Logs
            if ($subType === 'all' || $subType === 'permissions') {
                $permissionsQuery = AdminPermissionsLog::with('user')
                    ->when($search, function ($q) use ($search) {
                        $q->where('description', 'like', "%{$search}%")
                          ->orWhere('target_user_name', 'like', "%{$search}%")
                          ->orWhere('permission_type', 'like', "%{$search}%")
                          ->orWhereHas('user', function ($q) use ($search) {
                              $q->where('name', 'like', "%{$search}%");
                          });
                    })
                    ->when($dateFrom, function ($q) use ($dateFrom) {
                        $q->whereDate('created_at', '>=', $dateFrom);
                    })
                    ->when($dateTo, function ($q) use ($dateTo) {
                        $q->whereDate('created_at', '<=', $dateTo);
                    })
                    ->orderBy('created_at', 'desc');
                $permissionsLogs = $permissionsQuery->paginate(20, ['*'], 'permissions_page');
            }

            // Admin Location Logs
            if ($subType === 'all' || $subType === 'locations') {
                $locationQuery = AdminActivityLog::with('user')
                    ->where('resource_type', 'Location')
                    ->when($search, function ($q) use ($search) {
                        $q->where('description', 'like', "%{$search}%")
                          ->orWhere('old_values', 'like', "%{$search}%")
                          ->orWhere('new_values', 'like', "%{$search}%")
                          ->orWhereHas('user', function ($q) use ($search) {
                              $q->where('name', 'like', "%{$search}%");
                          });
                    })
                    ->when($request->get('action'), function ($q) use ($request) {
                        $q->where('action', $request->get('action'));
                    })
                    ->when($dateFrom, function ($q) use ($dateFrom) {
                        $q->whereDate('created_at', '>=', $dateFrom);
                    })
                    ->when($dateTo, function ($q) use ($dateTo) {
                        $q->whereDate('created_at', '<=', $dateTo);
                    })
                    ->orderBy('created_at', 'desc');
                $locationLogs = $locationQuery->paginate(20, ['*'], 'locations_page');
            }
        }

        if ($type === 'all' || $type === 'user' || $type === 'admin') {
            $userQuery = UserActivityLog::with('user')
                ->when($search, function ($q) use ($search) {
                    $q->where('description', 'like', "%{$search}%")
                      ->orWhere('resource_name', 'like', "%{$search}%")
                      ->orWhereHas('user', function ($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%");
                      });
                })
                ->when($dateFrom, function ($q) use ($dateFrom) {
                    $q->whereDate('created_at', '>=', $dateFrom);
                })
                ->when($dateTo, function ($q) use ($dateTo) {
                    $q->whereDate('created_at', '<=', $dateTo);
                })
                ->orderBy('created_at', 'desc');

            $userLogs = $userQuery->paginate(20, ['*'], 'user_page');

            // Attendance-specific logs (user activity logs filtered by resource_type or description)
            $attendanceQuery = UserActivityLog::with('user')
                ->when($search, function ($q) use ($search) {
                    $q->where('description', 'like', "%{$search}%")
                      ->orWhere('resource_name', 'like', "%{$search}%")
                      ->orWhereHas('user', function ($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%");
                      });
                })
                ->when($dateFrom, function ($q) use ($dateFrom) {
                    $q->whereDate('created_at', '>=', $dateFrom);
                })
                ->when($dateTo, function ($q) use ($dateTo) {
                    $q->whereDate('created_at', '<=', $dateTo);
                })
                ->where(function ($q) {
                    $q->where('resource_type', 'Attendance')
                      ->orWhere('description', 'like', '%Check In%')
                      ->orWhere('description', 'like', '%Check Out%')
                      ->orWhere('description', 'like', '%Absent%');
                })
                ->orderBy('created_at', 'desc');

            $attendanceLogs = $attendanceQuery->paginate(20, ['*'], 'attendance_page');
        }

        if ($type === 'all' || $type === 'auth') {
            $authQuery = AuthActivityLog::with('user')
                ->when($search, function ($q) use ($search) {
                    $q->where('description', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhereHas('user', function ($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%");
                      });
                })
                ->when($dateFrom, function ($q) use ($dateFrom) {
                    $q->whereDate('created_at', '>=', $dateFrom);
                })
                ->when($dateTo, function ($q) use ($dateTo) {
                    $q->whereDate('created_at', '<=', $dateTo);
                })
                ->orderBy('created_at', 'desc');

            $authLogs = $authQuery->paginate(20, ['*'], 'auth_page');
        }

        return view('admin.activity-logs.index', compact(
            'shiftsLogs',
            'usersLogs',
            'schedulesLogs',
            'permissionsLogs',
            'locationLogs',
            'userLogs',
            'authLogs',
            'attendanceLogs',
            'type',
            'subType',
            'search',
            'dateFrom',
            'dateTo'
        ));
    }

    public function show($type, $id)
    {
        $log = match ($type) {
            'shifts' => AdminShiftsLog::with('user')->findOrFail($id),
            'users' => AdminUsersLog::with('user')->findOrFail($id),
            'schedules' => AdminSchedulesLog::with('user')->findOrFail($id),
            'permissions' => AdminPermissionsLog::with('user')->findOrFail($id),
            'user' => UserActivityLog::with('user')->findOrFail($id),
            'auth' => AuthActivityLog::with('user')->findOrFail($id),
            default => abort(404)
        };

        return view('admin.activity-logs.show', compact('log', 'type'));
    }

    public function destroy($type, $id)
    {
        $log = match ($type) {
            'shifts' => AdminShiftsLog::findOrFail($id),
            'users' => AdminUsersLog::findOrFail($id),
            'schedules' => AdminSchedulesLog::findOrFail($id),
            'permissions' => AdminPermissionsLog::findOrFail($id),
            'user' => UserActivityLog::findOrFail($id),
            'auth' => AuthActivityLog::findOrFail($id),
            default => abort(404)
        };

        $log->delete();

        return redirect()->route('admin.activity-logs.index')
            ->with('success', 'Log aktivitas berhasil dihapus.');
    }

    public function clear(Request $request)
    {
        $type = $request->get('type', 'all');
        $olderThan = $request->get('older_than', 30); // days

        $date = now()->subDays($olderThan);

        if ($type === 'all' || $type === 'admin') {
            AdminShiftsLog::where('created_at', '<', $date)->delete();
            AdminUsersLog::where('created_at', '<', $date)->delete();
            AdminSchedulesLog::where('created_at', '<', $date)->delete();
            AdminPermissionsLog::where('created_at', '<', $date)->delete();
        }

        if ($type === 'shifts') {
            AdminShiftsLog::where('created_at', '<', $date)->delete();
        }

        if ($type === 'users') {
            AdminUsersLog::where('created_at', '<', $date)->delete();
        }

        if ($type === 'schedules') {
            AdminSchedulesLog::where('created_at', '<', $date)->delete();
        }

        if ($type === 'permissions') {
            AdminPermissionsLog::where('created_at', '<', $date)->delete();
        }

        if ($type === 'all' || $type === 'user') {
            UserActivityLog::where('created_at', '<', $date)->delete();
        }

        if ($type === 'all' || $type === 'auth') {
            AuthActivityLog::where('created_at', '<', $date)->delete();
        }

        return redirect()->route('admin.activity-logs.index')
            ->with('success', "Log aktivitas lebih dari {$olderThan} hari berhasil dihapus.");
    }
}
