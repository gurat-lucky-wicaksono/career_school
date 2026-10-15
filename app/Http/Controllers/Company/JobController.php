<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\Skill;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class JobController extends Controller
{
    /**
     * Display a listing of jobs published by the authenticated company.
     */
    public function index(Request $request): View
    {
        $company = auth()->user()->company;

        $query = $company->jobs()->withCount('applications')->with('skills');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('lokasi', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        $status = $request->input('filter_status', $request->input('status'));
        if ($status) {
            $query->where('status', $status);
        }

        $jenis = $request->input('filter_jenis', $request->input('jenis'));
        if ($jenis) {
            $query->where('jenis', $jenis);
        }

        $sortField = $request->input('sort', 'created_at');
        $sortDirection = $request->input('direction', 'desc');

        if (in_array($sortField, ['judul', 'jenis', 'status', 'batas_lamaran', 'kuota', 'created_at'])) {
            $query->orderBy($sortField, $sortDirection === 'asc' ? 'asc' : 'desc');
        } else {
            $query->latest();
        }

        $jobs = $query->paginate(10)->withQueryString();
        $skills = Skill::orderBy('kategori')->orderBy('nama')->get();

        $breadcrumbs = [
            ['label' => 'Dashboard Mitra', 'url' => route('company.dashboard')],
            ['label' => 'Kelola Lowongan', 'url' => null],
        ];

        return view('company.jobs.index', compact('jobs', 'company', 'skills', 'breadcrumbs', 'sortField', 'sortDirection'));
    }

    /**
     * Show the form for creating a new job opening.
     */
    public function create(): View|RedirectResponse
    {
        $company = auth()->user()->company;

        if (!$company || !$company->isVerified()) {
            return redirect()->route('company.jobs.index')->with(
                'warning',
                'Akun mitra Anda belum diverifikasi oleh administrator sekolah. Publikasi lowongan baru dapat dilakukan setelah akun berstatus terverifikasi.'
            );
        }

        $skills = Skill::orderBy('kategori')->orderBy('nama')->get();

        $breadcrumbs = [
            ['label' => 'Dashboard Mitra', 'url' => route('company.dashboard')],
            ['label' => 'Kelola Lowongan', 'url' => route('company.jobs.index')],
            ['label' => 'Pasang Lowongan Baru', 'url' => null],
        ];

        return view('company.jobs.create', compact('company', 'skills', 'breadcrumbs'));
    }

    /**
     * Store a newly created job in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $company = auth()->user()->company;

        if (!$company || !$company->isVerified()) {
            return redirect()->route('company.jobs.index')->with(
                'warning',
                'Akun belum diverifikasi oleh administrator BKK SMKN 1 Cirebon.'
            );
        }

        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:150'],
            'jenis' => ['required', 'in:penuh_waktu,paruh_waktu,magang,proyek'],
            'lokasi' => ['nullable', 'string', 'max:120'],
            'kuota' => ['required', 'integer', 'min:1', 'max:100'],
            'gaji_min' => ['nullable', 'numeric', 'min:0'],
            'gaji_max' => ['nullable', 'numeric', 'gte:gaji_min'],
            'batas_lamaran' => ['nullable', 'date', 'after_or_equal:today'],
            'deskripsi' => ['required', 'string'],
            'skills' => ['nullable', 'array'],
            'skills.*.id' => ['required', 'exists:skills,id'],
            'skills.*.bobot' => ['required', 'integer', 'between:1,5'],
            'skills.*.wajib' => ['nullable', 'boolean'],
        ]);

        $slugBase = Str::slug($validated['judul']);
        $slug = $slugBase . '-' . Str::lower(Str::random(6));

        $job = $company->jobs()->create([
            'judul' => $validated['judul'],
            'slug' => $slug,
            'deskripsi' => $validated['deskripsi'],
            'lokasi' => $validated['lokasi'] ?? ($company->kota ?? 'Cirebon'),
            'jenis' => $validated['jenis'],
            'gaji_min' => $validated['gaji_min'] ?? null,
            'gaji_max' => $validated['gaji_max'] ?? null,
            'kuota' => $validated['kuota'],
            'batas_lamaran' => $validated['batas_lamaran'] ?? null,
            'status' => 'dibuka',
        ]);

        if (!empty($validated['skills'])) {
            $syncData = [];
            foreach ($validated['skills'] as $skillItem) {
                if (!empty($skillItem['id'])) {
                    $syncData[$skillItem['id']] = [
                        'bobot' => (int) ($skillItem['bobot'] ?? 1),
                        'wajib' => !empty($skillItem['wajib']),
                    ];
                }
            }
            $job->skills()->sync($syncData);
        }

        return redirect()->route('company.jobs.index')->with('success', 'Lowongan pekerjaan berhasil dipublikasikan.');
    }

    /**
     * Show the form for editing the specified job.
     */
    public function edit(Job $job): View|RedirectResponse
    {
        $company = auth()->user()->company;

        if ($job->company_id !== $company->id) {
            abort(403, 'Akses tidak sah ke lowongan ini.');
        }

        $job->load('skills');
        $skills = Skill::orderBy('kategori')->orderBy('nama')->get();

        $breadcrumbs = [
            ['label' => 'Dashboard Mitra', 'url' => route('company.dashboard')],
            ['label' => 'Kelola Lowongan', 'url' => route('company.jobs.index')],
            ['label' => 'Edit Lowongan', 'url' => null],
        ];

        return view('company.jobs.edit', compact('job', 'company', 'skills', 'breadcrumbs'));
    }

    /**
     * Update the specified job in storage.
     */
    public function update(Request $request, Job $job): RedirectResponse
    {
        $company = auth()->user()->company;

        if ($job->company_id !== $company->id) {
            abort(403, 'Akses tidak sah ke lowongan ini.');
        }

        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:150'],
            'jenis' => ['required', 'in:penuh_waktu,paruh_waktu,magang,proyek'],
            'lokasi' => ['nullable', 'string', 'max:120'],
            'kuota' => ['required', 'integer', 'min:1', 'max:100'],
            'gaji_min' => ['nullable', 'numeric', 'min:0'],
            'gaji_max' => ['nullable', 'numeric', 'gte:gaji_min'],
            'batas_lamaran' => ['nullable', 'date'],
            'deskripsi' => ['required', 'string'],
            'skills' => ['nullable', 'array'],
            'skills.*.id' => ['required', 'exists:skills,id'],
            'skills.*.bobot' => ['required', 'integer', 'between:1,5'],
            'skills.*.wajib' => ['nullable', 'boolean'],
        ]);

        $job->update([
            'judul' => $validated['judul'],
            'deskripsi' => $validated['deskripsi'],
            'lokasi' => $validated['lokasi'] ?? ($company->kota ?? 'Cirebon'),
            'jenis' => $validated['jenis'],
            'gaji_min' => $validated['gaji_min'] ?? null,
            'gaji_max' => $validated['gaji_max'] ?? null,
            'kuota' => $validated['kuota'],
            'batas_lamaran' => $validated['batas_lamaran'] ?? null,
        ]);

        if (isset($validated['skills'])) {
            $syncData = [];
            foreach ($validated['skills'] as $skillItem) {
                if (!empty($skillItem['id'])) {
                    $syncData[$skillItem['id']] = [
                        'bobot' => (int) ($skillItem['bobot'] ?? 1),
                        'wajib' => !empty($skillItem['wajib']),
                    ];
                }
            }
            $job->skills()->sync($syncData);
        } else {
            $job->skills()->detach();
        }

        return redirect()->route('company.jobs.index')->with('success', 'Data lowongan berhasil diperbarui.');
    }

    /**
     * Toggle the status of a job opening (dibuka / ditutup).
     */
    public function toggleStatus(Job $job): RedirectResponse
    {
        $company = auth()->user()->company;

        if ($job->company_id !== $company->id) {
            abort(403);
        }

        $newStatus = $job->status === 'dibuka' ? 'ditutup' : 'dibuka';
        $job->update(['status' => $newStatus]);

        $message = $newStatus === 'dibuka' ? 'Lowongan telah dibuka kembali.' : 'Lowongan telah ditutup sementara.';

        return redirect()->back()->with('success', $message);
    }

    /**
     * Remove the specified job from storage.
     */
    public function destroy(Job $job): RedirectResponse
    {
        $company = auth()->user()->company;

        if ($job->company_id !== $company->id) {
            abort(403);
        }

        $job->skills()->detach();
        $job->delete();

        return redirect()->route('company.jobs.index')->with('success', 'Lowongan pekerjaan berhasil dihapus.');
    }
}
