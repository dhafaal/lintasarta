<?php

namespace App\Http\Middleware;

use App\Models\RememberToken;
use App\Models\UserSession;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RememberMeMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            // Skip if already authenticated
            if (!Auth::check()) {
                // Read remember token cookie if present
                $cookieName = 'remember_token';
                $token = $request->cookie($cookieName);

                if (!empty($token)) {
                    $ip = $request->ip();
                    $ua = $request->userAgent() ?? '';

                    // Validate token and get user
                    $user = RememberToken::validateToken($token, $ip, $ua);

                    if ($user) {
                        // Login the user without setting remember (we manage token ourselves)
                        Auth::login($user, false);

                        // Regenerate session to prevent fixation
                        $request->session()->regenerate();

                        // Ensure a UserSession record exists/updates (optional but aligns with AuthController)
                        if (class_exists(UserSession::class)) {
                            $fingerprint = UserSession::generateDeviceFingerprint($ua, $ip);
                            UserSession::createOrUpdate(
                                $user->id,
                                $request->session()->getId(),
                                $ip,
                                $ua,
                                $fingerprint
                            );
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::warning('RememberMeMiddleware error', [
                'message' => $e->getMessage(),
            ]);
        }

        return $next($request);
    }
}
