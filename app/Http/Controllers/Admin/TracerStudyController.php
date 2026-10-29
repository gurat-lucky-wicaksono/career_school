<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alumni;
use App\Models\Major;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TracerStudyController extends Controller
{
    /**
     * Display the tracer study dashboard and alumni management.
     */
    public function index(Request $request): View
    {
        $totalAlumni = Alumni::count();
        $bekerjaCount = Alumni::where('status_bmw', 'bekerja')->count();
        $kuliahCount = Alumni::where('status_bmw', 'kuliah')->count();
        $wirausahaCount = Alumni::where('status_bmw', 'wirausaha')->count();
        $mencariKerjaCount = Alumni::where('status_bmw', 'mencari_kerja')->count();

        $terserapCount = $bekerjaCount + $kuliahCount + $wirausahaCount;
        $persenTerserap = $totalAlumni > 0 ? round(($terserapCount / $totalAlumni) * 100, 1) : 0;

        // Keselarasan stats
        $sangatSesuai = Alumni::where('keselarasan', 'sangat_sesuai')->count();
        $sesuai = Alumni::where('keselarasan', 'sesuai')->count();
        $kurangSesuai = Alumni::where('keselarasan', 'kurang_sesuai')->count();
        $tidakSesuai = Alumni::where('keselarasan', 'tidak_sesuai')->count();
        $totalEvaluasi = $sangatSesuai + $sesuai + $kurangSesuai + $tidakSesuai;
        $persenSelaras = $totalEvaluasi > 0 ? round((($sangatSesuai + $sesuai) / $totalEvaluasi) * 100, 1) : 0;

        // Query list
        $query = Alumni::with(['user.profile', 'user.student.schoolClass', 'major']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($uq) use ($search) {
                    $uq->where('name', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhere('nama_instansi', 'like', "%{$search}%")
                ->orWhere('posisi_studi', 'like', "%{$search}%")
                ->orWhere('pekerjaan', 'like', "%{$search}%")
                ->orWhere('perusahaan', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status_bmw', $request->status);
        }

        if ($request->filled('tahun') && $request->tahun !== 'all') {
            $query->where('tahun_lulus', $request->tahun);
        }

        if ($request->filled('major_id') && $request->major_id !== 'all') {
            $query->where('major_id', $request->major_id);
        }

        $alumni = $query->orderByDesc('tahun_lulus')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $majors = Major::orderBy('nama')->get();
        $years = Alumni::select('tahun_lulus')
            ->distinct()
            ->orderByDesc('tahun_lulus')
            ->pluck('tahun_lulus');

        return view('admin.tracer-study.index', compact(
            'alumni',
            'totalAlumni',
            'bekerjaCount',
            'kuliahCount',
            'wirausahaCount',
            'mencariKerjaCount',
            'terserapCount',
            'persenTerserap',
            'sangatSesuai',
            'sesuai',
            'kurangSesuai',
            'tidakSesuai',
            'persenSelaras',
            'majors',
            'years'
        ));
    }

    /**
     * Toggle public visibility of an alumni record.
     */
    public function toggleVisibility(Alumni $alumni): RedirectResponse
    {
        $alumni->update(['is_public' => ! $alumni->is_public]);

        $status = $alumni->is_public ? 'ditampilkan di publik' : 'disembunyikan dari publik';

        return back()->with('success', "Data alumni berhasil {$status}.");
    }

    /**
     * Delete an alumni tracer study entry.
     */
    public function destroy(Alumni $alumni): RedirectResponse
    {
        $alumni->delete();

        return back()->with('success', 'Data penelusuran tamatan alumni berhasil dihapus.');
    }
}
