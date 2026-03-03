<?php

namespace App\Http\Middleware;

use App\Models\UserSession;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Enforce single concurrent session per user.
 * If the stored active session_token doesn't match the current session ID,
 * the user is logged out (their account was used elsewhere).
 * Also updates last_active_at on every request.
 */
class SingleSession
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user      = Auth::user();
            $sessionId = $request->session()->getId();

            // Find an active session for this user
            $activeSession = UserSession::where('user_id', $user->id)
                ->where('is_active', true)
                ->latest('logged_in_at')
                ->first();

            if ($activeSession) {
                // If token doesn't match → another login invalidated this session
                if ($activeSession->session_token !== $sessionId) {
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    return redirect()->route('login')
                        ->withErrors(['session' =>
                            'Sizning hisobingizga boshqa qurilmadan kirilgan. Qayta kiring.'
                        ]);
                }

                // Update heartbeat (throttled: only if last update > 60s ago)
                if (!$activeSession->last_active_at ||
                    $activeSession->last_active_at->lt(now()->subMinutes(1))) {
                    $activeSession->update(['last_active_at' => now()]);
                }
            }
        }

        return $next($request);
    }
}
