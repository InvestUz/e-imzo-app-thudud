<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /** GET /notifications — JSON dropdown data */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $notifications = $user->notifications()
            ->with('creator:id,name')
            ->latest()
            ->limit(20)
            ->get()
            ->map(fn($n) => [
                'id'         => $n->id,
                'type'       => $n->type,
                'icon'       => $n->icon,
                'color'      => $n->color_class,
                'title'      => $n->title,
                'body'       => $n->body,
                'read'       => $n->isRead(),
                'read_at'    => $n->read_at?->diffForHumans(),
                'created_at' => $n->created_at->diffForHumans(),
                'created_at_full' => $n->created_at->format('d.m.Y H:i'),
                'creator'    => $n->creator?->name,
                'related_type' => $n->related_type,
                'related_id'   => $n->related_id,
                'url'        => $this->resolveUrl($n),
            ]);

        return response()->json([
            'notifications' => $notifications,
            'unread'        => $user->unreadNotificationsCount(),
        ]);
    }

    /** POST /notifications/{id}/read */
    public function markRead(Notification $notification)
    {
        abort_unless($notification->user_id === Auth::id(), 403);
        $notification->markRead();
        return response()->json(['ok' => true]);
    }

    /** POST /notifications/read-all */
    public function markAllRead()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->notifications()->whereNull('read_at')->update(['read_at' => now()]);
        return response()->json(['ok' => true]);
    }

    /** GET /notifications/page — full page listing */
    public function page()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $notifications = $user->notifications()
            ->with('creator:id,name')
            ->latest()
            ->paginate(30);

        return view('notifications.index', compact('notifications'));
    }

    private function resolveUrl(Notification $n): ?string
    {
        if ($n->related_type === 'application' && $n->related_id) {
            return route('applications.show', $n->related_id);
        }
        return null;
    }
}
