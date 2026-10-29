<?php

namespace Tests\Feature;

use App\Models\Alumni;
use App\Models\Major;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TracerStudyTest extends TestCase
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
                'deskripsi' => 'Konsentrasi keahlian software engineering.',
            ]
        );
    }

    public function test_guest_can_view_alumni_directory(): void
    {
        $response = $this->get(route('alumni.index'));

        $response->assertStatus(200);
        $response->assertSee('Direktori Alumni Vokasi');
        $response->assertSee('Total Alumni Terdata');
    }

    public function test_guest_can_filter_alumni_directory_by_status(): void
    {
        $major = $this->getMajor();
        $user = $this->getUser('alumni_filter_test@demo.com', 'alumni_filter', 'Alumni Filter Tester');

        Alumni::updateOrCreate(
            ['user_id' => $user->id],
            [
                'major_id' => $major->id,
                'tahun_lulus' => 2024,
                'status_bmw' => 'wirausaha',
                'nama_instansi' => 'Studio Koding Filter',
                'posisi_studi' => 'Tech Lead',
                'is_public' => true,
            ]
        );

        $response = $this->get(route('alumni.index', ['status' => 'wirausaha']));

        $response->assertStatus(200);
        $response->assertSee('Studio Koding Filter');
    }

    public function test_guest_can_search_alumni(): void
    {
        $major = $this->getMajor();
        $user = $this->getUser('alumni_search_unique@demo.com', 'unique_alumni_search', 'Alumni Unik Pencarian');

        Alumni::updateOrCreate(
            ['user_id' => $user->id],
            [
                'major_id' => $major->id,
                'tahun_lulus' => 2024,
                'status_bmw' => 'bekerja',
                'nama_instansi' => 'PT Algoritma Unggul Indonesia',
                'posisi_studi' => 'Backend Specialist',
                'is_public' => true,
            ]
        );

        $response = $this->get(route('alumni.index', ['search' => 'Alumni Unik Pencarian']));

        $response->assertStatus(200);
        $response->assertSee('Alumni Unik Pencarian');
        $response->assertSee('PT Algoritma Unggul Indonesia');
    }

    public function test_guest_can_view_public_alumni_detail(): void
    {
        $major = $this->getMajor();
        $user = $this->getUser('alumni_detail_test@demo.com', 'alumni_detail_test', 'Alumni Detail Tester');

        $alumni = Alumni::updateOrCreate(
            ['user_id' => $user->id],
            [
                'major_id' => $major->id,
                'tahun_lulus' => 2023,
                'status_bmw' => 'bekerja',
                'nama_instansi' => 'PT Inovasi Cipta',
                'posisi_studi' => 'Fullstack Developer',
                'keselarasan' => 'sangat_sesuai',
                'testimoni' => 'Pengalaman belajar sangat berharga dan relevan.',
                'is_public' => true,
            ]
        );

        $response = $this->get(route('alumni.show', $alumni->id));

        $response->assertStatus(200);
        $response->assertSee('Alumni Detail Tester');
        $response->assertSee('PT Inovasi Cipta');
        $response->assertSee('Fullstack Developer');
        $response->assertSee('Pengalaman belajar sangat berharga dan relevan.');
    }

    public function test_guest_cannot_view_private_alumni_detail(): void
    {
        $major = $this->getMajor();
        $user = $this->getUser('alumni_private_test@demo.com', 'alumni_private_test', 'Alumni Privat Tester');

        $alumni = Alumni::updateOrCreate(
            ['user_id' => $user->id],
            [
                'major_id' => $major->id,
                'tahun_lulus' => 2024,
                'status_bmw' => 'mencari_kerja',
                'is_public' => false,
            ]
        );

        $response = $this->get(route('alumni.show', $alumni->id));

        $response->assertStatus(404);
    }

    public function test_unauthenticated_user_cannot_access_tracer_questionnaire(): void
    {
        $response = $this->get(route('tracer.create'));

        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_tracer_questionnaire(): void
    {
        $user = $this->getUser('student_tracer_view@demo.com', 'student_tracer_view', 'Siswa Kuesioner View');

        $response = $this->actingAs($user)->get(route('tracer.create'));

        $response->assertStatus(200);
        $response->assertSee('Kuesioner Tracer Study Vokasi');
        $response->assertSee('Status Keterserapan Saat Ini (BMW)');
    }

    public function test_authenticated_user_can_submit_tracer_questionnaire(): void
    {
        $major = $this->getMajor();
        $user = $this->getUser('student_submit_tracer@demo.com', 'student_submit_tracer', 'Siswa Tracer Submit');

        $payload = [
            'major_id' => $major->id,
            'tahun_lulus' => 2024,
            'status_bmw' => 'bekerja',
            'nama_instansi' => 'PT Solusi Vokasi Mandiri',
            'posisi_studi' => 'Web Developer',
            'pendapatan_bulanan' => 'Rp 4.000.000 - Rp 7.000.000',
            'keselarasan' => 'sangat_sesuai',
            'waktu_tunggu_bulan' => 2,
            'testimoni' => 'SMKN 1 Cirebon memberikan fondasi kejuruan terbaik.',
            'is_public' => '1',
        ];

        $response = $this->actingAs($user)->post(route('tracer.store'), $payload);

        $response->assertRedirect(route('tracer.create'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('alumni', [
            'user_id' => $user->id,
            'major_id' => $major->id,
            'status_bmw' => 'bekerja',
            'nama_instansi' => 'PT Solusi Vokasi Mandiri',
            'posisi_studi' => 'Web Developer',
            'keselarasan' => 'sangat_sesuai',
        ]);
    }

    public function test_authenticated_user_can_update_existing_tracer_questionnaire(): void
    {
        $major = $this->getMajor();
        $user = $this->getUser('student_update_tracer@demo.com', 'student_update_tracer', 'Siswa Tracer Update');

        Alumni::updateOrCreate(
            ['user_id' => $user->id],
            [
                'major_id' => $major->id,
                'tahun_lulus' => 2023,
                'status_bmw' => 'mencari_kerja',
                'is_public' => true,
            ]
        );

        $updatePayload = [
            'major_id' => $major->id,
            'tahun_lulus' => 2023,
            'status_bmw' => 'kuliah',
            'nama_instansi' => 'Universitas Indonesia',
            'posisi_studi' => 'Ilmu Komputer',
            'keselarasan' => 'sangat_sesuai',
            'is_public' => '1',
        ];

        $response = $this->actingAs($user)->post(route('tracer.store'), $updatePayload);

        $response->assertRedirect(route('tracer.create'));
        $this->assertDatabaseHas('alumni', [
            'user_id' => $user->id,
            'status_bmw' => 'kuliah',
            'nama_instansi' => 'Universitas Indonesia',
            'posisi_studi' => 'Ilmu Komputer',
        ]);
    }

    public function test_tracer_questionnaire_validates_required_fields(): void
    {
        $user = $this->getUser('student_val_tracer@demo.com', 'student_val_tracer', 'Siswa Val Tracer');

        $response = $this->actingAs($user)->post(route('tracer.store'), []);

        $response->assertSessionHasErrors(['major_id', 'tahun_lulus', 'status_bmw']);
    }

    public function test_non_admin_cannot_access_admin_tracer_dashboard(): void
    {
        $user = $this->getUser('student_no_admin@demo.com', 'student_no_admin', 'Siswa No Admin');

        $response = $this->actingAs($user)->get(route('admin.tracer.index'));

        // role middleware will redirect or abort
        $this->assertTrue(in_array($response->status(), [302, 403]));
    }

    public function test_admin_can_view_tracer_dashboard_with_kpi_metrics(): void
    {
        $admin = $this->getUser('admin_tracer_test@demo.com', 'admin_tracer_test', 'Admin Tracer Tester', 'admin');

        $response = $this->actingAs($admin)->get(route('admin.tracer.index'));

        $response->assertStatus(200);
        $response->assertSee('Tracer Study & Keterserapan Alumni', false);
        $response->assertSee('Total Alumni Terdata');
        $response->assertSee('Keterserapan BMW');
    }

    public function test_admin_can_toggle_alumni_visibility(): void
    {
        $admin = $this->getUser('admin_toggle_vis@demo.com', 'admin_toggle_vis', 'Admin Toggle Vis', 'admin');
        $major = $this->getMajor();
        $alumniUser = $this->getUser('alumni_toggle_target@demo.com', 'alumni_toggle_target', 'Alumni Target Toggle');

        $alumni = Alumni::updateOrCreate(
            ['user_id' => $alumniUser->id],
            [
                'major_id' => $major->id,
                'tahun_lulus' => 2024,
                'status_bmw' => 'bekerja',
                'is_public' => true,
            ]
        );

        $response = $this->actingAs($admin)->patch(route('admin.tracer.toggle-visibility', $alumni->id));

        $response->assertRedirect();
        $this->assertDatabaseHas('alumni', [
            'id' => $alumni->id,
            'is_public' => false,
        ]);
    }

    public function test_admin_can_delete_alumni_record(): void
    {
        $admin = $this->getUser('admin_del_alumni@demo.com', 'admin_del_alumni', 'Admin Delete Alumni', 'admin');
        $major = $this->getMajor();
        $alumniUser = $this->getUser('alumni_del_target@demo.com', 'alumni_del_target', 'Alumni Target Delete');

        $alumni = Alumni::updateOrCreate(
            ['user_id' => $alumniUser->id],
            [
                'major_id' => $major->id,
                'tahun_lulus' => 2024,
                'status_bmw' => 'mencari_kerja',
                'is_public' => true,
            ]
        );

        $response = $this->actingAs($admin)->delete(route('admin.tracer.destroy', $alumni->id));

        $response->assertRedirect();
        $this->assertDatabaseMissing('alumni', [
            'id' => $alumni->id,
        ]);
    }
}
