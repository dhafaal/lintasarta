        <?php

        use Illuminate\Support\Facades\Route;
        use Illuminate\Support\Facades\Auth;
        use App\Http\Controllers\AuthController;

        use App\Http\Controllers\Admin\UserController;
        use App\Http\Controllers\Admin\ShiftController;
        use App\Http\Controllers\Admin\ScheduleController;
        use App\Http\Controllers\Admin\PermissionController;

        Route::middleware(['auth', \App\Http\Middleware\CheckRole::class . ':Admin'])->prefix('admin')->name('admin.')->group(function () {
            // Dashboard
            Route::get('/admin/dashboard', function () {
                return view('admin.dashboard');
            })->name('admin.dashboard');

            // Fitur Admin
            Route::resource('users', UserController::class);
            Route::resource('shifts', ShiftController::class);
            Route::resource('schedules', ScheduleController::class);

            // Izin (permissions)
            Route::get('permissions', [PermissionController::class, 'index'])->name('permissions.index');
            Route::post('permissions/{id}/approve', [PermissionController::class, 'approve'])->name('permissions.approve');
            Route::post('permissions/{id}/reject', [PermissionController::class, 'reject'])->name('permissions.reject');
        });

        // Operator
        Route::get('/operator/dashboard', function () {
            return view('operator.dashboard');
        })->middleware('auth')->name('operator.dashboard');

        // User
        Route::get('/user/dashboard', function () {
            return view('users.dashboard');
        })->middleware('auth')->name('user.dashboard');

        Route::get('/', function () {
            return redirect()->route('login');
        });

        Route::post('/logout', function () {
            Auth::logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
            return redirect()->route('login');
        })->name('logout');

        Route::get('login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('login', [AuthController::class, 'login']);

        Route::get('forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
        Route::post('forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');

        Route::get('reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
        Route::post('reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
