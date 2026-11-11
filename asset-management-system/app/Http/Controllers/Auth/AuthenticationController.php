<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Mail\LoginOtpMail;
use App\Models\Employee;
use App\Models\LoginOtpChallenge;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class AuthenticationController extends Controller
{
    public function dashboard(Request $request): View|RedirectResponse
    {
        return $this->redirectAuthenticatedUser($request) ?? view('auth.login');
    }

    public function createLogin(Request $request): View|RedirectResponse
    {
        return $this->redirectAuthenticatedUser($request) ?? view('auth.login');
    }

    public function storeLogin(LoginRequest $request): RedirectResponse
    {
        $identifier = $request->validated('identifier');
        $user = User::query()
            ->where(str_contains($identifier, '@') ? 'email' : 'phone', $identifier)
            ->first();

        if ($user === null || ! Hash::check($request->validated('password'), $user->password)
            || ! $user->is_active || $user->dashboardRoute() === null) {
            return back()
                ->withErrors(['identifier' => 'These credentials do not match an active account.'])
                ->onlyInput('identifier');
        }

        if ($user->requiresEmailVerification() && ! $user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();

            return to_route('verification.notice', ['email' => $user->email])
                ->with('status', 'Verify your Gmail address before signing in. We sent you a verification link.');
        }

        if (! $user->requiresTwoFactorAuthentication()) {
            $request->session()->forget([
                'pending_login_challenge_id',
                'pending_login_remember',
                'two_factor_verified_challenge_id',
            ]);
            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();

            return redirect()->intended(route($user->dashboardRoute()));
        }

        $code = (string) random_int(100000, 999999);
        $challenge = DB::transaction(function () use ($user, $code): LoginOtpChallenge {
            User::query()->whereKey($user->id)->lockForUpdate()->firstOrFail();
            $user->loginOtpChallenges()->whereNull('verified_at')->delete();

            return $user->loginOtpChallenges()->create([
                'otp_hash' => Hash::make($code),
                'expires_at' => now()->addMinutes(5),
                'sent_at' => now(),
                'attempts' => 0,
            ]);
        });

        Mail::to($user->email)->send(new LoginOtpMail($code));

        $request->session()->put([
            'pending_login_challenge_id' => $challenge->id,
            'pending_login_remember' => $request->boolean('remember'),
        ]);

        return to_route('otp.show')->with('status', 'A sign-in code was sent to your verified Gmail address.');
    }

    public function createRegistration(Request $request): View|RedirectResponse
    {
        return $this->redirectAuthenticatedUser($request) ?? view('auth.register');
    }

    public function storeRegistration(RegisterRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $employeeRole = Role::query()->firstOrCreate(
            ['slug' => User::ROLE_EMPLOYEE],
            ['name' => 'Employee'],
        );

        $user = DB::transaction(function () use ($validated, $employeeRole): User {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'password' => $validated['password'],
                'role_id' => $employeeRole->id,
                'is_active' => true,
            ]);
            $employee = Employee::createForUser($user);
            $employee->update(['phone' => $user->phone]);

            return $user;
        });

        $user->sendEmailVerificationNotification();

        return to_route('verification.notice', ['email' => $user->email])
            ->with('status', 'Account created. Verify your Gmail address using the link we sent you.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function redirectAuthenticatedUser(Request $request): ?RedirectResponse
    {
        $user = Auth::user();

        if ($user === null) {
            return null;
        }

        $challengeId = $request->session()->get('two_factor_verified_challenge_id');
        $verified = ! $user->requiresTwoFactorAuthentication()
            || ($challengeId && $user->loginOtpChallenges()
                ->whereKey($challengeId)
                ->whereNotNull('verified_at')
                ->exists());
        if ($user->is_active
            && (! $user->requiresEmailVerification() || $user->hasVerifiedEmail())
            && $verified
            && $user->dashboardRoute() !== null) {
            return redirect()->route($user->dashboardRoute());
        }

        if ($request->session()->has('pending_login_challenge_id')) {
            return redirect()->route('otp.show');
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return null;
    }
}
