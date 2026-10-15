<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Major;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminGlobalSearchTest extends TestCase
{
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

    public function test_guest_cannot_access_global_search(): void
    {
        $response = $this->getJson('/admin/search?q=pplg');
        $response->assertStatus(401);
    }

    public function test_search_returns_empty_when_query_is_less_than_two_characters(): void
    {
        $admin = $this->getAdminUser();

        $response = $this->actingAs($admin)->getJson('/admin/search?q=a');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'groups' => [],
            ]);
    }

    public function test_search_returns_navigation_pages_matching_query(): void
    {
        $admin = $this->getAdminUser();

        $response = $this->actingAs($admin)->getJson('/admin/search?q=guru');

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $data = $response->json();
        $this->assertIsArray($data['groups']);
        
        $categories = collect($data['groups'])->pluck('category')->all();
        $this->assertContains('Menu & Navigasi', $categories);
    }

    public function test_search_returns_matching_majors(): void
    {
        $admin = $this->getAdminUser();

        $response = $this->actingAs($admin)->getJson('/admin/search?q=Perangkat Lunak');

        $response->assertStatus(200);
        $data = $response->json();

        $allTitles = collect($data['groups'])->flatMap(fn($g) => collect($g['items'])->pluck('title'))->all();
        $this->assertTrue(
            collect($allTitles)->contains(fn($title) => str_contains($title, 'Pengembangan Perangkat Lunak'))
        );
    }
}
