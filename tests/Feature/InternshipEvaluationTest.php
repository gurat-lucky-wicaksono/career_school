<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Application;
use App\Models\Company;
use App\Models\InternshipEvaluation;
use App\Models\Job;
use App\Models\Major;
use App\Models\Notification;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class InternshipEvaluationTest extends TestCase
{
    private function getCompanyUser(): array
    {
        $role = Role::firstOrCreate(['nama' => 'perusahaan'], ['label' => 'Perusahaan Mitra']);
        $user = User::firstOrCreate(
            ['email' => 'eval_company@test.com'],
            [
                'role_id' => $role->id,
                'name' => 'PT Mitra Evaluasi Industri',
                'username' => 'eval_company_' . Str::random(5),
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );

        $company = Company::firstOrCreate(
            ['user_id' => $user->id],
            [
                'nama' => 'PT Mitra Evaluasi Industri',
                'bidang' => 'Teknologi Informasi & Rekayasa Perangkat Lunak',
                'kota' => 'Kota Cirebon',
                'status_verifikasi' => 'terverifikasi',
            ]
        );

        return [$user, $company];
    }

    private function getTeacherUser(): array
    {
        $role = Role::firstOrCreate(['nama' => 'guru'], ['label' => 'Guru']);
        $user = User::firstOrCreate(
            ['email' => 'eval_teacher@test.com'],
            [
                'role_id' => $role->id,
                'name' => 'Suryana, S.T., M.Kom.',
                'username' => 'eval_teacher_' . Str::random(5),
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );

        $teacher = Teacher::firstOrCreate(
            ['user_id' => $user->id],
            [
                'nip' => '198205122008011005',
                'mata_pelajaran' => 'Kejuruan Rekayasa Perangkat Lunak',
                'is_wali_kelas' => true,
            ]
        );

        return [$user, $teacher];
    }

    private function getStudentUser(): array
    {
        $major = Major::firstOrCreate(['kode' => 'RPL'], ['nama' => 'Rekayasa Perangkat Lunak']);
        $teacherUser = $this->getTeacherUser()[1];
        $class = SchoolClass::firstOrCreate(
            ['nama' => 'XII RPL 1', 'tahun_ajaran' => '2025/2026'],
            [
                'major_id' => $major->id,
                'wali_kelas_id' => $teacherUser->id,
                'tingkat' => 12,
            ]
        );

        $role = Role::firstOrCreate(['nama' => 'siswa'], ['label' => 'Siswa']);
        $user = User::firstOrCreate(
            ['email' => 'eval_student@test.com'],
            [
                'role_id' => $role->id,
                'name' => 'Ahmad Fauzi Praktikan',
                'username' => 'eval_student_' . Str::random(5),
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );

        $student = Student::firstOrCreate(
            ['user_id' => $user->id],
            [
                'nisn' => '0069812345',
                'class_id' => $class->id,
                'major_id' => $major->id,
                'angkatan' => 2026,
                'status' => 'aktif',
            ]
        );

        return [$user, $student];
    }

    private function createInternshipApplication(string $status = 'diterima'): array
    {
        [$companyUser, $company] = $this->getCompanyUser();
        [$studentUser, $student] = $this->getStudentUser();

        $job = Job::firstOrCreate(
            ['slug' => 'junior-software-developer-eval'],
            [
                'company_id' => $company->id,
                'judul' => 'Junior Web Developer PKL',
                'deskripsi' => 'Praktik kerja lapangan posisi Web Developer PHP Laravel.',
                'lokasi' => 'Cirebon',
                'jenis' => 'magang',
                'kuota' => 5,
                'status' => 'dibuka',
            ]
        );

        $application = Application::firstOrCreate(
            [
                'job_id' => $job->id,
                'student_id' => $student->id,
            ],
            [
                'skor_kecocokan' => 88.50,
                'status' => $status,
                'pesan' => 'Saya siap melaksanakan PKL.',
                'reviewed_by' => $companyUser->id,
                'reviewed_at' => now(),
            ]
        );
        $application->update(['status' => $status]);

        return [$companyUser, $company, $studentUser, $student, $job, $application];
    }

    public function test_company_can_view_interns_evaluation_index(): void
    {
        [$companyUser] = $this->createInternshipApplication('diterima');

        $response = $this->actingAs($companyUser)->get(route('company.evaluations.index'));
        $response->assertStatus(200);
        $response->assertSee('Evaluasi Kinerja PKL');
        $response->assertSee('Ahmad Fauzi Praktikan');
    }

    public function test_company_can_view_evaluation_create_form_for_accepted_intern(): void
    {
        [$companyUser, , , , , $application] = $this->createInternshipApplication('diterima');
        InternshipEvaluation::where('application_id', $application->id)->delete();

        $response = $this->actingAs($companyUser)->get(route('company.evaluations.create', $application));
        $response->assertStatus(200);
        $response->assertSee('Rubrik Penilaian Kinerja Magang');
        $response->assertSee('Kompetensi Teknis');
        $response->assertSee('Disiplin');
    }

    public function test_company_cannot_evaluate_non_accepted_application(): void
    {
        [$companyUser, , , , , $application] = $this->createInternshipApplication('tertunda');
        $application->update(['status' => 'tertunda']);

        $response = $this->actingAs($companyUser)->get(route('company.evaluations.create', $application));
        $response->assertRedirect(route('company.evaluations.index'));
        $response->assertSessionHas('error');
    }

    public function test_company_can_save_evaluation_draft(): void
    {
        [$companyUser, , , , , $application] = $this->createInternshipApplication('diterima');
        InternshipEvaluation::where('application_id', $application->id)->delete();

        $payload = [
            'nilai_teknis' => 80,
            'nilai_disiplin' => 85,
            'nilai_kerjasama' => 90,
            'catatan_industri' => 'Siswa memiliki antusiasme belajar yang cukup baik.',
            'submit_action' => 'draft',
        ];

        $response = $this->actingAs($companyUser)->post(route('company.evaluations.store', $application), $payload);
        $response->assertRedirect(route('company.evaluations.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('internship_evaluations', [
            'application_id' => $application->id,
            'status' => 'draft',
            'nilai_teknis' => 80,
            'predikat' => 'B',
        ]);
    }

    public function test_company_can_submit_evaluation_with_calculated_scores_and_predicate(): void
    {
        [$companyUser, , , , , $application] = $this->createInternshipApplication('diterima');
        InternshipEvaluation::where('application_id', $application->id)->delete();

        // 90 * 0.40 = 36.0 | 95 * 0.30 = 28.5 | 90 * 0.30 = 27.0 -> Final = 91.5 (A)
        $payload = [
            'nilai_teknis' => 90,
            'nilai_disiplin' => 95,
            'nilai_kerjasama' => 90,
            'catatan_industri' => 'Performa sangat memuaskan dan berhasil merampungkan proyek modul sistem.',
            'submit_action' => 'submit',
        ];

        $response = $this->actingAs($companyUser)->post(route('company.evaluations.store', $application), $payload);
        $response->assertRedirect(route('company.evaluations.index'));

        $evaluation = InternshipEvaluation::where('application_id', $application->id)->first();
        $this->assertNotNull($evaluation);
        $this->assertEquals('submitted', $evaluation->status);
        $this->assertEquals(91.5, (float) $evaluation->nilai_akhir);
        $this->assertEquals('A', $evaluation->predikat);
        $this->assertNotNull($evaluation->nomor_sertifikat);
    }

    public function test_teacher_can_view_submitted_evaluations_list(): void
    {
        [$companyUser, $company, $studentUser, $student, $job, $application] = $this->createInternshipApplication('diterima');
        InternshipEvaluation::where('application_id', $application->id)->delete();

        $eval = InternshipEvaluation::create([
            'application_id' => $application->id,
            'company_id' => $company->id,
            'student_id' => $student->id,
            'nilai_teknis' => 88,
            'nilai_disiplin' => 90,
            'nilai_kerjasama' => 85,
            'nilai_akhir' => 87.7,
            'predikat' => 'A',
            'status' => 'submitted',
            'submitted_at' => now(),
            'nomor_sertifikat' => 'SMKN1-PKL/2026/0001',
        ]);

        [$teacherUser] = $this->getTeacherUser();

        $response = $this->actingAs($teacherUser)->get(route('teacher.evaluations.index'));
        $response->assertStatus(200);
        $response->assertSee('Pengesahan Nilai PKL');
        $response->assertSee('Ahmad Fauzi Praktikan');
    }

    public function test_teacher_can_verify_and_endorse_internship_evaluation(): void
    {
        [$companyUser, $company, $studentUser, $student, $job, $application] = $this->createInternshipApplication('diterima');
        InternshipEvaluation::where('application_id', $application->id)->delete();

        $eval = InternshipEvaluation::create([
            'application_id' => $application->id,
            'company_id' => $company->id,
            'student_id' => $student->id,
            'nilai_teknis' => 88,
            'nilai_disiplin' => 90,
            'nilai_kerjasama' => 85,
            'nilai_akhir' => 87.7,
            'predikat' => 'A',
            'status' => 'submitted',
            'submitted_at' => now(),
            'nomor_sertifikat' => 'SMKN1-PKL/2026/0001',
        ]);

        [$teacherUser, $teacher] = $this->getTeacherUser();

        $payload = [
            'catatan_guru' => 'Siswa menunjukkan disiplin tinggi dan capaian kompetensi sesuai standar kurikulum.',
        ];

        $response = $this->actingAs($teacherUser)->post(route('teacher.evaluations.verify', $eval), $payload);
        $response->assertRedirect(route('teacher.evaluations.show', $eval));

        $eval->refresh();
        $this->assertEquals('verified', $eval->status);
        $this->assertEquals($teacher->id, $eval->teacher_id);
        $this->assertNotNull($eval->verified_at);
        $this->assertStringContainsString('disiplin tinggi', $eval->catatan_guru);
    }

    public function test_student_can_view_verified_evaluation_transcript(): void
    {
        [$companyUser, $company, $studentUser, $student, $job, $application] = $this->createInternshipApplication('diterima');
        InternshipEvaluation::where('application_id', $application->id)->delete();

        $eval = InternshipEvaluation::create([
            'application_id' => $application->id,
            'company_id' => $company->id,
            'student_id' => $student->id,
            'nilai_teknis' => 90,
            'nilai_disiplin' => 90,
            'nilai_kerjasama' => 90,
            'nilai_akhir' => 90.0,
            'predikat' => 'A',
            'status' => 'verified',
            'submitted_at' => now()->subDay(),
            'verified_at' => now(),
            'nomor_sertifikat' => 'SMKN1-PKL/2026/0002',
        ]);

        $response = $this->actingAs($studentUser)->get(route('student.evaluations.index'));
        $response->assertStatus(200);
        $response->assertSee('Transkrip PKL');
        $response->assertSee('90.0');

        $showResponse = $this->actingAs($studentUser)->get(route('student.evaluations.show', $eval));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Transkrip Hasil Evaluasi PKL');
    }

    public function test_student_cannot_view_draft_evaluation(): void
    {
        [$companyUser, $company, $studentUser, $student, $job, $application] = $this->createInternshipApplication('diterima');
        InternshipEvaluation::where('application_id', $application->id)->delete();

        $eval = InternshipEvaluation::create([
            'application_id' => $application->id,
            'company_id' => $company->id,
            'student_id' => $student->id,
            'nilai_teknis' => 75,
            'nilai_disiplin' => 75,
            'nilai_kerjasama' => 75,
            'nilai_akhir' => 75.0,
            'predikat' => 'B',
            'status' => 'draft',
        ]);

        $response = $this->actingAs($studentUser)->get(route('student.evaluations.show', $eval));
        $response->assertRedirect(route('student.evaluations.index'));
        $response->assertSessionHas('info');
    }

    public function test_student_can_view_printable_certificate_when_verified(): void
    {
        [$companyUser, $company, $studentUser, $student, $job, $application] = $this->createInternshipApplication('diterima');
        InternshipEvaluation::where('application_id', $application->id)->delete();

        $eval = InternshipEvaluation::create([
            'application_id' => $application->id,
            'company_id' => $company->id,
            'student_id' => $student->id,
            'nilai_teknis' => 88,
            'nilai_disiplin' => 88,
            'nilai_kerjasama' => 88,
            'nilai_akhir' => 88.0,
            'predikat' => 'A',
            'status' => 'verified',
            'submitted_at' => now()->subDays(2),
            'verified_at' => now(),
            'nomor_sertifikat' => 'SMKN1-PKL/2026/0003',
        ]);

        $response = $this->actingAs($studentUser)->get(route('student.evaluations.print', $eval));
        $response->assertStatus(200);
        $response->assertSee('Surat Keterangan Selesai PKL');
        $response->assertSee('SMKN1-PKL/2026/0003');
    }

    public function test_student_cannot_print_certificate_if_unverified(): void
    {
        [$companyUser, $company, $studentUser, $student, $job, $application] = $this->createInternshipApplication('diterima');
        InternshipEvaluation::where('application_id', $application->id)->delete();

        $eval = InternshipEvaluation::create([
            'application_id' => $application->id,
            'company_id' => $company->id,
            'student_id' => $student->id,
            'nilai_teknis' => 85,
            'nilai_disiplin' => 85,
            'nilai_kerjasama' => 85,
            'nilai_akhir' => 85.0,
            'predikat' => 'A',
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        $response = $this->actingAs($studentUser)->get(route('student.evaluations.print', $eval));
        $response->assertRedirect(route('student.evaluations.index'));
        $response->assertSessionHas('error');
    }
}
