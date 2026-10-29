<?php

namespace App\Http\Controllers;

use App\Models\Alumni;
use App\Models\Major;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TracerStudyController extends Controller
{
    /**
     * Display the public directory of alumni.
     */
    public function index(Request $request): View
    {
        $query = Alumni::with(['user.profile', 'major'])
            ->where('is_public', true);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($uq) use ($search) {
                    $uq->where('name', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%");
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

        $totalAlumni = Alumni::count();
        $bekerjaCount = Alumni::where('status_bmw', 'bekerja')->count();
        $kuliahCount = Alumni::where('status_bmw', 'kuliah')->count();
        $wirausahaCount = Alumni::where('status_bmw', 'wirausaha')->count();
        $mencariKerjaCount = Alumni::where('status_bmw', 'mencari_kerja')->count();

        $alumni = $query->orderByDesc('tahun_lulus')
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        $majors = Major::orderBy('nama')->get();
        $years = Alumni::select('tahun_lulus')
            ->distinct()
            ->orderByDesc('tahun_lulus')
            ->pluck('tahun_lulus');

        return view('alumni.directory', compact(
            'alumni',
            'totalAlumni',
            'bekerjaCount',
            'kuliahCount',
            'wirausahaCount',
            'mencariKerjaCount',
            'majors',
            'years'
        ));
    }

    /**
     * Display a specific alumni public profile.
     */
    public function show(Alumni $alumni): View
    {
        if (! $alumni->is_public && (! auth()->check() || (! auth()->user()->isAdmin() && auth()->id() !== $alumni->user_id))) {
            abort(404);
        }

        $alumni->load(['user.profile', 'major']);

        return view('alumni.show', compact('alumni'));
    }

    /**
     * Show the tracer study survey form for authenticated user.
     */
    public function create(): View
    {
        $user = auth()->user();
        $alumni = $user->alumni ?? new Alumni();
        $majors = Major::orderBy('nama')->get();
        $currentYear = (int) date('Y');
        $years = range($currentYear, $currentYear - 15);

        return view('alumni.tracer-form', compact('alumni', 'majors', 'years'));
    }

    /**
     * Store or update the user's tracer study questionnaire data.
     */
    public function store(Request $request): RedirectResponse
    {
        $currentYear = (int) date('Y');

        $validated = $request->validate([
            'major_id' => ['required', 'exists:majors,id'],
            'tahun_lulus' => ['required', 'integer', 'min:2000', "max:{$currentYear}"],
            'status_bmw' => ['required', 'in:bekerja,kuliah,wirausaha,mencari_kerja'],
            'nama_instansi' => ['nullable', 'string', 'max:150'],
            'posisi_studi' => ['nullable', 'string', 'max:100'],
            'pendapatan_bulanan' => ['nullable', 'string', 'max:50'],
            'keselarasan' => ['nullable', 'in:sangat_sesuai,sesuai,kurang_sesuai,tidak_sesuai'],
            'waktu_tunggu_bulan' => ['nullable', 'integer', 'min:0', 'max:120'],
            'testimoni' => ['nullable', 'string', 'max:1000'],
            'is_public' => ['nullable', 'boolean'],
        ]);

        $validated['is_public'] = $request->boolean('is_public', true);
        $validated['pekerjaan'] = $validated['posisi_studi'] ?? null;
        $validated['perusahaan'] = $validated['nama_instansi'] ?? null;

        $user = auth()->user();

        Alumni::updateOrCreate(
            ['user_id' => $user->id],
            $validated
        );

        return redirect()->route('tracer.create')->with('success', 'Data kuesioner Tracer Study berhasil disimpan.');
    }
}
