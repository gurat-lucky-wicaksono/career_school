<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agenda;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AgendaController extends Controller
{
    /**
     * Display a listing of agendas.
     */
    public function index(Request $request): View
    {
        $query = Agenda::with('creator');

        // Filter Jenis
        if ($jenis = $request->query('jenis')) {
            if (in_array($jenis, ['akademik', 'umum', 'ujian', 'libur'])) {
                $query->where('jenis', $jenis);
            }
        }

        // Search Filter
        if ($search = $request->query('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%")
                  ->orWhere('lokasi', 'like', "%{$search}%");
            });
        }

        $agendas = $query->orderBy('tanggal_mulai', 'desc')->paginate(10)->withQueryString();

        $jenisCounts = [
            'all' => Agenda::count(),
            'akademik' => Agenda::where('jenis', 'akademik')->count(),
            'umum' => Agenda::where('jenis', 'umum')->count(),
            'ujian' => Agenda::where('jenis', 'ujian')->count(),
            'libur' => Agenda::where('jenis', 'libur')->count(),
        ];

        $breadcrumbs = [
            ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['label' => 'Agenda Kegiatan BKK', 'url' => null],
        ];

        return view('admin.agendas.index', compact('agendas', 'jenisCounts', 'breadcrumbs'));
    }

    /**
     * Show the form for creating a new agenda.
     */
    public function create(): View
    {
        $breadcrumbs = [
            ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['label' => 'Agenda Kegiatan BKK', 'url' => route('admin.agendas.index')],
            ['label' => 'Tambah Agenda Kegiatan', 'url' => null],
        ];

        return view('admin.agendas.create', compact('breadcrumbs'));
    }

    /**
     * Store a newly created agenda in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:150',
            'deskripsi' => 'nullable|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'lokasi' => 'nullable|string|max:120',
            'jenis' => 'required|in:akademik,umum,ujian,libur',
        ]);

        $validated['user_id'] = auth()->id();

        Agenda::create($validated);

        return redirect()->route('admin.agendas.index')
            ->with('success', 'Agenda kegiatan BKK berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified agenda.
     */
    public function edit(Agenda $agenda): View
    {
        $breadcrumbs = [
            ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['label' => 'Agenda Kegiatan BKK', 'url' => route('admin.agendas.index')],
            ['label' => 'Edit Agenda', 'url' => null],
        ];

        return view('admin.agendas.edit', compact('agenda', 'breadcrumbs'));
    }

    /**
     * Update the specified agenda in storage.
     */
    public function update(Request $request, Agenda $agenda): RedirectResponse
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:150',
            'deskripsi' => 'nullable|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'lokasi' => 'nullable|string|max:120',
            'jenis' => 'required|in:akademik,umum,ujian,libur',
        ]);

        $agenda->update($validated);

        return redirect()->route('admin.agendas.index')
            ->with('success', 'Agenda kegiatan BKK berhasil diperbarui.');
    }

    /**
     * Remove the specified agenda from storage.
     */
    public function destroy(Agenda $agenda): RedirectResponse
    {
        $agenda->delete();

        return redirect()->route('admin.agendas.index')
            ->with('success', 'Agenda kegiatan BKK berhasil dihapus.');
    }
}
