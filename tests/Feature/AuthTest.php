<?php

namespace Tests\Feature;

use App\Models\Major;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    public function test_login_page_renders_successfully(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Selamat Datang', false);
        $response->assertSee('Masuk Sekarang', false);
    }

    public function test_register_page_renders_successfully(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertSee('Pendaftaran Akun', false);
        $response->assertSee('Aktivasi Siswa (NISN)', false);
        $response->assertSee('Mitra Perusahaan', false);
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::where('email', 'siswa@demo.com')->first();
        if (!$user) {
            $role = Role::firstOrCreate(['nama' => 'siswa'], ['label' => 'Siswa']);
            $user = User::create([
                'role_id' => $role->id,
                'name' => 'Rifqi Pratama',
                'username' => 'rifqipratama',
                'email' => 'siswa@demo.com',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]);
        }

        $response = $this->post('/login', [
            'login' => 'siswa@demo.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('student.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        $response = $this->post('/login', [
            'login' => 'nonexistent@demo.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors('login');
        $this->assertGuest();
    }

    public function test_student_can_register_via_nisn(): void
    {
        $major = Major::first() ?? Major::create(['kode' => 'PPLG', 'nama' => 'Pengembangan Perangkat Lunak']);
        $class = SchoolClass::first() ?? SchoolClass::create([
            'major_id' => $major->id,
            'nama' => 'X PPLG 1',
            'tingkat' => 10,
            'tahun_ajaran' => '2025/2026',
        ]);

        $randomNisn = '99' . str_pad((string) mt_rand(10000000, 99999999), 8, '0', STR_PAD_LEFT);
        $randomUsername = 'siswa_' . substr(md5(uniqid()), 0, 8);
        $email = $randomUsername . '@smkn1.sch.id';

        $response = $this->post('/register', [
            'role_type' => 'siswa',
            'nisn' => $randomNisn,
            'name' => 'Siswa Baru Vokasi',
            'major_id' => $major->id,
            'class_id' => $class->id,
            'email' => $email,
            'username' => $randomUsername,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('student.dashboard'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('students', ['nisn' => $randomNisn]);

        // Cleanup test user after verification
        User::where('email', $email)->delete();
    }

    public function test_teacher_can_register(): void
    {
        $randomNip = '1988' . str_pad((string) mt_rand(10000000, 99999999), 12, '0', STR_PAD_LEFT);
        $randomUsername = 'guru_' . substr(md5(uniqid()), 0, 8);
        $email = $randomUsername . '@smkn1.sch.id';

        $response = $this->post('/register', [
            'role_type' => 'guru',
            'nip' => $randomNip,
            'name' => 'Guru Penguji Vokasi, S.T.',
            'mata_pelajaran' => 'Pemrograman Web',
            'email' => $email,
            'username' => $randomUsername,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('teacher.dashboard'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('teachers', ['nip' => $randomNip]);

        // Cleanup test user after verification
        User::where('email', $email)->delete();
    }

    public function test_login_validation_rules_via_form_request(): void
    {
        $response = $this->from('/login')->post('/login', [
            'login' => '',
            'password' => '',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors([
            'login' => 'Email atau username wajib diisi.',
            'password' => 'Kata sandi wajib diisi.',
        ]);
    }

    public function test_student_registration_validation_via_form_request(): void
    {
        $response = $this->from('/register')->post('/register', [
            'role_type' => 'siswa',
            'nisn' => '123',
            'name' => '',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors(['nisn', 'name', 'email', 'username', 'major_id', 'class_id', 'password']);
    }

    public function test_user_can_logout(): void
    {
        $user = User::first();
        if ($user) {
            $response = $this->actingAs($user)->post('/logout');
            $response->assertRedirect(route('landing'));
            $this->assertGuest();
        }
    }
}
