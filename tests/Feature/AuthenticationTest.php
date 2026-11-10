<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_creates_an_employee_even_if_admin_role_is_submitted(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => 'Taylor Employee',
            'email' => 'taylor@example.com',
            'password' => 'secure-password',
            'password_confirmation' => 'secure-password',
            'role' => User::ROLE_ADMIN,
        ]);

        $response->assertRedirect(route('employee.dashboard'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'taylor@example.com',
            'role' => User::ROLE_EMPLOYEE,
        ]);
    }

    public function test_user_sign_in_redirects_to_their_role_dashboard(): void
    {
        $manager = User::factory()->create([
            'email' => 'manager@example.com',
            'password' => 'secure-password',
            'role' => User::ROLE_MANAGER,
        ]);

        $response = $this->post(route('login.store'), [
            'email' => 'manager@example.com',
            'password' => 'secure-password',
        ]);

        $response->assertRedirect(route('manager.dashboard'));
        $this->assertAuthenticatedAs($manager);
    }

    public function test_invalid_credentials_do_not_authenticate_a_user(): void
    {
        User::factory()->create([
            'email' => 'employee@example.com',
            'password' => 'secure-password',
        ]);

        $response = $this->from(route('login'))->post(route('login.store'), [
            'email' => 'employee@example.com',
            'password' => 'incorrect-password',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors(['email' => 'These credentials do not match our records.']);
        $this->assertGuest();
    }

    public function test_logout_ends_the_authenticated_session(): void
    {
        $employee = User::factory()->create();

        $response = $this->actingAs($employee)->post(route('logout'));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_trusted_role_command_assigns_an_existing_users_role(): void
    {
        $employee = User::factory()->create(['email' => 'employee@example.com']);

        $exitCode = Artisan::call('users:assign-role', [
            'email' => $employee->email,
            'role' => User::ROLE_ADMIN,
        ]);

        $this->assertSame(0, $exitCode);
        $this->assertDatabaseHas('users', [
            'email' => $employee->email,
            'role' => User::ROLE_ADMIN,
        ]);
    }

    public function test_trusted_role_command_rejects_an_unknown_role(): void
    {
        $employee = User::factory()->create(['email' => 'employee@example.com']);

        $exitCode = Artisan::call('users:assign-role', [
            'email' => $employee->email,
            'role' => 'superuser',
        ]);

        $this->assertSame(2, $exitCode);
        $this->assertDatabaseHas('users', [
            'email' => $employee->email,
            'role' => User::ROLE_EMPLOYEE,
        ]);
    }

    public function test_password_command_updates_password_after_matching_hidden_prompts(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'role' => User::ROLE_ADMIN,
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
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'role' => User::ROLE_ADMIN,
        ]);
        $originalPassword = $admin->password;

        $this->artisan('users:set-password', ['email' => $admin->email])
            ->expectsQuestion('New password (minimum 12 characters)', 'a-new-strong-password')
            ->expectsQuestion('Confirm new password', 'a-different-strong-password')
            ->assertExitCode(2);

        $this->assertSame($originalPassword, $admin->fresh()->password);
    }
}
