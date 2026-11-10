<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManagerDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_dashboard_renders_assignment_and_approval_workflows(): void
    {
        $manager = User::factory()->create(['role' => User::ROLE_MANAGER]);

        $response = $this->actingAs($manager)->get('/manager');

        $response->assertOk();
        $response->assertViewIs('manager.dashboard');
        $response->assertSeeText('Asset assignments');
        $response->assertSeeText('Pending transfers');
        $response->assertSeeText('Approve transfer');
        $response->assertDontSeeText('Admin view');
    }

    public function test_manager_is_forbidden_from_the_admin_dashboard(): void
    {
        $manager = User::factory()->create(['role' => User::ROLE_MANAGER]);

        $response = $this->actingAs($manager)->get(route('admin.dashboard'));

        $response->assertForbidden();
    }
}
