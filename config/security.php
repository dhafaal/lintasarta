<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Authentication Security Settings
    |--------------------------------------------------------------------------
    |
    | These settings control various security aspects of the authentication
    | system including rate limiting, session management, and device tracking.
    |
    */

    'rate_limiting' => [
        'max_attempts' => 5,
        'decay_minutes' => 15,
        'lockout_duration' => 30, // minutes
    ],

    'session' => [
        'max_concurrent_sessions' => 3,
        'session_timeout' => 120, // minutes
        'auto_logout_inactive' => 30, // minutes
        'require_device_verification' => true,
    ],

    'remember_me' => [
        'enabled' => true,
        'token_lifetime' => 30, // days
        'max_tokens_per_user' => 5,
        'secure_cookies_only' => true,
        'auto_cleanup_expired' => true,
    ],

    'ip_blocking' => [
        'enabled' => true,
        'max_failed_attempts' => 10,
        'block_duration' => 60, // minutes
        'auto_block_suspicious_ips' => true,
        'whitelist' => [
            '127.0.0.1',
            '::1',
        ],
    ],

    'device_fingerprinting' => [
        'enabled' => true,
        'trust_known_devices' => true,
        'require_verification_new_device' => false,
        'device_memory_days' => 90,
    ],

    'suspicious_activity' => [
        'detect_location_changes' => true,
        'detect_user_agent_changes' => true,
        'detect_concurrent_logins' => true,
        'auto_logout_suspicious' => false,
        'notify_admin' => true,
    ],

    'two_factor_auth' => [
        'enabled' => false,
        'required_for_admin' => false,
        'backup_codes_count' => 8,
        'totp_window' => 30, // seconds
    ],

    'password_security' => [
        'min_length' => 8,
        'require_uppercase' => true,
        'require_lowercase' => true,
        'require_numbers' => true,
        'require_symbols' => false,
        'prevent_common_passwords' => true,
        'password_history' => 5, // prevent reusing last N passwords
    ],

    'audit_logging' => [
        'log_all_auth_attempts' => true,
        'log_session_activities' => true,
        'log_security_events' => true,
        'retention_days' => 90,
        'auto_cleanup' => true,
    ],

    'notifications' => [
        'notify_new_device_login' => true,
        'notify_suspicious_activity' => true,
        'notify_password_changes' => true,
        'notify_account_lockout' => true,
    ],
];
