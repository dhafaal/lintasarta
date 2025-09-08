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

        $this->ensureIsNotRateLimited($request);

        $credentials = $request->only('email', 'password');
        $remember    = $request->filled('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            RateLimiter::clear($this->throttleKey($request));

            $user = Auth::user();

            return match ($user->role) {
                'Admin'    => redirect()->route('admin.dashboard'),
                'Operator' => redirect()->route('operator.dashboard'),
                'User'     => redirect()->route('user.dashboard'),
                default    => tap(function () {
                    Auth::logout();
                })() ?? redirect()->route('login')->withErrors(['role' => 'Role tidak valid']),
            };
        }

        RateLimiter::hit($this->throttleKey($request), $seconds = 60);

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

        return redirect()->route('login')->with('status', 'Password berhasil direset. Silakan login dengan password baru.');
    }
}
