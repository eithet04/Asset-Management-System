<?php

namespace Tests;

use App\Models\LoginOtpChallenge;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

abstract class TestCase extends BaseTestCase
{
    public function actingAs(Authenticatable $user, $guard = null)
    {
        parent::actingAs($user, $guard);

        if ($user instanceof User && $user->exists && Schema::hasTable('login_otp_challenges')) {
            $challenge = LoginOtpChallenge::query()->create([
                'user_id' => $user->id,
                'otp_hash' => Hash::make('000000'),
                'expires_at' => now()->addMinutes(5),
                'sent_at' => now(),
                'attempts' => 0,
                'verified_at' => now(),
            ]);
            $this->withSession(['two_factor_verified_challenge_id' => $challenge->id]);
        }

        return $this;
    }
}
