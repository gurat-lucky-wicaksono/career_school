<?php

namespace Tests\Feature;

use App\Models\Agenda;
use App\Models\Announcement;
use App\Models\Major;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class BkkInformationCenterTest extends TestCase
{
    private function getAdminUser(): User
    {
        $admin = User::where('email', 'admin@demo.com')->first();
        if (!$admin) {
            $adminRole = Role::firstOrCreate(['nama' => 'admin'], ['label' => 'Administrator']);
            $admin = User::create([
                'role_id' => $adminRole->id,
                'name' => 'Admin BKK Test',
                'username' => 'admin_bkk_test',
                'email' => 'admin@demo.com',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]);
        }
        return $admin;
    }

    private function getStudentUser(): User
    {
        $user = User::where('email', 'siswa@demo.com')->first();
        if (!$user) {
            $role = Role::firstOrCreate(['nama' => 'siswa'], ['label' => 'Siswa']);
            $user = User::create([
                'role_id' => $role->id,
                'name' => 'Siswa Test',
                'username' => 'siswa_test',
                'email' => 'siswa@demo.com',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]);

            $major = Major::firstOrCreate(['kode' => 'PPLG'], ['nama' => 'Pengembangan Perangkat Lunak & Gim']);
            $class = SchoolClass::firstOrCreate(['nama' => 'XI PPLG 1'], ['major_id' => $major->id, 'tingkat' => 11]);

            Student::create([
                'user_id' => $user->id,
                'nisn' => '0061234567',
                'class_id' => $class->id,
                'major_id' => $major->id,
                'angkatan' => 2024,
                'status' => 'aktif',
            ]);
        }
        return $user;
    }

    private function getTeacherUser(): User
    {
        $user = User::where('email', 'guru@demo.com')->first();
        if (!$user) {
            $role = Role::firstOrCreate(['nama' => 'guru'], ['label' => 'Guru']);
            $user = User::create([
                'role_id' => $role->id,
                'name' => 'Bambang Sudarsono, S.Kom.',
                'username' => 'bambang_guru_test',
                'email' => 'guru@demo.com',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]);

            Teacher::create([
                'user_id' => $user->id,
                'nip' => '198503152010011002',
                'mata_pelajaran' => 'Pemrograman Web & PPLG',
                'is_wali_kelas' => true,
            ]);
        }
        return $user;
    }

    public function test_admin_can_view_announcements_index(): void
    {
        $admin = $this->getAdminUser();

        $response = $this->actingAs($admin)->get(route('admin.announcements.index'));

        $response->assertStatus(200);
        $response->assertSee('Pengumuman BKK SMKN 1 Cirebon');
        $response->assertSee('Tulis Pengumuman Baru');
    }

    public function test_admin_can_create_announcement(): void
    {
        $admin = $this->getAdminUser();

        $response = $this->actingAs($admin)->post(route('admin.announcements.store'), [
            'judul' => 'Rekrutmen Bersama PT Telkom Akses 2026',
            'isi' => 'Pendaftaran rekrutmen teknisi jaringan dan programmer junior untuk lulusan PPLG.',
            'prioritas' => 'penting',
            'status' => 'terbit',
            'mulai_tampil' => now()->format('Y-m-d\TH:i'),
        ]);

        $response->assertRedirect(route('admin.announcements.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('announcements', [
            'judul' => 'Rekrutmen Bersama PT Telkom Akses 2026',
            'prioritas' => 'penting',
            'status' => 'terbit',
        ]);
    }

    public function test_admin_can_update_announcement(): void
    {
        $admin = $this->getAdminUser();

        $announcement = Announcement::create([
            'user_id' => $admin->id,
            'judul' => 'Judul Sebelum Update',
            'isi' => 'Konten lama',
            'prioritas' => 'normal',
            'status' => 'draft',
        ]);

        $response = $this->actingAs($admin)->put(route('admin.announcements.update', $announcement), [
            'judul' => 'Judul Setelah Diperbarui',
            'isi' => 'Konten baru yang telah diperbarui',
            'prioritas' => 'penting',
            'status' => 'terbit',
        ]);

        $response->assertRedirect(route('admin.announcements.index'));
        $response->assertSessionHas('success');

        $announcement->refresh();
        $this->assertEquals('Judul Setelah Diperbarui', $announcement->judul);
        $this->assertEquals('penting', $announcement->prioritas);
        $this->assertEquals('terbit', $announcement->status);
    }

    public function test_admin_can_delete_announcement(): void
    {
        $admin = $this->getAdminUser();

        $announcement = Announcement::create([
            'user_id' => $admin->id,
            'judul' => 'Pengumuman Dihapus',
            'isi' => 'Akan segera dihapus',
            'prioritas' => 'rendah',
            'status' => 'arsip',
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.announcements.destroy', $announcement));

        $response->assertRedirect(route('admin.announcements.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('announcements', [
            'id' => $announcement->id,
        ]);
    }

    public function test_admin_can_view_agendas_index(): void
    {
        $admin = $this->getAdminUser();

        $response = $this->actingAs($admin)->get(route('admin.agendas.index'));

        $response->assertStatus(200);
        $response->assertSee('Agenda Kegiatan BKK SMKN 1 Cirebon');
        $response->assertSee('Tambah Agenda Kegiatan');
    }

    public function test_admin_can_create_agenda(): void
    {
        $admin = $this->getAdminUser();

        $response = $this->actingAs($admin)->post(route('admin.agendas.store'), [
            'judul' => 'Pembekalan PKL Gelombang 2',
            'deskripsi' => 'Pengarahan etika profesi dan penyusunan laporan harian jurnal PKL.',
            'tanggal_mulai' => now()->addDays(5)->format('Y-m-d'),
            'lokasi' => 'Aula Gedung B SMKN 1 Cirebon',
            'jenis' => 'akademik',
        ]);

        $response->assertRedirect(route('admin.agendas.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('agendas', [
            'judul' => 'Pembekalan PKL Gelombang 2',
            'lokasi' => 'Aula Gedung B SMKN 1 Cirebon',
            'jenis' => 'akademik',
        ]);
    }

    public function test_admin_can_update_agenda(): void
    {
        $admin = $this->getAdminUser();

        $agenda = Agenda::create([
            'user_id' => $admin->id,
            'judul' => 'Agenda Awal',
            'tanggal_mulai' => now()->format('Y-m-d'),
            'jenis' => 'umum',
        ]);

        $response = $this->actingAs($admin)->put(route('admin.agendas.update', $agenda), [
            'judul' => 'Agenda Diperbarui',
            'tanggal_mulai' => now()->addDay()->format('Y-m-d'),
            'jenis' => 'ujian',
            'lokasi' => 'Lab Komputer PPLG',
        ]);

        $response->assertRedirect(route('admin.agendas.index'));
        $response->assertSessionHas('success');

        $agenda->refresh();
        $this->assertEquals('Agenda Diperbarui', $agenda->judul);
        $this->assertEquals('ujian', $agenda->jenis);
    }

    public function test_admin_can_delete_agenda(): void
    {
        $admin = $this->getAdminUser();

        $agenda = Agenda::create([
            'user_id' => $admin->id,
            'judul' => 'Agenda Hapus',
            'tanggal_mulai' => now()->format('Y-m-d'),
            'jenis' => 'libur',
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.agendas.destroy', $agenda));

        $response->assertRedirect(route('admin.agendas.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('agendas', [
            'id' => $agenda->id,
        ]);
    }

    public function test_public_can_view_published_announcements_and_filtered(): void
    {
        $admin = $this->getAdminUser();

        Announcement::create([
            'user_id' => $admin->id,
            'judul' => 'Pengumuman Publik Uji Coba',
            'isi' => 'Konten pengumuman yang dapat dilihat oleh publik',
            'prioritas' => 'normal',
            'status' => 'terbit',
            'mulai_tampil' => now(),
        ]);

        Announcement::create([
            'user_id' => $admin->id,
            'judul' => 'Pengumuman Draft Rahasia',
            'isi' => 'Konten draft tidak boleh tampil di publik',
            'prioritas' => 'rendah',
            'status' => 'draft',
        ]);

        $response = $this->get(route('public.announcements'));

        $response->assertStatus(200);
        $response->assertSee('Pengumuman Bursa Kerja Khusus');
        $response->assertSee('Pengumuman Publik Uji Coba');
        $response->assertDontSee('Pengumuman Draft Rahasia');
    }

    public function test_public_can_view_published_announcement_detail(): void
    {
        $admin = $this->getAdminUser();

        $announcement = Announcement::create([
            'user_id' => $admin->id,
            'judul' => 'Pengumuman Detail Terbuka',
            'isi' => 'Informasi lengkap terkait rekrutmen mitra DUDI.',
            'prioritas' => 'penting',
            'status' => 'terbit',
        ]);

        $response = $this->get(route('public.announcements.show', $announcement));

        $response->assertStatus(200);
        $response->assertSee('Pengumuman Detail Terbuka');
        $response->assertSee('Informasi lengkap terkait rekrutmen mitra DUDI.');
    }

    public function test_public_cannot_view_draft_announcement_detail(): void
    {
        $admin = $this->getAdminUser();

        $draft = Announcement::create([
            'user_id' => $admin->id,
            'judul' => 'Pengumuman Tertutup',
            'isi' => 'Masih draft',
            'status' => 'draft',
        ]);

        $response = $this->get(route('public.announcements.show', $draft));

        $response->assertStatus(404);
    }

    public function test_public_can_view_agendas_list(): void
    {
        $admin = $this->getAdminUser();

        Agenda::create([
            'user_id' => $admin->id,
            'judul' => 'Job Fair Vokasi SMKN 1 Cirebon 2026',
            'deskripsi' => 'Pameran bursa kerja menghadirkan 20+ mitra industri.',
            'tanggal_mulai' => now()->addWeeks(2)->format('Y-m-d'),
            'lokasi' => 'Lapangan Utama SMKN 1 Cirebon',
            'jenis' => 'umum',
        ]);

        $response = $this->get(route('public.agendas'));

        $response->assertStatus(200);
        $response->assertSee('Kalender Kegiatan BKK & Industri', false);
        $response->assertSee('Job Fair Vokasi SMKN 1 Cirebon 2026');
    }

    public function test_student_dashboard_displays_bkk_information_widget(): void
    {
        $student = $this->getStudentUser();
        $admin = $this->getAdminUser();

        Announcement::create([
            'user_id' => $admin->id,
            'judul' => 'Pengumuman Khusus Siswa',
            'isi' => 'Pemberitahuan resmi BKK untuk seluruh siswa.',
            'status' => 'terbit',
        ]);

        $response = $this->actingAs($student)->get(route('student.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Pengumuman BKK Terkini');
        $response->assertSee('Agenda Kegiatan Vokasi');
    }

    public function test_teacher_dashboard_displays_bkk_information_widget(): void
    {
        $teacher = $this->getTeacherUser();
        $admin = $this->getAdminUser();

        Announcement::create([
            'user_id' => $admin->id,
            'judul' => 'Pengumuman Khusus Guru Pembimbing',
            'isi' => 'Surat edaran BKK untuk pembimbing PKL.',
            'status' => 'terbit',
        ]);

        $response = $this->actingAs($teacher)->get(route('teacher.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Pengumuman BKK Sekolah');
        $response->assertSee('Agenda Vokasi & PKL Terdekat', false);
    }
}
