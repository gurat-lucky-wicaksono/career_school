<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Major;
use App\Models\Profile;
use App\Models\SchoolClass;
use App\Models\Skill;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
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

        return $student->load(['schoolClass', 'major', 'skills', 'user.profile']);
    }

    /**
     * Display student profile, educational info, and competencies.
     */
    public function index(): View
    {
        $student = $this->getStudent();
        $user = auth()->user();

        $existingSkillIds = $student->skills->pluck('id')->toArray();
        $availableSkills = Skill::whereNotIn('id', $existingSkillIds)->orderBy('nama')->get();

        $breadcrumbs = [
            ['label' => 'Dashboard Siswa', 'url' => route('student.dashboard')],
            ['label' => 'Keahlian & Profil', 'url' => null],
        ];

        return view('student.profile.index', compact('student', 'user', 'availableSkills', 'breadcrumbs'));
    }

    /**
     * Update student personal profile details.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'bio' => 'nullable|string|max:1000',
            'telepon' => 'nullable|string|max:20',
            'kota' => 'nullable|string|max:80',
            'jenis_kelamin' => 'nullable|in:L,P',
            'tanggal_lahir' => 'nullable|date',
        ]);

        $user->update(['name' => $validated['name']]);

        $profile = $user->profile ?? Profile::create(['user_id' => $user->id]);
        $profile->update([
            'bio' => $validated['bio'] ?? null,
            'telepon' => $validated['telepon'] ?? null,
            'kota' => $validated['kota'] ?? null,
            'jenis_kelamin' => $validated['jenis_kelamin'] ?? null,
            'tanggal_lahir' => $validated['tanggal_lahir'] ?? null,
        ]);

        return redirect()->route('student.profile')
            ->with('success', 'Profil biodata siswa berhasil diperbarui.');
    }

    /**
     * Add a vocational skill to student profile.
     */
    public function addSkill(Request $request): RedirectResponse
    {
        $student = $this->getStudent();

        $validated = $request->validate([
            'skill_id' => 'required|exists:skills,id',
            'tingkat' => 'required|integer|min:1|max:5',
        ]);

        // If already exists, update level; otherwise attach new skill
        if ($student->skills()->where('skills.id', $validated['skill_id'])->exists()) {
            $student->skills()->updateExistingPivot($validated['skill_id'], [
                'tingkat' => $validated['tingkat'],
            ]);
            $message = 'Tingkat kemahiran keahlian berhasil diperbarui.';
        } else {
            $student->skills()->attach($validated['skill_id'], [
                'tingkat' => $validated['tingkat'],
                'tervalidasi' => false,
            ]);
            $message = 'Keahlian baru berhasil ditambahkan. Guru pengampu akan memvalidasi kompetensi Anda.';
        }

        return redirect()->route('student.profile')
            ->with('success', $message);
    }

    /**
     * Remove a vocational skill from student profile.
     */
    public function removeSkill(Skill $skill): RedirectResponse
    {
        $student = $this->getStudent();

        $student->skills()->detach($skill->id);

        return redirect()->route('student.profile')
            ->with('success', 'Keahlian berhasil dihapus dari profil.');
    }
}
