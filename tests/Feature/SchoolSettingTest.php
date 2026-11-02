<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Application;
use App\Models\Company;
use App\Models\Job;
use App\Models\Major;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use App\Models\Student;
use App\Models\User;
use App\Services\SchoolSettingService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class SchoolSettingTest extends TestCase
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

    protected function setUp(): void
    {
        parent::setUp();
        // Seed default school settings if not present
        if (SchoolSetting::count() === 0) {
            $this->seed(\Database\Seeders\SchoolSettingSeeder::class);
        }
    }

    protected function tearDown(): void
    {
        // Reset settings back to defaults so other test classes are not affected
        $seeder = new \Database\Seeders\SchoolSettingSeeder();
        $seeder->run();
        SchoolSetting::flushCache();

        parent::tearDown();
    }

    public function test_admin_can_access_school_settings_page(): void
    {
        $admin = $this->getUser('admin_settings_tester@demo.com', 'admin_settings_tester', 'Admin Settings Tester', 'admin');

        $response = $this->actingAs($admin)->get(route('admin.settings'));

        $response->assertStatus(200);
        $response->assertSee('Pengaturan Sistem &amp; Identitas Lembaga', false);
        $response->assertSee('Identitas Pokok Sekolah');
        $response->assertSee('Bursa Kerja Khusus (BKK) Bina Mandiri');
        $response->assertSee('Tahun Ajaran Berjalan');
    }

    public function test_non_admin_cannot_access_school_settings_page(): void
    {
        $siswa = $this->getUser('siswa_no_settings@demo.com', 'siswa_no_settings', 'Siswa No Settings', 'siswa');

        $response = $this->actingAs($siswa)->get(route('admin.settings'));

        // Middleware redirects non-admin or returns forbidden
        $this->assertTrue(in_array($response->status(), [302, 403]));
    }

    public function test_admin_can_update_school_settings_and_invalidates_cache(): void
    {
        $admin = $this->getUser('admin_updater_settings@demo.com', 'admin_updater_settings', 'Admin Updater Settings', 'admin');

        $payload = [
            'school_name' => 'SMK Negeri 1 Cirebon Vokasi Unggul',
            'school_npsn' => '20222159',
            'school_address' => 'Jl. Perjuangan No. 1, Kota Cirebon, Jawa Barat',
            'school_phone' => '(0231) 480202',
            'school_email' => 'info@smkn1-cirebon.sch.id',
            'school_website' => 'https://smkn1-cirebon.sch.id',
            'principal_name' => 'Drs. H. Arifudin, M.Pd.',
            'principal_nip' => '19680512 199303 1 008',
            'bkk_name' => 'BKK Bina Mandiri SMKN 1 Cirebon',
            'bkk_leader_name' => 'Bambang Sudarsono, S.Kom.',
            'bkk_leader_nip' => '19820719 200902 1 003',
            'bkk_sk_number' => '560/042/BKK-Disnaker/2023',
            'bkk_contact_phone' => '0812-3456-7890',
            'active_academic_year' => '2026/2027',
            'active_semester' => 'Ganjil',
            'max_active_applications_per_student' => 4,
            'allow_student_registration' => 1,
        ];

        $response = $this->actingAs($admin)->put(route('admin.settings.update'), $payload);

        $response->assertRedirect(route('admin.settings'));
        $response->assertSessionHas('success');

        $this->assertEquals('SMK Negeri 1 Cirebon Vokasi Unggul', SchoolSettingService::getSchoolName());
        $this->assertEquals('2026/2027', SchoolSettingService::getActiveAcademicYear());
        $this->assertEquals(4, SchoolSettingService::getMaxActiveApplications());
    }

    public function test_admin_setting_update_triggers_activity_log(): void
    {
        $admin = $this->getUser('admin_audit_log_tester@demo.com', 'admin_audit_log_tester', 'Admin Audit Log Tester', 'admin');

        $uniqueSchoolName = 'SMK Negeri 1 Cirebon Pusat Keunggulan ' . Str::random(5);

        $payload = [
            'school_name' => $uniqueSchoolName,
            'school_npsn' => '20222159',
            'school_address' => 'Jl. Perjuangan, Kota Cirebon',
            'principal_name' => 'Drs. H. Arifudin, M.Pd.',
            'principal_nip' => '19680512 199303 1 008',
            'bkk_name' => 'BKK SMKN 1 Cirebon',
            'bkk_leader_name' => 'Bambang Sudarsono, S.Kom.',
            'bkk_leader_nip' => '19820719 200902 1 003',
            'active_academic_year' => '2025/2026',
            'active_semester' => 'Genap',
            'max_active_applications_per_student' => 3,
        ];

        $response = $this->actingAs($admin)->put(route('admin.settings.update'), $payload);

        $response->assertRedirect(route('admin.settings'));

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $admin->id,
            'action' => 'system.settings_updated',
        ]);
    }

    public function test_setting_validation_rules_are_enforced(): void
    {
        $admin = $this->getUser('admin_val_settings@demo.com', 'admin_val_settings', 'Admin Val Settings', 'admin');

        // Missing required school_name and invalid active_semester
        $payload = [
            'school_name' => '',
            'school_npsn' => '20222159',
            'school_address' => 'Jl. Perjuangan',
            'principal_name' => 'Kepala Sekolah',
            'principal_nip' => '19800101',
            'bkk_name' => 'BKK',
            'bkk_leader_name' => 'Ketua BKK',
            'bkk_leader_nip' => '19850101',
            'active_academic_year' => '2025/2026',
            'active_semester' => 'Kuartal', // invalid
            'max_active_applications_per_student' => 50, // exceeds max 20
        ];

        $response = $this->actingAs($admin)->put(route('admin.settings.update'), $payload);

        $response->assertSessionHasErrors(['school_name', 'active_semester', 'max_active_applications_per_student']);
    }

    public function test_print_reports_summary_displays_updated_school_identity(): void
    {
        $admin = $this->getUser('admin_print_dyn@demo.com', 'admin_print_dyn', 'Admin Print Dyn', 'admin');

        $customSchool = 'SMK Negeri 1 Cirebon Kampus Mandiri';
        SchoolSetting::set('school_name', $customSchool);

        $response = $this->actingAs($admin)->get(route('admin.reports.print-summary'));

        $response->assertStatus(200);
        $response->assertSee($customSchool);
    }

    public function test_student_application_limit_enforced_by_setting(): void
    {
        // Configure max active applications to 2
        SchoolSetting::set('max_active_applications_per_student', 2);

        $compUser = $this->getUser('pt_limit_test@demo.com', 'pt_limit_test', 'PT Limit Test HRD', 'perusahaan');
        $company = Company::firstOrCreate(
            ['user_id' => $compUser->id],
            [
                'nama' => 'PT Limit Technology Testing',
                'alamat' => 'Jl. Tuparev No. 10, Cirebon',
                'status_verifikasi' => 'terverifikasi',
            ]
        );

        $job1 = Job::create([
            'company_id' => $company->id,
            'judul' => 'Job Posisi 1',
            'slug' => 'job-posisi-1-' . Str::random(6),
            'posisi' => 'Junior Dev 1',
            'tipe' => 'penuh_waktu',
            'deskripsi' => 'Deskripsi testing 1',
            'status' => 'dibuka',
            'dipublikasikan_pada' => now(),
        ]);

        $job2 = Job::create([
            'company_id' => $company->id,
            'judul' => 'Job Posisi 2',
            'slug' => 'job-posisi-2-' . Str::random(6),
            'posisi' => 'Junior Dev 2',
            'tipe' => 'penuh_waktu',
            'deskripsi' => 'Deskripsi testing 2',
            'status' => 'dibuka',
            'dipublikasikan_pada' => now(),
        ]);

        $job3 = Job::create([
            'company_id' => $company->id,
            'judul' => 'Job Posisi 3',
            'slug' => 'job-posisi-3-' . Str::random(6),
            'posisi' => 'Junior Dev 3',
            'tipe' => 'penuh_waktu',
            'deskripsi' => 'Deskripsi testing 3',
            'status' => 'dibuka',
            'dipublikasikan_pada' => now(),
        ]);

        $studentUser = $this->getUser('siswa_limit_tester@demo.com', 'siswa_limit_tester', 'Siswa Limit Tester', 'siswa');
        $major = $this->getMajor();
        $class = SchoolClass::firstOrCreate(
            ['nama' => 'XII PPLG 5'],
            ['major_id' => $major->id, 'tingkat' => 12, 'tahun_ajaran' => '2024/2025']
        );

        $student = Student::firstOrCreate(
            ['user_id' => $studentUser->id],
            [
                'nisn' => '0091122334',
                'class_id' => $class->id,
                'major_id' => $major->id,
                'angkatan' => 2024,
                'status' => 'aktif',
            ]
        );

        // Application 1
        Application::create([
            'job_id' => $job1->id,
            'student_id' => $student->id,
            'status' => 'tertunda',
        ]);

        // Application 2
        Application::create([
            'job_id' => $job2->id,
            'student_id' => $student->id,
            'status' => 'wawancara',
        ]);

        // Attempting to apply to Job 3 must fail due to limit of 2 active applications
        $response = $this->actingAs($studentUser)->post("/student/jobs/{$job3->id}/apply", [
            'pesan' => 'Mencoba melamar ke lowongan ketiga.',
        ]);

        $response->assertRedirect(route('student.jobs.show', $job3));
        $response->assertSessionHas('error');
    }

    public function test_student_registration_toggle_disables_new_student_signup(): void
    {
        // Disable student self-registration
        SchoolSetting::set('allow_student_registration', '0');

        $major = $this->getMajor();
        $class = SchoolClass::firstOrCreate(
            ['nama' => 'XI PPLG Reg'],
            ['major_id' => $major->id, 'tingkat' => 11, 'tahun_ajaran' => '2024/2025']
        );

        $payload = [
            'role_type' => 'siswa',
            'name' => 'Calon Siswa Baru Ditutup',
            'username' => 'siswa_ditutup_' . Str::random(4),
            'email' => 'siswa_ditutup_' . Str::random(4) . '@demo.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'nisn' => '00' . rand(10000000, 99999999),
            'class_id' => $class->id,
            'major_id' => $major->id,
            'angkatan' => 2024,
        ];

        $response = $this->post('/register', $payload);

        $response->assertRedirect(route('register', ['type' => 'siswa']));
        $response->assertSessionHas('error', 'Pendaftaran akun siswa mandiri saat ini sedang ditutup oleh pihak sekolah.');

        // Re-enable for subsequent tests
        SchoolSetting::set('allow_student_registration', '1');
    }
}
