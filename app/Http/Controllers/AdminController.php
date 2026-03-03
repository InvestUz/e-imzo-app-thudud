<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\District;
use App\Models\Notification;
use App\Models\User;
use App\Models\UserSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    // ─── Dashboard overview ────────────────────────────────────────────
    public function index()
    {
        $stats = [
            'total'    => Application::count(),
            'pending'  => Application::whereNotIn('status', ['approved', 'rejected'])->count(),
            'approved' => Application::where('status', 'approved')->count(),
            'rejected' => Application::where('status', 'rejected')->count(),
            'users'    => User::count(),
            'sessions' => UserSession::where('is_active', true)->count(),
        ];

        $recent = Application::with(['district', 'applicant'])
            ->latest('submitted_at')
            ->limit(10)
            ->get();

        $activeSessions = UserSession::with('user')
            ->where('is_active', true)
            ->latest('logged_in_at')
            ->limit(8)
            ->get();

        return view('admin.dashboard', compact('stats', 'recent', 'activeSessions'));
    }

    // ─── All applications with filters ────────────────────────────────
    public function applications(Request $request)
    {
        $query = Application::with(['district', 'applicant', 'approvals'])
            ->latest('submitted_at');

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('number', 'like', "%{$search}%")
                  ->orWhere('cadastral_number', 'like', "%{$search}%")
                  ->orWhereHas('applicant', fn($u) =>
                        $u->where('name', 'like', "%{$search}%")
                          ->orWhere('pinfl', 'like', "%{$search}%")
                  );
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($district = $request->input('district_id')) {
            $query->where('district_id', $district);
        }

        if ($from = $request->input('from')) {
            $query->whereDate('submitted_at', '>=', $from);
        }

        if ($to = $request->input('to')) {
            $query->whereDate('submitted_at', '<=', $to);
        }

        $applications = $query->paginate(20)->withQueryString();
        $districts    = District::where('is_active', true)->orderBy('name')->get();

        return view('admin.applications', compact('applications', 'districts'));
    }

    // ─── User management ──────────────────────────────────────────────
    public function users(Request $request)
    {
        $query = User::with(['district', 'activeSessions'])->latest();

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('pinfl', 'like', "%{$search}%");
            });
        }

        if ($role = $request->input('role')) {
            $query->where('role', $role);
        }

        $users = $query->paginate(20)->withQueryString();

        return view('admin.users', compact('users'));
    }

    // ─── Sessions monitor ─────────────────────────────────────────────
    public function sessions(Request $request)
    {
        $query = UserSession::with('user')->latest('logged_in_at');

        if ($request->input('active_only')) {
            $query->where('is_active', true);
        }

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('ip_address', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($u) =>
                        $u->where('name', 'like', "%{$search}%")
                          ->orWhere('pinfl', 'like', "%{$search}%")
                  );
            });
        }

        $sessions = $query->paginate(30)->withQueryString();

        return view('admin.sessions', compact('sessions'));
    }

    // ─── Force logout a user ──────────────────────────────────────────
    public function forceLogout(User $user)
    {
        UserSession::where('user_id', $user->id)
            ->where('is_active', true)
            ->get()
            ->each(fn($s) => $s->terminate());

        // Send a notification to the user
        Notification::send(
            $user->id,
            Notification::TYPE_SYSTEM,
            'Sessiya tugatildi',
            'Sizning sessiyangiz administrator tomonidan tugatildi.',
            [],
            Auth::id()
        );

        return back()->with('success', $user->name . ' foydalanuvchisi tizimdan chiqarildi.');
    }

    // ─── Send system notification to user ─────────────────────────────
    public function sendNotification(Request $request, User $user)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body'  => 'nullable|string|max:1000',
        ]);

        Notification::send(
            $user->id,
            Notification::TYPE_SYSTEM,
            $request->title,
            $request->body ?? '',
            [],
            Auth::id()
        );

        return back()->with('success', 'Xabar yuborildi.');
    }
}
