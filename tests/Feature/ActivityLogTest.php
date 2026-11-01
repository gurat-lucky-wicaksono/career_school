<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Application;
use App\Models\Company;
use App\Models\Job;
use App\Models\Major;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\Skill;
use App\Models\Student;
use App\Models\StudentSkill;
use App\Models\Teacher;
use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class ActivityLogTest extends TestCase
{
    private function getUser(string $email, string $username, string $name, string $roleName = 'siswa'): User
    {
        $user = User::where('email', $email)->first();
        if (! $user) {
            $role = Role::firstOrCreate(['nama' => $roleName], ['label' => ucfirst($roleName)]);
            $user = User::create([
                'role_id' => $role->id,
                'name' => $name,
                'username' => $username,
                'email' => $email,
                'password' => Hash::make('password'),
                'is_active' => true,
            ]);
        }
        return $user;
    }

    private function getMajor(): Major
    {
        return Major::firstOrCreate(
            ['kode' => 'PPLG'],
            [
                'nama' => 'Pengembangan Perangkat Lunak dan Gim',
                'deskripsi' => 'Konsentrasi keahlian rekayasa perangkat lunak.',
            ]
        );
    }

    public function test_admin_can_access_activity_logs_dashboard(): void
    {
        $admin = $this->getUser('admin_audit_dash@demo.com', 'admin_audit_dash', 'Admin Audit Dash', 'admin');

        $response = $this->actingAs($admin)->get(route('admin.activity-logs.index'));

        $response->assertStatus(200);
        $response->assertSee('Audit Trail &amp; Log Aktivitas Sistem', false);
        $response->assertSee('Semua Peran');
        $response->assertSee('Semua Kategori Aksi');
    }

    public function test_admin_can_filter_activity_logs_by_role_and_action(): void
    {
        $admin = $this->getUser('admin_audit_filt@demo.com', 'admin_audit_filt', 'Admin Audit Filt', 'admin');

        ActivityLogService::log(
            action: 'auth.login',
            description: 'Filter test login activity',
            user: $admin
        );

        $response = $this->actingAs($admin)->get(route('admin.activity-logs.index', [
            'role' => 'admin',
            'action' => 'auth.*',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Filter test login activity');
    }

    public function test_admin_can_search_activity_logs_by_keyword(): void
    {
        $admin = $this->getUser('admin_audit_srch@demo.com', 'admin_audit_srch', 'Admin Audit Srch', 'admin');

        ActivityLogService::log(
            action: 'company.verified',
            description: 'Verifikasi mitra industri unik PT Maju Terus Audit Sukses',
            user: $admin
        );

        $response = $this->actingAs($admin)->get(route('admin.activity-logs.index', [
            'q' => 'Maju Terus Audit Sukses',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Verifikasi mitra industri unik PT Maju Terus Audit Sukses');
    }

    public function test_admin_can_view_activity_log_detail_via_json(): void
    {
        $admin = $this->getUser('admin_audit_view@demo.com', 'admin_audit_view', 'Admin Audit View', 'admin');

        $log = ActivityLogService::log(
            action: 'tracer.exported',
            description: 'Export tracer report detail json test',
            properties: ['format' => 'csv', 'count' => 15],
            user: $admin
        );

        $response = $this->actingAs($admin)->getJson(route('admin.activity-logs.show', $log->id));

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $log->id,
            'action' => 'tracer.exported',
            'description' => 'Export tracer report detail json test',
        ]);
    }

    public function test_admin_can_export_activity_logs_csv(): void
    {
        $admin = $this->getUser('admin_audit_csv@demo.com', 'admin_audit_csv', 'Admin Audit Csv', 'admin');

        ActivityLogService::log(
            action: 'system.export',
            description: 'Activity stream log test',
            user: $admin
        );

        $response = $this->actingAs($admin)->get(route('admin.activity-logs.export'));

        $response->assertStatus(200);
        $this->assertTrue(
            Str::contains($response->headers->get('content-disposition', ''), 'attachment') ||
            Str::contains($response->headers->get('content-type', ''), 'text/csv')
        );
    }

    public function test_non_admin_cannot_access_activity_logs_dashboard(): void
    {
        $siswa = $this->getUser('siswa_audit_no@demo.com', 'siswa_audit_no', 'Siswa No Access', 'siswa');

        $response = $this->actingAs($siswa)->get(route('admin.activity-logs.index'));

        // Middleware redirects non-admin users to their dashboard or returns forbidden
        $this->assertTrue(in_array($response->status(), [302, 403]));
    }

    public function test_auth_login_and_logout_triggers_activity_log(): void
    {
        $user = $this->getUser('auth_logger_user@demo.com', 'auth_logger_user', 'Auth Logger User', 'siswa');

        $loginResponse = $this->post('/login', [
            'login' => $user->email,
            'password' => 'password',
        ]);

        $loginResponse->assertRedirect();
        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $user->id,
            'action' => 'auth.login',
        ]);

        $logoutResponse = $this->actingAs($user)->post('/logout');
        $logoutResponse->assertRedirect();

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $user->id,
            'action' => 'auth.logout',
        ]);
    }

    public function test_company_verification_triggers_activity_log(): void
    {
        $admin = $this->getUser('admin_verif_comp@demo.com', 'admin_verif_comp', 'Admin Verif Comp', 'admin');
        $compUser = $this->getUser('pt_log_target@demo.com', 'pt_log_target', 'PT Log Target HRD', 'perusahaan');

        $company = Company::firstOrCreate(
            ['user_id' => $compUser->id],
            [
                'nama' => 'PT Audit Log Target Nusantara',
                'alamat' => 'Jl. Kesambi No. 44, Cirebon',
                'status_verifikasi' => 'menunggu',
            ]
        );

        $response = $this->actingAs($admin)->post("/admin/companies/{$company->id}/verify", [
            'status' => 'terverifikasi',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $admin->id,
            'action' => 'company.verified',
            'subject_type' => Company::class,
            'subject_id' => $company->id,
        ]);
    }

    public function test_application_status_update_triggers_activity_log(): void
    {
        $compUser = $this->getUser('pt_status_test@demo.com', 'pt_status_test', 'PT Status Target HRD', 'perusahaan');
        $company = Company::firstOrCreate(
            ['user_id' => $compUser->id],
            [
                'nama' => 'PT Status Target Solusindo',
                'alamat' => 'Jl. Wahidin No. 12, Cirebon',
                'status_verifikasi' => 'terverifikasi',
            ]
        );

        $job = Job::create([
            'company_id' => $company->id,
            'judul' => 'Audit Software QA Engineer',
            'slug' => 'audit-software-qa-engineer-' . Str::random(6),
            'posisi' => 'QA Engineer',
            'tipe' => 'penuh_waktu',
            'deskripsi' => 'Pengujian fungsionalitas dan integritas sistem.',
            'status' => 'dibuka',
            'dipublikasikan_pada' => now(),
        ]);

        $studentUser = $this->getUser('siswa_app_status@demo.com', 'siswa_app_status', 'Siswa App Status', 'siswa');
        $major = $this->getMajor();
        $class = SchoolClass::firstOrCreate(
            ['nama' => 'XII PPLG 2'],
            ['major_id' => $major->id, 'tingkat' => 12, 'tahun_ajaran' => '2024/2025']
        );

        $student = Student::firstOrCreate(
            ['user_id' => $studentUser->id],
            [
                'nisn' => '0098765432',
                'class_id' => $class->id,
                'major_id' => $major->id,
                'angkatan' => 2024,
                'status' => 'aktif',
            ]
        );

        $application = Application::create([
            'job_id' => $job->id,
            'student_id' => $student->id,
            'status' => 'tertunda',
            'pesan' => 'Siap berkontribusi maksimal.',
        ]);

        $response = $this->actingAs($compUser)->patch("/company/applications/{$application->id}/status", [
            'status' => 'wawancara',
            'catatan_hrd' => 'Jadwal wawancara daring akan dikirimkan.',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $compUser->id,
            'action' => 'application.status_changed',
            'subject_type' => Application::class,
            'subject_id' => $application->id,
        ]);
    }

    public function test_skill_validation_triggers_activity_log(): void
    {
        $teacherUser = $this->getUser('guru_audit_skill@demo.com', 'guru_audit_skill', 'Guru Audit Skill', 'guru');
        $teacher = Teacher::firstOrCreate(
            ['user_id' => $teacherUser->id],
            [
                'nip' => '198701012015011003',
                'mata_pelajaran' => 'Pemrograman Web',
                'is_wali_kelas' => false,
            ]
        );

        $studentUser = $this->getUser('siswa_skill_target@demo.com', 'siswa_skill_target', 'Siswa Skill Target', 'siswa');
        $major = $this->getMajor();
        $class = SchoolClass::firstOrCreate(
            ['nama' => 'XII PPLG 3'],
            ['major_id' => $major->id, 'tingkat' => 12, 'tahun_ajaran' => '2024/2025']
        );

        $student = Student::firstOrCreate(
            ['user_id' => $studentUser->id],
            [
                'nisn' => '0078901234',
                'class_id' => $class->id,
                'major_id' => $major->id,
                'angkatan' => 2024,
                'status' => 'aktif',
            ]
        );

        $skill = Skill::firstOrCreate(['nama' => 'Laravel REST API Audit'], ['kategori' => 'Backend']);

        $studentSkill = StudentSkill::firstOrCreate(
            [
                'student_id' => $student->id,
                'skill_id' => $skill->id,
            ],
            [
                'tingkat' => 2,
                'tervalidasi' => false,
            ]
        );

        $response = $this->actingAs($teacherUser)->post("/teacher/skills/{$studentSkill->id}/validate", [
            'tingkat' => 4,
            'catatan' => 'Siswa telah menyelesaikan proyek Laravel dengan standar industri.',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $teacherUser->id,
            'action' => 'skill.validated',
            'subject_type' => StudentSkill::class,
            'subject_id' => $studentSkill->id,
        ]);
    }

    public function test_student_application_submission_and_withdrawal_triggers_activity_log(): void
    {
        $compUser = $this->getUser('pt_apply_target@demo.com', 'pt_apply_target', 'PT Apply Target HRD', 'perusahaan');
        $company = Company::firstOrCreate(
            ['user_id' => $compUser->id],
            [
                'nama' => 'PT Apply Target Cyber',
                'alamat' => 'Jl. Dr. Cipto No. 88, Cirebon',
                'status_verifikasi' => 'terverifikasi',
            ]
        );

        $job = Job::create([
            'company_id' => $company->id,
            'judul' => 'Junior Security Specialist',
            'slug' => 'junior-security-specialist-' . Str::random(6),
            'posisi' => 'Security Specialist',
            'tipe' => 'magang',
            'deskripsi' => 'Pengawasan sistem keamanan dan audit jaringan.',
            'status' => 'dibuka',
            'dipublikasikan_pada' => now(),
        ]);

        $studentUser = $this->getUser('siswa_apply_action@demo.com', 'siswa_apply_action', 'Siswa Apply Action', 'siswa');
        $major = $this->getMajor();
        $class = SchoolClass::firstOrCreate(
            ['nama' => 'XII PPLG 4'],
            ['major_id' => $major->id, 'tingkat' => 12, 'tahun_ajaran' => '2024/2025']
        );

        $student = Student::firstOrCreate(
            ['user_id' => $studentUser->id],
            [
                'nisn' => '0087654321',
                'class_id' => $class->id,
                'major_id' => $major->id,
                'angkatan' => 2024,
                'status' => 'aktif',
            ]
        );

        // Student applies to the job
        $applyResponse = $this->actingAs($studentUser)->post("/student/jobs/{$job->id}/apply", [
            'pesan' => 'Saya sangat tertarik dengan posisi ini.',
        ]);

        $applyResponse->assertRedirect();

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $studentUser->id,
            'action' => 'application.submitted',
        ]);

        $application = Application::where('job_id', $job->id)->where('student_id', $student->id)->first();
        $this->assertNotNull($application);

        // Student withdraws the application
        $withdrawResponse = $this->actingAs($studentUser)->delete("/student/applications/{$application->id}");
        $withdrawResponse->assertRedirect();

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $studentUser->id,
            'action' => 'application.withdrawn',
        ]);
    }
}
