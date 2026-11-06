<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\InternshipEvaluation;
use App\Services\ActivityLogService;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InternshipEvaluationController extends Controller
{
    /**
     * Display listing of accepted interns and their evaluation status.
     */
    public function index(Request $request): View
    {
        $company = auth()->user()->company;
        $jobIds = $company->jobs()->pluck('id');

        $query = Application::whereIn('job_id', $jobIds)
            ->where('status', 'diterima')
            ->with(['student.user', 'student.major', 'student.schoolClass', 'job', 'evaluation']);

        // Filter by evaluation status
        if ($evalStatus = $request->input('eval_status')) {
            if ($evalStatus === 'belum') {
                $query->whereDoesntHave('evaluation');
            } elseif ($evalStatus === 'draft') {
                $query->whereHas('evaluation', fn($q) => $q->where('status', 'draft'));
            } elseif ($evalStatus === 'submitted') {
                $query->whereHas('evaluation', fn($q) => $q->where('status', 'submitted'));
            } elseif ($evalStatus === 'verified') {
                $query->whereHas('evaluation', fn($q) => $q->where('status', 'verified'));
            }
        }

        // Search by student name, NISN, or job title
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('student.user', fn($uq) => $uq->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('student', fn($sq) => $sq->where('nisn', 'like', "%{$search}%"))
                  ->orWhereHas('job', fn($jq) => $jq->where('judul', 'like', "%{$search}%"));
            });
        }

        $interns = $query->latest('reviewed_at')->paginate(12)->withQueryString();

        // Calculate statistics for badges
        $allAccepted = Application::whereIn('job_id', $jobIds)->where('status', 'diterima');
        $totalAccepted = (clone $allAccepted)->count();
        $totalEvaluated = (clone $allAccepted)->whereHas('evaluation')->count();
        $totalVerified = (clone $allAccepted)->whereHas('evaluation', fn($q) => $q->where('status', 'verified'))->count();
        $totalPending = $totalAccepted - $totalEvaluated;

        $stats = [
            'total_accepted' => $totalAccepted,
            'total_evaluated' => $totalEvaluated,
            'total_verified' => $totalVerified,
            'total_pending' => $totalPending,
        ];

        $breadcrumbs = [
            ['label' => 'Dashboard Mitra', 'url' => route('company.dashboard')],
            ['label' => 'Evaluasi Kinerja PKL', 'url' => null],
        ];

        return view('company.evaluations.index', compact('interns', 'stats', 'company', 'breadcrumbs'));
    }

    /**
     * Show the form for creating a new evaluation for an accepted intern.
     */
    public function create(Application $application): View|RedirectResponse
    {
        $company = auth()->user()->company;

        if ($application->job->company_id !== $company->id) {
            abort(403, 'Akses evaluasi ditolak.');
        }

        if ($application->status !== 'diterima') {
            return redirect()->route('company.evaluations.index')
                ->with('error', 'Hanya siswa dengan status diterima yang dapat dievaluasi kinerjanya.');
        }

        if ($application->evaluation) {
            return redirect()->route('company.evaluations.edit', $application->evaluation);
        }

        $application->load(['student.user', 'student.major', 'student.schoolClass', 'job']);

        $breadcrumbs = [
            ['label' => 'Dashboard Mitra', 'url' => route('company.dashboard')],
            ['label' => 'Evaluasi Kinerja PKL', 'url' => route('company.evaluations.index')],
            ['label' => 'Form Penilaian: ' . ($application->student->user->name ?? 'Siswa'), 'url' => null],
        ];

        return view('company.evaluations.create', compact('application', 'breadcrumbs'));
    }

    /**
     * Store a newly created evaluation in database.
     */
    public function store(Request $request, Application $application): RedirectResponse
    {
        $company = auth()->user()->company;

        if ($application->job->company_id !== $company->id) {
            abort(403, 'Akses evaluasi ditolak.');
        }

        if ($application->status !== 'diterima') {
            return redirect()->route('company.evaluations.index')
                ->with('error', 'Status lamaran siswa belum diterima.');
        }

        if ($application->evaluation) {
            return redirect()->route('company.evaluations.edit', $application->evaluation);
        }

        $validated = $request->validate([
            'nilai_teknis' => ['required', 'numeric', 'min:0', 'max:100'],
            'nilai_disiplin' => ['required', 'numeric', 'min:0', 'max:100'],
            'nilai_kerjasama' => ['required', 'numeric', 'min:0', 'max:100'],
            'catatan_industri' => ['nullable', 'string', 'max:2000'],
            'submit_action' => ['required', 'in:draft,submit'],
        ]);

        $finalScore = InternshipEvaluation::calculateFinalScore(
            (float) $validated['nilai_teknis'],
            (float) $validated['nilai_disiplin'],
            (float) $validated['nilai_kerjasama']
        );

        $predicate = InternshipEvaluation::determinePredicate($finalScore);
        $isSubmit = $validated['submit_action'] === 'submit';
        $status = $isSubmit ? 'submitted' : 'draft';

        $evaluation = InternshipEvaluation::create([
            'application_id' => $application->id,
            'company_id' => $company->id,
            'student_id' => $application->student_id,
            'nilai_teknis' => $validated['nilai_teknis'],
            'nilai_disiplin' => $validated['nilai_disiplin'],
            'nilai_kerjasama' => $validated['nilai_kerjasama'],
            'nilai_akhir' => $finalScore,
            'predikat' => $predicate,
            'catatan_industri' => $validated['catatan_industri'] ?? null,
            'status' => $status,
            'submitted_at' => $isSubmit ? now() : null,
        ]);

        if ($isSubmit) {
            $evaluation->update([
                'nomor_sertifikat' => InternshipEvaluation::generateCertificateNumber($evaluation->id),
            ]);

            NotificationService::notifyEvaluationSubmitted($evaluation);
            ActivityLogService::logEvaluationCreated($evaluation, 'submitted', auth()->user());

            return redirect()->route('company.evaluations.index')
                ->with('success', 'Nilai kinerja PKL berhasil dikirimkan ke sekolah dan sedang menunggu verifikasi guru.');
        }

        ActivityLogService::logEvaluationCreated($evaluation, 'draft', auth()->user());

        return redirect()->route('company.evaluations.index')
            ->with('success', 'Draf penilaian kinerja PKL berhasil disimpan sementara.');
    }

    /**
     * Show form for editing an evaluation.
     */
    public function edit(InternshipEvaluation $evaluation): View|RedirectResponse
    {
        $company = auth()->user()->company;

        if ($evaluation->company_id !== $company->id) {
            abort(403, 'Akses evaluasi ditolak.');
        }

        if ($evaluation->status === 'verified') {
            return redirect()->route('company.evaluations.show', $evaluation)
                ->with('info', 'Penilaian ini telah diverifikasi dan disahkan oleh pihak sekolah, sehingga tidak dapat diubah lagi.');
        }

        $evaluation->load(['application.job', 'student.user', 'student.major', 'student.schoolClass']);

        $breadcrumbs = [
            ['label' => 'Dashboard Mitra', 'url' => route('company.dashboard')],
            ['label' => 'Evaluasi Kinerja PKL', 'url' => route('company.evaluations.index')],
            ['label' => 'Edit Penilaian: ' . ($evaluation->student->user->name ?? 'Siswa'), 'url' => null],
        ];

        return view('company.evaluations.edit', compact('evaluation', 'breadcrumbs'));
    }

    /**
     * Update an evaluation in database.
     */
    public function update(Request $request, InternshipEvaluation $evaluation): RedirectResponse
    {
        $company = auth()->user()->company;

        if ($evaluation->company_id !== $company->id) {
            abort(403, 'Akses evaluasi ditolak.');
        }

        if ($evaluation->status === 'verified') {
            return redirect()->route('company.evaluations.show', $evaluation)
                ->with('error', 'Penilaian telah disahkan oleh sekolah dan terkunci.');
        }

        $validated = $request->validate([
            'nilai_teknis' => ['required', 'numeric', 'min:0', 'max:100'],
            'nilai_disiplin' => ['required', 'numeric', 'min:0', 'max:100'],
            'nilai_kerjasama' => ['required', 'numeric', 'min:0', 'max:100'],
            'catatan_industri' => ['nullable', 'string', 'max:2000'],
            'submit_action' => ['required', 'in:draft,submit'],
        ]);

        $finalScore = InternshipEvaluation::calculateFinalScore(
            (float) $validated['nilai_teknis'],
            (float) $validated['nilai_disiplin'],
            (float) $validated['nilai_kerjasama']
        );

        $predicate = InternshipEvaluation::determinePredicate($finalScore);
        $wasDraft = $evaluation->status === 'draft';
        $isSubmit = $validated['submit_action'] === 'submit';
        $status = $isSubmit ? 'submitted' : 'draft';

        $evaluation->update([
            'nilai_teknis' => $validated['nilai_teknis'],
            'nilai_disiplin' => $validated['nilai_disiplin'],
            'nilai_kerjasama' => $validated['nilai_kerjasama'],
            'nilai_akhir' => $finalScore,
            'predikat' => $predicate,
            'catatan_industri' => $validated['catatan_industri'] ?? null,
            'status' => $status,
            'submitted_at' => ($isSubmit && !$evaluation->submitted_at) ? now() : $evaluation->submitted_at,
            'nomor_sertifikat' => ($isSubmit && !$evaluation->nomor_sertifikat)
                ? InternshipEvaluation::generateCertificateNumber($evaluation->id)
                : $evaluation->nomor_sertifikat,
        ]);

        if ($isSubmit) {
            if ($wasDraft) {
                NotificationService::notifyEvaluationSubmitted($evaluation);
            }
            ActivityLogService::logEvaluationCreated($evaluation, 'submitted', auth()->user());

            return redirect()->route('company.evaluations.index')
                ->with('success', 'Nilai kinerja PKL berhasil dikirimkan dan menunggu verifikasi guru.');
        }

        ActivityLogService::logEvaluationCreated($evaluation, 'draft', auth()->user());

        return redirect()->route('company.evaluations.index')
            ->with('success', 'Perubahan draf penilaian PKL berhasil disimpan.');
    }

    /**
     * Show evaluation details view.
     */
    public function show(InternshipEvaluation $evaluation): View
    {
        $company = auth()->user()->company;

        if ($evaluation->company_id !== $company->id) {
            abort(403, 'Akses evaluasi ditolak.');
        }

        $evaluation->load(['application.job', 'student.user', 'student.major', 'student.schoolClass', 'teacher.user']);

        $breadcrumbs = [
            ['label' => 'Dashboard Mitra', 'url' => route('company.dashboard')],
            ['label' => 'Evaluasi Kinerja PKL', 'url' => route('company.evaluations.index')],
            ['label' => 'Detail Transkrip: ' . ($evaluation->student->user->name ?? 'Siswa'), 'url' => null],
        ];

        return view('company.evaluations.show', compact('evaluation', 'breadcrumbs'));
    }
}
