<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;

class SetupSecurity extends Command
{
    protected $signature = 'security:setup';
    protected $description = 'Setup enhanced security features for the application';

    public function handle()
    {
        $this->info('🔒 Setting up Enhanced Security Features...');
        $this->newLine();

        // Run migrations
        $this->info('Running security migrations...');
        Artisan::call('migrate', ['--force' => true]);
        $this->info('✅ Migrations completed.');

        // Check if tables exist
        $tables = [
            'login_attempts',
            'blocked_ips',
            'user_sessions'
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                $this->info("✅ Table '{$table}' created successfully.");
            } else {
                $this->error("❌ Table '{$table}' was not created.");
            }
        }

        $this->newLine();
        $this->info('🛡️ Enhanced Security Features Setup Completed!');
        
        $this->newLine();
        $this->info('Security Features Enabled:');
        $this->line('🔐 Account Lockout: 5 failed attempts = 30 min lockout');
        $this->line('🚫 IP Blocking: Auto-block after 10/20/50 failed attempts');
        $this->line('📱 Device Tracking: Monitor trusted/untrusted devices');
        $this->line('👥 Session Management: Track and manage user sessions');
        $this->line('🕵️ Suspicious Activity Detection: Alert on unusual patterns');
        $this->line('📊 Security Monitoring: Comprehensive logging and stats');
        
        $this->newLine();
        $this->info('Available Commands:');
        $this->line('• php artisan security:maintenance - Run security maintenance');
        $this->line('• php artisan security:maintenance --show-stats - Show security statistics');
        $this->line('• php artisan security:maintenance --clean-old - Clean old records');
        $this->line('• php artisan security:maintenance --unblock-expired - Unblock expired IPs');
        
        $this->newLine();
        $this->info('Admin Panel:');
        $this->line('• Access Security Management at: /admin/security');
        $this->line('• View Activity Logs at: /admin/activity-logs');
        
        $this->newLine();
        $this->warn('⚠️  Important Security Notes:');
        $this->line('• Set up a cron job to run security:maintenance daily');
        $this->line('• Monitor blocked IPs regularly to avoid blocking legitimate users');
        $this->line('• Review suspicious activity alerts promptly');
        $this->line('• Keep security logs for compliance and forensic analysis');

        return 0;
    }
}
