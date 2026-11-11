<?php

namespace App\Http\Middleware;

use App\Models\LoginOtpChallenge;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureTwoFactorVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $challengeId = $request->session()->get('two_factor_verified_challenge_id');

        if ($user && (! $user->requiresTwoFactorAuthentication()
            || ($challengeId && LoginOtpChallenge::query()
                ->whereKey($challengeId)
                ->where('user_id', $user->id)
                ->whereNotNull('verified_at')
                ->exists()))) {
            return $next($request);
        }

        if ($user) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        if ($request->session()->has('pending_login_challenge_id')) {
            return redirect()->route('otp.show');
        }

        return redirect()->route('login')->withErrors([
            'identifier' => 'Please sign in and complete email verification to continue.',
        ]);
    }
}
