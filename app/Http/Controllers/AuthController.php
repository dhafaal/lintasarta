<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use App\Models\User;
use App\Models\AuthActivityLog;
use App\Models\LoginAttempt;
use App\Models\BlockedIP;
use App\Models\UserSession;
use App\Models\RememberToken;

class AuthController extends Controller
{
    /**
     * Halaman login
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Proses login dengan keamanan tambahan
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|min:8',
        ]);

        $ipAddress = $request->ip();
        $userAgent = $request->userAgent();
        $email = $request->email;

        // Check if IP is blocked
        if (BlockedIP::isBlocked($ipAddress)) {
            $blockInfo = BlockedIP::getBlockInfo($ipAddress);
            AuthActivityLog::log(
                'blocked_login_attempt',
                'blocked',
                $email,
                null,
                "Login attempt from blocked IP: {$ipAddress}"
            );
            
            return back()->withErrors([
                'email' => 'Your IP address is temporarily blocked due to suspicious activity. Please try again later.'
            ]);
        }

        // Check if email is locked out
        if (LoginAttempt::isEmailLockedOut($email)) {
            $timeRemaining = LoginAttempt::getLockoutTimeRemaining($email);
            AuthActivityLog::log(
                'locked_out_login_attempt',
                'blocked',
                $email,
                null,
                "Login attempt for locked out email: {$email}"
            );
            
            return back()->withErrors([
                'email' => "Account temporarily locked due to too many failed attempts. Please try again in {$timeRemaining} minutes."
            ]);
        }

        // Check rate limiting
        $this->ensureIsNotRateLimited($request);

        $credentials = $request->only('email', 'password');
        $remember = $request->filled('remember');

        if (Auth::attempt($credentials, false)) { // Don't use Laravel's built-in remember
            $request->session()->regenerate();
            RateLimiter::clear($this->throttleKey($request));

            $user = Auth::user();

            // Record successful login attempt
            LoginAttempt::record($email, $ipAddress, $userAgent, true);
            
            // Clear previous failed attempts for this email
            LoginAttempt::clearSuccessfulAttempts($email);

            // Create or update user session
            $deviceFingerprint = UserSession::generateDeviceFingerprint($userAgent, $ipAddress);
            $userSession = UserSession::createOrUpdate(
                $user->id,
                $request->session()->getId(),
                $ipAddress,
                $userAgent,
                $deviceFingerprint
            );

            // Handle remember me with custom secure token
            if ($remember) {
                $this->handleRememberMe($user->id, $ipAddress, $userAgent);
            }

            // Check for suspicious activity
            $suspiciousActivities = UserSession::checkSuspiciousActivity($user->id);
            if (!empty($suspiciousActivities)) {
                AuthActivityLog::log(
                    'suspicious_login',
                    'warning',
                    $email,
                    $user->id,
                    "Suspicious login detected: " . implode(', ', $suspiciousActivities)
                );
            }

            // Log successful login
            AuthActivityLog::log(
                'login',
                'success',
                $request->email,
                $user->id,
                "Login berhasil sebagai {$user->role}" . (!$userSession->is_trusted_device ? ' (New Device)' : '') . ($remember ? ' (Remember Me)' : '')
            );

            // Redirect ke intended URL atau dashboard sesuai role
            $intendedUrl = session('url.intended');
            
            if ($intendedUrl && $this->isValidIntendedUrl($intendedUrl, $user->role)) {
                session()->forget('url.intended');
                return redirect($intendedUrl);
            }

            return match ($user->role) {
                'Admin'    => redirect()->route('admin.dashboard'),
                'Operator' => redirect()->route('operator.dashboard'),
                'User'     => redirect()->route('user.dashboard'),
                default    => tap(function () {
                    Auth::logout();
                })() ?? redirect()->route('login')->withErrors(['role' => 'Role tidak valid']),
            };
        }

        // Record failed login attempt
        LoginAttempt::record($email, $ipAddress, $userAgent, false, 'Invalid credentials');
        
        // Check if we should auto-block this IP
        $ipFailedAttempts = LoginAttempt::getFailedAttemptsForIP($ipAddress, 60);
        BlockedIP::autoBlockIP($ipAddress, $ipFailedAttempts);

        RateLimiter::hit($this->throttleKey($request), $seconds = 60);

        // Log failed login attempt
        AuthActivityLog::log(
            'failed_login',
            'failed',
            $request->email,
            null,
            "Login gagal untuk email: {$request->email} dari IP: {$ipAddress}"
        );

        throw ValidationException::withMessages([
            'email' => __('Email atau password salah.'),
        ]);
    }

    protected function ensureIsNotRateLimited(Request $request)
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey($request), 5)) {
            return;
        }

        throw ValidationException::withMessages([
            'email' => __('Terlalu banyak percobaan login. Coba lagi dalam :seconds detik.', [
                'seconds' => RateLimiter::availableIn($this->throttleKey($request)),
            ]),
        ]);
    }

    protected function throttleKey(Request $request)
    {
        return Str::lower($request->input('email')).'|'.$request->ip();
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        $sessionId = $request->session()->getId();
        $ipAddress = $request->ip();
        $userAgent = $request->userAgent();

        if ($user) {
            // Remove user session from database
            UserSession::where('session_id', $sessionId)
                ->where('user_id', $user->id)
                ->delete();

            // Revoke remember token if exists
            if ($request->hasCookie('remember_token')) {
                $deviceFingerprint = RememberToken::generateDeviceFingerprint($userAgent, $ipAddress);
                RememberToken::revokeAllForDevice($deviceFingerprint);
                cookie()->queue(cookie()->forget('remember_token'));
            }

            // Log logout activity
            AuthActivityLog::log(
                'logout',
                'success',
                $user->email,
                $user->id,
                "Logout berhasil dari IP: {$ipAddress}"
            );
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Anda telah berhasil logout.');
    }

    // ------------------------
    // FORGOT PASSWORD (STEP HANDLING)
    // ------------------------

    public function showForgotPassword()
    {
        return view('auth.forgot-password')->with([
            'step'  => 'email',
            'email' => null,
        ]);
    }

    public function sendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $otp = rand(100000, 999999);

        DB::table('password_otps')->updateOrInsert(
            ['email' => $request->email],
            [
                'otp'        => $otp,
                'expires_at' => Carbon::now()->addMinutes(5),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // kirim OTP ke email (sementara ke log/mailhog)
        Mail::raw("Kode OTP reset password kamu adalah: $otp", function ($m) use ($request) {
            $m->to($request->email)->subject('OTP Reset Password');
        });

        return back()->with([
            'status' => 'OTP sudah dikirim ke email.',
            'step'   => 'otp',
            'email'  => $request->email,
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp'   => 'required|numeric',
        ]);

        $record = DB::table('password_otps')->where('email', $request->email)->first();

        if (! $record || $record->otp != $request->otp || Carbon::now()->greaterThan($record->expires_at)) {
            return back()->withErrors(['otp' => 'OTP tidak valid atau sudah expired'])
                         ->with(['step' => 'otp', 'email' => $request->email]);
        }

        return back()->with([
            'status' => 'OTP valid, silakan reset password.',
            'step'   => 'reset',
            'email'  => $request->email,
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email'    => 'required|email|exists:users,email',
            'password' => 'required|confirmed|min:8',
        ]);

        User::where('email', $request->email)->update([
            'password' => Hash::make($request->password),
        ]);

        DB::table('password_otps')->where('email', $request->email)->delete();

        // Log password reset
        $user = User::where('email', $request->email)->first();
        if ($user) {
            AuthActivityLog::log(
                'password_reset',
                'success',
                $request->email,
                $user->id,
                "Password berhasil direset"
            );
        }

        return redirect()->route('login')->with('status', 'Password berhasil direset. Silakan login dengan password baru.');
    }

    /**
     * Handle remember me token creation
     */
    private function handleRememberMe(int $userId, string $ipAddress, string $userAgent): void
    {
        // Clean up old tokens for this user
        RememberToken::where('user_id', $userId)
            ->where('last_used_at', '<', Carbon::now()->subDays(30))
            ->delete();

        // Generate new secure token
        $tokenData = RememberToken::generateToken($userId, $ipAddress, $userAgent);
        
        // Set secure cookie (30 days)
        cookie()->queue(cookie(
            'remember_token',
            $tokenData['token'],
            60 * 24 * 30, // 30 days in minutes
            '/', // path
            null, // domain
            true, // secure (HTTPS only)
            true, // httpOnly
            false, // raw
            'Strict' // sameSite
        ));

        AuthActivityLog::log(
            'remember_token_created',
            'success',
            null,
            $userId,
            'Remember me token created for device: ' . substr(hash('sha256', $userAgent), 0, 8)
        );
    }

    public function logoutAllSessions(Request $request)
    {
        $user = Auth::user();
        
        if ($user) {
            // Remove all user sessions
            UserSession::where('user_id', $user->id)->delete();
            
            // Revoke all remember tokens
            RememberToken::revokeAllForUser($user->id);
            
            // Log security action
            AuthActivityLog::log(
                'logout_all_sessions',
                'success',
                $user->email,
                $user->id,
                "All sessions terminated by user request"
            );
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        cookie()->queue(cookie()->forget('remember_token'));

        return redirect()->route('login')->with('status', 'Semua sesi telah dihentikan. Silakan login kembali.');
    }

    /**
     * Get user's active sessions
     */
    public function getActiveSessions(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $sessions = UserSession::where('user_id', $user->id)
            ->where('last_activity', '>', Carbon::now()->subMinutes(config('session.lifetime', 120)))
            ->orderBy('last_activity', 'desc')
            ->get()
            ->map(function ($session) use ($request) {
                return [
                    'id' => $session->id,
                    'ip_address' => $session->ip_address,
                    'user_agent' => $session->user_agent,
                    'is_current' => $session->session_id === $request->session()->getId(),
                    'is_trusted' => $session->is_trusted_device,
                    'last_activity' => $session->last_activity->diffForHumans(),
                    'location' => $this->getLocationFromIP($session->ip_address),
                ];
            });

        return response()->json(['sessions' => $sessions]);
    }

    /**
     * Terminate specific session
     */
    public function terminateSession(Request $request, $sessionId)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $session = UserSession::where('user_id', $user->id)
            ->where('id', $sessionId)
            ->first();

        if (!$session) {
            return response()->json(['error' => 'Session not found'], 404);
        }

        // Don't allow terminating current session
        if ($session->session_id === $request->session()->getId()) {
            return response()->json(['error' => 'Cannot terminate current session'], 400);
        }

        $session->delete();

        AuthActivityLog::log(
            'session_terminated',
            'success',
            $user->email,
            $user->id,
            "Session terminated: {$session->ip_address}"
        );

        return response()->json(['message' => 'Session terminated successfully']);
    }

    /**
     * Get approximate location from IP (basic implementation)
     */
    private function getLocationFromIP(string $ipAddress): string
    {
        // This is a basic implementation
        // In production, you might want to use a proper IP geolocation service
        if ($ipAddress === '127.0.0.1' || $ipAddress === '::1') {
            return 'Local Machine';
        }
        
        // You can integrate with services like MaxMind, IPinfo, etc.
        return 'Unknown Location';
    }

    /**
     * Validate if intended URL is accessible for user role
     */
    private function isValidIntendedUrl(string $url, string $userRole): bool
    {
        // Parse URL to get path
        $path = parse_url($url, PHP_URL_PATH);
        
        // Define role-based access patterns
        $rolePatterns = [
            'Admin' => [
                '/admin/',
                '/operator/', // Admin can access operator pages
                '/user/',     // Admin can access user pages
            ],
            'Operator' => [
                '/operator/',
                '/user/',     // Operator can access user pages
            ],
            'User' => [
                '/user/',
            ],
        ];

        // Check if path matches allowed patterns for user role
        $allowedPatterns = $rolePatterns[$userRole] ?? [];
        
        foreach ($allowedPatterns as $pattern) {
            if (strpos($path, $pattern) === 0) {
                return true;
            }
        }

        // Don't redirect to login or auth pages
        $authPages = ['/login', '/register', '/password', '/auth/'];
        foreach ($authPages as $authPage) {
            if (strpos($path, $authPage) === 0) {
                return false;
            }
        }

        return false;
    }
}
