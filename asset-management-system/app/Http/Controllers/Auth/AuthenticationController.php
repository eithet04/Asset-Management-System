<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;
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

    public function storeLogin(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => 'These credentials do not match our records.'])
                ->onlyInput('email');
        }

        $user = Auth::user();
        $dashboardRoute = $user?->dashboardRoute();

        if ($user === null || ! $user->is_active || $dashboardRoute === null) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()
                ->withErrors(['email' => 'This account is not permitted to sign in.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->route($dashboardRoute);
    }

    public function createRegistration(Request $request): View|RedirectResponse
    {
        return $this->redirectAuthenticatedUser($request) ?? view('auth.register');
    }

    public function storeRegistration(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $employeeRole = Role::query()->firstOrCreate(
            ['slug' => User::ROLE_EMPLOYEE],
            ['name' => 'Employee'],
        );

        $user = DB::transaction(function () use ($validated, $employeeRole): User {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $validated['password'],
                'role_id' => $employeeRole->id,
                'is_active' => true,
            ]);
            Employee::createForUser($user);

            return $user;
        });

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('employee.dashboard');
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

        $dashboardRoute = $user->dashboardRoute();

        if ($user->is_active && $dashboardRoute !== null) {
            return redirect()->route($dashboardRoute);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return null;
    }
}
