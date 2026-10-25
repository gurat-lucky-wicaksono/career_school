<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Job;
use App\Models\Major;
use App\Models\Profile;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Services\JobMatchingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

class JobController extends Controller
{
    /**
     * Get or initialize authenticated student model.
     */
    protected function getStudent(): Student
    {
        $user = auth()->user();
        $student = $user->student;

        if (!$student) {
            $defaultMajor = Major::first();
            $defaultClass = SchoolClass::first();
            $student = Student::create([
                'user_id' => $user->id,
                'nisn' => '00' . rand(10000000, 99999999),
                'class_id' => $defaultClass?->id,
                'major_id' => $defaultMajor?->id,
                'angkatan' => date('Y'),
                'status' => 'aktif',
            ]);
        }

        if (!$user->profile) {
            Profile::create([
                'user_id' => $user->id,
                'bio' => 'Siswa SMKN 1 Cirebon',
                'kota' => 'Cirebon',
            ]);
            $user->load('profile');
        }

        return $student->load(['schoolClass', 'major', 'skills', 'portfolios', 'user.profile']);
    }

    /**
     * Display a listing of open industrial jobs with Smart Match ranking.
     */
    public function index(Request $request, JobMatchingService $matchingService): View
    {
        $student = $this->getStudent();
        $appliedJobIds = $student->applications()->pluck('job_id')->toArray();

        $query = Job::query()
            ->where('status', 'dibuka')
            ->with(['company', 'skills']);

        // Search Keyword
        if ($search = $request->query('q') ?: $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%")
                  ->orWhereHas('company', fn($c) => $c->where('nama', 'like', "%{$search}%"));
            });
        }

        // Filter Job Type (magang / penuh_waktu)
        if ($type = $request->query('jenis')) {
            if (in_array($type, ['magang', 'penuh_waktu'])) {
                $query->where('jenis', $type);
            }
        }

        // Filter Location
        if ($location = $request->query('lokasi')) {
            $query->where('lokasi', 'like', "%{$location}%");
        }

        $allJobs = $query->get();

        // Calculate Smart Match score for each job
        $scoredJobs = $allJobs->map(function ($job) use ($student, $matchingService, $appliedJobIds) {
            $match = $matchingService->calculate($student, $job);
            $job->match_score = $match['total'];
            $job->match_percentage = $match['percentage'];
            $job->match_details = $match;
            $job->is_applied = in_array($job->id, $appliedJobIds);
            return $job;
        });

        // Sorting
        $sort = $request->query('sort', 'match_desc');
        if ($sort === 'latest') {
            $scoredJobs = $scoredJobs->sortByDesc('created_at')->values();
        } elseif ($sort === 'salary_desc') {
            $scoredJobs = $scoredJobs->sortByDesc('gaji_min')->values();
        } else {
            // Default: Highest Smart Match first
            $scoredJobs = $scoredJobs->sortByDesc('match_score')->values();
        }

        // Custom pagination for in-memory scored collection
        $perPage = 8;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $scoredJobs->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $jobs = new LengthAwarePaginator(
            $currentItems,
            $scoredJobs->count(),
            $perPage,
            $currentPage,
            ['path' => LengthAwarePaginator::resolveCurrentPath(), 'query' => $request->query()]
        );

        $breadcrumbs = [
            ['label' => 'Dashboard Siswa', 'url' => route('student.dashboard')],
            ['label' => 'Eksplorasi Lowongan', 'url' => null],
        ];

        return view('student.jobs.index', compact('jobs', 'student', 'breadcrumbs'));
    }

    /**
     * Display the specified job with detailed match analysis and application form.
     */
    public function show(Job $job, JobMatchingService $matchingService): View
    {
        $student = $this->getStudent();
        $job->load(['company.user.profile', 'skills']);

        // Calculate detailed 5-component score
        $match = $matchingService->calculate($student, $job);

        // Check if student already submitted application
        $existingApplication = Application::where('student_id', $student->id)
            ->where('job_id', $job->id)
            ->first();

        // Increment view count
        $job->increment('view_count');

        $breadcrumbs = [
            ['label' => 'Dashboard Siswa', 'url' => route('student.dashboard')],
            ['label' => 'Eksplorasi Lowongan', 'url' => route('student.jobs.index')],
            ['label' => $job->judul, 'url' => null],
        ];

        return view('student.jobs.show', compact('job', 'student', 'match', 'existingApplication', 'breadcrumbs'));
    }

    /**
     * Handle the submission of an application for the specified job.
     */
    public function apply(Request $request, Job $job, JobMatchingService $matchingService): RedirectResponse
    {
        $student = $this->getStudent();

        if ($job->status !== 'dibuka') {
            return redirect()->route('student.jobs.show', $job)
                ->with('error', 'Lowongan ini telah ditutup atau tidak menerima lamaran baru.');
        }

        // Prevent duplicate application
        $existingApplication = Application::where('student_id', $student->id)
            ->where('job_id', $job->id)
            ->first();

        if ($existingApplication) {
            return redirect()->route('student.jobs.show', $job)
                ->with('info', 'Anda sudah pernah mengajukan lamaran untuk posisi ini.');
        }

        $validated = $request->validate([
            'pesan' => 'nullable|string|max:1500',
        ]);

        // Compute match score at moment of application
        $match = $matchingService->calculate($student, $job);

        Application::create([
            'job_id' => $job->id,
            'student_id' => $student->id,
            'skor_kecocokan' => $match['total'],
            'status' => 'tertunda',
            'pesan' => $validated['pesan'] ?? null,
        ]);

        $job->increment('application_count');

        return redirect()->route('student.jobs.show', $job)
            ->with('success', 'Lamaran Anda berhasil dikirim ke ' . ($job->company->nama ?? 'mitra industri') . '! Pantau perkembangan seleksi pada menu Lamaran Saya.');
    }
}
