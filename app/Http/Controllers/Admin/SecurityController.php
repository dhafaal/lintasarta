<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoginAttempt;
use App\Models\BlockedIP;
use App\Models\UserSession;
use App\Models\AuthActivityLog;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class SecurityController extends Controller
{
    public function index(Request $request)
    {
        // Security statistics
        $stats = $this->getSecurityStats();
        
        // Recent failed login attempts
        $recentFailedAttempts = LoginAttempt::where('successful', false)
            ->where('attempted_at', '>=', Carbon::now()->subHours(24))
            ->orderBy('attempted_at', 'desc')
            ->limit(20)
            ->get();

        // Currently blocked IPs
        $blockedIPs = BlockedIP::orderBy('blocked_at', 'desc')
            ->paginate(20, ['*'], 'blocked_page');

        // Active user sessions
        $activeSessions = UserSession::with('user')
            ->where('last_activity', '>=', Carbon::now()->subMinutes(30))
            ->orderBy('last_activity', 'desc')
            ->paginate(20, ['*'], 'sessions_page');

        // Suspicious activities
        $suspiciousActivities = AuthActivityLog::with('user')
            ->where('action', 'suspicious_login')
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.security.index', compact(
            'stats',
            'recentFailedAttempts',
            'blockedIPs',
            'activeSessions',
            'suspiciousActivities'
        ));
    }

    public function blockIP(Request $request)
    {
        $request->validate([
            'ip_address' => 'required|ip',
            'reason' => 'required|string|max:255',
            'duration' => 'required|in:1,24,168,permanent', // 1h, 24h, 1week, permanent
        ]);

        $duration = $request->duration;
        $blockMinutes = match($duration) {
            '1' => 60,
            '24' => 1440,
            '168' => 10080,
            'permanent' => null,
        };

        $isPermanent = $duration === 'permanent';

        BlockedIP::blockIP(
            $request->ip_address,
            $request->reason,
            0,
            $blockMinutes,
            $isPermanent,
            auth()->user()->name
        );

        // Log the manual block
        AuthActivityLog::log(
            'manual_ip_block',
            'success',
            null,
            Auth::id(),
            "Admin manually blocked IP {$request->ip_address}. Reason: {$request->reason}"
        );

        return back()->with('success', "IP address {$request->ip_address} has been blocked successfully.");
    }

    public function unblockIP(Request $request)
    {
        $request->validate([
            'ip_address' => 'required|ip',
        ]);

        $blocked = BlockedIP::unblockIP($request->ip_address);

        if ($blocked) {
            // Log the unblock
            AuthActivityLog::log(
                'manual_ip_unblock',
                'success',
                null,
                Auth::id(),
                "Admin manually unblocked IP {$request->ip_address}"
            );

            return back()->with('success', "IP address {$request->ip_address} has been unblocked successfully.");
        }

        return back()->with('error', "IP address {$request->ip_address} was not found in blocked list.");
    }

    public function terminateSession(Request $request)
    {
        $request->validate([
            'session_id' => 'required|string',
        ]);

        $session = UserSession::where('session_id', $request->session_id)->first();

        if ($session) {
            $userName = $session->user->name;
            $session->delete();

            // Log session termination
            AuthActivityLog::log(
                'admin_session_termination',
                'success',
                null,
                Auth::id(),
                "Admin terminated session for user: {$userName}"
            );

            return back()->with('success', "Session for {$userName} has been terminated successfully.");
        }

        return back()->with('error', 'Session not found.');
    }

    public function terminateAllUserSessions(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = \App\Models\User::find($request->user_id);
        $terminatedCount = UserSession::terminateUserSessions($request->user_id);

        // Log mass session termination
        AuthActivityLog::log(
            'admin_mass_session_termination',
            'success',
            null,
            Auth::id(),
            "Admin terminated all sessions for user: {$user->name} ({$terminatedCount} sessions)"
        );

        return back()->with('success', "All {$terminatedCount} sessions for {$user->name} have been terminated.");
    }

    public function clearFailedAttempts(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $deletedCount = LoginAttempt::where('email', $request->email)
            ->where('successful', false)
            ->delete();

        // Log clearing failed attempts
        AuthActivityLog::log(
            'admin_clear_failed_attempts',
            'success',
            $request->email,
            Auth::id(),
            "Admin cleared {$deletedCount} failed login attempts for {$request->email}"
        );

        return back()->with('success', "Cleared {$deletedCount} failed login attempts for {$request->email}.");
    }

    private function getSecurityStats(): array
    {
        $now = Carbon::now();

        return [
            // Login attempts
            'total_attempts_24h' => LoginAttempt::where('attempted_at', '>=', $now->copy()->subDay())->count(),
            'successful_attempts_24h' => LoginAttempt::where('attempted_at', '>=', $now->copy()->subDay())->where('successful', true)->count(),
            'failed_attempts_24h' => LoginAttempt::where('attempted_at', '>=', $now->copy()->subDay())->where('successful', false)->count(),
            
            // Blocked IPs
            'total_blocked_ips' => BlockedIP::count(),
            'permanent_blocks' => BlockedIP::where('is_permanent', true)->count(),
            'temporary_blocks' => BlockedIP::where('is_permanent', false)->count(),
            
            // Active sessions
            'active_sessions' => UserSession::where('last_activity', '>=', $now->copy()->subMinutes(30))->count(),
            'total_sessions_24h' => UserSession::where('created_at', '>=', $now->copy()->subDay())->count(),
            
            // Suspicious activities
            'suspicious_logins_24h' => AuthActivityLog::where('action', 'suspicious_login')->where('created_at', '>=', $now->copy()->subDay())->count(),
            'blocked_attempts_24h' => AuthActivityLog::where('action', 'blocked_login_attempt')->where('created_at', '>=', $now->copy()->subDay())->count(),
            
            // Top failed IPs
            'top_failed_ips' => LoginAttempt::where('attempted_at', '>=', $now->copy()->subWeek())
                ->where('successful', false)
                ->selectRaw('ip_address, COUNT(*) as failed_count')
                ->groupBy('ip_address')
                ->orderBy('failed_count', 'desc')
                ->limit(5)
                ->get(),
        ];
    }
}
