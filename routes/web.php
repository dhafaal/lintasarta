<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FeatureController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ShiftController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\DashboardController;

// ======= ADMIN =======
Route::middleware(['auth', \App\Http\Middleware\CheckRole::class . ':Admin'])
    ->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Users (Resource + tambahan fitur export/bulk)
    Route::resource('users', UserController::class);

    // Optional: tambahan fitur export/bulk delete
    Route::delete('users/bulk-delete', [UserController::class, 'bulkDelete'])->name('users.bulkDelete');
    Route::get('users/export/excel', [UserController::class, 'exportExcel'])->name('users.exportExcel');
    Route::get('users/export/pdf', [UserController::class, 'exportPdf'])->name('users.exportPdf');

    // Shifts
    Route::resource('shifts', ShiftController::class);

    // Schedules
    Route::resource('schedules', ScheduleController::class);

    // Calendar tambahan
    Route::get('calendar', [ScheduleController::class, 'calendarView'])->name('calendar.view');
    Route::get('calendar/data', [ScheduleController::class, 'calendarData'])->name('calendar.data');
    Route::get('calendar/report', [ScheduleController::class, 'report'])->name('calendar.report');
    Route::get('calendar/export', [ScheduleController::class, 'exportReport'])->name('calendar.export');

    // Permissions
    Route::get('permissions', [PermissionController::class, 'index'])->name('permissions.index');
    Route::post('permissions/{id}/approve', [PermissionController::class, 'approve'])->name('permissions.approve');
    Route::post('permissions/{id}/reject', [PermissionController::class, 'reject'])->name('permissions.reject');
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
    Route::get('/dashboard', function () {
        return view('users.dashboard');
    })->name('dashboard');
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
Route::post('forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
Route::get('reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
