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
use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;
use App\Http\Controllers\Users\AttendanceController as UsersAttendanceController;
use App\Http\Controllers\Users\PermissionController as UsersPermissionController;
use App\Http\Controllers\Users\CalendarController;
use App\Models\User;

// ======= ADMIN =======
Route::middleware(['auth', \App\Http\Middleware\CheckRole::class . ':Admin'])
    ->prefix('admin')->name('admin.')->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Users (Resource + tambahan fitur export/bulk)
        Route::resource('users', UserController::class);
        Route::get('users/{user}/history', [\App\Http\Controllers\Admin\UserController::class, 'history'])
            ->name('users.history');

        // Optional: tambahan fitur export/bulk delete
        Route::delete('users/bulk-delete', [UserController::class, 'bulkDelete'])->name('users.bulkDelete');
        Route::get('users/export/excel', [UserController::class, 'exportExcel'])->name('users.exportExcel');
        Route::get('users/export/pdf', [UserController::class, 'exportPdf'])->name('users.exportPdf');

        // Shifts
        Route::resource('shifts', ShiftController::class);

        // Schedules
        Route::resource('schedules', ScheduleController::class);
        Route::post('schedules/bulk-store', [ScheduleController::class, 'bulkStore'])->name('schedules.bulkStore');
        Route::get('calendar', [ScheduleController::class, 'calendarView'])->name('calendar.view');
        Route::get('calendar/data', [ScheduleController::class, 'calendarData'])->name('calendar.data');
        Route::get('calendar/report', [ScheduleController::class, 'report'])->name('calendar.report');
        Route::get('calendar/export', [ScheduleController::class, 'exportReport'])->name('calendar.export');
        
        // Attendances (Admin)
        Route::prefix('attendances')->name('attendances.')->group(function () {
            Route::get('/', [AdminAttendanceController::class, 'index'])->name('index');
            Route::get('/{user}', [AdminAttendanceController::class, 'show'])->name('show');
            Route::delete('/{attendance}', [AdminAttendanceController::class, 'destroy'])->name('destroy');
            Route::post('/{attendance}/approve', [AdminAttendanceController::class, 'approve'])->name('approve');
        });

        // Permissions (Admin)
        Route::post('permissions/{permission}/approve', [AdminPermissionController::class, 'approve'])
            ->name('permissions.approve');
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
    ->prefix('user')
    ->name('user.')
    ->group(function () {
        Route::get('/dashboard', function () {
            return view('users.dashboard');
        })->name('dashboard');

        // Calendar
        Route::get('calendar', [CalendarController::class, 'calendarView'])->name('calendar.view');
        Route::get('calendar/data', [CalendarController::class, 'calendarData'])->name('calendar.data');

        // Attendance (User)
        Route::prefix('attendance')->name('attendance.')->group(function () {
            Route::get('/', [UsersAttendanceController::class, 'index'])->name('index');
            Route::post('/{scheduleId}/checkin', [UsersAttendanceController::class, 'store'])->name('store');
            Route::post('/{scheduleId}/checkout', [UsersAttendanceController::class, 'checkout'])->name('checkout');
            Route::post('/{scheduleId}/izin', [UsersPermissionController::class, 'izin'])->name('izin');
        });
    });



// ======= AUTH =======
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('login', [AuthController::class, 'showLogin'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('login');
})->name('logout');

Route::get('forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('forgot-password/send-otp', [AuthController::class, 'sendOtp'])->name('password.send.otp');
Route::post('forgot-password/verify-otp', [AuthController::class, 'verifyOtp'])->name('password.verify.otp');
Route::post('forgot-password/reset', [AuthController::class, 'resetPassword'])->name('password.reset');