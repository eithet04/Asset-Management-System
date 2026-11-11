<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationController extends Controller
{
    public function notice(Request $request): View
    {
        return view('auth.verify-email', ['email' => $request->query('email', '')]);
    }

    public function verify(Request $request, int $id, string $hash): RedirectResponse
    {
        $user = User::query()->findOrFail($id);
        abort_unless(hash_equals(sha1($user->getEmailForVerification()), $hash), 403);

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return to_route('login')->with('status', 'Gmail verified. Sign in with your password to continue.');
    }

    public function resend(Request $request): RedirectResponse
    {
        $request->merge(['email' => strtolower(trim((string) $request->input('email', '')))]);
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255', 'ends_with:@gmail.com', function ($attribute, $value, $fail): void {
                if (! filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $fail('Enter a valid Gmail address.');
                }
            }],
        ]);
        $email = strtolower(trim($validated['email']));
        $user = User::query()->where('email', $email)->first();
        if ($user && ! $user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();
        }

        return to_route('verification.notice', ['email' => $email])
            ->with('status', 'If the account exists and still needs verification, a new link has been sent.');
    }
}
