<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardRedirectController extends Controller
{
    /**
     * Redirect user to appropriate dashboard based on their role
     */
    public function redirectToDashboard(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // Log dashboard access
        \App\Models\AuthActivityLog::log(
            'dashboard_access',
            'success',
            $user->email,
            $user->id,
            "Accessing dashboard as {$user->role}"
        );

        return match ($user->role) {
            'Admin'    => redirect()->route('admin.dashboard'),
            'Operator' => redirect()->route('operator.dashboard'),
            'User'     => redirect()->route('user.dashboard'),
            default    => redirect()->route('login')->withErrors(['role' => 'Role tidak valid']),
        };
    }

    /**
     * Handle role-based access validation
     */
    public function validateRoleAccess(Request $request, string $requiredRole)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // Check if user has required role or higher privileges
        $roleHierarchy = [
            'User' => 1,
            'Operator' => 2,
            'Admin' => 3,
        ];

        $userLevel = $roleHierarchy[$user->role] ?? 0;
        $requiredLevel = $roleHierarchy[$requiredRole] ?? 999;

        if ($userLevel < $requiredLevel) {
            // Log unauthorized access attempt
            \App\Models\AuthActivityLog::log(
                'unauthorized_access',
                'warning',
                $user->email,
                $user->id,
                "Attempted to access {$requiredRole} area with {$user->role} role"
            );

            return redirect()->route($this->getDashboardRoute($user->role))
                ->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
        }

        return null; // Access granted
    }

    /**
     * Get dashboard route for role
     */
    private function getDashboardRoute(string $role): string
    {
        return match ($role) {
            'Admin'    => 'admin.dashboard',
            'Operator' => 'operator.dashboard', 
            'User'     => 'user.dashboard',
            default    => 'login',
        };
    }
}
