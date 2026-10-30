<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Company;
use App\Models\Job;
use App\Models\Major;
use App\Models\Notification;
use App\Models\Post;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\Skill;
use App\Models\Student;
use App\Models\StudentSkill;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UnifiedNotificationTest extends TestCase
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

    public function test_guest_cannot_access_notifications_page(): void
    {
        $response = $this->get(route('notifications.index'));
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_notifications_page(): void
    {
        $user = $this->getUser('notif_view_user@demo.com', 'notif_view_user', 'Notif View Tester');

        Notification::create([
            'user_id' => $user->id,
            'jenis' => 'komunitas',
            'judul' => 'Siswa Lain menyukai kiriman Anda.',
            'dibaca' => false,
        ]);

        $response = $this->actingAs($user)->get(route('notifications.index'));

        $response->assertStatus(200);
        $response->assertSee('Pusat Notifikasi Akun', false);
        $response->assertSee('Siswa Lain menyukai kiriman Anda.', false);
    }

    public function test_user_can_fetch_notifications_via_json(): void
    {
        $user = $this->getUser('notif_json_user@demo.com', 'notif_json_user', 'Notif Json Tester');

        Notification::create([
            'user_id' => $user->id,
            'jenis' => 'lamaran',
            'judul' => 'Lamaran Anda telah diterima perusahaan.',
            'dibaca' => false,
        ]);

        $response = $this->actingAs($user)->getJson(route('notifications.index'));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
        $this->assertGreaterThanOrEqual(1, $response->json('unread_count'));
    }

    public function test_user_can_mark_single_notification_as_read(): void
    {
        $user = $this->getUser('notif_read_user@demo.com', 'notif_read_user', 'Notif Read Tester');

        $notif = Notification::create([
            'user_id' => $user->id,
            'jenis' => 'keahlian',
            'judul' => 'Keahlian Anda telah divalidasi guru.',
            'dibaca' => false,
        ]);

        $response = $this->actingAs($user)->postJson(route('notifications.read', $notif->id));

        $response->assertStatus(200);
        $this->assertDatabaseHas('notifications', [
            'id' => $notif->id,
            'dibaca' => true,
        ]);
    }

    public function test_user_cannot_mark_other_users_notification_as_read(): void
    {
        $userA = $this->getUser('notif_owner_a@demo.com', 'notif_owner_a', 'Owner A');
        $userB = $this->getUser('notif_owner_b@demo.com', 'notif_owner_b', 'Owner B');

        $notifA = Notification::create([
            'user_id' => $userA->id,
            'jenis' => 'keahlian',
            'judul' => 'Pemberitahuan milik user A',
            'dibaca' => false,
        ]);

        // User B attempts to mark User A's notification as read
        $this->actingAs($userB)->postJson(route('notifications.read', $notifA->id));

        // It should still be unread
        $this->assertDatabaseHas('notifications', [
            'id' => $notifA->id,
            'dibaca' => false,
        ]);
    }

    public function test_user_can_mark_all_notifications_as_read(): void
    {
        $user = $this->getUser('notif_all_read@demo.com', 'notif_all_read', 'Notif All Read Tester');

        Notification::create([
            'user_id' => $user->id,
            'jenis' => 'lamaran',
            'judul' => 'Notif 1',
            'dibaca' => false,
        ]);
        Notification::create([
            'user_id' => $user->id,
            'jenis' => 'komunitas',
            'judul' => 'Notif 2',
            'dibaca' => false,
        ]);

        $response = $this->actingAs($user)->post(route('notifications.markAllRead'));

        $response->assertRedirect();
        $this->assertEquals(0, Notification::where('user_id', $user->id)->where('dibaca', false)->count());
    }

    public function test_submitting_application_triggers_notification_to_company(): void
    {
        $companyUser = $this->getUser('notif_comp_user@demo.com', 'notif_comp_user', 'PT Notif Tester', 'perusahaan');
        $company = Company::firstOrCreate(
            ['user_id' => $companyUser->id],
            [
                'nama' => 'PT Notifikasi Industri',
                'bidang_usaha' => 'Software House',
                'status_verifikasi' => 'terverifikasi',
                'is_active' => true,
            ]
        );

        $job = Job::firstOrCreate(
            ['company_id' => $company->id, 'judul' => 'Backend Developer Magang'],
            [
                'slug' => 'backend-developer-magang-' . uniqid(),
                'posisi' => 'Backend Developer Magang',
                'jenis' => 'magang',
                'status' => 'dibuka',
                'lokasi' => 'Cirebon',
                'deskripsi' => 'Lowongan magang PKL kejuruan',
            ]
        );

        $major = Major::firstOrCreate(['kode' => 'PPLG'], ['nama' => 'PPLG']);
        $schoolClass = SchoolClass::firstOrCreate(['nama' => 'XI PPLG 1'], ['major_id' => $major->id, 'tingkat' => 11, 'tahun_ajaran' => '2024/2025']);

        $studentUser = $this->getUser('notif_stud_apply@demo.com', 'notif_stud_apply', 'Siswa Melamar Notif', 'siswa');
        $student = Student::firstOrCreate(
            ['user_id' => $studentUser->id],
            [
                'nisn' => (string) rand(1000000000, 9999999999),
                'major_id' => $major->id,
                'class_id' => $schoolClass->id,
                'angkatan' => 2024,
            ]
        );

        // Student applies to job
        $this->actingAs($studentUser)->post(route('student.jobs.apply', $job->id), [
            'pesan' => 'Saya tertarik dengan posisi ini.',
        ]);

        // Assert notification created for company user
        $this->assertDatabaseHas('notifications', [
            'user_id' => $companyUser->id,
            'jenis' => 'lamaran',
        ]);
    }

    public function test_updating_application_status_triggers_notification_to_student(): void
    {
        $companyUser = $this->getUser('notif_comp_update@demo.com', 'notif_comp_update', 'PT Status Update', 'perusahaan');
        $company = Company::firstOrCreate(
            ['user_id' => $companyUser->id],
            [
                'nama' => 'PT Status Tester',
                'bidang_usaha' => 'Teknologi',
                'status_verifikasi' => 'terverifikasi',
                'is_active' => true,
            ]
        );

        $job = Job::firstOrCreate(
            ['company_id' => $company->id, 'judul' => 'Mobile Dev PKL'],
            [
                'slug' => 'mobile-dev-pkl-' . uniqid(),
                'posisi' => 'Mobile Dev PKL',
                'jenis' => 'magang',
                'status' => 'dibuka',
                'lokasi' => 'Cirebon',
                'deskripsi' => 'Lowongan mobile dev',
            ]
        );

        $major = Major::firstOrCreate(['kode' => 'PPLG'], ['nama' => 'PPLG']);
        $schoolClass = SchoolClass::firstOrCreate(['nama' => 'XI PPLG 2'], ['major_id' => $major->id, 'tingkat' => 11, 'tahun_ajaran' => '2024/2025']);

        $studentUser = $this->getUser('notif_stud_target@demo.com', 'notif_stud_target', 'Siswa Target Notif', 'siswa');
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
            ['status' => 'tertunda', 'skor_kecocokan' => 85]
        );

        // Company updates status to 'wawancara'
        $this->actingAs($companyUser)->patch(route('company.applications.update-status', $application->id), [
            'status' => 'wawancara',
            'catatan_hrd' => 'Jadwal wawancara Senin pukul 09.00',
        ]);

        // Assert notification created for student user
        $this->assertDatabaseHas('notifications', [
            'user_id' => $studentUser->id,
            'jenis' => 'lamaran',
        ]);
    }

    public function test_teacher_endorsing_skill_triggers_notification_to_student(): void
    {
        $teacherUser = $this->getUser('notif_teacher_user@demo.com', 'notif_teacher_user', 'Guru Notif Tester', 'guru');
        $teacher = Teacher::firstOrCreate(
            ['user_id' => $teacherUser->id],
            ['nip' => '1987' . rand(10000000, 99999999), 'mata_pelajaran' => 'Pemrograman Web']
        );

        $major = Major::firstOrCreate(['kode' => 'PPLG'], ['nama' => 'PPLG']);
        $schoolClass = SchoolClass::firstOrCreate(['nama' => 'XI PPLG 3'], ['major_id' => $major->id, 'tingkat' => 11, 'tahun_ajaran' => '2024/2025']);

        $studentUser = $this->getUser('notif_stud_skill@demo.com', 'notif_stud_skill', 'Siswa Skill Target', 'siswa');
        $student = Student::firstOrCreate(
            ['user_id' => $studentUser->id],
            [
                'nisn' => (string) rand(1000000000, 9999999999),
                'major_id' => $major->id,
                'class_id' => $schoolClass->id,
                'angkatan' => 2024,
            ]
        );

        $skill = Skill::firstOrCreate(['nama' => 'PHP Laravel'], ['kategori' => 'Backend']);

        $studentSkill = StudentSkill::updateOrCreate(
            ['student_id' => $student->id, 'skill_id' => $skill->id],
            ['tingkat' => 3, 'tervalidasi' => false]
        );

        // Teacher validates skill
        $this->actingAs($teacherUser)->post(route('teacher.skills.validate', $studentSkill->id), [
            'tingkat' => 4,
            'catatan' => 'Kompetensi teruji sangat baik.',
        ]);

        // Assert notification created for student user
        $this->assertDatabaseHas('notifications', [
            'user_id' => $studentUser->id,
            'jenis' => 'keahlian',
        ]);
    }

    public function test_liking_and_commenting_triggers_notification_to_post_author(): void
    {
        $author = $this->getUser('author_post_notif@demo.com', 'author_post_notif', 'Author Post Notif');
        $actor = $this->getUser('actor_post_notif@demo.com', 'actor_post_notif', 'Actor Post Notif');

        $post = Post::create([
            'user_id' => $author->id,
            'isi' => 'Kiriman uji interaksi sosial dan notifikasi.',
            'jenis' => 'post',
        ]);

        // Actor likes the post
        $this->actingAs($actor)->post(route('posts.like', $post->id));

        $this->assertDatabaseHas('notifications', [
            'user_id' => $author->id,
            'jenis' => 'komunitas',
        ]);

        // Actor comments on the post
        $this->actingAs($actor)->post(route('posts.comments.store', $post->id), [
            'isi' => 'Komentar inspiratif dari rekan vokasi!',
        ]);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $author->id,
            'jenis' => 'komunitas',
        ]);

        // Actor follows author
        $this->actingAs($actor)->post(route('users.follow', $author->id));

        $this->assertDatabaseHas('notifications', [
            'user_id' => $author->id,
            'jenis' => 'komunitas',
        ]);
    }
}
