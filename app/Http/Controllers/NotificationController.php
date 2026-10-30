<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * Display notification center or return JSON for live header dropdown.
     */
    public function index(Request $request): View|JsonResponse
    {
        $userId = auth()->id();

        if ($request->wantsJson() || $request->ajax()) {
            $notifications = Notification::where('user_id', $userId)
                ->latest('created_at')
                ->take(8)
                ->get()
                ->map(function ($n) {
                    return [
                        'id' => $n->id,
                        'jenis' => $n->jenis,
                        'judul' => $n->judul,
                        'dibaca' => (bool) $n->dibaca,
                        'created_at' => Carbon::parse($n->created_at)->diffForHumans(),
                        'url' => $n->url,
                        'icon' => $n->icon,
                        'badge_class' => $n->jenis_badge_class,
                    ];
                });

            $unreadCount = Notification::where('user_id', $userId)
                ->where('dibaca', false)
                ->count();

            return response()->json([
                'success' => true,
                'unread_count' => $unreadCount,
                'notifications' => $notifications,
            ]);
        }

        $query = Notification::where('user_id', $userId);

        if ($request->query('status') === 'unread') {
            $query->where('dibaca', false);
        }

        if ($request->filled('jenis') && $request->jenis !== 'all') {
            $query->where('jenis', $request->jenis);
        }

        $notifications = $query->latest('created_at')->paginate(15)->withQueryString();

        $unreadCount = Notification::where('user_id', $userId)
            ->where('dibaca', false)
            ->count();

        $totalCount = Notification::where('user_id', $userId)->count();

        return view('notifications.index', compact('notifications', 'unreadCount', 'totalCount'));
    }

    /**
     * Mark a single notification as read and redirect to target URL.
     */
    public function markAsRead(Request $request, Notification $notification): RedirectResponse|JsonResponse
    {
        if ($notification->user_id === auth()->id()) {
            $notification->update(['dibaca' => true]);
        }

        if ($request->wantsJson() || $request->ajax()) {
            $unreadCount = Notification::where('user_id', auth()->id())
                ->where('dibaca', false)
                ->count();

            return response()->json([
                'success' => true,
                'unread_count' => $unreadCount,
            ]);
        }

        return redirect($notification->url);
    }

    /**
     * Mark all notifications as read for current user.
     */
    public function markAllRead(Request $request): RedirectResponse|JsonResponse
    {
        Notification::where('user_id', auth()->id())
            ->where('dibaca', false)
            ->update(['dibaca' => true]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'unread_count' => 0,
            ]);
        }

        return back()->with('success', 'Semua notifikasi telah ditandai sebagai dibaca.');
    }
}
