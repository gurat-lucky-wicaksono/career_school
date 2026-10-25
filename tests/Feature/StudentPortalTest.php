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

class StudentPortalTest extends TestCase
{
    private function getStudentUser(): array
    {
        $user = User::where('email', 'siswa@demo.com')->first();
        if (!$user) {
            $role = Role::firstOrCreate(['nama' => 'siswa'], ['label' => 'Siswa']);
            $user = User::create([
                'role_id' => $role->id,
                'name' => 'Rifqi Pratama',
                'username' => 'rifqi_pplg',
                'email' => 'siswa@demo.com',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]);

            $major = Major::firstOrCreate(['kode' => 'PPLG'], ['nama' => 'Pengembangan Perangkat Lunak & Gim']);
            $class = SchoolClass::firstOrCreate(['nama' => 'XI PPLG 1'], ['major_id' => $major->id, 'tingkat' => 11]);

            $student = Student::create([
                'user_id' => $user->id,
                'nisn' => '0061234567',
                'class_id' => $class->id,
                'major_id' => $major->id,
                'angkatan' => 2024,
                'status' => 'aktif',
            ]);
        } else {
            $student = $user->student;
        }

        return [$user, $student];
    }

    public function test_guest_cannot_access_student_dashboard(): void
    {
        $response = $this->get('/student/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_non_student_cannot_access_student_portal(): void
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

        $response = $this->actingAs($admin)->get('/student/dashboard');
        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_authenticated_student_can_view_dashboard(): void
    {
        [$user, $student] = $this->getStudentUser();

        $response = $this->actingAs($user)->get('/student/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Halo, ' . $user->name);
        $response->assertSee('Portal Siswa Vokasi');
        $response->assertSee('Rekomendasi Lowongan Industri');
        $response->assertSee('Keahlian Terdaftar');
    }

    public function test_student_can_view_job_exploration_index(): void
    {
        [$user, $student] = $this->getStudentUser();

        $response = $this->actingAs($user)->get('/student/jobs');

        $response->assertStatus(200);
        $response->assertSee('Eksplorasi Lowongan Industri');
        $response->assertSee('Formula KAK 15 Terapan');
    }

    public function test_student_can_filter_jobs_by_keyword_and_type(): void
    {
        [$user, $student] = $this->getStudentUser();

        $response = $this->actingAs($user)->get('/student/jobs?q=Web&jenis=magang');

        $response->assertStatus(200);
        $response->assertSee('Eksplorasi Lowongan Industri');
    }

    public function test_student_can_view_job_details_with_match_breakdown(): void
    {
        [$user, $student] = $this->getStudentUser();

        $job = Job::where('status', 'dibuka')->first();
        if (!$job) {
            $company = Company::first();
            $job = Job::create([
                'company_id' => $company->id,
                'judul' => 'Junior Web Developer',
                'slug' => 'junior-web-developer-' . time(),
                'deskripsi' => 'Pengembangan antarmuka web vokasi.',
                'lokasi' => 'Kota Cirebon',
                'jenis' => 'magang',
                'status' => 'dibuka',
            ]);
        }

        $response = $this->actingAs($user)->get('/student/jobs/' . $job->id);

        $response->assertStatus(200);
        $response->assertSee($job->judul);
        $response->assertSee('Analisis Kecocokan Rekomendasi');
        $response->assertSee('Formula KAK 15');
        $response->assertSee('Keahlian (60%)');
    }

    public function test_student_can_apply_to_open_job(): void
    {
        [$user, $student] = $this->getStudentUser();

        // Create a unique test job so application is fresh
        $company = Company::first();
        $testJob = Job::create([
            'company_id' => $company->id,
            'judul' => 'Mobile App Tester PKL ' . time(),
            'slug' => 'mobile-app-tester-pkl-' . time(),
            'deskripsi' => 'Pengujian fungsionalitas aplikasi mobile sekolah.',
            'lokasi' => 'Kota Cirebon',
            'jenis' => 'magang',
            'status' => 'dibuka',
        ]);

        $response = $this->actingAs($user)->post('/student/jobs/' . $testJob->id . '/apply', [
            'pesan' => 'Saya sangat tertarik mengikuti program PKL ini karena sesuai kompetensi saya.',
        ]);

        $response->assertRedirect('/student/jobs/' . $testJob->id);
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('applications', [
            'job_id' => $testJob->id,
            'student_id' => $student->id,
            'status' => 'tertunda',
        ]);

        // Second attempt to apply to the same job should be prevented
        $duplicateResponse = $this->actingAs($user)->post('/student/jobs/' . $testJob->id . '/apply', [
            'pesan' => 'Mencoba melamar kembali.',
        ]);

        $duplicateResponse->assertRedirect('/student/jobs/' . $testJob->id);
        $duplicateResponse->assertSessionHas('info');
    }
}
