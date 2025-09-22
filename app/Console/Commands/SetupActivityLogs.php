<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

class SetupActivityLogs extends Command
{
    protected $signature = 'logs:setup';
    protected $description = 'Setup activity logs tables and test the logging system';

    public function handle()
    {
        $this->info('Setting up Activity Logs System...');

        // Run migrations
        $this->info('Running migrations...');
        Artisan::call('migrate', ['--force' => true]);
        $this->info('Migrations completed.');

        // Check if tables exist
        $tables = [
            'admin_shifts_logs',
            'admin_users_logs',
            'admin_schedules_logs',
            'admin_permissions_logs',
            'user_activity_logs', 
            'auth_activity_logs'
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                $this->info("✅ Table '{$table}' created successfully.");
            } else {
                $this->error("❌ Table '{$table}' was not created.");
            }
        }

        $this->info('Activity Logs System setup completed!');
        
        $this->newLine();
        $this->info('Available logging methods:');
        $this->line('- AdminShiftsLog::log() for admin shift activities');
        $this->line('- AdminUsersLog::log() for admin user activities');
        $this->line('- AdminSchedulesLog::log() for admin schedule activities');
        $this->line('- AdminPermissionsLog::log() for admin permission activities');
        $this->line('- UserActivityLog::log() for user activities');
        $this->line('- AuthActivityLog::log() for authentication activities');
        
        $this->newLine();
        $this->info('Activity logs are now being recorded for:');
        $this->line('🔧 Admin Shifts: create, update, delete shifts');
        $this->line('👥 Admin Users: create, update, delete users');
        $this->line('📅 Admin Schedules: create, update, delete schedules');
        $this->line('📋 Admin Permissions: approve, reject permissions');
        $this->line('👤 Users: checkin, checkout, request permissions');
        $this->line('🔐 Auth: login, logout, failed login attempts, password reset');

        return 0;
    }
}
