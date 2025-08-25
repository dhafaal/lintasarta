<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\RateLimiter;

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
        // Validasi standar
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|min:8',
        ]);

        $this->ensureIsNotRateLimited($request);

        $credentials = $request->only('email', 'password');
        $remember    = $request->filled('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            // Hapus limiter kalau berhasil login
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

        // Tambah attempt jika gagal login
        RateLimiter::hit($this->throttleKey($request), $seconds = 60);

        throw ValidationException::withMessages([
            'email' => __('Email atau password salah.'),
        ]);
    }

    /**
     * Cek brute force login
     */
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

    /**
     * Generate throttle key berdasarkan email + IP
     */
    protected function throttleKey(Request $request)
    {
        return Str::lower($request->input('email')).'|'.$request->ip();
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Anda berhasil logout.');
    }

    /**
     * Forgot Password - tampilkan form
     */
    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    /**
     * Kirim email reset password
     */
    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    /**
     * Halaman form reset password
     */
    public function showResetForm(Request $request, $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    /**
     * Proses reset password
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email|exists:users,email',
            'password' => 'required|confirmed|min:8',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password'       => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                // Auto login setelah reset
                Auth::login($user);
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __('Password berhasil direset. Silakan login dengan password baru.'))
            : back()->withErrors(['email' => [__($status)]]);
    }
}
