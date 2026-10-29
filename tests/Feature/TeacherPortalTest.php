<?php

namespace Tests\Feature;

use App\Models\Endorsement;
use App\Models\Major;
use App\Models\Portfolio;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\Skill;
use App\Models\Student;
use App\Models\StudentSkill;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TeacherPortalTest extends TestCase
{
    private function getTeacherUser(): array
    {
        $user = User::where('email', 'guru@demo.com')->first();
        if (!$user) {
            $role = Role::firstOrCreate(['nama' => 'guru'], ['label' => 'Guru']);
            $user = User::create([
                'role_id' => $role->id,
                'name' => 'Bambang Sudarsono, S.Kom.',
                'username' => 'bambang_guru',
                'email' => 'guru@demo.com',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]);

            $teacher = Teacher::create([
                'user_id' => $user->id,
                'nip' => '198503152010011002',
                'mata_pelajaran' => 'Pemrograman Berorientasi Objek & Web',
                'is_wali_kelas' => true,
            ]);
        } else {
            $teacher = $user->teacher;
            if (!$teacher) {
                $teacher = Teacher::create([
                    'user_id' => $user->id,
                    'nip' => '198503152010011002',
                    'mata_pelajaran' => 'Pemrograman Berorientasi Objek & Web',
                    'is_wali_kelas' => true,
                ]);
            }
        }

        return [$user, $teacher];
    }

    private function getStudentWithSkill(): array
    {
        $role = Role::firstOrCreate(['nama' => 'siswa'], ['label' => 'Siswa']);
        $studentUser = User::firstOrCreate(
            ['email' => 'siswa_test@demo.com'],
            [
                'role_id' => $role->id,
                'name' => 'Siswa Pengujian Vokasi',
                'username' => 'siswa_test_' . rand(100, 999),
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );

        $major = Major::firstOrCreate(['kode' => 'PPLG'], ['nama' => 'Pengembangan Perangkat Lunak & Gim']);
        $class = SchoolClass::firstOrCreate(['nama' => 'XI PPLG 1'], ['major_id' => $major->id, 'tingkat' => 11]);

        $student = Student::firstOrCreate(
            ['user_id' => $studentUser->id],
            [
                'nisn' => '9988776655',
                'class_id' => $class->id,
                'major_id' => $major->id,
                'angkatan' => 2024,
                'status' => 'aktif',
            ]
        );

        $skill = Skill::firstOrCreate(['nama' => 'Laravel Framework PHP'], ['kategori' => 'Teknis', 'deskripsi' => 'Backend development']);

        $studentSkill = StudentSkill::firstOrCreate(
            [
                'student_id' => $student->id,
                'skill_id' => $skill->id,
            ],
            [
                'tingkat' => 3,
                'tervalidasi' => false,
            ]
        );

        return [$student, $studentSkill];
    }

    public function test_guest_cannot_access_teacher_dashboard(): void
    {
        $response = $this->get('/teacher/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_non_teacher_redirected_to_own_portal(): void
    {
        $admin = User::where('email', 'admin@demo.com')->first();
        if (!$admin) {
            $adminRole = Role::firstOrCreate(['nama' => 'admin'], ['label' => 'Administrator']);
            $admin = User::create([
                'role_id' => $adminRole->id,
                'name' => 'Admin Test',
                'username' => 'admintest',
                'email' => 'admin@demo.com',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]);
        }

        $response = $this->actingAs($admin)->get('/teacher/dashboard');
        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_authenticated_teacher_can_view_dashboard(): void
    {
        [$user, $teacher] = $this->getTeacherUser();

        $response = $this->actingAs($user)->get('/teacher/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Portal Pembimbing Vokasi');
        $response->assertSee('Selamat Datang, ' . $user->name);
        $response->assertSee('Validasi Keahlian');
        $response->assertSee('Siswa Bimbingan');
    }

    public function test_teacher_can_view_skill_validation_index(): void
    {
        [$user, $teacher] = $this->getTeacherUser();

        $response = $this->actingAs($user)->get('/teacher/skills');

        $response->assertStatus(200);
        $response->assertSee('Validasi Keahlian Siswa Vokasi');
        $response->assertSee('Menunggu Validasi');
        $response->assertSee('Telah Tervalidasi');
    }

    public function test_teacher_can_filter_skills_by_status(): void
    {
        [$user, $teacher] = $this->getTeacherUser();

        $responsePending = $this->actingAs($user)->get('/teacher/skills?status=pending');
        $responsePending->assertStatus(200);

        $responseValidated = $this->actingAs($user)->get('/teacher/skills?status=validated');
        $responseValidated->assertStatus(200);

        $responseAll = $this->actingAs($user)->get('/teacher/skills?status=all');
        $responseAll->assertStatus(200);
    }

    public function test_teacher_can_validate_student_skill(): void
    {
        [$user, $teacher] = $this->getTeacherUser();
        [$student, $studentSkill] = $this->getStudentWithSkill();

        // Ensure initially not validated
        $studentSkill->update(['tervalidasi' => false]);

        $response = $this->actingAs($user)->post(route('teacher.skills.validate', $studentSkill), [
            'tingkat' => 4,
            'catatan' => 'Siswa telah menyelesaikan proyek Laravel MVC dengan standar industri.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $studentSkill->refresh();
        $this->assertTrue((bool) $studentSkill->tervalidasi);
        $this->assertEquals(4, $studentSkill->tingkat);

        $this->assertDatabaseHas('endorsements', [
            'student_skill_id' => $studentSkill->id,
            'teacher_id' => $teacher->id,
            'catatan' => 'Siswa telah menyelesaikan proyek Laravel MVC dengan standar industri.',
        ]);
    }

    public function test_teacher_can_bulk_validate_student_skills(): void
    {
        [$user, $teacher] = $this->getTeacherUser();
        [$student, $studentSkill] = $this->getStudentWithSkill();

        $studentSkill->update(['tervalidasi' => false]);

        $response = $this->actingAs($user)->post(route('teacher.skills.bulk-validate'), [
            'skill_ids' => [$studentSkill->id],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $studentSkill->refresh();
        $this->assertTrue((bool) $studentSkill->tervalidasi);
    }

    public function test_teacher_can_reject_or_revoke_skill_validation(): void
    {
        [$user, $teacher] = $this->getTeacherUser();
        [$student, $studentSkill] = $this->getStudentWithSkill();

        $studentSkill->update(['tervalidasi' => true]);
        Endorsement::create([
            'student_skill_id' => $studentSkill->id,
            'teacher_id' => $teacher->id,
            'catatan' => 'Validasi awal',
            'tanggal' => now(),
        ]);

        $response = $this->actingAs($user)->delete(route('teacher.skills.reject', $studentSkill));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $studentSkill->refresh();
        $this->assertFalse((bool) $studentSkill->tervalidasi);
        $this->assertDatabaseMissing('endorsements', [
            'student_skill_id' => $studentSkill->id,
        ]);
    }

    public function test_teacher_can_view_student_mentoring_index(): void
    {
        [$user, $teacher] = $this->getTeacherUser();

        $response = $this->actingAs($user)->get('/teacher/mentoring');

        $response->assertStatus(200);
        $response->assertSee('Pemantauan Siswa Bimbingan');
        $response->assertSee('Total Siswa Terdaftar');
        $response->assertSee('Diterima PKL');
    }

    public function test_teacher_can_filter_mentoring_students(): void
    {
        [$user, $teacher] = $this->getTeacherUser();

        $response = $this->actingAs($user)->get('/teacher/mentoring?status=diterima&q=Rifqi');
        $response->assertStatus(200);
    }

    public function test_teacher_can_view_portfolios_index(): void
    {
        [$user, $teacher] = $this->getTeacherUser();
        [$student, $studentSkill] = $this->getStudentWithSkill();

        $portfolio = Portfolio::updateOrCreate(
            ['judul' => 'Sistem Informasi PKL Berbasis Laravel', 'student_id' => $student->id],
            [
                'kategori' => 'Web Development',
                'deskripsi' => 'Aplikasi pemantauan PKL siswa vokasi',
                'tautan' => 'https://github.com/smkn1cirebon/portfolio-test',
                'status' => 'terbit',
                'created_at' => now(),
            ]
        );
        $portfolio->touch();

        $response = $this->actingAs($user)->get('/teacher/portfolios?q=Sistem+Informasi+PKL');

        $response->assertStatus(200);
        $response->assertSee('Portofolio Siswa Bimbingan');
        $response->assertSee('Sistem Informasi PKL Berbasis Laravel');
    }
}
