<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Agenda;
use App\Models\Announcement;
use App\Models\Job;
use App\Models\Major;
use App\Models\Profile;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Services\JobMatchingService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the Student Vocational Dashboard.
     */
    public function index(Request $request, JobMatchingService $matchingService): View
    {
        $user = auth()->user();
        $student = $user->student;

        // Fallback safety if student profile does not exist yet
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

        $student->load(['schoolClass', 'major', 'skills', 'portfolios', 'user.profile']);

        $appliedJobIds = $student->applications()->pluck('job_id')->toArray();

        $stats = [
            'total_applications' => $student->applications()->count(),
            'interview_applications' => $student->applications()->where('status', 'wawancara')->count(),
            'accepted_applications' => $student->applications()->where('status', 'diterima')->count(),
            'portfolios_count' => $student->portfolios()->count(),
            'skills_count' => $student->skills()->count(),
            'validated_skills_count' => $student->skills()->wherePivot('tervalidasi', true)->count(),
        ];

        // Active application highlight (wawancara or ditinjau priority)
        $activeApplication = $student->applications()
            ->with(['job.company'])
            ->whereIn('status', ['wawancara', 'ditinjau', 'tertunda'])
            ->orderByRaw("CASE 
                WHEN status = 'wawancara' THEN 1 
                WHEN status = 'ditinjau' THEN 2 
                ELSE 3 END")
            ->latest()
            ->first();

        // Calculate recommendations for open jobs using KAK 15 Formula
        $openJobs = Job::where('status', 'dibuka')
            ->with(['company', 'skills'])
            ->latest()
            ->get();

        $recommendedJobs = $openJobs->map(function ($job) use ($student, $matchingService, $appliedJobIds) {
            $match = $matchingService->calculate($student, $job);
            $job->match_score = $match['total'];
            $job->match_percentage = $match['percentage'];
            $job->match_details = $match;
            $job->is_applied = in_array($job->id, $appliedJobIds);
            return $job;
        })->sortByDesc('match_score')->take(4)->values();

        // Student's recent portfolios and top skills
        $recentPortfolios = $student->portfolios()->latest()->take(3)->get();
        $studentSkills = $student->skills()->orderByPivot('tingkat', 'desc')->take(6)->get();

        // BKK Information & Agendas for Student
        $recentAnnouncements = Announcement::where('status', 'terbit')
            ->latest('mulai_tampil')
            ->latest('created_at')
            ->take(3)
            ->get();

        $upcomingAgendas = Agenda::where('tanggal_mulai', '>=', now()->subDays(1))
            ->orderBy('tanggal_mulai')
            ->take(3)
            ->get();

        $breadcrumbs = [
            ['label' => 'Dashboard Siswa', 'url' => null],
        ];

        return view('student.dashboard', compact(
            'student',
            'user',
            'stats',
            'activeApplication',
            'recommendedJobs',
            'recentPortfolios',
            'studentSkills',
            'recentAnnouncements',
            'upcomingAgendas',
            'breadcrumbs'
        ));
    }
}
