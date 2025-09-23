<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use App\Models\UserSession;
use App\Models\RememberToken;
use App\Models\AuthActivityLog;
use App\Models\BlockedIP;
use Carbon\Carbon;

class SecureSessionMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $ipAddress = $request->ip();
        $userAgent = $request->userAgent();

        // Check if IP is blocked
        if (BlockedIP::isBlocked($ipAddress)) {
            Auth::logout();
            Session::flush();
            return redirect()->route('login')->withErrors([
                'security' => 'Access denied from this IP address.'
            ]);
        }

        // If user is authenticated
        if (Auth::check()) {
            $user = Auth::user();
            $sessionId = Session::getId();

            // Validate current session
            $userSession = UserSession::where('session_id', $sessionId)
                ->where('user_id', $user->id)
                ->first();

            if (!$userSession) {
                // Session not found in database, create new one
                $deviceFingerprint = UserSession::generateDeviceFingerprint($userAgent, $ipAddress);
                UserSession::createOrUpdate($user->id, $sessionId, $ipAddress, $userAgent, $deviceFingerprint);
            } else {
                // Validate session integrity
                if (!$this->validateSessionIntegrity($userSession, $request)) {
                    $this->logSuspiciousActivity($user, $request, 'Session integrity validation failed');
                    Auth::logout();
                    Session::flush();
                    return redirect()->route('login')->withErrors([
                        'security' => 'Session validation failed. Please login again.'
                    ]);
                }

                // Update last activity
                $userSession->update([
                    'last_activity' => Carbon::now(),
                    'ip_address' => $ipAddress, // Update IP if changed
                ]);
            }

            // Check for session timeout (configurable)
            $sessionTimeout = config('session.lifetime', 120); // minutes
            if ($userSession && $userSession->last_activity->diffInMinutes(Carbon::now()) > $sessionTimeout) {
                $this->logSuspiciousActivity($user, $request, 'Session timeout exceeded');
                Auth::logout();
                Session::flush();
                return redirect()->route('login')->withErrors([
                    'security' => 'Session expired due to inactivity.'
                ]);
            }

            // Check for concurrent sessions (optional - can be configured)
            $this->checkConcurrentSessions($user, $sessionId);
        }

        // Handle remember me token validation
        if (!Auth::check() && $request->hasCookie('remember_token')) {
            $this->handleRememberToken($request);
        }

        return $next($request);
    }

    /**
     * Validate session integrity
     */
    private function validateSessionIntegrity(UserSession $userSession, Request $request): bool
    {
        $currentFingerprint = UserSession::generateDeviceFingerprint(
            $request->userAgent(),
            $request->ip()
        );

        // Allow some flexibility for IP changes (mobile users)
        $ipChanged = $userSession->ip_address !== $request->ip();
        $userAgentChanged = $userSession->user_agent !== $request->userAgent();

        // If both IP and User Agent changed, it's suspicious
        if ($ipChanged && $userAgentChanged) {
            return false;
        }

        // If device fingerprint completely changed, it's suspicious
        if ($userSession->device_fingerprint !== $currentFingerprint) {
            // Allow if it's a trusted device
            if (!$userSession->is_trusted_device) {
                return false;
            }
        }

        return true;
    }

    /**
     * Handle remember me token
     */
    private function handleRememberToken(Request $request): void
    {
        $token = $request->cookie('remember_token');
        
        if ($token) {
            $user = RememberToken::validateToken(
                $token,
                $request->ip(),
                $request->userAgent()
            );

            if ($user) {
                Auth::login($user);
                
                // Create new session
                $deviceFingerprint = UserSession::generateDeviceFingerprint(
                    $request->userAgent(),
                    $request->ip()
                );
                
                UserSession::createOrUpdate(
                    $user->id,
                    Session::getId(),
                    $request->ip(),
                    $request->userAgent(),
                    $deviceFingerprint
                );

                // Log successful remember me login
                AuthActivityLog::log(
                    'remember_me_login',
                    'success',
                    $user->email,
                    $user->id,
                    'Automatic login via remember me token'
                );
            } else {
                // Invalid token, remove cookie
                cookie()->queue(cookie()->forget('remember_token'));
                
                AuthActivityLog::log(
                    'invalid_remember_token',
                    'warning',
                    null,
                    null,
                    'Invalid remember me token attempted from IP: ' . $request->ip()
                );
            }
        }
    }

    /**
     * Check for concurrent sessions
     */
    private function checkConcurrentSessions(User $user, string $currentSessionId): void
    {
        $maxConcurrentSessions = config('auth.max_concurrent_sessions', 3);
        
        $activeSessions = UserSession::where('user_id', $user->id)
            ->where('last_activity', '>', Carbon::now()->subMinutes(config('session.lifetime', 120)))
            ->where('session_id', '!=', $currentSessionId)
            ->count();

        if ($activeSessions >= $maxConcurrentSessions) {
            // Optionally terminate oldest sessions
            UserSession::where('user_id', $user->id)
                ->where('session_id', '!=', $currentSessionId)
                ->orderBy('last_activity', 'asc')
                ->limit($activeSessions - $maxConcurrentSessions + 1)
                ->delete();

            AuthActivityLog::log(
                'concurrent_session_limit',
                'warning',
                $user->email,
                $user->id,
                "Concurrent session limit exceeded. Terminated {$activeSessions} old sessions."
            );
        }
    }

    /**
     * Log suspicious activity
     */
    private function logSuspiciousActivity(User $user, Request $request, string $reason): void
    {
        AuthActivityLog::log(
            'suspicious_activity',
            'warning',
            $user->email,
            $user->id,
            "Suspicious activity detected: {$reason}. IP: {$request->ip()}, User Agent: {$request->userAgent()}"
        );
    }
}
