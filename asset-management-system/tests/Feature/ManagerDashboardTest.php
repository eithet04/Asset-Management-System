<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManagerDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_landing_page_opens_the_live_asset_assignment_workflow(): void
    {
        $manager = $this->createUserWithRole(User::ROLE_MANAGER);

        $response = $this->actingAs($manager)->get(route('manager.dashboard'));

        $response->assertOk();
        $response->assertViewIs('operations.index');
        $response->assertSeeText('Asset assignments');
        $response->assertSeeText('Assign an available asset');
        $response->assertSeeText('Request approvals');
        $response->assertDontSeeText('Admin view');
        $response->assertDontSeeText('Need a hand?');
    }

    public function test_manager_is_forbidden_from_the_admin_dashboard(): void
    {
        $manager = $this->createUserWithRole(User::ROLE_MANAGER);

        $response = $this->actingAs($manager)->get(route('admin.dashboard'));

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
