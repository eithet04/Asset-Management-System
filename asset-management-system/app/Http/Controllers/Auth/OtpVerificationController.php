<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Mail\LoginOtpMail;
use App\Models\LoginOtpChallenge;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class OtpVerificationController extends Controller
{
    private const MAX_ATTEMPTS = 5;

    public function show(Request $request): View|RedirectResponse
    {
        $challenge = $this->pendingChallenge($request);
        if ($challenge === null) {
            return to_route('login')->withErrors(['identifier' => 'Start a new sign-in attempt to receive a code.']);
        }

        return view('auth.verify-otp', [
            'email' => $this->maskEmail($challenge->user->email),
            'resendAfter' => max(0, 60 - (int) $challenge->sent_at->diffInSeconds(now())),
            'expired' => $challenge->expires_at->isPast(),
            'locked' => $challenge->attempts >= self::MAX_ATTEMPTS,
        ]);
    }

    public function verify(VerifyOtpRequest $request): RedirectResponse
    {
        $challengeId = $request->session()->get('pending_login_challenge_id');
        $result = DB::transaction(function () use ($challengeId, $request): string {
            $challenge = LoginOtpChallenge::query()->whereKey($challengeId)->lockForUpdate()->first();
            if ($challenge === null || $challenge->verified_at !== null) {
                return 'invalid';
            }
            if ($challenge->expires_at->isPast()) {
                return 'expired';
            }
            if ($challenge->attempts >= self::MAX_ATTEMPTS) {
                return 'locked';
            }
            if (! Hash::check($request->validated('code'), $challenge->otp_hash)) {
                $challenge->increment('attempts');

                return $challenge->attempts >= self::MAX_ATTEMPTS ? 'locked' : 'invalid';
            }

            $challenge->forceFill(['verified_at' => now()])->save();

            return 'verified';
        });

        if ($result !== 'verified') {
            $message = match ($result) {
                'expired' => 'This code has expired. Request a new code to continue.',
                'locked' => 'Too many incorrect attempts. Request a new code to continue.',
                default => 'The code is incorrect. Check it and try again.',
            };

            return back()->withErrors(['code' => $message]);
        }

        $challenge = LoginOtpChallenge::query()
            ->with('user.role')
            ->findOrFail($challengeId);
        $user = $challenge->user;
        if (! $user->is_active || ! $user->hasVerifiedEmail() || $user->dashboardRoute() === null) {
            $request->session()->forget(['pending_login_challenge_id', 'pending_login_remember']);

            return to_route('login')->withErrors(['identifier' => 'This account is not permitted to sign in.']);
        }

        $remember = $request->session()->pull('pending_login_remember', false);
        $request->session()->forget('pending_login_challenge_id');
        Auth::login($user, (bool) $remember);
        $request->session()->regenerate();
        $request->session()->put('two_factor_verified_challenge_id', $challenge->id);

        return redirect()->intended(route($user->dashboardRoute()));
    }

    public function resend(Request $request): RedirectResponse
    {
        $challenge = $this->pendingChallenge($request);
        if ($challenge === null) {
            return to_route('login')->withErrors(['identifier' => 'Start a new sign-in attempt to receive a code.']);
        }

        $code = (string) random_int(100000, 999999);
        $result = DB::transaction(function () use ($challenge, $code): array {
            $lockedChallenge = LoginOtpChallenge::query()
                ->whereKey($challenge->id)
                ->lockForUpdate()
                ->first();
            if ($lockedChallenge === null || $lockedChallenge->verified_at !== null) {
                return ['missing' => true];
            }

            $remaining = 60 - (int) $lockedChallenge->sent_at->diffInSeconds(now());
            if ($remaining > 0) {
                return ['remaining' => $remaining];
            }

            $lockedChallenge->forceFill([
                'otp_hash' => Hash::make($code),
                'expires_at' => now()->addMinutes(5),
                'sent_at' => now(),
                'attempts' => 0,
                'verified_at' => null,
            ])->save();

            return ['challenge' => $lockedChallenge->load('user')];
        });

        if (isset($result['missing'])) {
            return to_route('login')->withErrors(['identifier' => 'Start a new sign-in attempt to receive a code.']);
        }
        if (isset($result['remaining'])) {
            $remaining = $result['remaining'];

            return back()->withErrors(['code' => "Please wait {$remaining} seconds before requesting another code."]);
        }

        $challenge = $result['challenge'];
        Mail::to($challenge->user->email)->send(new LoginOtpMail($code));

        return back()->with('status', 'A new sign-in code has been sent to your Gmail address.');
    }

    private function pendingChallenge(Request $request): ?LoginOtpChallenge
    {
        $id = $request->session()->get('pending_login_challenge_id');
        if (! $id) {
            return null;
        }

        return LoginOtpChallenge::query()->with('user')->find($id);
    }

    private function maskEmail(string $email): string
    {
        [$name, $domain] = explode('@', $email, 2);

        return substr($name, 0, 1).str_repeat('*', max(1, strlen($name) - 1)).'@'.$domain;
    }
}
