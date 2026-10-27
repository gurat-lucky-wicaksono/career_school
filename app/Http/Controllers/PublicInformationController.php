<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicInformationController extends Controller
{
    /**
     * Display published announcements for students, partners, and public.
     */
    public function announcements(Request $request): View
    {
        $query = Announcement::with('author')
            ->where('status', 'terbit');

        if ($search = $request->query('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('isi', 'like', "%{$search}%");
            });
        }

        if ($prioritas = $request->query('prioritas')) {
            if (in_array($prioritas, ['penting', 'normal', 'rendah'])) {
                $query->where('prioritas', $prioritas);
            }
        }

        $announcements = $query->latest('mulai_tampil')->latest('created_at')->paginate(9)->withQueryString();

        return view('public.announcements.index', compact('announcements'));
    }

    /**
     * Display a specific announcement detail.
     */
    public function showAnnouncement(Announcement $announcement): View
    {
        // Only allow published announcements to be viewed by public
        if ($announcement->status !== 'terbit') {
            abort(404);
        }

        $recentAnnouncements = Announcement::where('status', 'terbit')
            ->where('id', '!=', $announcement->id)
            ->latest('created_at')
            ->take(4)
            ->get();

        return view('public.announcements.show', compact('announcement', 'recentAnnouncements'));
    }

    /**
     * Display vocational school agendas and BKK events.
     */
    public function agendas(Request $request): View
    {
        $query = Agenda::with('creator');

        if ($jenis = $request->query('jenis')) {
            if (in_array($jenis, ['akademik', 'umum', 'ujian', 'libur'])) {
                $query->where('jenis', $jenis);
            }
        }

        if ($search = $request->query('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%")
                  ->orWhere('lokasi', 'like', "%{$search}%");
            });
        }

        $agendas = $query->orderBy('tanggal_mulai', 'desc')->paginate(12)->withQueryString();

        return view('public.agendas.index', compact('agendas'));
    }
}
