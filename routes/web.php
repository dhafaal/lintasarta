<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FeatureController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ShiftController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Admin\PermissionController as AdminPermissionController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AttendancesController as AdminAttendanceController;
use App\Http\Controllers\Users\AttendancesController as UsersAttendanceController;
use App\Http\Controllers\Users\PermissionController as UsersPermissionController;
use App\Http\Controllers\Users\CalendarController;

// ======= ADMIN =======
Route::middleware(['auth', \App\Http\Middleware\CheckRole::class . ':Admin'])
    ->prefix('admin')->name('admin.')->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Users
        Route::resource('users', UserController::class);
        Route::get('users/{user}/history', [UserController::class, 'history'])->name('users.history');
        Route::delete('users/bulk-delete', [UserController::class, 'bulkDelete'])->name('users.bulkDelete');
        Route::get('users/export/excel', [UserController::class, 'exportExcel'])->name('users.exportExcel');
        Route::get('users/export/pdf', [UserController::class, 'exportPdf'])->name('users.exportPdf');

        // Shifts
        Route::resource('shifts', ShiftController::class);

        /**
         * --- SCHEDULES & CALENDAR ---
         * NOTE: letakkan custom route DULU sebelum resource supaya tidak bentrok dengan show()
         */
        Route::get('schedules/user/{id}', [ScheduleController::class, 'userSchedules'])->name('schedules.user');
        Route::post('schedules/bulk-store', [ScheduleController::class, 'bulkStore'])->name('schedules.bulkStore');
        Route::get('schedules/calendar-grid-data', [ScheduleController::class, 'calendarGridData'])
            ->name('schedules.calendar-grid-data');

        // Kalender untuk admin
        Route::prefix('calendar')->name('calendar.')->group(function () {
            Route::get('/', [ScheduleController::class, 'calendarView'])->name('view');
            Route::get('/data', [ScheduleController::class, 'calendarData'])->name('data');
            Route::get('/report', [ScheduleController::class, 'report'])->name('report');
            Route::get('/export', [ScheduleController::class, 'exportReport'])->name('export');
        });

        // Baru panggil resource (paling bawah supaya route di atas tidak ketimpa)
        Route::resource('schedules', ScheduleController::class);

        // Attendances
        Route::prefix('attendances')->name('attendances.')->group(function () {
            Route::get('/', [AdminAttendanceController::class, 'index'])->name('index');
            Route::get('/history', [AdminAttendanceController::class, 'history'])->name('history');
            Route::post('/permissions/{permission}/approve', [AdminAttendanceController::class, 'approvePermission'])
                ->name('permission.approve');
            Route::post('/permissions/{permission}/reject', [AdminAttendanceController::class, 'rejectPermission'])
                ->name('permission.reject');
            Route::get('/{user}', [AdminAttendanceController::class, 'show'])->name('show');
            Route::delete('/{attendance}', [AdminAttendanceController::class, 'destroy'])->name('destroy');
        });

        // Permissions (Admin)
        Route::prefix('permissions')->name('permissions.')->group(function () {
            Route::post('/{permission}/approve', [AdminPermissionController::class, 'approve'])->name('approve');
        });
    });

// ======= OPERATOR =======
Route::middleware(['auth', \App\Http\Middleware\CheckRole::class . ':Operator'])
    ->prefix('operator')->name('operator.')->group(function () {
        Route::get('/dashboard', function () {
            return view('operator.dashboard');
        })->name('dashboard');
    });

// ======= USER =======
Route::middleware(['auth', \App\Http\Middleware\CheckRole::class . ':User'])
    ->prefix('user')->name('user.')->group(function () {
        Route::get('/dashboard', fn() => view('users.dashboard'))->name('dashboard');

        // Calendar
        Route::get('calendar', [CalendarController::class, 'calendarView'])->name('calendar.view');
        Route::get('calendar/data', [CalendarController::class, 'calendarData'])->name('calendar.data');

        // Attendance
        Route::get('attendances', [UsersAttendanceController::class, 'index'])->name('attendances.index');
        Route::post('attendances/checkin', [UsersAttendanceController::class, 'checkin'])->name('attendances.checkin');
        Route::post('attendances/checkout', [UsersAttendanceController::class, 'checkout'])->name('attendances.checkout');
        Route::get('attendances/history', [UsersAttendanceController::class, 'history'])->name('attendances.history');

        // Permissions
        Route::get('permissions/create', [UsersPermissionController::class, 'create'])->name('attendances.permission.create');
        Route::get('permissions', [UsersPermissionController::class, 'create'])->name('permissions.index');
        Route::post('permissions', [UsersPermissionController::class, 'store'])->name('permissions.store');
        Route::delete('permissions/{schedule}', [UsersPermissionController::class, 'cancel'])->name('permissions.cancel');
    });

// ======= AUTH =======
Route::get('/', fn() => redirect()->route('login'));
Route::get('login', [AuthController::class, 'showLogin'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('login');
})->name('logout');

// Forgot Password
Route::get('forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('forgot-password/send-otp', [AuthController::class, 'sendOtp'])->name('password.send.otp');
Route::post('forgot-password/verify-otp', [AuthController::class, 'verifyOtp'])->name('password.verify.otp');
Route::post('forgot-password/reset', [AuthController::class, 'resetPassword'])->name('password.reset');
