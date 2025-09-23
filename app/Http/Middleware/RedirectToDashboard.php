<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectToDashboard
{
    /**
     * Handle an incoming request.
     * Redirect user ke dashboard yang sesuai berdasarkan role mereka
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Jika user sudah login, redirect ke dashboard sesuai role
        if (Auth::check()) {
            $user = Auth::user();
            
            // Cek apakah user mengakses root URL atau halaman yang tidak sesuai dengan role
            $currentRoute = $request->route()->getName();
            
            // Jika mengakses root URL atau halaman yang tidak sesuai
            if ($currentRoute === 'home' || $this->shouldRedirectToDashboard($currentRoute, $user->role)) {
                return match ($user->role) {
                    'Admin'    => redirect()->route('admin.dashboard'),
                    'Operator' => redirect()->route('operator.dashboard'), 
                    'User'     => redirect()->route('user.dashboard'),
                    default    => redirect()->route('login')->withErrors(['role' => 'Role tidak valid']),
                };
            }
        }

        return $next($request);
    }

    /**
     * Tentukan apakah user harus di-redirect ke dashboard
     */
    private function shouldRedirectToDashboard(string $currentRoute, string $userRole): bool
    {
        // Daftar route yang harus di-redirect berdasarkan role
        $redirectRules = [
            'Admin' => [
                'operator.dashboard',
                'user.dashboard',
            ],
            'Operator' => [
                'admin.dashboard', 
                'user.dashboard',
            ],
            'User' => [
                'admin.dashboard',
                'operator.dashboard',
            ],
        ];

        // Jika user mengakses dashboard yang bukan untuk role mereka
        return in_array($currentRoute, $redirectRules[$userRole] ?? []);
    }
}
