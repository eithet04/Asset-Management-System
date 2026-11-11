<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_sees_the_login_form_at_the_root_url(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertViewIs('auth.login');
    }

    public function test_registration_always_creates_an_employee_account(): void
    {
        $adminRole = $this->createRole(User::ROLE_ADMIN);

        $response = $this->post(route('register.store'), [
            'name' => 'Taylor Employee',
            'email' => 'taylor@example.com',
            'password' => 'secure-password',
            'password_confirmation' => 'secure-password',
            'role_id' => $adminRole->id,
        ]);

        $employeeRole = Role::query()->where('slug', User::ROLE_EMPLOYEE)->firstOrFail();
        $response->assertRedirect(route('employee.dashboard'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'taylor@example.com',
            'role_id' => $employeeRole->id,
            'is_active' => true,
        ]);
        $this->get(route('employee.dashboard'))->assertOk();
    }

    public function test_user_sign_in_redirects_to_their_role_dashboard(): void
    {
        $manager = $this->createUserWithRole(User::ROLE_MANAGER, [
            'email' => 'manager@example.com',
            'password' => 'secure-password',
        ]);

        $response = $this->post(route('login.store'), [
            'email' => 'manager@example.com',
            'password' => 'secure-password',
        ]);

        $response->assertRedirect(route('manager.dashboard'));
        $this->assertAuthenticatedAs($manager);
    }

    public function test_new_roles_redirect_to_their_role_specific_workspace(): void
    {
        foreach ([
            User::ROLE_ASSET_CONTROLLER => 'manager.dashboard',
            User::ROLE_HR_ADMIN_STAFF => 'operations.people',
            User::ROLE_IT_TECHNICIAN => 'operations.maintenance',
            User::ROLE_DEPARTMENT_MANAGER => 'manager.dashboard',
            User::ROLE_MANAGEMENT_VIEWER => 'operations.reports',
        ] as $role => $destination) {
            $user = $this->createUserWithRole($role, [
                'email' => $role.'@example.com',
                'password' => 'secure-password',
            ]);

            $this->post(route('login.store'), [
                'email' => $user->email,
                'password' => 'secure-password',
            ])->assertRedirect(route($destination));
            $this->post(route('logout'));
        }
    }

    public function test_role_command_creates_an_employee_profile_for_new_staff_roles(): void
    {
        $user = User::factory()->create(['email' => 'technician@example.com']);

        $this->artisan('users:assign-role', [
            'email' => $user->email,
            'role' => User::ROLE_IT_TECHNICIAN,
        ])->assertExitCode(0);

        $this->assertDatabaseHas('employees', ['user_id' => $user->id]);
    }

    public function test_inactive_accounts_cannot_sign_in(): void
    {
        $this->createUserWithRole(User::ROLE_EMPLOYEE, [
            'email' => 'inactive@example.com',
            'password' => 'secure-password',
            'is_active' => false,
        ]);

        $response = $this->from(route('login'))->post(route('login.store'), [
            'email' => 'inactive@example.com',
            'password' => 'secure-password',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors([
            'email' => 'This account is not permitted to sign in.',
        ]);
        $this->assertGuest();
    }

    public function test_invalid_credentials_do_not_authenticate_a_user(): void
    {
        $this->createUserWithRole(User::ROLE_EMPLOYEE, [
            'email' => 'employee@example.com',
            'password' => 'secure-password',
        ]);

        $response = $this->from(route('login'))->post(route('login.store'), [
            'email' => 'employee@example.com',
            'password' => 'incorrect-password',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors([
            'email' => 'These credentials do not match our records.',
        ]);
        $this->assertGuest();
    }

    public function test_logout_ends_the_authenticated_session(): void
    {
        $employee = $this->createUserWithRole(User::ROLE_EMPLOYEE);

        $response = $this->actingAs($employee)->post(route('logout'));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_trusted_role_command_assigns_an_existing_users_role(): void
    {
        $employee = User::factory()->create(['email' => 'employee@example.com']);

        $this->artisan('users:assign-role', [
            'email' => $employee->email,
            'role' => User::ROLE_ADMIN,
        ])->assertExitCode(0)->run();
        $adminRole = Role::query()->where('slug', User::ROLE_ADMIN)->firstOrFail();
        $this->assertDatabaseHas('users', [
            'email' => $employee->email,
            'role_id' => $adminRole->id,
        ]);
    }

    public function test_admin_creation_command_creates_an_active_admin_account(): void
    {
        $password = 'a-strong-admin-password';

        $this->artisan('users:create-admin', ['email' => ' ADMIN@example.com '])
            ->expectsQuestion('Administrator name', 'New Administrator')
            ->expectsQuestion('Password (minimum 12 characters)', $password)
            ->expectsQuestion('Confirm password', $password)
            ->assertExitCode(0)
            ->run();

        $adminRole = Role::query()->where('slug', User::ROLE_ADMIN)->firstOrFail();
        $admin = User::query()->where('email', 'admin@example.com')->firstOrFail();
        $this->assertSame($adminRole->id, $admin->role_id);
        $this->assertTrue($admin->is_active);
        $this->assertTrue(password_verify($password, $admin->password));
    }

    public function test_admin_creation_command_does_not_replace_an_existing_account(): void
    {
        $existingUser = User::factory()->create(['email' => 'admin@example.com']);

        $this->artisan('users:create-admin', ['email' => $existingUser->email])
            ->assertExitCode(1);

        $this->assertSame(1, User::query()->where('email', $existingUser->email)->count());
    }

    public function test_trusted_role_command_rejects_an_unknown_role(): void
    {
        $employee = User::factory()->create(['email' => 'employee@example.com']);

        $this->artisan('users:assign-role', [
            'email' => $employee->email,
            'role' => 'superuser',
        ])->assertExitCode(2);

        $this->assertDatabaseHas('users', [
            'email' => $employee->email,
            'role_id' => null,
        ]);
    }

    public function test_password_command_updates_password_after_matching_prompts(): void
    {
        $admin = $this->createUserWithRole(User::ROLE_ADMIN, [
            'email' => 'admin@example.com',
        ]);
        $newPassword = 'a-new-strong-password';

        $this->artisan('users:set-password', ['email' => $admin->email])
            ->expectsQuestion('New password (minimum 12 characters)', $newPassword)
            ->expectsQuestion('Confirm new password', $newPassword)
            ->assertExitCode(0);

        $this->assertDatabaseMissing('users', [
            'email' => $admin->email,
            'password' => $newPassword,
        ]);
        $this->assertTrue(password_verify($newPassword, $admin->fresh()->password));
    }

    public function test_password_command_does_not_change_password_when_confirmation_mismatches(): void
    {
        $admin = $this->createUserWithRole(User::ROLE_ADMIN, [
            'email' => 'admin@example.com',
        ]);
        $originalPassword = $admin->password;

        $this->artisan('users:set-password', ['email' => $admin->email])
            ->expectsQuestion('New password (minimum 12 characters)', 'a-new-strong-password')
            ->expectsQuestion('Confirm new password', 'a-different-strong-password')
            ->assertExitCode(2);

        $this->assertSame($originalPassword, $admin->fresh()->password);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function createUserWithRole(string $slug, array $attributes = []): User
    {
        $role = $this->createRole($slug);

        return User::factory()->create(array_merge(['role_id' => $role->id], $attributes));
    }

    private function createRole(string $slug): Role
    {
        return Role::query()->firstOrCreate(
            ['slug' => $slug],
            ['name' => ucfirst($slug)],
        );
    }
}
