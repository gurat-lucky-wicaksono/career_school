<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Endorsement;
use App\Models\SchoolClass;
use App\Models\Skill;
use App\Models\StudentSkill;
use App\Models\Teacher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SkillValidationController extends Controller
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
     * Display listing of student skills pending validation or validated.
     */
    public function index(Request $request): View
    {
        $teacher = $this->getTeacher();

        $query = StudentSkill::with(['student.user', 'student.schoolClass', 'skill', 'endorsements.teacher.user']);

        // Filter Status Tab (pending / validated / all)
        $status = $request->query('status', 'pending');
        if ($status === 'pending') {
            $query->where('tervalidasi', false);
        } elseif ($status === 'validated') {
            $query->where('tervalidasi', true);
        }

        // Filter by Class
        if ($classId = $request->query('class_id')) {
            $query->whereHas('student', fn($s) => $s->where('class_id', $classId));
        }

        // Filter by Skill Master
        if ($skillId = $request->query('skill_id')) {
            $query->where('skill_id', $skillId);
        }

        // Search by Student Name or NISN
        if ($search = $request->query('q') ?: $request->query('search')) {
            $query->whereHas('student', function ($s) use ($search) {
                $s->where('nisn', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        $studentSkills = $query->latest('updated_at')->paginate(15)->withQueryString();

        // Counters for status tabs
        $statusCounts = [
            'all' => StudentSkill::count(),
            'pending' => StudentSkill::where('tervalidasi', false)->count(),
            'validated' => StudentSkill::where('tervalidasi', true)->count(),
        ];

        $classes = SchoolClass::orderBy('tingkat')->orderBy('nama')->get();
        $skills = Skill::orderBy('nama')->get();

        $breadcrumbs = [
            ['label' => 'Dashboard Guru', 'url' => route('teacher.dashboard')],
            ['label' => 'Validasi Keahlian Siswa', 'url' => null],
        ];

        return view('teacher.skills.index', compact(
            'studentSkills',
            'statusCounts',
            'classes',
            'skills',
            'teacher',
            'breadcrumbs'
        ));
    }

    /**
     * Approve and validate a student skill with teacher endorsement.
     */
    public function validateSkill(Request $request, StudentSkill $studentSkill): RedirectResponse
    {
        $teacher = $this->getTeacher();

        $validated = $request->validate([
            'tingkat' => 'required|integer|min:1|max:5',
            'catatan' => 'nullable|string|max:500',
        ]);

        $studentSkill->update([
            'tingkat' => $validated['tingkat'],
            'tervalidasi' => true,
        ]);

        Endorsement::updateOrCreate(
            [
                'student_skill_id' => $studentSkill->id,
                'teacher_id' => $teacher->id,
            ],
            [
                'catatan' => $validated['catatan'] ?? 'Kompetensi telah diverifikasi dan memenuhi standar kejuruan SMKN 1 Cirebon.',
                'tanggal' => now(),
            ]
        );

        return redirect()->back()
            ->with('success', 'Kompetensi ' . ($studentSkill->skill->nama ?? 'keahlian') . ' siswa berhasil divalidasi resmi.');
    }

    /**
     * Bulk approve multiple pending student skills.
     */
    public function bulkValidate(Request $request): RedirectResponse
    {
        $teacher = $this->getTeacher();

        $validated = $request->validate([
            'skill_ids' => 'required|array',
            'skill_ids.*' => 'exists:student_skills,id',
        ]);

        $skills = StudentSkill::whereIn('id', $validated['skill_ids'])->get();

        foreach ($skills as $skill) {
            $skill->update(['tervalidasi' => true]);

            Endorsement::updateOrCreate(
                [
                    'student_skill_id' => $skill->id,
                    'teacher_id' => $teacher->id,
                ],
                [
                    'catatan' => 'Divalidasi secara kolektif oleh guru pembimbing kejuruan.',
                    'tanggal' => now(),
                ]
            );
        }

        return redirect()->back()
            ->with('success', count($skills) . ' kompetensi keahlian siswa berhasil disetujui sekaligus.');
    }

    /**
     * Revoke or reject a validated skill status.
     */
    public function rejectSkill(StudentSkill $studentSkill): RedirectResponse
    {
        $studentSkill->update(['tervalidasi' => false]);
        $studentSkill->endorsements()->delete();

        return redirect()->back()
            ->with('success', 'Status validasi keahlian berhasil dikembalikan ke status antrean.');
    }
}
