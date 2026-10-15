<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    public function test_guest_cannot_access_admin_dashboard(): void
    {
        $response = $this->get('/admin/dashboard');

        $response->assertRedirect('/login');
    }

    private function getAdminUser(): User
    {
        $user = User::where('email', 'admin@demo.com')->first();
        if (!$user) {
            $role = Role::firstOrCreate(['nama' => 'admin'], ['label' => 'Administrator']);
            $user = User::create([
                'role_id' => $role->id,
                'name' => 'Administrator BKK SMKN 1',
                'username' => 'admin_bkk',
                'email' => 'admin@demo.com',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]);
        }
        return $user;
    }

    public function test_admin_can_access_dashboard_and_see_components(): void
    {
        $admin = $this->getAdminUser();

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Career School', false);
        $response->assertSee('Total Siswa Terdaftar', false);
        $response->assertSee('Riwayat Aktivitas & Pendaftaran Terkini', false);
        $response->assertSee('Tren Keterserapan Lulusan', false);
    }

    public function test_admin_login_redirects_directly_to_admin_dashboard(): void
    {
        $admin = $this->getAdminUser();

        $response = $this->post('/login', [
            'login' => 'admin@demo.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
    }

    public function test_admin_can_access_all_sidebar_navigation_routes(): void
    {
        $admin = $this->getAdminUser();

        $routes = [
            '/admin/teachers',
            '/admin/students',
            '/admin/majors',
            '/admin/classes',
            '/admin/skills',
            '/admin/companies',
            '/admin/companies/verification',
            '/admin/jobs',
            '/admin/settings',
        ];

        foreach ($routes as $route) {
            $response = $this->actingAs($admin)->get($route);
            $response->assertStatus(200);
            $response->assertSee('Career School', false);
            $response->assertSee('Keluar Akun', false);
        }
    }
}

