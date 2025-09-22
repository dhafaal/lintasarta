<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\BlockedIP;
use App\Models\AuthActivityLog;
use Symfony\Component\HttpFoundation\Response;

class BlockSuspiciousIPs
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ipAddress = $request->ip();

        // Check if IP is blocked
        if (BlockedIP::isBlocked($ipAddress)) {
            $blockInfo = BlockedIP::getBlockInfo($ipAddress);
            
            // Log blocked access attempt
            AuthActivityLog::log(
                'blocked_access',
                'blocked',
                null,
                null,
                "Blocked IP {$ipAddress} attempted to access the system. Reason: {$blockInfo->reason}"
            );

            // Return blocked response
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Access denied',
                    'message' => 'Your IP address has been blocked due to suspicious activity.',
                    'reason' => $blockInfo->reason,
                    'blocked_at' => $blockInfo->blocked_at->format('Y-m-d H:i:s'),
                    'time_remaining' => $blockInfo->getTimeRemaining(),
                ], 403);
            }

            return response()->view('auth.blocked', [
                'blockInfo' => $blockInfo,
                'timeRemaining' => $blockInfo->getTimeRemaining(),
            ], 403);
        }

        return $next($request);
    }
}
