<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAuthenticated
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            // Jika request adalah AJAX, return JSON response
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            
            // Simpan intended URL untuk redirect setelah login
            session(['url.intended' => $request->url()]);
            
            return redirect()->route('login')->with('message', 'Silakan login terlebih dahulu.');
        }

        return $next($request);
    }
}
