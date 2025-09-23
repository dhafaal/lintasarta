<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SetupSecuritySystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'security:setup {--force : Force setup without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup security system tables and initial configuration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔐 Setting up Security System...');

        if (!$this->option('force') && !$this->confirm('This will setup security system. Continue?')) {
            $this->info('Setup cancelled.');
            return 0;
        }

        // Check if migrations are run
        $this->checkMigrations();

        // Setup initial configuration
        $this->setupConfiguration();

        // Create indexes for performance
        $this->createIndexes();

        // Display setup summary
        $this->displaySummary();

        $this->info('✅ Security system setup completed successfully!');
        return 0;
    }

    /**
     * Check if required migrations are run
     */
    private function checkMigrations(): void
    {
        $this->info('📋 Checking required tables...');

        $requiredTables = [
            'users',
            'user_sessions',
            'remember_tokens',
            'auth_activity_logs',
            'login_attempts',
            'blocked_ips',
        ];

        $missingTables = [];
        foreach ($requiredTables as $table) {
            if (!Schema::hasTable($table)) {
                $missingTables[] = $table;
            }
        }

        if (!empty($missingTables)) {
            $this->error('❌ Missing required tables: ' . implode(', ', $missingTables));
            $this->info('Run: php artisan migrate');
            exit(1);
        }

        $this->info('✅ All required tables exist.');
    }

    /**
     * Setup initial configuration
     */
    private function setupConfiguration(): void
    {
        $this->info('⚙️ Setting up configuration...');

        // Check if security config exists
        if (!file_exists(config_path('security.php'))) {
            $this->error('❌ Security config file not found at config/security.php');
            exit(1);
        }

        // Validate session configuration
        if (config('session.driver') !== 'database') {
            $this->warn('⚠️ Session driver is not set to database. Consider changing SESSION_DRIVER=database in .env');
        }

        $this->info('✅ Configuration validated.');
    }

    /**
     * Create additional indexes for performance
     */
    private function createIndexes(): void
    {
        $this->info('🚀 Creating performance indexes...');

        try {
            // User sessions indexes
            if (Schema::hasTable('user_sessions')) {
                $this->createIndexIfNotExists('user_sessions', 'idx_user_sessions_last_activity', 'last_activity');
                $this->createIndexIfNotExists('user_sessions', 'idx_user_sessions_user_active', 'user_id, last_activity');
            }

            // Auth activity logs indexes
            if (Schema::hasTable('auth_activity_logs')) {
                $this->createIndexIfNotExists('auth_activity_logs', 'idx_auth_logs_created_at', 'created_at');
                $this->createIndexIfNotExists('auth_activity_logs', 'idx_auth_logs_user_action', 'user_id, action');
            }

            // Login attempts indexes
            if (Schema::hasTable('login_attempts')) {
                $this->createIndexIfNotExists('login_attempts', 'idx_login_attempts_email_time', 'email, attempted_at');
                $this->createIndexIfNotExists('login_attempts', 'idx_login_attempts_ip_time', 'ip_address, attempted_at');
            }

            // Remember tokens indexes
            if (Schema::hasTable('remember_tokens')) {
                $this->createIndexIfNotExists('remember_tokens', 'idx_remember_tokens_user_revoked', 'user_id, is_revoked');
                $this->createIndexIfNotExists('remember_tokens', 'idx_remember_tokens_expires', 'expires_at, is_revoked');
            }

            $this->info('✅ Performance indexes created.');
        } catch (\Exception $e) {
            $this->warn('⚠️ Some indexes may already exist: ' . $e->getMessage());
        }
    }

    /**
     * Create index if not exists (MySQL compatible)
     */
    private function createIndexIfNotExists(string $table, string $indexName, string $columns): void
    {
        try {
            // Check if index exists
            $indexExists = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]);
            
            if (empty($indexExists)) {
                DB::statement("CREATE INDEX {$indexName} ON {$table}({$columns})");
                $this->info("  ✓ Created index: {$indexName}");
            } else {
                $this->info("  - Index already exists: {$indexName}");
            }
        } catch (\Exception $e) {
            $this->warn("  ⚠️ Could not create index {$indexName}: " . $e->getMessage());
        }
    }

    /**
     * Display setup summary
     */
    private function displaySummary(): void
    {
        $this->info('');
        $this->info('📊 Security System Summary:');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        // Count records safely
        $stats = [];
        
        try {
            $stats['Users'] = DB::table('users')->count();
        } catch (\Exception $e) {
            $stats['Users'] = 'N/A';
        }

        try {
            if (Schema::hasTable('user_sessions')) {
                $stats['Active Sessions'] = DB::table('user_sessions')->where('last_activity', '>', now()->subHours(2))->count();
            } else {
                $stats['Active Sessions'] = 'N/A';
            }
        } catch (\Exception $e) {
            $stats['Active Sessions'] = 'N/A';
        }

        try {
            if (Schema::hasTable('remember_tokens')) {
                $stats['Remember Tokens'] = DB::table('remember_tokens')->where('is_revoked', false)->count();
            } else {
                $stats['Remember Tokens'] = 'N/A';
            }
        } catch (\Exception $e) {
            $stats['Remember Tokens'] = 'N/A';
        }

        try {
            if (Schema::hasTable('auth_activity_logs')) {
                $stats['Auth Logs (24h)'] = DB::table('auth_activity_logs')->where('created_at', '>', now()->subDay())->count();
            } else {
                $stats['Auth Logs (24h)'] = 'N/A';
            }
        } catch (\Exception $e) {
            $stats['Auth Logs (24h)'] = 'N/A';
        }

        try {
            if (Schema::hasTable('blocked_ips')) {
                // Check if expires_at column exists
                if (Schema::hasColumn('blocked_ips', 'expires_at')) {
                    $stats['Blocked IPs'] = DB::table('blocked_ips')->where('expires_at', '>', now())->count();
                } else {
                    $stats['Blocked IPs'] = DB::table('blocked_ips')->count();
                }
            } else {
                $stats['Blocked IPs'] = 'N/A';
            }
        } catch (\Exception $e) {
            $stats['Blocked IPs'] = 'N/A';
        }

        foreach ($stats as $label => $count) {
            if (is_numeric($count)) {
                $this->info(sprintf('%-20s: %d', $label, $count));
            } else {
                $this->info(sprintf('%-20s: %s', $label, $count));
            }
        }

        $this->info('');
        $this->info('🔧 Available Commands:');
        $this->info('  php artisan security:cleanup    - Clean expired data');
        $this->info('  php artisan tinker              - Inspect security data');
        $this->info('');
        $this->info('🌐 Access Points:');
        $this->info('  /                               - Auto-redirect to dashboard');
        $this->info('  /login                          - Enhanced login page');
        $this->info('  /auth/sessions-page             - Session management');
        $this->info('  /admin/security                 - Security dashboard (Admin only)');
        $this->info('');
    }
}
