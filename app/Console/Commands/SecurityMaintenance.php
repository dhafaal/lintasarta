<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LoginAttempt;
use App\Models\BlockedIP;
use App\Models\UserSession;
use Carbon\Carbon;

class SecurityMaintenance extends Command
{
    protected $signature = 'security:maintenance {--clean-old : Clean old records} {--unblock-expired : Unblock expired IPs} {--show-stats : Show security statistics}';
    protected $description = 'Perform security maintenance tasks';

    public function handle()
    {
        $this->info('🔒 Starting Security Maintenance...');
        $this->newLine();

        if ($this->option('clean-old') || !$this->hasOptions()) {
            $this->cleanOldRecords();
        }

        if ($this->option('unblock-expired') || !$this->hasOptions()) {
            $this->unblockExpiredIPs();
        }

        if ($this->option('show-stats') || !$this->hasOptions()) {
            $this->showSecurityStats();
        }

        $this->newLine();
        $this->info('✅ Security maintenance completed!');
    }

    private function hasOptions(): bool
    {
        return $this->option('clean-old') || $this->option('unblock-expired') || $this->option('show-stats');
    }

    private function cleanOldRecords(): void
    {
        $this->info('🧹 Cleaning old records...');

        // Clean login attempts older than 30 days
        $oldLoginAttempts = LoginAttempt::where('attempted_at', '<', Carbon::now()->subDays(30))->count();
        LoginAttempt::where('attempted_at', '<', Carbon::now()->subDays(30))->delete();
        $this->line("   - Deleted {$oldLoginAttempts} old login attempts");

        // Clean expired user sessions
        $expiredSessions = UserSession::cleanExpiredSessions();
        $this->line("   - Deleted {$expiredSessions} expired user sessions");

        // Clean old user sessions (older than 90 days)
        $oldSessions = UserSession::where('created_at', '<', Carbon::now()->subDays(90))->count();
        UserSession::where('created_at', '<', Carbon::now()->subDays(90))->delete();
        $this->line("   - Deleted {$oldSessions} old user sessions");
    }

    private function unblockExpiredIPs(): void
    {
        $this->info('🔓 Unblocking expired IP addresses...');

        $expiredBlocks = BlockedIP::where('is_permanent', false)
            ->where('blocked_until', '<', Carbon::now())
            ->count();

        BlockedIP::where('is_permanent', false)
            ->where('blocked_until', '<', Carbon::now())
            ->delete();

        $this->line("   - Unblocked {$expiredBlocks} expired IP addresses");
    }

    private function showSecurityStats(): void
    {
        $this->info('📊 Security Statistics:');

        // Login attempts in last 24 hours
        $recentAttempts = LoginAttempt::where('attempted_at', '>=', Carbon::now()->subDay())->count();
        $recentSuccessful = LoginAttempt::where('attempted_at', '>=', Carbon::now()->subDay())
            ->where('successful', true)->count();
        $recentFailed = $recentAttempts - $recentSuccessful;

        $this->line("   📈 Login Attempts (24h): {$recentAttempts} total, {$recentSuccessful} successful, {$recentFailed} failed");

        // Currently blocked IPs
        $blockedIPs = BlockedIP::count();
        $permanentBlocks = BlockedIP::where('is_permanent', true)->count();
        $temporaryBlocks = $blockedIPs - $permanentBlocks;

        $this->line("   🚫 Blocked IPs: {$blockedIPs} total ({$permanentBlocks} permanent, {$temporaryBlocks} temporary)");

        // Active user sessions
        $activeSessions = UserSession::where('last_activity', '>=', Carbon::now()->subMinutes(30))->count();
        $this->line("   👥 Active Sessions: {$activeSessions}");

        // Top failed login IPs (last 7 days)
        $topFailedIPs = LoginAttempt::where('attempted_at', '>=', Carbon::now()->subWeek())
            ->where('successful', false)
            ->selectRaw('ip_address, COUNT(*) as failed_count')
            ->groupBy('ip_address')
            ->orderBy('failed_count', 'desc')
            ->limit(5)
            ->get();

        if ($topFailedIPs->count() > 0) {
            $this->line("   🎯 Top Failed Login IPs (7d):");
            foreach ($topFailedIPs as $ip) {
                $this->line("      - {$ip->ip_address}: {$ip->failed_count} attempts");
            }
        }

        // Suspicious activity alerts
        $suspiciousLogins = \App\Models\AuthActivityLog::where('action', 'suspicious_login')
            ->where('created_at', '>=', Carbon::now()->subDay())
            ->count();

        if ($suspiciousLogins > 0) {
            $this->warn("   ⚠️  Suspicious Activity: {$suspiciousLogins} suspicious login(s) detected in last 24h");
        }
    }
}
