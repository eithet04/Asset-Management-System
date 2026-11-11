<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class EmployeeDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_sees_their_self_service_dashboard(): void
    {
        $employee = $this->createUserWithRole(User::ROLE_EMPLOYEE);

        $response = $this->actingAs($employee)->get(route('employee.dashboard'));

        $response->assertOk();
        $response->assertViewIs('employee.dashboard');
        $response->assertSeeText('My assigned assets');
        $response->assertSeeText('Report an issue');
        $response->assertDontSeeText('Admin view');
        $response->assertDontSeeText('Manager view');
        $response->assertDontSeeText('Need a hand?');
    }

    public static function privilegedDashboardRoutes(): array
    {
        return [
            'admin dashboard' => ['admin.dashboard'],
            'manager dashboard' => ['manager.dashboard'],
        ];
    }

    #[DataProvider('privilegedDashboardRoutes')]
    public function test_employee_is_forbidden_from_privileged_dashboards(string $routeName): void
    {
        $employee = $this->createUserWithRole(User::ROLE_EMPLOYEE);

        $response = $this->actingAs($employee)->get(route($routeName));

        $response->assertForbidden();
    }

    public function test_guest_is_redirected_to_sign_in_from_employee_dashboard(): void
    {
        $response = $this->get(route('employee.dashboard'));

        $response->assertRedirect(route('login'));
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
