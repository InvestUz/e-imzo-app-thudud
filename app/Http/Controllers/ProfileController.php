<?php

namespace App\Http\Controllers;

use App\Models\UserSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $sessions = UserSession::where('user_id', $user->id)
            ->latest('logged_in_at')
            ->limit(10)
            ->get();

        $activeSessions = $sessions->where('is_active', true);

        return view('profile.show', compact('user', 'sessions', 'activeSessions'));
    }

    /** Terminate a specific session owned by the authenticated user */
    public function terminateSession(UserSession $session)
    {
        abort_unless($session->user_id === Auth::id(), 403);
        $session->terminate();
        return back()->with('success', 'Sessiya tugatildi.');
    }

    /** Terminate ALL sessions (force re-login everywhere) */
    public function terminateAllSessions(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        UserSession::where('user_id', $user->id)
            ->where('is_active', true)
            ->get()
            ->each(fn($s) => $s->terminate());

        // Also log out this session
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Barcha sessiyalar tugatildi. Qayta kiring.');
    }
}
