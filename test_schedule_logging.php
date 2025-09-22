<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use App\Models\AdminSchedulesLog;
use App\Models\User;
use App\Models\Shift;
use App\Models\Schedules;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing Schedule Logging Functionality...\n\n";

try {
    // Simulate a user (admin)
    $admin = User::where('role', 'admin')->first();
    if (!$admin) {
        echo "❌ No admin user found. Please create an admin user first.\n";
        exit;
    }
    
    // Simulate authentication
    auth()->login($admin);
    
    echo "✅ Admin user authenticated: {$admin->name}\n";
    
    // Get a user and shift for testing
    $user = User::where('role', 'user')->first();
    $shift = Shift::first();
    
    if (!$user || !$shift) {
        echo "❌ Need at least one user and one shift for testing.\n";
        exit;
    }
    
    echo "✅ Test user: {$user->name}\n";
    echo "✅ Test shift: {$shift->shift_name}\n\n";
    
    // Test AdminSchedulesLog
    echo "Testing AdminSchedulesLog...\n";
    AdminSchedulesLog::log(
        'create',
        999, // fake schedule id
        $user->id,
        $user->name,
        $shift->id,
        $shift->shift_name,
        '2025-09-22',
        null,
        ['user_id' => $user->id, 'shift_id' => $shift->id, 'schedule_date' => '2025-09-22'],
        'Test schedule logging functionality'
    );
    
    $latestLog = AdminSchedulesLog::latest()->first();
    if ($latestLog) {
        echo "✅ AdminSchedulesLog created successfully!\n";
        echo "   - ID: {$latestLog->id}\n";
        echo "   - Action: {$latestLog->action}\n";
        echo "   - Target User: {$latestLog->target_user_name}\n";
        echo "   - Shift: {$latestLog->shift_name}\n";
        echo "   - Schedule Date: {$latestLog->schedule_date}\n";
        echo "   - Description: {$latestLog->description}\n";
        echo "   - Created: {$latestLog->created_at}\n";
    } else {
        echo "❌ AdminSchedulesLog not created\n";
    }
    
    // Test count of logs
    echo "\nCounting logs in all tables:\n";
    $shiftsCount = \App\Models\AdminShiftsLog::count();
    $usersCount = \App\Models\AdminUsersLog::count();
    $schedulesCount = \App\Models\AdminSchedulesLog::count();
    $permissionsCount = \App\Models\AdminPermissionsLog::count();
    $userActivityCount = \App\Models\UserActivityLog::count();
    $authCount = \App\Models\AuthActivityLog::count();
    
    echo "- AdminShiftsLog: {$shiftsCount} records\n";
    echo "- AdminUsersLog: {$usersCount} records\n";
    echo "- AdminSchedulesLog: {$schedulesCount} records\n";
    echo "- AdminPermissionsLog: {$permissionsCount} records\n";
    echo "- UserActivityLog: {$userActivityCount} records\n";
    echo "- AuthActivityLog: {$authCount} records\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\nTest completed!\n";
