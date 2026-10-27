<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of announcements.
     */
    public function index(Request $request): View
    {
        $query = Announcement::with('author');

        // Status Filter
        if ($status = $request->query('status')) {
            if (in_array($status, ['draft', 'terbit', 'arsip'])) {
                $query->where('status', $status);
            }
        }

        // Prioritas Filter
        if ($prioritas = $request->query('prioritas')) {
            if (in_array($prioritas, ['rendah', 'normal', 'penting'])) {
                $query->where('prioritas', $prioritas);
            }
        }

        // Search Filter
        if ($search = $request->query('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('isi', 'like', "%{$search}%");
            });
        }

        $announcements = $query->latest()->paginate(10)->withQueryString();

        $statusCounts = [
            'all' => Announcement::count(),
            'terbit' => Announcement::where('status', 'terbit')->count(),
            'draft' => Announcement::where('status', 'draft')->count(),
            'arsip' => Announcement::where('status', 'arsip')->count(),
        ];

        $breadcrumbs = [
            ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['label' => 'Pengumuman BKK', 'url' => null],
        ];

        return view('admin.announcements.index', compact('announcements', 'statusCounts', 'breadcrumbs'));
    }

    /**
     * Show the form for creating a new announcement.
     */
    public function create(): View
    {
        $breadcrumbs = [
            ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['label' => 'Pengumuman BKK', 'url' => route('admin.announcements.index')],
            ['label' => 'Tulis Pengumuman Baru', 'url' => null],
        ];

        return view('admin.announcements.create', compact('breadcrumbs'));
    }

    /**
     * Store a newly created announcement in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:150',
            'isi' => 'required|string',
            'prioritas' => 'required|in:rendah,normal,penting',
            'status' => 'required|in:draft,terbit,arsip',
            'mulai_tampil' => 'nullable|date',
            'selesai_tampil' => 'nullable|date|after_or_equal:mulai_tampil',
            'lampiran' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        if ($request->hasFile('lampiran')) {
            $validated['lampiran'] = $request->file('lampiran')->store('announcements', 'public');
        }

        $validated['user_id'] = auth()->id();

        Announcement::create($validated);

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Pengumuman BKK berhasil diterbitkan.');
    }

    /**
     * Show the form for editing the specified announcement.
     */
    public function edit(Announcement $announcement): View
    {
        $breadcrumbs = [
            ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['label' => 'Pengumuman BKK', 'url' => route('admin.announcements.index')],
            ['label' => 'Edit Pengumuman', 'url' => null],
        ];

        return view('admin.announcements.edit', compact('announcement', 'breadcrumbs'));
    }

    /**
     * Update the specified announcement in storage.
     */
    public function update(Request $request, Announcement $announcement): RedirectResponse
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:150',
            'isi' => 'required|string',
            'prioritas' => 'required|in:rendah,normal,penting',
            'status' => 'required|in:draft,terbit,arsip',
            'mulai_tampil' => 'nullable|date',
            'selesai_tampil' => 'nullable|date|after_or_equal:mulai_tampil',
            'lampiran' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        if ($request->hasFile('lampiran')) {
            if ($announcement->lampiran && Storage::disk('public')->exists($announcement->lampiran)) {
                Storage::disk('public')->delete($announcement->lampiran);
            }
            $validated['lampiran'] = $request->file('lampiran')->store('announcements', 'public');
        }

        $announcement->update($validated);

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Pengumuman BKK berhasil diperbarui.');
    }

    /**
     * Remove the specified announcement from storage.
     */
    public function destroy(Announcement $announcement): RedirectResponse
    {
        if ($announcement->lampiran && Storage::disk('public')->exists($announcement->lampiran)) {
            Storage::disk('public')->delete($announcement->lampiran);
        }

        $announcement->delete();

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Pengumuman BKK berhasil dihapus.');
    }
}
