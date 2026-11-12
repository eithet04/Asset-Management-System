<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_sees_the_login_form_at_the_root_url(): void
    {
        $this->get(route('home'))->assertOk()->assertViewIs('auth.login');
    }

    public function test_registration_creates_an_employee_and_requires_gmail_verification(): void
    {
        Notification::fake();
        $adminRole = $this->createRole(User::ROLE_ADMIN);

        $response = $this->post(route('register.store'), [
            'name' => 'Taylor Employee',
            'email' => 'Taylor@gmail.com',
            'phone' => '09 761234567',
            'password' => 'Secure-password1!',
            'password_confirmation' => 'Secure-password1!',
            'role_id' => $adminRole->id,
        ]);

        $employeeRole = Role::query()->where('slug', User::ROLE_EMPLOYEE)->firstOrFail();
        $response->assertRedirect(route('verification.notice', ['email' => 'taylor@gmail.com']));
        $this->assertGuest();
        $this->assertDatabaseHas('users', [
            'email' => 'taylor@gmail.com',
            'phone' => '+959761234567',
            'role_id' => $employeeRole->id,
            'is_active' => true,
        ]);
        $user = User::query()->where('email', 'taylor@gmail.com')->firstOrFail();
        Notification::assertSentTo($user, VerifyEmail::class);
        $this->get(route('employee.dashboard'))->assertRedirect(route('login'));
    }

    public function test_gmail_address_is_verified_before_login_and_phone_login_works_without_otp(): void
    {
        $manager = $this->createUserWithRole(User::ROLE_MANAGER, [
            'email' => 'manager@gmail.com',
            'phone' => '+959761234567',
            'password' => 'Secure-password1!',
        ]);
        $this->signIn($manager, '959 761234567')
            ->assertRedirect(route('manager.dashboard'));
        $this->assertAuthenticatedAs($manager);
    }

    public function test_new_roles_redirect_to_their_role_specific_workspace_without_otp(): void
    {
        foreach ([
            User::ROLE_HR_ADMIN_STAFF => 'operations.people',
            User::ROLE_IT_TECHNICIAN => 'operations.maintenance',
            User::ROLE_DEPARTMENT_MANAGER => 'manager.dashboard',
            User::ROLE_MANAGEMENT_VIEWER => 'operations.reports',
        ] as $role => $destination) {
            $user = $this->createUserWithRole($role, [
                'email' => $role.'@gmail.com',
                'password' => 'Secure-password1!',
            ]);
            $this->signIn($user, $user->email)->assertRedirect(route($destination));
            $this->post(route('logout'));
        }
    }

    public function test_admins_and_asset_controllers_sign_in_without_email_otp(): void
    {
        foreach ([
            User::ROLE_ADMIN => 'admin.dashboard',
            User::ROLE_ASSET_CONTROLLER => 'manager.dashboard',
        ] as $role => $destination) {
            $user = $this->createUserWithRole($role, [
                'email' => $role.'@gmail.com',
                'password' => 'Secure-password1!',
            ]);

            $this->post(route('login.store'), [
                'identifier' => $user->email,
                'password' => 'Secure-password1!',
            ])->assertRedirect(route($destination));
            $this->assertAuthenticatedAs($user);
            $this->get(route($destination))->assertOk();
            $this->post(route('logout'));
        }
    }

    public function test_unverified_admin_can_sign_in_without_sending_verification_email(): void
    {
        Notification::fake();
        $admin = $this->createUserWithRole(User::ROLE_ADMIN, [
            'email' => 'admin.unverified@gmail.com',
            'password' => 'Secure-password1!',
            'email_verified_at' => null,
        ]);

        $this->post(route('login.store'), [
            'identifier' => $admin->email,
            'password' => 'Secure-password1!',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($admin);
        $this->get(route('admin.dashboard'))->assertOk();
        Notification::assertNothingSent();
    }

    public function test_role_command_creates_an_employee_profile_for_new_staff_roles(): void
    {
        $user = User::factory()->create(['email' => 'technician@gmail.com']);

        $this->artisan('users:assign-role', [
            'email' => $user->email,
            'role' => User::ROLE_IT_TECHNICIAN,
        ])->assertExitCode(0);

        $this->assertDatabaseHas('employees', ['user_id' => $user->id]);
    }

    public function test_unverified_email_cannot_start_login_and_receives_a_verification_link(): void
    {
        Notification::fake();
        $user = $this->createUserWithRole(User::ROLE_EMPLOYEE, [
            'email' => 'unverified@gmail.com',
            'password' => 'Secure-password1!',
            'email_verified_at' => null,
        ]);

        $this->post(route('login.store'), [
            'identifier' => $user->email,
            'password' => 'Secure-password1!',
        ])->assertRedirect(route('verification.notice', ['email' => $user->email]));
        $this->assertGuest();
        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_signed_verification_link_marks_email_verified_without_authenticating(): void
    {
        $user = $this->createUserWithRole(User::ROLE_EMPLOYEE, [
            'email' => 'verify.me@gmail.com',
            'email_verified_at' => null,
        ]);
        $url = URL::temporarySignedRoute('verification.verify', now()->addMinutes(30), [
            'id' => $user->id,
            'hash' => sha1($user->email),
        ]);

        $this->get($url)->assertRedirect(route('login'));
        $this->assertNotNull($user->fresh()->email_verified_at);
        $this->assertGuest();
    }

    public function test_employee_signs_in_without_email_otp(): void
    {
        $user = $this->createUserWithRole(User::ROLE_EMPLOYEE, [
            'email' => 'otp.user@gmail.com',
            'password' => 'Secure-password1!',
        ]);
        $this->post(route('login.store'), [
            'identifier' => $user->email,
            'password' => 'Secure-password1!',
        ])->assertRedirect(route('employee.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_protected_routes_only_require_authentication_and_role(): void
    {
        $employee = $this->createUserWithRole(User::ROLE_EMPLOYEE);

        $this->actingAs($employee)
            ->get(route('employee.dashboard'))
            ->assertOk();
    }

    public function test_forgot_password_resets_password_and_invalidates_the_token(): void
    {
        Notification::fake();
        $user = $this->createUserWithRole(User::ROLE_EMPLOYEE, ['email' => 'reset.user@gmail.com']);
        $oldPassword = $user->password;

        $this->post(route('password.email'), ['email' => $user->email])->assertSessionHas('status');
        Notification::assertSentTo($user, ResetPassword::class);
        $token = Notification::sent($user, ResetPassword::class)->first()->token;

        $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'New-secure-password2!',
            'password_confirmation' => 'New-secure-password2!',
        ])->assertRedirect(route('login'));

        $this->assertNotSame($oldPassword, $user->fresh()->password);
        $this->assertTrue(Hash::check('New-secure-password2!', $user->fresh()->password));
        $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'Another-secure-password3!',
            'password_confirmation' => 'Another-secure-password3!',
        ])->assertSessionHasErrors('email');
    }

    public function test_registration_rejects_non_gmail_invalid_phone_and_weak_password(): void
    {
        $this->post(route('register.store'), [
            'name' => 'Invalid Account',
            'email' => 'invalid@example.com',
            'phone' => '123456789',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertSessionHasErrors(['email', 'phone', 'password']);

        $this->assertDatabaseCount('users', 0);
    }

    public function test_inactive_accounts_cannot_sign_in(): void
    {
        $this->createUserWithRole(User::ROLE_EMPLOYEE, [
            'email' => 'inactive@gmail.com',
            'password' => 'Secure-password1!',
            'is_active' => false,
        ]);

        $this->from(route('login'))->post(route('login.store'), [
            'identifier' => 'inactive@gmail.com',
            'password' => 'Secure-password1!',
        ])->assertRedirect(route('login'))->assertSessionHasErrors('identifier');
        $this->assertGuest();
    }

    public function test_invalid_credentials_do_not_authenticate_a_user(): void
    {
        $this->createUserWithRole(User::ROLE_EMPLOYEE, [
            'email' => 'employee@gmail.com',
            'password' => 'Secure-password1!',
        ]);

        $this->from(route('login'))->post(route('login.store'), [
            'identifier' => 'employee@gmail.com',
            'password' => 'incorrect-password',
        ])->assertRedirect(route('login'))->assertSessionHasErrors('identifier');
        $this->assertGuest();
    }

    public function test_logout_ends_the_authenticated_session(): void
    {
        $employee = $this->createUserWithRole(User::ROLE_EMPLOYEE);

        $this->actingAs($employee)->post(route('logout'))->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_trusted_role_command_assigns_an_existing_users_role(): void
    {
        $employee = User::factory()->create(['email' => 'employee@gmail.com']);

        $this->artisan('users:assign-role', [
            'email' => $employee->email,
            'role' => User::ROLE_ADMIN,
        ])->assertExitCode(0);
        $adminRole = Role::query()->where('slug', User::ROLE_ADMIN)->firstOrFail();
        $this->assertDatabaseHas('users', [
            'email' => $employee->email,
            'role_id' => $adminRole->id,
        ]);
    }

    public function test_admin_creation_command_creates_an_active_admin_account(): void
    {
        $password = 'Strong-Admin-Password1!';

        $this->artisan('users:create-admin', ['email' => ' ADMIN@gmail.com '])
            ->expectsQuestion('Administrator name', 'New Administrator')
            ->expectsQuestion('Password (minimum 12 characters)', $password)
            ->expectsQuestion('Confirm password', $password)
            ->assertExitCode(0);

        $adminRole = Role::query()->where('slug', User::ROLE_ADMIN)->firstOrFail();
        $admin = User::query()->where('email', 'admin@gmail.com')->firstOrFail();
        $this->assertSame($adminRole->id, $admin->role_id);
        $this->assertTrue($admin->is_active);
        $this->assertTrue(password_verify($password, $admin->password));
    }

    public function test_admin_creation_command_does_not_replace_an_existing_account(): void
    {
        $existingUser = User::factory()->create(['email' => 'admin@gmail.com']);

        $this->artisan('users:create-admin', ['email' => $existingUser->email])->assertExitCode(1);
        $this->assertSame(1, User::query()->where('email', $existingUser->email)->count());
    }

    public function test_trusted_role_command_rejects_an_unknown_role(): void
    {
        $employee = User::factory()->create(['email' => 'employee@gmail.com']);

        $this->artisan('users:assign-role', [
            'email' => $employee->email,
            'role' => 'superuser',
        ])->assertExitCode(2);
        $this->assertDatabaseHas('users', ['email' => $employee->email, 'role_id' => null]);
    }

    public function test_password_command_updates_password_after_matching_prompts(): void
    {
        $admin = $this->createUserWithRole(User::ROLE_ADMIN, ['email' => 'admin@gmail.com']);
        $newPassword = 'A-New-Strong-Password1!';

        $this->artisan('users:set-password', ['email' => $admin->email])
            ->expectsQuestion('New password (minimum 12 characters)', $newPassword)
            ->expectsQuestion('Confirm new password', $newPassword)
            ->assertExitCode(0);

        $this->assertDatabaseMissing('users', ['email' => $admin->email, 'password' => $newPassword]);
        $this->assertTrue(password_verify($newPassword, $admin->fresh()->password));
    }

    public function test_password_command_does_not_change_password_when_confirmation_mismatches(): void
    {
        $admin = $this->createUserWithRole(User::ROLE_ADMIN, ['email' => 'admin@gmail.com']);
        $originalPassword = $admin->password;

        $this->artisan('users:set-password', ['email' => $admin->email])
            ->expectsQuestion('New password (minimum 12 characters)', 'A-New-Strong-Password1!')
            ->expectsQuestion('Confirm new password', 'A-Different-Strong-Password2!')
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
        return Role::query()->firstOrCreate(['slug' => $slug], ['name' => ucfirst($slug)]);
    }

    private function signIn(User $user, string $identifier)
    {
        return $this->post(route('login.store'), [
            'identifier' => $identifier,
            'password' => 'Secure-password1!',
        ]);
    }
}
