<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Job;
use App\Models\Notification;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * Display notifications list or return JSON for header dropdown.
     */
    public function index(Request $request): View|JsonResponse
    {
        $user = auth()->user();

        // Seed initial notifications if none exist for this admin
        $this->ensureSampleNotifications($user);

        if ($request->wantsJson() || $request->ajax()) {
            $notifications = Notification::where('user_id', $user->id)
                ->latest('created_at')
                ->take(8)
                ->get()
                ->map(function ($notif) {
                    return [
                        'id' => $notif->id,
                        'jenis' => $notif->jenis,
                        'judul' => $notif->judul,
                        'dibaca' => (bool)$notif->dibaca,
                        'created_at' => Carbon::parse($notif->created_at)->diffForHumans(),
                        'url' => $this->resolveNotificationUrl($notif),
                        'icon' => $this->resolveNotificationIcon($notif->jenis),
                    ];
                });

            $unreadCount = Notification::where('user_id', $user->id)
                ->where('dibaca', false)
                ->count();

            return response()->json([
                'success' => true,
                'unread_count' => $unreadCount,
                'notifications' => $notifications,
            ]);
        }

        $query = Notification::where('user_id', $user->id);

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->query('jenis'));
        }

        if ($request->query('status') === 'unread') {
            $query->where('dibaca', false);
        }

        $notifications = $query->latest('created_at')->paginate(15)->withQueryString();

        $unreadCount = Notification::where('user_id', $user->id)
            ->where('dibaca', false)
            ->count();

        $breadcrumbs = [
            ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['label' => 'Pusat Notifikasi', 'url' => null],
        ];

        return view('admin.notifications.index', compact('notifications', 'unreadCount', 'breadcrumbs'));
    }

    /**
     * Mark a specific notification as read and redirect to its target if requested.
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

        $targetUrl = $this->resolveNotificationUrl($notification);
        return redirect($targetUrl);
    }

    /**
     * Mark all notifications as read for current user.
     */
    public function markAllAsRead(Request $request): RedirectResponse|JsonResponse
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

    /**
     * Resolve target redirect URL based on notification type and reference.
     */
    protected function resolveNotificationUrl(Notification $notification): string
    {
        return match ($notification->jenis) {
            'kemitraan' => route('admin.companies.verification'),
            'lowongan' => route('admin.jobs.index'),
            'siswa' => route('admin.students.index'),
            'guru' => route('admin.teachers.index'),
            default => route('admin.notifications.index'),
        };
    }

    /**
     * Resolve Lucide icon name based on notification type.
     */
    protected function resolveNotificationIcon(string $jenis): string
    {
        return match ($jenis) {
            'kemitraan' => 'building-2',
            'lowongan' => 'briefcase',
            'siswa' => 'graduation-cap',
            'guru' => 'user-check',
            default => 'bell',
        };
    }

    /**
     * Ensure realistic initial notifications exist for admin.
     */
    protected function ensureSampleNotifications(User $user): void
    {
        if (Notification::where('user_id', $user->id)->count() > 0) {
            return;
        }

        $now = now();

        $samples = [
            [
                'user_id' => $user->id,
                'jenis' => 'kemitraan',
                'judul' => 'Pengajuan Kemitraan: PT Telkom Indonesia menunggu verifikasi kerja sama industri.',
                'referensi_tipe' => 'company',
                'referensi_id' => null,
                'dibaca' => false,
                'created_at' => $now->copy()->subMinutes(18),
            ],
            [
                'user_id' => $user->id,
                'jenis' => 'lowongan',
                'judul' => 'Lowongan Baru: Astra Honda Motor membuka lowongan Teknisi Otomotif & Mekatronika.',
                'referensi_tipe' => 'job',
                'referensi_id' => null,
                'dibaca' => false,
                'created_at' => $now->copy()->subHours(2),
            ],
            [
                'user_id' => $user->id,
                'jenis' => 'siswa',
                'judul' => 'Aktivasi Siswa: 14 siswa baru konsentrasi PPLG telah memvalidasi portofolio kejuruan.',
                'referensi_tipe' => 'student',
                'referensi_id' => null,
                'dibaca' => false,
                'created_at' => $now->copy()->subHours(6),
            ],
            [
                'user_id' => $user->id,
                'jenis' => 'sistem',
                'judul' => 'Tahun Ajaran Baru: Sinkronisasi semester ganjil TA 2025/2026 telah aktif.',
                'referensi_tipe' => 'system',
                'referensi_id' => null,
                'dibaca' => true,
                'created_at' => $now->copy()->subDays(2),
            ],
        ];

        foreach ($samples as $sample) {
            Notification::create($sample);
        }
    }
}
