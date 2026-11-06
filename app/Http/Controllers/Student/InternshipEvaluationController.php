<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\InternshipEvaluation;
use App\Models\SchoolSetting;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InternshipEvaluationController extends Controller
{
    /**
     * Get authenticated student model.
     */
    protected function getStudent(): Student
    {
        $user = auth()->user();
        $student = $user->student;

        if (!$student) {
            abort(403, 'Akses khusus profil siswa.');
        }

        return $student;
    }

    /**
     * Display a listing of internships, transcripts, and verified certificates.
     */
    public function index(Request $request): View
    {
        $student = $this->getStudent();

        // Get all accepted internship applications
        $acceptedApplications = Application::where('student_id', $student->id)
            ->where('status', 'diterima')
            ->with(['job.company', 'evaluation.teacher.user'])
            ->latest()
            ->get();

        // Get student evaluations (excluding unsubmitted drafts)
        $evaluations = InternshipEvaluation::where('student_id', $student->id)
            ->whereIn('status', ['submitted', 'verified'])
            ->with(['application.job', 'company', 'teacher.user'])
            ->latest()
            ->get();

        $stats = [
            'total_accepted' => $acceptedApplications->count(),
            'total_verified' => $evaluations->where('status', 'verified')->count(),
            'highest_score' => $evaluations->where('status', 'verified')->max('nilai_akhir') ?? 0,
        ];

        $breadcrumbs = [
            ['label' => 'Dashboard Siswa', 'url' => route('student.dashboard')],
            ['label' => 'Sertifikat & Nilai PKL', 'url' => null],
        ];

        return view('student.evaluations.index', compact(
            'student',
            'acceptedApplications',
            'evaluations',
            'stats',
            'breadcrumbs'
        ));
    }

    /**
     * Show detailed transcript view.
     */
    public function show(InternshipEvaluation $evaluation): View|RedirectResponse
    {
        $student = $this->getStudent();

        if ($evaluation->student_id !== $student->id) {
            abort(403, 'Akses evaluasi ditolak.');
        }

        if ($evaluation->status === 'draft') {
            return redirect()->route('student.evaluations.index')
                ->with('info', 'Penilaian masih dalam proses penyusunan oleh mitra industri.');
        }

        $evaluation->load([
            'application.job',
            'company',
            'student.user',
            'student.schoolClass',
            'student.major',
            'teacher.user',
        ]);

        $breadcrumbs = [
            ['label' => 'Dashboard Siswa', 'url' => route('student.dashboard')],
            ['label' => 'Sertifikat & Nilai PKL', 'url' => route('student.evaluations.index')],
            ['label' => 'Transkrip Nilai: ' . ($evaluation->application->job->judul ?? 'Magang'), 'url' => null],
        ];

        return view('student.evaluations.show', compact('evaluation', 'breadcrumbs'));
    }

    /**
     * Display printable Certificate of Internship Completion.
     */
    public function printCertificate(InternshipEvaluation $evaluation): View|RedirectResponse
    {
        $student = $this->getStudent();

        if ($evaluation->student_id !== $student->id) {
            abort(403, 'Akses evaluasi ditolak.');
        }

        if ($evaluation->status !== 'verified') {
            return redirect()->route('student.evaluations.index')
                ->with('error', 'Sertifikat hanya dapat dicetak setelah divalidasi dan disahkan oleh guru pembimbing sekolah.');
        }

        $evaluation->load([
            'application.job',
            'company',
            'student.user',
            'student.schoolClass',
            'student.major',
            'teacher.user',
        ]);

        $schoolName = SchoolSetting::get('school_name', 'SMK Negeri 1 Cirebon');
        $schoolAddress = SchoolSetting::get('school_address', 'Jl. Perjuangan No. 1, Cirebon, Jawa Barat');
        $headmasterName = SchoolSetting::get('headmaster_name', 'Drs. H. Hendra Wijaya, M.Pd.');
        $bkkHeadName = SchoolSetting::get('bkk_head_name', 'Ir. Budi Santoso, S.T.');

        return view('student.evaluations.certificate', compact(
            'evaluation',
            'schoolName',
            'schoolAddress',
            'headmasterName',
            'bkkHeadName'
        ));
    }
}
