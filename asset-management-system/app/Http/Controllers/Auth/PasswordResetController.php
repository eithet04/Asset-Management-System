<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PasswordResetController extends Controller
{
    public function requestForm(): View
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(ForgotPasswordRequest $request): RedirectResponse
    {
        Password::sendResetLink($request->validated());

        return back()->with('status', 'If an account exists for that Gmail address, a password reset link has been sent.');
    }

    public function resetForm(string $token): View
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => request()->query('email', ''),
        ]);
    }

    public function reset(ResetPasswordRequest $request): RedirectResponse
    {
        $status = Password::reset(
            $request->validated(),
            function ($user, string $password): void {
                $user->forceFill([
                    'password' => $password,
                    'remember_token' => Str::random(60),
                ])->save();
                event(new PasswordReset($user));
            },
        );

        return $status === Password::PASSWORD_RESET
            ? to_route('login')->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }
}
