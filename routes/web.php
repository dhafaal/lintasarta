<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\LocationsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ShiftController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Admin\PermissionController as AdminPermissionController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AttendancesController as AdminAttendanceController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\SecurityController;
use App\Http\Controllers\Users\AttendancesController as UsersAttendanceController;
use App\Http\Controllers\Users\PermissionController as UsersPermissionController;
use App\Http\Controllers\Users\CalendarController;
use App\Http\Controllers\DashboardRedirectController;

// ======= ADMIN =======
Route::middleware(['auth', \App\Http\Middleware\CheckRole::class . ':Admin'])
    ->prefix('admin')->name('admin.')->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::resource('locations', LocationsController::class);   

        // Users
        Route::resource('users', UserController::class);
        Route::delete('users/bulk-delete', [UserController::class, 'bulkDelete'])->name('users.bulkDelete');
        Route::get('users/export/excel', [UserController::class, 'exportExcel'])->name('users.exportExcel');
        Route::get('users/export/pdf', [UserController::class, 'exportPdf'])->name('users.exportPdf');

        // Shifts
        Route::resource('shifts', ShiftController::class);
        Route::get('schedules/user/{id}', [ScheduleController::class, 'userSchedules'])->name('schedules.user');
        Route::post('schedules/bulk-store', [ScheduleController::class, 'bulkStore'])->name('schedules.bulkStore');
        Route::get('schedules/history/{user}', [ScheduleController::class, 'history'])->name('schedules.history');
        Route::get('schedules/calendAar-grid-data', [ScheduleController::class, 'calendarGridData'])
            ->name('schedules.calendar-grid-data');
        
        // Swap schedules routes
        Route::get('schedules/users-with-schedules', [ScheduleController::class, 'getUsersWithSchedules'])
            ->name('schedules.users-with-schedules');
        Route::get('schedules/user-schedules/{userId}', [ScheduleController::class, 'getUserSchedulesForSwap'])
            ->name('schedules.user-schedules-for-swap');
        Route::post('schedules/swap', [ScheduleController::class, 'swapSchedules'])
            ->name('schedules.swap');
        
        // Get existing schedules for user
        Route::get('schedules/user-existing-schedules', [ScheduleController::class, 'getUserExistingSchedules'])
            ->name('schedules.user-existing-schedules');

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

        // Activity Logs
        Route::prefix('activity-logs')->name('activity-logs.')->group(function () {
            Route::get('/', [ActivityLogController::class, 'index'])->name('index');
            Route::get('/{type}/{id}', [ActivityLogController::class, 'show'])->name('show');
            Route::delete('/{type}/{id}', [ActivityLogController::class, 'destroy'])->name('destroy');
            Route::post('/clear', [ActivityLogController::class, 'clear'])->name('clear');
        });

        // Security Management
        Route::prefix('security')->name('security.')->group(function () {
            Route::get('/', [SecurityController::class, 'index'])->name('index');
            Route::post('/block-ip', [SecurityController::class, 'blockIP'])->name('block-ip');
            Route::post('/unblock-ip', [SecurityController::class, 'unblockIP'])->name('unblock-ip');
            Route::post('/terminate-session', [SecurityController::class, 'terminateSession'])->name('terminate-session');
            Route::post('/terminate-all-sessions', [SecurityController::class, 'terminateAllUserSessions'])->name('terminate-all-sessions');
            Route::post('/clear-failed-attempts', [SecurityController::class, 'clearFailedAttempts'])->name('clear-failed-attempts');
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

        // Attendances
        Route::prefix('attendances')->name('attendances.')->group(function () {
            Route::get('/', [UsersAttendanceController::class, 'index'])->name('index');
            Route::post('/checkin', [UsersAttendanceController::class, 'checkin'])->name('checkin');
            Route::post('/checkout', [UsersAttendanceController::class, 'checkout'])->name('checkout');
            Route::post('/absent', [UsersAttendanceController::class, 'absent'])->name('absent');
            Route::get('/history', [UsersAttendanceController::class, 'history'])->name('history');
        });

        // Permissions
        Route::prefix('permissions')->name('permissions.')->group(function () {
            Route::get('/', [UsersPermissionController::class, 'index'])->name('index');
            Route::get('/create', [UsersPermissionController::class, 'create'])->name('create');
            Route::post('/', [UsersPermissionController::class, 'store'])->name('store');
            Route::delete('/{schedule}', [UsersPermissionController::class, 'cancel'])->name('cancel');
        });
    });

// ======= AUTH =======
// Home route - redirect to appropriate dashboard if authenticated, otherwise to login
Route::get('/', [DashboardRedirectController::class, 'redirectToDashboard'])->name('home');

Route::middleware(['guest'])->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
});
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

// Session Management Routes
Route::middleware(['auth'])->prefix('auth')->name('auth.')->group(function () {
    Route::get('/sessions', [AuthController::class, 'getActiveSessions'])->name('sessions');
    Route::delete('/sessions/{id}', [AuthController::class, 'terminateSession'])->name('sessions.terminate');
    Route::post('/logout-all', [AuthController::class, 'logoutAllSessions'])->name('logout-all');
    Route::get('/sessions-page', function () {
        return view('auth.sessions');
    })->name('sessions.page');
});

// Forgot Password
Route::get('forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('forgot-password/send-otp', [AuthController::class, 'sendOtp'])->name('password.send.otp');
Route::post('forgot-password/verify-otp', [AuthController::class, 'verifyOtp'])->name('password.verify.otp');
Route::post('forgot-password/reset', [AuthController::class, 'resetPassword'])->name('password.reset');

