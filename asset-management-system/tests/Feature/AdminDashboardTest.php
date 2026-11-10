<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_the_admin_dashboard(): void
    {
        $admin = $this->createUserWithRole(User::ROLE_ADMIN);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertViewIs('admin.dashboard');
        $response->assertDontSeeText('Manager view');
    }

    public function test_admin_is_forbidden_from_the_manager_dashboard(): void
    {
        $admin = $this->createUserWithRole(User::ROLE_ADMIN);

        $response = $this->actingAs($admin)->get(route('manager.dashboard'));

        $response->assertForbidden();
    }

    private function createUserWithRole(string $slug): User
    {
        $role = Role::query()->create([
            'name' => ucfirst($slug),
            'slug' => $slug,
        ]);

        return User::factory()->create(['role_id' => $role->id]);
    }
}
