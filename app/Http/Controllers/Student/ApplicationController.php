<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Major;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApplicationController extends Controller
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

        return $student;
    }

    /**
     * Display a listing of applications submitted by the student.
     */
    public function index(Request $request): View
    {
        $student = $this->getStudent();

        $query = Application::where('student_id', $student->id)
            ->with(['job.company', 'job.skills'])
            ->latest();

        // Status counts for pill tabs
        $statusCounts = [
            'all' => Application::where('student_id', $student->id)->count(),
            'tertunda' => Application::where('student_id', $student->id)->where('status', 'tertunda')->count(),
            'ditinjau' => Application::where('student_id', $student->id)->where('status', 'ditinjau')->count(),
            'wawancara' => Application::where('student_id', $student->id)->where('status', 'wawancara')->count(),
            'diterima' => Application::where('student_id', $student->id)->where('status', 'diterima')->count(),
            'ditolak' => Application::where('student_id', $student->id)->where('status', 'ditolak')->count(),
        ];

        // Filter by Status Tab
        $status = $request->query('status');
        if ($status && in_array($status, ['tertunda', 'ditinjau', 'wawancara', 'diterima', 'ditolak'])) {
            $query->where('status', $status);
        }

        // Search by Job Title or Company Name
        if ($search = $request->query('q') ?: $request->query('search')) {
            $query->whereHas('job', function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhereHas('company', fn($c) => $c->where('nama', 'like', "%{$search}%"));
            });
        }

        $applications = $query->paginate(10)->withQueryString();

        $breadcrumbs = [
            ['label' => 'Dashboard Siswa', 'url' => route('student.dashboard')],
            ['label' => 'Lamaran Saya', 'url' => null],
        ];

        return view('student.applications.index', compact('applications', 'statusCounts', 'student', 'breadcrumbs'));
    }

    /**
     * Display the specified application progress and HRD review feedback.
     */
    public function show(Application $application): View
    {
        $student = $this->getStudent();

        // Authorization: Ensure application belongs to logged-in student
        if ($application->student_id !== $student->id) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat rincian lamaran ini.');
        }

        $application->load(['job.company', 'job.skills', 'reviewer']);

        $breadcrumbs = [
            ['label' => 'Dashboard Siswa', 'url' => route('student.dashboard')],
            ['label' => 'Lamaran Saya', 'url' => route('student.applications.index')],
            ['label' => $application->job->judul, 'url' => null],
        ];

        return view('student.applications.show', compact('application', 'student', 'breadcrumbs'));
    }

    /**
     * Cancel / withdraw a pending application.
     */
    public function destroy(Application $application): RedirectResponse
    {
        $student = $this->getStudent();

        if ($application->student_id !== $student->id) {
            abort(403, 'Anda tidak memiliki hak akses untuk membatalkan lamaran ini.');
        }

        if ($application->status !== 'tertunda') {
            return redirect()->route('student.applications.show', $application)
                ->with('error', 'Hanya berkas lamaran dengan status menunggu review yang dapat dibatalkan.');
        }

        $job = $application->job;
        if ($job && $job->application_count > 0) {
            $job->decrement('application_count');
        }

        $application->delete();

        return redirect()->route('student.applications.index')
            ->with('success', 'Lamaran Anda berhasil dibatalkan dari sistem seleksi.');
    }
}
