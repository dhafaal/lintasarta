<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RememberToken;
use App\Models\UserSession;
use App\Models\LoginAttempt;
use App\Models\BlockedIP;
use App\Models\AuthActivityLog;
use Carbon\Carbon;

class CleanupSecurityData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'security:cleanup {--force : Force cleanup without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up expired security data (tokens, sessions, logs, etc.)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('force') && !$this->confirm('This will clean up expired security data. Continue?')) {
            $this->info('Cleanup cancelled.');
            return 0;
        }

        $this->info('Starting security data cleanup...');

        // Cleanup expired remember tokens
        $expiredTokens = RememberToken::cleanupExpired();
        $this->info("Cleaned up {$expiredTokens} expired remember tokens.");

        // Cleanup inactive sessions
        $inactiveSessions = $this->cleanupInactiveSessions();
        $this->info("Cleaned up {$inactiveSessions} inactive sessions.");

        // Cleanup old login attempts
        $oldAttempts = $this->cleanupOldLoginAttempts();
        $this->info("Cleaned up {$oldAttempts} old login attempts.");

        // Cleanup expired IP blocks
        $expiredBlocks = $this->cleanupExpiredIPBlocks();
        $this->info("Cleaned up {$expiredBlocks} expired IP blocks.");

        // Cleanup old activity logs
        $oldLogs = $this->cleanupOldActivityLogs();
        $this->info("Cleaned up {$oldLogs} old activity logs.");

        $this->info('Security data cleanup completed successfully!');
        return 0;
    }

    /**
     * Clean up inactive user sessions
     */
    private function cleanupInactiveSessions(): int
    {
        $sessionTimeout = config('session.lifetime', 120); // minutes
        $cutoffTime = Carbon::now()->subMinutes($sessionTimeout);

        return UserSession::where('last_activity', '<', $cutoffTime)->delete();
    }

    /**
     * Clean up old login attempts
     */
    private function cleanupOldLoginAttempts(): int
    {
        $retentionDays = config('security.audit_logging.retention_days', 90);
        $cutoffTime = Carbon::now()->subDays($retentionDays);

        return LoginAttempt::where('created_at', '<', $cutoffTime)->delete();
    }

    /**
     * Clean up expired IP blocks
     */
    private function cleanupExpiredIPBlocks(): int
    {
        return BlockedIP::where('expires_at', '<', Carbon::now())
            ->where('is_permanent', false)
            ->delete();
    }

    /**
     * Clean up old activity logs
     */
    private function cleanupOldActivityLogs(): int
    {
        $retentionDays = config('security.audit_logging.retention_days', 90);
        $cutoffTime = Carbon::now()->subDays($retentionDays);

        return AuthActivityLog::where('created_at', '<', $cutoffTime)->delete();
    }
}
