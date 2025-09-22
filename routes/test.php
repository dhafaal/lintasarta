<?php

use Illuminate\Support\Facades\Route;
use App\Models\AdminShiftsLog;
use App\Models\User;

Route::get('/test-logging', function () {
    try {
        // Get admin user
        $admin = User::where('role', 'admin')->first();
        if (!$admin) {
            return response()->json(['error' => 'No admin user found']);
        }
        
        // Login admin
        auth()->login($admin);
        
        // Test logging
        AdminShiftsLog::log(
            'create',
            999,
            'Web Test Shift',
            'Siang',
            null,
            ['test' => 'data'],
            'Testing from web route'
        );
        
        $latestLog = AdminShiftsLog::latest()->first();
        
        return response()->json([
            'success' => true,
            'message' => 'Logging test successful',
            'log' => $latestLog
        ]);
        
    } catch (Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
});
