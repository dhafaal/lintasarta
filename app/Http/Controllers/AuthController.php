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

        if (Auth::attempt($credentials, $remember)) {
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
                "Login berhasil sebagai {$user->role}" . (!$userSession->is_trusted_device ? ' (New Device)' : '')
            );

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
        
        // Log logout before actually logging out
        if ($user) {
            AuthActivityLog::log(
                'logout',
                'success',
                $user->email,
                $user->id,
                "Logout berhasil"
            );
        }
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Anda berhasil logout.');
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
}
