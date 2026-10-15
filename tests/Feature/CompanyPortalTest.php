<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Company;
use App\Models\Job;
use App\Models\Major;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\Skill;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CompanyPortalTest extends TestCase
{
    private function getCompanyUser(string $verificationStatus = 'terverifikasi'): array
    {
        $user = User::where('email', 'perusahaan@demo.com')->first();
        if (!$user) {
            $role = Role::firstOrCreate(['nama' => 'perusahaan'], ['label' => 'Perusahaan Mitra']);
            $user = User::create([
                'role_id' => $role->id,
                'name' => 'PT Telkom Akses Cirebon',
                'username' => 'telkom_cirebon',
                'email' => 'perusahaan@demo.com',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]);
            $company = Company::create([
                'user_id' => $user->id,
                'nama' => 'PT Telkom Akses Cirebon',
                'bidang' => 'Telekomunikasi & Jaringan Digital',
                'kota' => 'Kota Cirebon',
                'status_verifikasi' => $verificationStatus,
            ]);
        } else {
            $company = $user->company;
            if ($company) {
                $company->update(['status_verifikasi' => $verificationStatus]);
            }
        }

        return [$user, $company];
    }

    public function test_guest_cannot_access_company_portal(): void
    {
        $response = $this->get('/company/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_non_company_user_cannot_access_company_portal(): void
    {
        $admin = User::where('email', 'admin@demo.com')->first();
        if (!$admin) {
            $adminRole = Role::firstOrCreate(['nama' => 'admin'], ['label' => 'Administrator']);
            $admin = User::create([
                'role_id' => $adminRole->id,
                'name' => 'Administrator BKK SMKN 1',
                'username' => 'admin_bkk',
                'email' => 'admin@demo.com',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]);
        }

        $response = $this->actingAs($admin)->get('/company/dashboard');
        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_verified_company_can_view_dashboard(): void
    {
        [$user, $company] = $this->getCompanyUser('terverifikasi');

        $response = $this->actingAs($user)->get('/company/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Dashboard Mitra Industri', false);
        $response->assertSee($company->nama, false);
        $response->assertSee('Total Lowongan Kerja', false);
    }

    public function test_unverified_company_sees_pending_banner_and_cannot_access_create_job(): void
    {
        [$user, $company] = $this->getCompanyUser('menunggu');

        $response = $this->actingAs($user)->get('/company/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Akun Mitra Sedang Dalam Proses Verifikasi', false);

        $createResponse = $this->actingAs($user)->get('/company/jobs/create');
        $createResponse->assertRedirect(route('company.jobs.index'));
        $createResponse->assertSessionHas('warning');
    }

    public function test_verified_company_can_create_job_opening_with_skills(): void
    {
        [$user, $company] = $this->getCompanyUser('terverifikasi');

        $skill = Skill::firstOrCreate(
            ['nama' => 'PHP Laravel Test'],
            ['kategori' => 'Pemrograman']
        );

        $jobData = [
            'judul' => 'Junior Fullstack Engineer Test',
            'jenis' => 'penuh_waktu',
            'lokasi' => 'Cirebon',
            'kuota' => 2,
            'gaji_min' => 3000000,
            'gaji_max' => 4500000,
            'batas_lamaran' => now()->addDays(30)->format('Y-m-d'),
            'deskripsi' => 'Pengembangan aplikasi berbasis web Laravel dan database MySQL.',
            'skills' => [
                [
                    'id' => $skill->id,
                    'bobot' => 4,
                    'wajib' => 1,
                ],
            ],
        ];

        $response = $this->actingAs($user)->post('/company/jobs', $jobData);

        $response->assertRedirect(route('company.jobs.index'));
        $this->assertDatabaseHas('jobs', [
            'company_id' => $company->id,
            'judul' => 'Junior Fullstack Engineer Test',
            'status' => 'dibuka',
        ]);

        $createdJob = Job::where('company_id', $company->id)->where('judul', 'Junior Fullstack Engineer Test')->first();
        $this->assertNotNull($createdJob);
        $this->assertTrue($createdJob->skills->contains($skill->id));

        // Cleanup test job and skill
        $createdJob->delete();
        $skill->delete();
    }

    public function test_company_can_toggle_job_status(): void
    {
        [$user, $company] = $this->getCompanyUser('terverifikasi');

        $job = Job::firstOrCreate(
            [
                'company_id' => $company->id,
                'judul' => 'Job Toggle Test',
            ],
            [
                'slug' => 'job-toggle-test-' . uniqid(),
                'deskripsi' => 'Deskripsi job toggle.',
                'lokasi' => 'Cirebon',
                'jenis' => 'magang',
                'kuota' => 1,
                'status' => 'dibuka',
            ]
        );

        $job->update(['status' => 'dibuka']);

        $response = $this->actingAs($user)->patch("/company/jobs/{$job->id}/toggle-status");
        $response->assertSessionHas('success');

        $job->refresh();
        $this->assertEquals('ditutup', $job->status);

        // Cleanup test job
        $job->delete();
    }

    public function test_company_can_view_applicant_and_update_status(): void
    {
        [$user, $company] = $this->getCompanyUser('terverifikasi');

        $job = Job::firstOrCreate(
            [
                'company_id' => $company->id,
                'judul' => 'Job Review Applicant Test',
            ],
            [
                'slug' => 'job-review-' . uniqid(),
                'deskripsi' => 'Deskripsi job review.',
                'lokasi' => 'Cirebon',
                'jenis' => 'penuh_waktu',
                'kuota' => 1,
                'status' => 'dibuka',
            ]
        );

        $student = Student::whereHas('user', fn($q) => $q->where('email', 'siswa@demo.com'))->first();

        $application = Application::firstOrCreate(
            [
                'job_id' => $job->id,
                'student_id' => $student->id,
            ],
            [
                'skor_kecocokan' => 88.50,
                'status' => 'tertunda',
                'pesan' => 'Saya tertarik melamar posisi ini.',
            ]
        );

        // View applicant
        $viewResponse = $this->actingAs($user)->get("/company/applications/{$application->id}");
        $viewResponse->assertStatus(200);
        $viewResponse->assertSee($student->user->name, false);
        $viewResponse->assertSee('89%', false); // 88.50 formatted as 89%

        // Update status to wawancara
        $updateResponse = $this->actingAs($user)->patch("/company/applications/{$application->id}/status", [
            'status' => 'wawancara',
            'catatan_hrd' => 'Jadwal wawancara hari Jumat jam 09.00',
        ]);

        $updateResponse->assertSessionHas('success');

        $application->refresh();
        $this->assertEquals('wawancara', $application->status);
        $this->assertEquals('Jadwal wawancara hari Jumat jam 09.00', $application->catatan_hrd);

        // Cleanup temporary records
        $application->delete();
        $job->delete();
    }

    public function test_company_can_perform_global_search(): void
    {
        [$user, $company] = $this->getCompanyUser('terverifikasi');

        $response = $this->actingAs($user)->getJson('/company/search?q=lowongan');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'query',
            'groups',
        ]);
        $response->assertJson([
            'success' => true,
        ]);
    }

    public function test_company_jobs_index_renders_modals(): void
    {
        [$user, $company] = $this->getCompanyUser('terverifikasi');

        $response = $this->actingAs($user)->get('/company/jobs');

        $response->assertStatus(200);
        $response->assertSee('modal-create-job', false);
        $response->assertSee('modal-edit-job', false);
        $response->assertSee('modal-detail-job', false);
        $response->assertSee('Tambah Lowongan', false);
    }
}
