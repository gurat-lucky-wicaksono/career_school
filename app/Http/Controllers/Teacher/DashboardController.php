<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Agenda;
use App\Models\Announcement;
use App\Models\Application;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentSkill;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
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
                'mata_pelajaran' => 'Pemrograman Web & Perangkat Lunak',
                'is_wali_kelas' => true,
            ]);
        }

        return $teacher;
    }

    /**
     * Display the Vocational Teacher & Internship Mentor Dashboard.
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        $teacher = $this->getTeacher();

        // Classes where this teacher is assigned as wali kelas
        $managedClasses = $teacher->classes()->withCount('students')->get();
        $managedClassIds = $managedClasses->pluck('id')->toArray();

        // If teacher is not assigned to a specific class, default to all PPLG classes
        if (empty($managedClassIds)) {
            $managedClasses = SchoolClass::withCount('students')->get();
            $managedClassIds = $managedClasses->pluck('id')->toArray();
        }

        $totalStudents = Student::whereIn('class_id', $managedClassIds)->count();

        // Statistics
        $stats = [
            'total_students' => $totalStudents,
            'pending_skills' => StudentSkill::where('tervalidasi', false)->count(),
            'validated_skills' => StudentSkill::where('tervalidasi', true)->count(),
            'active_internships' => Application::whereIn('status', ['wawancara', 'diterima'])->count(),
        ];

        // Recent Skills Awaiting Validation
        $pendingSkillSubmissions = StudentSkill::where('tervalidasi', false)
            ->with(['student.user', 'student.schoolClass', 'skill'])
            ->latest('updated_at')
            ->take(5)
            ->get();

        // Recent Student Applications to DUDI Partners
        $recentApplications = Application::with(['student.user', 'student.schoolClass', 'job.company'])
            ->whereIn('status', ['wawancara', 'diterima', 'ditinjau'])
            ->latest()
            ->take(6)
            ->get();

        // BKK Information & Agendas for Teacher
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
            ['label' => 'Dashboard Guru', 'url' => null],
        ];

        return view('teacher.dashboard', compact(
            'teacher',
            'user',
            'managedClasses',
            'stats',
            'pendingSkillSubmissions',
            'recentApplications',
            'recentAnnouncements',
            'upcomingAgendas',
            'breadcrumbs'
        ));
    }
}
