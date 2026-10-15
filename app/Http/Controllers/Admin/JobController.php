<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Job;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class JobController extends Controller
{
    /**
     * Display a listing of job vacancies with search and filtering.
     */
    public function index(Request $request): View
    {
        $query = Job::with(['company'])->withCount('applications');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('lokasi', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%")
                  ->orWhereHas('company', function ($cq) use ($search) {
                      $cq->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        if ($filterJenis = $request->input('filter_jenis')) {
            $query->where('jenis', $filterJenis);
        }

        if ($filterStatus = $request->input('filter_status')) {
            $query->where('status', $filterStatus);
        }

        if ($filterCompany = $request->input('filter_company')) {
            $query->where('company_id', $filterCompany);
        }

        $sortField = $request->input('sort', 'created_at');
        $sortDirection = $request->input('direction', 'desc');

        if (in_array($sortField, ['judul', 'jenis', 'status', 'batas_lamaran', 'created_at'])) {
            $query->orderBy($sortField, $sortDirection === 'asc' ? 'asc' : 'desc');
        } else {
            $query->latest();
        }

        $jobs = $query->paginate(10)->withQueryString();

        $companies = Company::where('status_verifikasi', 'terverifikasi')->orWhere('status_verifikasi', 'menunggu')->orderBy('nama')->get();

        $breadcrumbs = [
            ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['label' => 'Lowongan Kerja', 'url' => null],
        ];

        return view('admin.jobs.index', compact(
            'jobs', 
            'breadcrumbs', 
            'sortField', 
            'sortDirection', 
            'companies'
        ));
    }

    /**
     * Store a newly created job in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:150'],
            'company_id' => ['required', 'exists:companies,id'],
            'jenis' => ['required', 'in:penuh_waktu,paruh_waktu,magang,proyek'],
            'lokasi' => ['nullable', 'string', 'max:120'],
            'kuota' => ['required', 'integer', 'min:1'],
            'batas_lamaran' => ['nullable', 'date'],
            'status' => ['required', 'in:dibuka,ditutup,selesai'],
            'deskripsi' => ['required', 'string'],
        ]);

        $baseSlug = Str::slug($validated['judul']);
        $slug = $baseSlug . '-' . rand(1000, 9999);

        Job::create(array_merge($validated, ['slug' => $slug]));

        return redirect()->route('admin.jobs.index')->with('success', 'Lowongan pekerjaan/magang baru berhasil dipublikasikan.');
    }

    /**
     * Update the specified job in storage.
     */
    public function update(Request $request, Job $job): RedirectResponse
    {
        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:150'],
            'company_id' => ['required', 'exists:companies,id'],
            'jenis' => ['required', 'in:penuh_waktu,paruh_waktu,magang,proyek'],
            'lokasi' => ['nullable', 'string', 'max:120'],
            'kuota' => ['required', 'integer', 'min:1'],
            'batas_lamaran' => ['nullable', 'date'],
            'status' => ['required', 'in:dibuka,ditutup,selesai'],
            'deskripsi' => ['required', 'string'],
        ]);

        $job->update($validated);

        return redirect()->route('admin.jobs.index')->with('success', 'Data lowongan kerja berhasil diperbarui.');
    }

    /**
     * Remove the specified job from storage.
     */
    public function destroy(Job $job): RedirectResponse
    {
        $job->delete();

        return redirect()->route('admin.jobs.index')->with('success', 'Lowongan kerja berhasil dihapus.');
    }
}
