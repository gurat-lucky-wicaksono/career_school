<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\InternshipEvaluation;
use App\Models\Major;
use App\Models\Teacher;
use App\Services\ActivityLogService;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InternshipEvaluationController extends Controller
{
    /**
     * Get or initialize authenticated teacher model.
     */
    protected function getTeacher(): Teacher
    {
        $user = auth()->user();
        $teacher = $user->teacher;

        if (!$teacher) {
            $teacher = Teacher::create([
                'user_id' => $user->id,
                'nip' => '1985' . rand(10000000, 99999999),
                'mata_pelajaran' => 'Kejuruan Vokasi',
                'is_wali_kelas' => true,
            ]);
        }

        return $teacher;
    }

    /**
     * Display a listing of internship evaluations submitted by partner companies.
     */
    public function index(Request $request): View
    {
        $teacher = $this->getTeacher();

        $query = InternshipEvaluation::with([
            'application.job',
            'company',
            'student.user',
            'student.schoolClass',
            'student.major',
            'teacher.user',
        ]);

        // Filter by Status
        $status = $request->input('status', 'all');
        if ($status === 'submitted') {
            $query->where('status', 'submitted');
        } elseif ($status === 'verified') {
            $query->where('status', 'verified');
        } elseif ($status === 'draft') {
            $query->where('status', 'draft');
        }

        // Filter by Major
        if ($majorId = $request->input('major_id')) {
            $query->whereHas('student', fn($sq) => $sq->where('major_id', $majorId));
        }

        // Search by student name, NISN, company name, or job title
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('student.user', fn($uq) => $uq->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('student', fn($sq) => $sq->where('nisn', 'like', "%{$search}%"))
                  ->orWhereHas('company', fn($cq) => $cq->where('nama', 'like', "%{$search}%"))
                  ->orWhereHas('application.job', fn($jq) => $jq->where('judul', 'like', "%{$search}%"));
            });
        }

        $evaluations = $query->latest('updated_at')->paginate(12)->withQueryString();

        $stats = [
            'total_submitted' => InternshipEvaluation::where('status', 'submitted')->count(),
            'total_verified' => InternshipEvaluation::where('status', 'verified')->count(),
            'average_score' => round((float) InternshipEvaluation::where('status', 'verified')->avg('nilai_akhir'), 1),
            'grade_a_count' => InternshipEvaluation::where('status', 'verified')->where('predikat', 'A')->count(),
        ];

        $majors = Major::orderBy('nama')->get();

        $breadcrumbs = [
            ['label' => 'Dashboard Guru', 'url' => route('teacher.dashboard')],
            ['label' => 'Validasi Evaluasi PKL', 'url' => null],
        ];

        return view('teacher.evaluations.index', compact('evaluations', 'stats', 'majors', 'teacher', 'breadcrumbs'));
    }

    /**
     * Show detailed evaluation scorecard with verification form.
     */
    public function show(InternshipEvaluation $evaluation): View
    {
        $evaluation->load([
            'application.job',
            'company',
            'student.user',
            'student.schoolClass',
            'student.major',
            'teacher.user',
        ]);

        $teacher = $this->getTeacher();

        $breadcrumbs = [
            ['label' => 'Dashboard Guru', 'url' => route('teacher.dashboard')],
            ['label' => 'Validasi Evaluasi PKL', 'url' => route('teacher.evaluations.index')],
            ['label' => 'Verifikasi: ' . ($evaluation->student->user->name ?? 'Siswa'), 'url' => null],
        ];

        return view('teacher.evaluations.show', compact('evaluation', 'teacher', 'breadcrumbs'));
    }

    /**
     * Verify and endorse the internship evaluation and lock the grade.
     */
    public function verify(Request $request, InternshipEvaluation $evaluation): RedirectResponse
    {
        $teacher = $this->getTeacher();

        $validated = $request->validate([
            'catatan_guru' => ['nullable', 'string', 'max:2000'],
        ]);

        $evaluation->update([
            'status' => 'verified',
            'teacher_id' => $teacher->id,
            'catatan_guru' => $validated['catatan_guru'] ?? null,
            'verified_at' => now(),
            'nomor_sertifikat' => $evaluation->nomor_sertifikat ?: InternshipEvaluation::generateCertificateNumber($evaluation->id),
        ]);

        NotificationService::notifyEvaluationVerified($evaluation);
        ActivityLogService::logEvaluationVerified($evaluation, $teacher, auth()->user());

        return redirect()->route('teacher.evaluations.show', $evaluation)
            ->with('success', 'Nilai kinerja PKL siswa berhasil diverifikasi dan disahkan.');
    }
}
