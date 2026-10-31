<?php

namespace Tests\Feature;

use App\Models\Alumni;
use App\Models\Application;
use App\Models\Company;
use App\Models\Job;
use App\Models\Major;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class BkkReportExportTest extends TestCase
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

    public function test_admin_can_access_reports_dashboard(): void
    {
        $admin = $this->getUser('admin_report_test@demo.com', 'admin_report_test', 'Admin Report Tester', 'admin');

        $response = $this->actingAs($admin)->get(route('admin.reports.index'));

        $response->assertStatus(200);
        $response->assertSee('Pusat Laporan & Ekspor Data BKK');
        $response->assertSee('Distribusi Status Lulusan (BMW)');
        $response->assertSee('Keselarasan Kurikulum Vokasi');
    }

    public function test_admin_can_filter_reports_dashboard_by_major_and_year(): void
    {
        $admin = $this->getUser('admin_filter_report@demo.com', 'admin_filter_report', 'Admin Filter Tester', 'admin');
        $major = $this->getMajor();

        $response = $this->actingAs($admin)->get(route('admin.reports.index', [
            'major_id' => $major->id,
            'tahun' => 2024,
        ]));

        $response->assertStatus(200);
        $response->assertSee($major->nama);
    }

    public function test_admin_can_access_print_summary_page(): void
    {
        $admin = $this->getUser('admin_print_test@demo.com', 'admin_print_test', 'Admin Print Tester', 'admin');

        $response = $this->actingAs($admin)->get(route('admin.reports.print-summary'));

        $response->assertStatus(200);
        $response->assertSee('Laporan Rekapitulasi Penyerapan Lulusan', false);
        $response->assertSee('Kemitraan DUDI', false);
        $response->assertSee('Pemerintah Daerah Provinsi Jawa Barat');
        $response->assertSee('SMK Negeri 1 Cirebon');
        $response->assertSee('Ketua Bursa Kerja Khusus (BKK)');
    }

    public function test_admin_can_export_tracer_study_csv(): void
    {
        $admin = $this->getUser('admin_csv_tracer@demo.com', 'admin_csv_tracer', 'Admin Tracer CSV', 'admin');
        $major = $this->getMajor();
        $alumniUser = $this->getUser('alumni_csv_data@demo.com', 'alumni_csv_data', 'Alumni CSV Tester', 'siswa');

        Alumni::firstOrCreate(
            ['user_id' => $alumniUser->id],
            [
                'major_id' => $major->id,
                'tahun_lulus' => 2024,
                'status_bmw' => 'bekerja',
                'nama_instansi' => 'PT Cloud Inovasi Cirebon',
                'posisi_studi' => 'Junior Web Developer',
                'keselarasan' => 'sangat_sesuai',
                'waktu_tunggu_bulan' => 2,
                'is_public' => true,
            ]
        );

        $response = $this->actingAs($admin)->get(route('admin.reports.export.tracer-csv'));

        $response->assertStatus(200);
        $this->assertStringContainsString('text/csv', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('attachment; filename="tracer-study-smkn1-cirebon-', $response->headers->get('Content-Disposition'));

        ob_start();
        $response->sendContent();
        $content = ob_get_clean();

        $this->assertStringContainsString('Nama Alumni', $content);
        $this->assertStringContainsString('Status BMW', $content);
        $this->assertStringContainsString('Alumni CSV Tester', $content);
        $this->assertStringContainsString('PT Cloud Inovasi Cirebon', $content);
    }

    public function test_admin_can_export_applications_csv(): void
    {
        $admin = $this->getUser('admin_csv_app@demo.com', 'admin_csv_app', 'Admin App CSV', 'admin');
        $companyUser = $this->getUser('comp_app_csv@demo.com', 'comp_app_csv', 'PT CSV Company', 'perusahaan');
        $company = Company::firstOrCreate(
            ['user_id' => $companyUser->id],
            [
                'nama' => 'PT Solusi Aplikasi Vokasi',
                'bidang_usaha' => 'IT Consultant',
                'status_verifikasi' => 'terverifikasi',
                'is_active' => true,
            ]
        );

        $job = Job::firstOrCreate(
            ['company_id' => $company->id, 'judul' => 'Fullstack Developer PKL'],
            [
                'slug' => 'fullstack-developer-pkl-' . uniqid(),
                'posisi' => 'Fullstack Developer PKL',
                'jenis' => 'magang',
                'status' => 'dibuka',
                'lokasi' => 'Cirebon',
                'deskripsi' => 'Lowongan magang kejuruan',
            ]
        );

        $major = $this->getMajor();
        $schoolClass = SchoolClass::firstOrCreate(
            ['nama' => 'XI PPLG 1'],
            ['major_id' => $major->id, 'tingkat' => 11, 'tahun_ajaran' => '2024/2025']
        );

        $studentUser = $this->getUser('student_app_csv@demo.com', 'student_app_csv', 'Siswa CSV Lamaran', 'siswa');
        $student = Student::firstOrCreate(
            ['user_id' => $studentUser->id],
            [
                'nisn' => (string) rand(1000000000, 9999999999),
                'major_id' => $major->id,
                'class_id' => $schoolClass->id,
                'angkatan' => 2024,
            ]
        );

        Application::firstOrCreate(
            ['job_id' => $job->id, 'student_id' => $student->id],
            [
                'skor_kecocokan' => 88.5,
                'status' => 'wawancara',
                'catatan_hrd' => 'Terjadwal interview teknis',
            ]
        );

        $response = $this->actingAs($admin)->get(route('admin.reports.export.applications-csv'));

        $response->assertStatus(200);
        $this->assertStringContainsString('text/csv', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('attachment; filename="rekap-lamaran-bkk-smkn1-cirebon-', $response->headers->get('Content-Disposition'));

        ob_start();
        $response->sendContent();
        $content = ob_get_clean();

        $this->assertStringContainsString('Kode Registrasi', $content);
        $this->assertStringContainsString('Nama Siswa', $content);
        $this->assertStringContainsString('Siswa CSV Lamaran', $content);
        $this->assertStringContainsString('PT Solusi Aplikasi Vokasi', $content);
    }

    public function test_non_admin_cannot_access_reports_hub(): void
    {
        $studentUser = $this->getUser('student_unauthorized_rep@demo.com', 'stud_unauth_rep', 'Student Unauth', 'siswa');

        $response = $this->actingAs($studentUser)->get(route('admin.reports.index'));

        $response->assertRedirect(route('student.dashboard'));

        $jsonResponse = $this->actingAs($studentUser)->getJson(route('admin.reports.index'));
        $jsonResponse->assertStatus(403);
    }

    public function test_student_can_print_proof_of_own_application(): void
    {
        $companyUser = $this->getUser('comp_print_proof@demo.com', 'comp_print_proof', 'PT Print Proof', 'perusahaan');
        $company = Company::firstOrCreate(
            ['user_id' => $companyUser->id],
            [
                'nama' => 'PT Bukti Pendaftaran',
                'bidang_usaha' => 'Software Industry',
                'status_verifikasi' => 'terverifikasi',
                'is_active' => true,
            ]
        );

        $job = Job::firstOrCreate(
            ['company_id' => $company->id, 'judul' => 'UI UX Designer Magang'],
            [
                'slug' => 'ui-ux-designer-magang-' . uniqid(),
                'posisi' => 'UI UX Designer Magang',
                'jenis' => 'magang',
                'status' => 'dibuka',
                'lokasi' => 'Kota Cirebon',
                'deskripsi' => 'Lowongan magang UI UX',
            ]
        );

        $major = $this->getMajor();
        $schoolClass = SchoolClass::firstOrCreate(
            ['nama' => 'XI PPLG 2'],
            ['major_id' => $major->id, 'tingkat' => 11, 'tahun_ajaran' => '2024/2025']
        );

        $studentUser = $this->getUser('student_print_owner@demo.com', 'student_print_owner', 'Siswa Print Owner', 'siswa');
        $student = Student::firstOrCreate(
            ['user_id' => $studentUser->id],
            [
                'nisn' => (string) rand(1000000000, 9999999999),
                'major_id' => $major->id,
                'class_id' => $schoolClass->id,
                'angkatan' => 2024,
            ]
        );

        $application = Application::firstOrCreate(
            ['job_id' => $job->id, 'student_id' => $student->id],
            [
                'skor_kecocokan' => 92.0,
                'status' => 'diterima',
            ]
        );

        $response = $this->actingAs($studentUser)->get(route('student.applications.print', $application->id));

        $response->assertStatus(200);
        $response->assertSee('Bukti Pendaftaran', false);
        $response->assertSee('Lembar Rekomendasi Seleksi', false);
        $response->assertSee('Siswa Print Owner');
        $response->assertSee('PT Bukti Pendaftaran');
        $response->assertSee('UI UX Designer Magang');
    }

    public function test_student_cannot_print_proof_of_another_students_application(): void
    {
        $companyUser = $this->getUser('comp_sec_print@demo.com', 'comp_sec_print', 'PT Security Print', 'perusahaan');
        $company = Company::firstOrCreate(
            ['user_id' => $companyUser->id],
            [
                'nama' => 'PT Security Test',
                'status_verifikasi' => 'terverifikasi',
                'is_active' => true,
            ]
        );

        $job = Job::firstOrCreate(
            ['company_id' => $company->id, 'judul' => 'Security Analyst PKL'],
            [
                'slug' => 'security-analyst-pkl-' . uniqid(),
                'posisi' => 'Security Analyst PKL',
                'jenis' => 'magang',
                'status' => 'dibuka',
                'lokasi' => 'Cirebon',
                'deskripsi' => 'Lowongan magang keamanan data',
            ]
        );

        $major = $this->getMajor();
        $schoolClass = SchoolClass::firstOrCreate(
            ['nama' => 'XI PPLG 3'],
            ['major_id' => $major->id, 'tingkat' => 11, 'tahun_ajaran' => '2024/2025']
        );

        $studentA = $this->getUser('student_a_print@demo.com', 'student_a_print', 'Siswa A', 'siswa');
        $studentObjA = Student::firstOrCreate(
            ['user_id' => $studentA->id],
            [
                'nisn' => (string) rand(1000000000, 9999999999),
                'major_id' => $major->id,
                'class_id' => $schoolClass->id,
                'angkatan' => 2024,
            ]
        );

        $studentB = $this->getUser('student_b_print@demo.com', 'student_b_print', 'Siswa B', 'siswa');
        Student::firstOrCreate(
            ['user_id' => $studentB->id],
            [
                'nisn' => (string) rand(1000000000, 9999999999),
                'major_id' => $major->id,
                'class_id' => $schoolClass->id,
                'angkatan' => 2024,
            ]
        );

        $appA = Application::firstOrCreate(
            ['job_id' => $job->id, 'student_id' => $studentObjA->id],
            ['skor_kecocokan' => 80.0, 'status' => 'tertunda']
        );

        // Student B attempts to access Student A's print proof
        $response = $this->actingAs($studentB)->get(route('student.applications.print', $appA->id));

        $response->assertStatus(403);
    }
}
