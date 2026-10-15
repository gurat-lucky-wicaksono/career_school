<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApplicationController extends Controller
{
    /**
     * Display a listing of candidates who applied to this company's jobs.
     */
    public function index(Request $request): View
    {
        $company = auth()->user()->company;
        $jobIds = $company->jobs()->pluck('id');

        $query = Application::whereIn('job_id', $jobIds)
            ->with(['student.user', 'student.major', 'student.schoolClass', 'job']);

        if ($jobId = $request->input('job_id')) {
            $query->where('job_id', $jobId);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($search = $request->input('search')) {
            $query->whereHas('student.user', function ($uq) use ($search) {
                $uq->where('name', 'like', "%{$search}%")
                   ->orWhere('email', 'like', "%{$search}%");
            })->orWhereHas('student', function ($sq) use ($search) {
                $sq->where('nisn', 'like', "%{$search}%");
            });
        }

        $sort = $request->input('sort', 'skor');
        if ($sort === 'tanggal') {
            $query->latest();
        } else {
            $query->orderByDesc('skor_kecocokan')->latest();
        }

        $applications = $query->paginate(12)->withQueryString();
        $companyJobs = $company->jobs()->select('id', 'judul')->get();

        $breadcrumbs = [
            ['label' => 'Dashboard Mitra', 'url' => route('company.dashboard')],
            ['label' => 'Daftar Pelamar Masuk', 'url' => null],
        ];

        return view('company.applications.index', compact(
            'applications',
            'companyJobs',
            'company',
            'breadcrumbs'
        ));
    }

    /**
     * Display candidate profile and application details.
     */
    public function show(Application $application): View
    {
        $company = auth()->user()->company;

        if ($application->job->company_id !== $company->id) {
            abort(403, 'Akses tidak sah ke berkas pelamar ini.');
        }

        $application->load([
            'student.user.profile',
            'student.major',
            'student.schoolClass',
            'student.skills',
            'student.portfolios',
            'job.skills',
        ]);

        $breadcrumbs = [
            ['label' => 'Dashboard Mitra', 'url' => route('company.dashboard')],
            ['label' => 'Daftar Pelamar', 'url' => route('company.applications.index')],
            ['label' => $application->student->user->name ?? 'Detail Pelamar', 'url' => null],
        ];

        return view('company.applications.show', compact('application', 'breadcrumbs'));
    }

    /**
     * Update the application review status and HR notes.
     */
    public function updateStatus(Request $request, Application $application): RedirectResponse
    {
        $company = auth()->user()->company;

        if ($application->job->company_id !== $company->id) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => ['required', 'in:tertunda,ditinjau,wawancara,diterima,ditolak'],
            'catatan_hrd' => ['nullable', 'string', 'max:1000'],
        ]);

        $oldStatus = $application->status;
        $newStatus = $validated['status'];

        $application->update([
            'status' => $newStatus,
            'catatan_hrd' => $validated['catatan_hrd'] ?? $application->catatan_hrd,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        // Send in-app notification to the applicant if status changed
        if ($oldStatus !== $newStatus && $application->student?->user_id) {
            $statusLabels = [
                'ditinjau' => 'sedang ditinjau oleh HRD',
                'wawancara' => 'memasuki tahap wawancara',
                'diterima' => 'dinyatakan DITERIMA',
                'ditolak' => 'belum dapat diterima',
                'tertunda' => 'dalam antrean',
            ];

            Notification::create([
                'user_id' => $application->student->user_id,
                'jenis' => 'karier',
                'judul' => 'Lamaran Anda pada posisi ' . $application->job->judul . ' di ' . $company->nama . ' ' . ($statusLabels[$newStatus] ?? 'telah diperbarui statusnya') . '.',
                'referensi_tipe' => 'application',
                'referensi_id' => $application->id,
                'dibaca' => false,
            ]);
        }

        return redirect()->back()->with('success', 'Status lamaran kandidat berhasil diperbarui.');
    }
}
