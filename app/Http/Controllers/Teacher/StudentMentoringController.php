<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Portfolio;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentMentoringController extends Controller
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
     * Display listing of mentored students and their internship/career statuses.
     */
    public function index(Request $request): View
    {
        $teacher = $this->getTeacher();

        $classes = SchoolClass::orderBy('tingkat')->orderBy('nama')->get();

        $query = Student::with([
            'user',
            'schoolClass.major',
            'applications.job.company',
            'studentSkills',
            'portfolios',
        ]);

        // Filter by Class
        if ($classId = $request->query('class_id')) {
            $query->where('class_id', $classId);
        }

        // Filter by Status / Progress
        $statusFilter = $request->query('status');
        if ($statusFilter === 'diterima') {
            $query->whereHas('applications', fn($q) => $q->where('status', 'diterima'));
        } elseif ($statusFilter === 'wawancara') {
            $query->whereHas('applications', fn($q) => $q->where('status', 'wawancara'));
        } elseif ($statusFilter === 'melamar') {
            $query->whereHas('applications', fn($q) => $q->whereIn('status', ['menunggu', 'ditinjau']));
        } elseif ($statusFilter === 'belum_melamar') {
            $query->doesntHave('applications');
        }

        // Search by student name or NISN
        if ($search = $request->query('q') ?: $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nisn', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        $students = $query->paginate(15)->withQueryString();

        // Calculate statistics
        $stats = [
            'total_students' => Student::count(),
            'placed_interns' => Student::whereHas('applications', fn($q) => $q->where('status', 'diterima'))->count(),
            'interviewing' => Student::whereHas('applications', fn($q) => $q->where('status', 'wawancara'))->count(),
            'pending_applications' => Application::whereIn('status', ['menunggu', 'ditinjau'])->count(),
        ];

        $breadcrumbs = [
            ['label' => 'Dashboard Guru', 'url' => route('teacher.dashboard')],
            ['label' => 'Bimbingan Siswa & PKL', 'url' => null],
        ];

        return view('teacher.mentoring.index', compact(
            'students',
            'classes',
            'stats',
            'teacher',
            'breadcrumbs'
        ));
    }

    /**
     * Display student portfolios submitted by students in managed or all classes.
     */
    public function portfolios(Request $request): View
    {
        $teacher = $this->getTeacher();
        $classes = SchoolClass::orderBy('tingkat')->orderBy('nama')->get();

        $query = Portfolio::with(['student.user', 'student.schoolClass']);

        // Filter by Class
        if ($classId = $request->query('class_id')) {
            $query->whereHas('student', fn($s) => $s->where('class_id', $classId));
        }

        // Filter by Category
        if ($category = $request->query('kategori')) {
            $query->where('kategori', $category);
        }

        // Search by portfolio title, description, or student name
        if ($search = $request->query('q') ?: $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%")
                  ->orWhereHas('student.user', fn($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        $portfolios = $query->latest()->paginate(12)->withQueryString();

        // Available categories in existing portfolios
        $categories = Portfolio::select('kategori')->distinct()->pluck('kategori')->filter();

        $breadcrumbs = [
            ['label' => 'Dashboard Guru', 'url' => route('teacher.dashboard')],
            ['label' => 'Portofolio Siswa Bimbingan', 'url' => null],
        ];

        return view('teacher.portfolios.index', compact(
            'portfolios',
            'classes',
            'categories',
            'teacher',
            'breadcrumbs'
        ));
    }
}
